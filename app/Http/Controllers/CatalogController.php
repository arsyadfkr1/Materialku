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
            default:
                $query->orderBy('id', 'asc'); // Keep consistent order
                break;
        }

        $totalActive = Material::where('is_active', true)->count();
        $materials = $query->paginate(8)->withQueryString();

        return Inertia::render('Catalog/Index', [
            'materials' => $materials,
            'categories' => $categories,
            'totalActive' => $totalActive,
            'filters' => $request->only(['search', 'category', 'available_only', 'calculator_only', 'sort']),
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
}
