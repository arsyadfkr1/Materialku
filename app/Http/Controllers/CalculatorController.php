<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Setting;
use App\Services\CalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalculatorController extends Controller
{
    protected CalculationService $calculationService;

    public function __construct(CalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    public function index(Request $request): Response
    {
        $setting = Setting::first();

        $ceramicMaterials = Material::where('calculation_type', 'ceramic')
            ->where('is_active', true)
            ->get();

        $paintMaterials = Material::where('calculation_type', 'paint')
            ->where('is_active', true)
            ->get();

        $selectedSlug = $request->query('product');
        $selectedMaterial = null;
        if ($selectedSlug) {
            $selectedMaterial = Material::where('slug', $selectedSlug)
                ->where('calculator_enabled', true)
                ->first();
        }

        return Inertia::render('Calculator/Index', [
            'ceramicMaterials' => $ceramicMaterials,
            'paintMaterials' => $paintMaterials,
            'selectedMaterial' => $selectedMaterial,
            'setting' => $setting,
        ]);
    }

    public function calculateCeramic(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'length' => 'required|numeric|min:0.1',
            'width' => 'required|numeric|min:0.1',
            'waste_percent' => 'nullable|numeric|min:0|max:50',
            'coverage_per_box' => 'required|numeric|min:0.1',
            'price_per_box' => 'nullable|numeric|min:0',
        ]);

        try {
            $result = $this->calculationService->calculateCeramic(
                (float) $validated['length'],
                (float) $validated['width'],
                (float) ($validated['waste_percent'] ?? 10),
                (float) $validated['coverage_per_box'],
                (float) ($validated['price_per_box'] ?? 0)
            );

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function calculatePaint(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'length' => 'required|numeric|min:0.1',
            'height' => 'required|numeric|min:0.1',
            'opening_area' => 'nullable|numeric|min:0',
            'coats' => 'nullable|integer|min:1|max:10',
            'spread_per_liter' => 'required|numeric|min:0.1',
            'package_volume_liters' => 'required|numeric|min:0.1',
            'waste_percent' => 'nullable|numeric|min:0|max:50',
            'price_per_can' => 'nullable|numeric|min:0',
        ]);

        try {
            $result = $this->calculationService->calculatePaint(
                (float) $validated['length'],
                (float) $validated['height'],
                (float) ($validated['opening_area'] ?? 0),
                (int) ($validated['coats'] ?? 2),
                (float) $validated['spread_per_liter'],
                (float) $validated['package_volume_liters'],
                (float) ($validated['waste_percent'] ?? 10),
                (float) ($validated['price_per_can'] ?? 0)
            );

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
