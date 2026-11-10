<?php

namespace Tests\Unit;

use App\Services\CalculationService;
use PHPUnit\Framework\TestCase;

class CalculationServiceTest extends TestCase
{
    private CalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CalculationService();
    }

    /**
     * UAT-01: Keramik 4 × 3 meter, cadangan 10%, cakupan 1,44 => 10 dus
     */
    public function test_uat_01_ceramic_calculation(): void
    {
        $result = $this->service->calculateCeramic(
            length: 4.0,
            width: 3.0,
            wastePercent: 10.0,
            coveragePerBox: 1.44,
            pricePerBox: 85000
        );

        $this->assertEquals(12.0, $result['area']);
        $this->assertEquals(13.2, $result['required_area']);
        $this->assertEquals(10, $result['quantity']); // 13.2 / 1.44 = 9.166 -> ceil -> 10
        $this->assertEquals(850000, $result['estimated_cost']);
    }

    /**
     * UAT-02: Cat luas bersih 30 m2, 2 lapis, daya sebar 10, cadangan 10%, isi 5 liter => 2 kaleng
     */
    public function test_uat_02_paint_calculation(): void
    {
        // 10m x 3m = 30m2, bukaan 0
        $result = $this->service->calculatePaint(
            length: 10.0,
            height: 3.0,
            openingArea: 0.0,
            coats: 2,
            spreadPerLiter: 10.0,
            packageVolumeLiters: 5.0,
            wastePercent: 10.0,
            pricePerCan: 150000
        );

        $this->assertEquals(30.0, $result['net_area']);
        $this->assertEquals(6.6, $result['liters_needed']); // (30 * 2 / 10) * 1.10 = 6.6
        $this->assertEquals(2, $result['quantity']); // 6.6 / 5 = 1.32 -> ceil -> 2
        $this->assertEquals(300000, $result['estimated_cost']);
    }

    /**
     * UAT-03: Panjang negatif => Ditolak
     */
    public function test_uat_03_negative_length_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->calculateCeramic(
            length: -4.0,
            width: 3.0,
            wastePercent: 10.0,
            coveragePerBox: 1.44
        );
    }
}
