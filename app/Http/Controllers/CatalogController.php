<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $setting = Setting::first();
        $categories = Category::withCount(['materials' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        $query = Material::with('category')->where('is_active', true);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($categorySlug = $request->input('category')) {
            if ($categorySlug !== 'all') {
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            }
        }

        // Available only toggle
        if ($request->boolean('available_only')) {
            $query->where('stock_badge', 'Tersedia');
        }

        // Calculator only
        if ($request->boolean('calculator_only')) {
            $query->where('calculator_enabled', true);
        }

        // Price range filter
        $priceRange = $request->input('price_range', 'all');
        if ($priceRange === 'under_100k') {
            $query->where('price', '<', 100000);
        } elseif ($priceRange === '100k_250k') {
            $query->whereBetween('price', [100000, 250000]);
        } elseif ($priceRange === 'above_250k') {
            $query->where('price', '>', 250000);
        }

        // Custom min price
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', (float) $minPrice);
        }

        // Custom max price
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        // Unit / Kemasan filter
        if ($unit = $request->input('unit')) {
            if ($unit !== 'all') {
                $query->where('sale_unit', $unit);
            }
        }

        // Sorting
        $sort = $request->input('sort', 'relevance');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->orderBy('id', 'asc'); // Keep consistent order
                break;
        }

        $totalActive = Material::where('is_active', true)->count();
        $materials = $query->paginate(8)->withQueryString();
        $availableUnits = Material::where('is_active', true)->distinct()->pluck('sale_unit')->filter()->values();

        return Inertia::render('Catalog/Index', [
            'materials' => $materials,
            'categories' => $categories,
            'totalActive' => $totalActive,
            'availableUnits' => $availableUnits,
            'filters' => $request->only([
                'search',
                'category',
                'available_only',
                'calculator_only',
                'price_range',
                'min_price',
                'max_price',
                'unit',
                'sort'
            ]),
            'setting' => $setting,
        ]);
    }

    public function show(string $slug): Response
    {
        $material = Material::with('category')->where('slug', $slug)->firstOrFail();
        $relatedMaterials = Material::where('category_id', $material->category_id)
            ->where('id', '!=', $material->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $setting = Setting::first();

        return Inertia::render('Catalog/Show', [
            'material' => $material,
            'relatedMaterials' => $relatedMaterials,
            'setting' => $setting,
        ]);
    }

    /**
     * API Live Search Autocomplete Suggestions.
     */
    public function suggestions(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim((string) $request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([
                'materials' => [],
                'total_count' => 0,
            ]);
        }

        $query = Material::with('category:id,name,slug')
            ->where('is_active', true)
            ->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('spec_badge', 'like', "%{$q}%")
                    ->orWhereHas('category', function ($catQuery) use ($q) {
                        $catQuery->where('name', 'like', "%{$q}%");
                    });
            });

        $totalCount = $query->count();

        $materials = $query->select('id', 'category_id', 'name', 'slug', 'price', 'sale_unit', 'image_path', 'spec_badge', 'stock_badge')
            ->limit(5)
            ->get();

        return response()->json([
            'materials' => $materials,
            'total_count' => $totalCount,
        ]);
    }
}
