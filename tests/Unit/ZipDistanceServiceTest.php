<?php

namespace Tests\Unit;

use App\Services\ZipDistanceService;
use Tests\TestCase;

class ZipDistanceServiceTest extends TestCase
{
    public function test_returns_zero_for_the_same_zip_code(): void
    {
        $this->assertSame(0.0, ZipDistanceService::milesBetween('30303', '30303'));
    }

    public function test_returns_a_plausible_distance_between_two_known_atlanta_area_zips(): void
    {
        // 30303 and 30309 are both in Atlanta, a few miles apart at most.
        $miles = ZipDistanceService::milesBetween('30303', '30309');

        $this->assertNotNull($miles);
        $this->assertLessThan(15, $miles);
    }

    public function test_returns_a_large_distance_between_zips_on_opposite_coasts(): void
    {
        // 10001 (New York, NY) and 90001 (Los Angeles, CA).
        $miles = ZipDistanceService::milesBetween('10001', '90001');

        $this->assertNotNull($miles);
        $this->assertGreaterThan(2000, $miles);
    }

    public function test_returns_null_when_a_zip_is_not_in_the_dataset_instead_of_guessing(): void
    {
        $this->assertNull(ZipDistanceService::milesBetween('00000', '30303'));
        $this->assertNull(ZipDistanceService::milesBetween('30303', null));
        $this->assertNull(ZipDistanceService::milesBetween(null, null));
    }

    /**
     * The Census ZCTA dataset only covers ZIPs with a residential population;
     * PO-box-only and business-only ZIPs (like downtown Atlanta's 30301) have
     * no centroid. This documents that gap on purpose: it's why the estimator
     * skips the travel fee instead of guessing when a ZIP isn't found.
     */
    public function test_a_po_box_only_zip_with_no_zcta_returns_null(): void
    {
        $this->assertNull(ZipDistanceService::milesBetween('30301', '30303'));
    }
}
