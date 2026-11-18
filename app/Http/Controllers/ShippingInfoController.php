<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;

class ShippingInfoController extends Controller
{
    /**
     * Menampilkan halaman lengkap informasi pengiriman, tarif ongkir, armada toko, dan FAQ.
     */
    public function index(): Response
    {
        $setting = Setting::firstOrCreate(
            ['id' => 1],
            [
                'store_name' => 'TB. Mandiri Sejahtera Abadi',
                'store_whatsapp' => '6281234567890',
                'store_address' => 'Jl. Soekarno-Hatta No. 88, By Pass, Kota Bandar Lampung',
            ]
        );

        return Inertia::render('Shipping/Index', [
            'setting' => $setting,
        ]);
    }
}
