<?php

namespace Database\Seeders;

use App\Models\PricingSetting;
use Illuminate\Database\Seeder;

class PricingSettingSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['key' => 'hourly_rate', 'value' => 140, 'unit' => 'usd', 'confirmed' => true, 'group' => 'rates', 'label' => 'Hourly rate', 'note' => '2 movers, 15-20ft truck, wrapping included'],
            ['key' => 'labor_only_rate', 'value' => 110, 'unit' => 'usd', 'confirmed' => true, 'group' => 'rates', 'label' => 'Labor-only rate', 'note' => 'Labor only, no truck'],
            ['key' => 'stairs_fee', 'value' => 100, 'unit' => 'usd', 'confirmed' => true, 'group' => 'fixed_fees', 'label' => 'Stairs fee', 'note' => 'Rolled into the estimate, not itemized to the customer'],
            ['key' => 'travel_threshold_miles', 'value' => 35, 'unit' => 'miles', 'confirmed' => true, 'group' => 'travel', 'label' => 'Travel fee threshold', 'note' => 'Travel is charged past this distance'],
            ['key' => 'hours_2', 'value' => 4, 'unit' => 'hours', 'confirmed' => true, 'group' => 'base_hours', 'label' => 'Base hours - 2 bedrooms', 'note' => "Owner's number: 2-3 rooms ≈ 4 hours"],

            ['key' => 'min_hours', 'value' => 2, 'unit' => 'hours', 'confirmed' => false, 'group' => 'base_hours', 'label' => 'Minimum billable hours', 'note' => 'MISSING: minimum hours the crew always charges'],
            ['key' => 'hours_studio', 'value' => 2, 'unit' => 'hours', 'confirmed' => false, 'group' => 'base_hours', 'label' => 'Base hours - Studio', 'note' => 'Provisional'],
            ['key' => 'hours_1', 'value' => 3, 'unit' => 'hours', 'confirmed' => false, 'group' => 'base_hours', 'label' => 'Base hours - 1 bedroom', 'note' => 'Provisional'],
            ['key' => 'hours_3', 'value' => 5, 'unit' => 'hours', 'confirmed' => false, 'group' => 'base_hours', 'label' => 'Base hours - 3 bedrooms', 'note' => 'Provisional'],
            ['key' => 'hours_4', 'value' => 7, 'unit' => 'hours', 'confirmed' => false, 'group' => 'base_hours', 'label' => 'Base hours - 4+ bedrooms', 'note' => 'Provisional'],
            ['key' => 'hours_office', 'value' => 5, 'unit' => 'hours', 'confirmed' => false, 'group' => 'base_hours', 'label' => 'Base hours - Office', 'note' => 'Provisional'],
            ['key' => 'stairs_hours_per_floor', 'value' => 0.25, 'unit' => 'hours', 'confirmed' => false, 'group' => 'access', 'label' => 'Stairs hours per floor', 'note' => 'Provisional'],
            ['key' => 'elevator_extra_hours', 'value' => 0.5, 'unit' => 'hours', 'confirmed' => false, 'group' => 'access', 'label' => 'Elevator extra hours', 'note' => "Owner says an elevator takes longer; by how much is still missing"],
            ['key' => 'packing_extra_hours', 'value' => 2, 'unit' => 'hours', 'confirmed' => false, 'group' => 'services', 'label' => 'Packing extra hours', 'note' => 'MISSING'],
            ['key' => 'travel_fee', 'value' => 0, 'unit' => 'usd', 'confirmed' => false, 'group' => 'travel', 'label' => 'Travel fee', 'note' => 'MISSING: surcharge amount for going past 35 miles'],
            ['key' => 'item_large_fridge', 'value' => 0, 'unit' => 'usd', 'confirmed' => false, 'group' => 'special_items', 'label' => 'Large fridge fee', 'note' => 'MISSING'],
            ['key' => 'item_piano', 'value' => 0, 'unit' => 'usd', 'confirmed' => false, 'group' => 'special_items', 'label' => 'Piano fee', 'note' => 'MISSING'],
            ['key' => 'item_pool_table', 'value' => 0, 'unit' => 'usd', 'confirmed' => false, 'group' => 'special_items', 'label' => 'Pool table fee', 'note' => 'MISSING'],
            ['key' => 'item_jacuzzi', 'value' => 0, 'unit' => 'usd', 'confirmed' => false, 'group' => 'special_items', 'label' => 'Jacuzzi / hot tub fee', 'note' => 'MISSING'],
            ['key' => 'item_safe', 'value' => 0, 'unit' => 'usd', 'confirmed' => false, 'group' => 'special_items', 'label' => 'Safe fee', 'note' => 'MISSING'],
            ['key' => 'range_pct', 'value' => 15, 'unit' => 'percent', 'confirmed' => false, 'group' => 'rates', 'label' => 'Estimate range', 'note' => 'Width of the +/- range; tune with calibration'],
        ];

        foreach ($rows as $row) {
            // firstOrCreate so re-running the seeder never overwrites values
            // the owner has already edited from the admin dashboard.
            PricingSetting::firstOrCreate(['key' => $row['key']], $row);
        }
    }
}
