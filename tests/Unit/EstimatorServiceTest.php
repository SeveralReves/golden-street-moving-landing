<?php

namespace Tests\Unit;

use App\Services\EstimatorService;
use PHPUnit\Framework\TestCase;

class EstimatorServiceTest extends TestCase
{
    /**
     * A confirmed, deterministic set of settings so every test below is
     * predictable. Individual tests override specific keys to exercise
     * a single rule at a time.
     */
    private function settings(array $overrides = []): array
    {
        $defaults = [
            'hourly_rate' => 140,
            'labor_only_rate' => 110,
            'stairs_fee' => 100,
            'travel_threshold_miles' => 35,
            'travel_fee' => 50,
            'min_hours' => 2,
            'hours_studio' => 2,
            'hours_1' => 3,
            'hours_2' => 4,
            'hours_3' => 5,
            'hours_4' => 7,
            'hours_office' => 5,
            'stairs_hours_per_floor' => 0.25,
            'elevator_extra_hours' => 0.5,
            'packing_extra_hours' => 2,
            'item_large_fridge' => 25,
            'item_piano' => 75,
            'item_pool_table' => 60,
            'item_jacuzzi' => 60,
            'item_safe' => 40,
            'range_pct' => 15,
        ];

        $settings = [];
        foreach (array_merge($defaults, $overrides) as $key => $value) {
            $settings[$key] = ['value' => $value, 'confirmed' => true];
        }

        return $settings;
    }

    private function baseInput(array $overrides = []): array
    {
        return array_merge([
            'move_type' => 'house',
            'bedrooms' => '2',
            'origin_floor' => 'ground',
            'origin_elevator' => false,
            'destination_floor' => 'ground',
            'destination_elevator' => false,
            'packing_service' => false,
            'special_items' => [],
            'miles' => null,
        ], $overrides);
    }

    public function test_minimum_hours_floor_applies_when_the_raw_total_is_lower(): void
    {
        // A studio move (2 hours base) with no stairs, elevator, or packing
        // would otherwise bill for less than the 2-hour minimum... so bump
        // min_hours above the base to make sure the floor actually kicks in.
        $service = new EstimatorService($this->settings(['min_hours' => 6, 'hours_studio' => 2]));

        $result = $service->estimate($this->baseInput(['bedrooms' => 'studio']));

        $this->assertSame(6.0, $result['hours']);
        $this->assertSame(6.0 * 140, $result['labor_cost']);
    }

    public function test_stairs_on_a_single_end_add_hours_and_a_single_stairs_fee(): void
    {
        $service = new EstimatorService($this->settings());

        $result = $service->estimate($this->baseInput([
            'origin_floor' => '2',
            'origin_elevator' => false,
            'destination_floor' => 'ground',
        ]));

        // base 4h + 2 floors * 0.25h = 4.5h
        $this->assertSame(4.5, $result['hours']);
        $this->assertContains(['label' => 'Stairs fee', 'amount' => 100.0], $result['breakdown']);
        $this->assertSame(100.0, $result['fixed_fees']);
    }

    public function test_stairs_on_both_ends_add_hours_for_each_end_but_only_one_stairs_fee(): void
    {
        $service = new EstimatorService($this->settings());

        $result = $service->estimate($this->baseInput([
            'origin_floor' => '2',
            'origin_elevator' => false,
            'destination_floor' => '1',
            'destination_elevator' => false,
        ]));

        // base 4h + origin 2*0.25 + destination 1*0.25 = 4.75h
        $this->assertSame(4.75, $result['hours']);
        // Stairs fee must appear exactly once even though both ends have stairs.
        $stairsFeeLines = array_filter($result['breakdown'], fn ($line) => $line['label'] === 'Stairs fee');
        $this->assertCount(1, $stairsFeeLines);
        $this->assertSame(100.0, $result['fixed_fees']);
    }

    public function test_elevator_adds_elevator_hours_instead_of_stairs_hours_and_no_stairs_fee(): void
    {
        $service = new EstimatorService($this->settings());

        $result = $service->estimate($this->baseInput([
            'origin_floor' => '3+',
            'origin_elevator' => true,
        ]));

        // base 4h + elevator 0.5h (flat, regardless of floor count)
        $this->assertSame(4.5, $result['hours']);
        $this->assertSame(0.0, $result['fixed_fees']);
        $stairsFeeLines = array_filter($result['breakdown'], fn ($line) => $line['label'] === 'Stairs fee');
        $this->assertCount(0, $stairsFeeLines);
    }

    public function test_labor_only_never_gets_a_travel_fee_even_when_miles_exceed_the_threshold(): void
    {
        $service = new EstimatorService($this->settings());

        $result = $service->estimate($this->baseInput([
            'move_type' => 'labor_only',
            'miles' => 100,
        ]));

        $this->assertSame(4.0 * 110, $result['labor_cost']);
        $this->assertSame(0.0, $result['fixed_fees']);
        $travelFeeLines = array_filter($result['breakdown'], fn ($line) => str_starts_with($line['label'], 'Travel fee'));
        $this->assertCount(0, $travelFeeLines);
    }

    public function test_a_non_labor_only_move_over_the_travel_threshold_is_charged_the_travel_fee(): void
    {
        $service = new EstimatorService($this->settings());

        $result = $service->estimate($this->baseInput(['miles' => 40]));

        $this->assertSame(50.0, $result['fixed_fees']);
    }

    public function test_special_items_accumulate_their_individual_fees(): void
    {
        $service = new EstimatorService($this->settings());

        $result = $service->estimate($this->baseInput([
            'special_items' => ['piano', 'safe', 'large_fridge'],
        ]));

        // 75 (piano) + 40 (safe) + 25 (large_fridge)
        $this->assertSame(140.0, $result['fixed_fees']);
    }

    public function test_office_move_type_uses_hours_office_regardless_of_bedrooms(): void
    {
        $service = new EstimatorService($this->settings());

        $result = $service->estimate($this->baseInput(['move_type' => 'office', 'bedrooms' => '4+']));

        $this->assertSame(5.0, $result['hours']);
    }

    public function test_unconfirmed_settings_used_in_the_calculation_are_reported(): void
    {
        $settings = $this->settings();
        $settings['elevator_extra_hours']['confirmed'] = false;
        $settings['hours_2']['confirmed'] = false; // used (base hours for 2 bedrooms)
        $settings['item_piano']['confirmed'] = false; // not used in this scenario

        $service = new EstimatorService($settings);

        $result = $service->estimate($this->baseInput([
            'origin_floor' => '2',
            'origin_elevator' => true,
        ]));

        $this->assertContains('elevator_extra_hours', $result['unconfirmed_used']);
        $this->assertContains('hours_2', $result['unconfirmed_used']);
        $this->assertNotContains('item_piano', $result['unconfirmed_used']);
    }

    public function test_range_is_calculated_as_a_percentage_band_around_the_total(): void
    {
        $service = new EstimatorService($this->settings(['range_pct' => 15]));

        $result = $service->estimate($this->baseInput());

        $expectedTotal = 4.0 * 140; // base 4h, no extras
        $this->assertSame(round($expectedTotal, 2), $result['total']);
        $this->assertSame(round($expectedTotal * 0.85, 2), $result['range_low']);
        $this->assertSame(round($expectedTotal * 1.15, 2), $result['range_high']);
    }
}
