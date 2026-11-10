<?php

namespace App\Services;

class CalculationService
{
    /**
     * Hitung kebutuhan keramik sesuai spesifikasi PRD 8.1
     *
     * @param float $length Panjang ruangan (meter)
     * @param float $width Lebar ruangan (meter)
     * @param float $wastePercent Persentase cadangan (contoh: 10 untuk 10%)
     * @param float $coveragePerBox Cakupan per dus (m2/dus)
     * @param float $pricePerBox Harga per dus
     * @return array
     */
    public function calculateCeramic(
        float $length,
        float $width,
        float $wastePercent,
        float $coveragePerBox,
        float $pricePerBox = 0
    ): array {
        if ($length <= 0 || $width <= 0 || $coveragePerBox <= 0) {
            throw new \InvalidArgumentException('Dimensi dan cakupan per dus harus bernilai positif.');
        }

        $area = $length * $width;
        $wasteRatio = max(0, $wastePercent) / 100;
        $requiredArea = $area * (1 + $wasteRatio);
        $boxCount = (int) ceil($requiredArea / $coveragePerBox);
        $estimatedCost = $boxCount * $pricePerBox;

        return [
            'type' => 'ceramic',
            'length' => round($length, 2),
            'width' => round($width, 2),
            'area' => round($area, 2),
            'waste_percent' => $wastePercent,
            'waste_ratio' => $wasteRatio,
            'coverage_per_box' => round($coveragePerBox, 2),
            'required_area' => round($requiredArea, 2),
            'quantity' => $boxCount,
            'unit' => 'dus',
            'unit_price' => $pricePerBox,
            'estimated_cost' => $estimatedCost,
        ];
    }

    /**
     * Hitung kebutuhan cat sesuai spesifikasi PRD 8.2
     *
     * @param float $length Panjang bidang (meter)
     * @param float $height Tinggi bidang (meter)
     * @param float $openingArea Luas bukaan (pintu/jendela dsb, meter persegi)
     * @param int $coats Jumlah lapisan (integer positif, default 2)
     * @param float $spreadPerLiter Daya sebar cat (m2 / liter / lapis)
     * @param float $packageVolumeLiters Isi kemasan (liter per kaleng/pail)
     * @param float $wastePercent Persentase cadangan (contoh: 10 untuk 10%)
     * @param float $pricePerCan Harga per kaleng/kemasan
     * @return array
     */
    public function calculatePaint(
        float $length,
        float $height,
        float $openingArea,
        int $coats,
        float $spreadPerLiter,
        float $packageVolumeLiters,
        float $wastePercent,
        float $pricePerCan = 0
    ): array {
        if ($length <= 0 || $height <= 0 || $spreadPerLiter <= 0 || $packageVolumeLiters <= 0 || $coats <= 0) {
            throw new \InvalidArgumentException('Dimensi, daya sebar, isi kemasan, dan jumlah lapisan harus bernilai positif.');
        }

        $grossArea = $length * $height;
        if ($openingArea < 0 || $openingArea >= $grossArea) {
            throw new \InvalidArgumentException('Luas bukaan tidak boleh negatif dan harus lebih kecil dari luas bidang dinding.');
        }

        $netArea = $grossArea - $openingArea;
        $wasteRatio = max(0, $wastePercent) / 100;
        $litersNeeded = ($netArea * $coats / $spreadPerLiter) * (1 + $wasteRatio);
        $canCount = (int) ceil($litersNeeded / $packageVolumeLiters);
        $estimatedCost = $canCount * $pricePerCan;

        return [
            'type' => 'paint',
            'length' => round($length, 2),
            'height' => round($height, 2),
            'gross_area' => round($grossArea, 2),
            'opening_area' => round($openingArea, 2),
            'net_area' => round($netArea, 2),
            'coats' => $coats,
            'spread_per_liter' => round($spreadPerLiter, 2),
            'package_volume_liters' => round($packageVolumeLiters, 2),
            'waste_percent' => $wastePercent,
            'waste_ratio' => $wasteRatio,
            'liters_needed' => round($litersNeeded, 2),
            'quantity' => $canCount,
            'unit' => 'kaleng',
            'unit_price' => $pricePerCan,
            'estimated_cost' => $estimatedCost,
        ];
    }
}
