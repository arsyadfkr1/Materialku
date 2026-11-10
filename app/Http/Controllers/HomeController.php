<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $setting = Setting::first() ?? Setting::create([
            'store_name' => 'MATERIALKU',
            'store_whatsapp' => '6281234567890',
            'store_address' => 'Jl. Merdeka No. 123, Kota Bandung',
        ]);

        $categories = Category::withCount(['materials' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        $featuredMaterials = Material::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        // Sample materials for hero calculator
        $ceramicMaterial = Material::where('calculation_type', 'ceramic')
            ->where('is_active', true)
            ->first();

        $paintMaterial = Material::where('calculation_type', 'paint')
            ->where('is_active', true)
            ->first();

        return Inertia::render('Home', [
            'setting' => $setting,
            'categories' => $categories,
            'featuredMaterials' => $featuredMaterials,
            'heroMaterials' => [
                'ceramic' => $ceramicMaterial,
                'paint' => $paintMaterial,
            ],
        ]);
    }
}
