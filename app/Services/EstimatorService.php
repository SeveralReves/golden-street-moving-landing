<?php

namespace App\Services;

use App\Models\PricingSetting;

class EstimatorService
{
    private const SPECIAL_ITEM_KEYS = [
        'large_fridge' => 'item_large_fridge',
        'piano' => 'item_piano',
        'pool_table' => 'item_pool_table',
        'jacuzzi' => 'item_jacuzzi',
        'safe' => 'item_safe',
    ];

    private const BEDROOM_HOURS_KEYS = [
        'studio' => 'hours_studio',
        '1' => 'hours_1',
        '2' => 'hours_2',
        '3' => 'hours_3',
        '4+' => 'hours_4',
    ];

    private const FLOOR_VALUES = [
        'ground' => 0,
        '1' => 1,
        '2' => 2,
        '3+' => 3,
    ];

    /**
     * @param array<string, array{value: float, confirmed: bool}> $settings keyed by pricing_settings.key
     */
    public function __construct(private array $settings)
    {
    }

    public static function fromDatabase(): self
    {
        $settings = PricingSetting::all()
            ->mapWithKeys(fn (PricingSetting $setting) => [
                $setting->key => [
                    'value' => (float) $setting->value,
                    'confirmed' => (bool) $setting->confirmed,
                ],
            ])
            ->all();

        return new self($settings);
    }

    /**
     * Calculate a moving estimate. This is the single source of truth used
     * by both the public booking flow and the admin simulator, so they can
     * never disagree.
     *
     * @param array{
     *     move_type: string,
     *     bedrooms: string,
     *     origin_floor: string,
     *     origin_elevator: bool,
     *     destination_floor: string,
     *     destination_elevator: bool,
     *     packing_service: bool,
     *     special_items?: array<int, string>,
     *     miles?: float|null,
     * } $input
     * @return array{
     *     hours: float,
     *     rate: float,
     *     labor_cost: float,
     *     fixed_fees: float,
     *     total: float,
     *     range_low: float,
     *     range_high: float,
     *     range_pct: float,
     *     breakdown: array<int, array{label: string, amount: float}>,
     *     unconfirmed_used: array<int, string>,
     * }
     */
    public function estimate(array $input): array
    {
        $used = [];
        $breakdown = [];

        $moveType = $input['move_type'];
        $isLaborOnly = $moveType === 'labor_only';

        // Base hours
        if ($moveType === 'office') {
            $baseHours = $this->value('hours_office', $used);
            $breakdown[] = ['label' => 'Base hours (office)', 'amount' => $baseHours];
        } else {
            $bedroomKey = self::BEDROOM_HOURS_KEYS[$input['bedrooms']] ?? null;
            $baseHours = $bedroomKey ? $this->value($bedroomKey, $used) : 0.0;
            $breakdown[] = ['label' => 'Base hours (' . $input['bedrooms'] . ' bedroom(s))', 'amount' => $baseHours];
        }

        $hours = $baseHours;

        // Origin / destination floor handling
        $anyUncoveredStairs = false;

        foreach (['origin', 'destination'] as $end) {
            $floor = self::FLOOR_VALUES[$input["{$end}_floor"]] ?? 0;

            if ($floor > 0) {
                if (! empty($input["{$end}_elevator"])) {
                    $extra = $this->value('elevator_extra_hours', $used);
                    $hours += $extra;
                    $breakdown[] = ['label' => ucfirst($end) . ' elevator', 'amount' => $extra];
                } else {
                    $anyUncoveredStairs = true;
                    $extra = $floor * $this->value('stairs_hours_per_floor', $used);
                    $hours += $extra;
                    $breakdown[] = ['label' => ucfirst($end) . " stairs ({$floor} floor(s))", 'amount' => $extra];
                }
            }
        }

        // Packing
        if (! empty($input['packing_service'])) {
            $extra = $this->value('packing_extra_hours', $used);
            $hours += $extra;
            $breakdown[] = ['label' => 'Packing service', 'amount' => $extra];
        }

        // Minimum hours floor
        $minHours = $this->value('min_hours', $used);
        $hours = max($hours, $minHours);

        // Labor cost
        $rate = $isLaborOnly
            ? $this->value('labor_only_rate', $used)
            : $this->value('hourly_rate', $used);

        $laborCost = $hours * $rate;
        $breakdown[] = ['label' => 'Labor (' . round($hours, 2) . ' hrs @ $' . $rate . '/hr)', 'amount' => $laborCost];

        // Fixed fees
        $fixedFees = 0.0;

        if ($anyUncoveredStairs) {
            $stairsFee = $this->value('stairs_fee', $used);
            $fixedFees += $stairsFee;
            $breakdown[] = ['label' => 'Stairs fee', 'amount' => $stairsFee];
        }

        $miles = $input['miles'] ?? null;
        if (! $isLaborOnly && $miles !== null) {
            $threshold = $this->value('travel_threshold_miles', $used);
            if ($miles > $threshold) {
                $travelFee = $this->value('travel_fee', $used);
                $fixedFees += $travelFee;
                $breakdown[] = ['label' => 'Travel fee (' . $miles . ' miles)', 'amount' => $travelFee];
            }
        }

        foreach ($input['special_items'] ?? [] as $item) {
            $key = self::SPECIAL_ITEM_KEYS[$item] ?? null;
            if (! $key) {
                continue;
            }
            $fee = $this->value($key, $used);
            $fixedFees += $fee;
            $breakdown[] = ['label' => 'Special item: ' . str_replace('_', ' ', $item), 'amount' => $fee];
        }

        $total = $laborCost + $fixedFees;

        $rangePct = $this->value('range_pct', $used);
        $rangeAmount = $total * ($rangePct / 100);

        $unconfirmedUsed = array_values(array_unique(array_keys(array_filter(
            $used,
            fn (bool $confirmed) => ! $confirmed
        ))));

        return [
            'hours' => round($hours, 2),
            'rate' => $rate,
            'labor_cost' => round($laborCost, 2),
            'fixed_fees' => round($fixedFees, 2),
            'total' => round($total, 2),
            'range_low' => round($total - $rangeAmount, 2),
            'range_high' => round($total + $rangeAmount, 2),
            'range_pct' => $rangePct,
            'miles' => $miles,
            'breakdown' => $breakdown,
            'unconfirmed_used' => $unconfirmedUsed,
        ];
    }

    /**
     * Reads a setting's value and records whether it was confirmed, so the
     * caller can tell which unconfirmed values fed into this calculation.
     *
     * @param array<string, bool> $used
     */
    private function value(string $key, array &$used): float
    {
        $setting = $this->settings[$key] ?? ['value' => 0.0, 'confirmed' => false];
        $used[$key] = $setting['confirmed'];

        return (float) $setting['value'];
    }
}
