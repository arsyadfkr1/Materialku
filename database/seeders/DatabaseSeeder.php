<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Material;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        User::firstOrCreate(
            ['email' => 'admin@materialku.com'],
            [
                'name' => 'Admin Toko Material',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Settings Toko
        Setting::firstOrCreate(
            ['id' => 1],
            [
                'store_name' => 'MATERIALKU',
                'store_whatsapp' => '6281234567890',
                'store_address' => 'Jl. Merdeka No. 123, Kota Bandung',
                'message_template' => "Halo Admin MATERIALKU, saya ingin mengonfirmasi pesanan berikut.\n\nKode: {ORDER_CODE}\nNama: {CUSTOMER_NAME}\n\nDetail material:\n{ORDER_ITEMS}\n\nEstimasi biaya material: {MATERIAL_TOTAL}\nOngkir belum termasuk.\n\nMohon konfirmasi ketersediaan, ongkir, dan cara pembayaran. Terima kasih.",
            ]
        );

        // 3. Kategori
        $catKeramik = Category::firstOrCreate(['slug' => 'keramik'], ['name' => 'Keramik']);
        $catCat = Category::firstOrCreate(['slug' => 'cat-pelapis'], ['name' => 'Cat & Pelapis']);
        $catSemen = Category::firstOrCreate(['slug' => 'semen-mortar'], ['name' => 'Semen & Mortar']);
        $catBaja = Category::firstOrCreate(['slug' => 'besi-baja'], ['name' => 'Besi & Baja']);

        // 4. Sample Materials
        // Keramik A (Sesuai ilustrasi PRD 8.1: Cakupan 1.44 m2/dus, harga 85.000)
        Material::updateOrCreate(
            ['slug' => 'keramik-granit-motif-marmer-60x60'],
            [
                'category_id' => $catKeramik->id,
                'name' => 'Keramik Granit Motif Marmer 60x60',
                'price' => 85000,
                'sale_unit' => 'dus',
                'description' => 'Keramik lantai motif marmer putih mewah ukuran 60x60 cm. 1 dus isi 4 keping (1.44 m2). Tekstur polished kilap anti gores.',
                'image_path' => null,
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => true,
                'calculation_type' => 'ceramic',
                'coverage_per_box' => 1.44,
                'waste_ratio' => 0.10,
                'calculation_note' => 'Disarankan menambah cadangan 10% untuk pemotongan sudut dan cadangan renovasi.',
            ]
        );

        Material::updateOrCreate(
            ['slug' => 'keramik-dinding-dapur-subway-10x20'],
            [
                'category_id' => $catKeramik->id,
                'name' => 'Keramik Dinding Subway Glossy 10x20',
                'price' => 95000,
                'sale_unit' => 'dus',
                'description' => 'Keramik dinding model subway tile bevel gloss. Cocok untuk backsplash dapur atau kamar mandi. 1 dus mengcover 1.0 m2.',
                'image_path' => null,
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => true,
                'calculation_type' => 'ceramic',
                'coverage_per_box' => 1.00,
                'waste_ratio' => 0.10,
                'calculation_note' => 'Untuk motif zig-zag / herringbone cadangan disarankan 15%.',
            ]
        );

        // Cat B (Sesuai ilustrasi PRD 8.2: Daya sebar 10 m2/liter/lapis, isi 5 liter, harga 150.000)
        Material::updateOrCreate(
            ['slug' => 'cat-tembok-interior-premium-putih-5l'],
            [
                'category_id' => $catCat->id,
                'name' => 'Cat Tembok Interior Premium Putih 5L',
                'price' => 150000,
                'sale_unit' => 'kaleng',
                'description' => 'Cat interior anti jamur, mudah dibersihkan, daya tutup optimal dengan aroma harum segar. Kemasan galon 5 Liter.',
                'image_path' => null,
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => true,
                'calculation_type' => 'paint',
                'spread_per_liter_per_coat' => 10.00,
                'package_volume_liters' => 5.00,
                'default_coats' => 2,
                'waste_ratio' => 0.10,
                'calculation_note' => 'Standar pengecatan tembok baru membutuhkan 2 lapisan cat.',
            ]
        );

        Material::updateOrCreate(
            ['slug' => 'cat-eksterior-weather-shield-20l'],
            [
                'category_id' => $catCat->id,
                'name' => 'Cat Eksterior Weather Shield 20L',
                'price' => 580000,
                'sale_unit' => 'pail',
                'description' => 'Cat pelindung cuaca luar tahan lumut, panas ekstrem, dan hujan lebat. Kemasan pail 20 Liter.',
                'image_path' => null,
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => true,
                'calculation_type' => 'paint',
                'spread_per_liter_per_coat' => 12.00,
                'package_volume_liters' => 20.00,
                'default_coats' => 2,
                'waste_ratio' => 0.10,
                'calculation_note' => 'Pastikan dinding sudah diplester dan diaci sempurna sebelum dilapisi.',
            ]
        );

        // Semen biasa (tanpa kalkulator)
        Material::updateOrCreate(
            ['slug' => 'semen-portland-composite-50kg'],
            [
                'category_id' => $catSemen->id,
                'name' => 'Semen Portland Composite (PCC) 50kg',
                'price' => 68000,
                'sale_unit' => 'sak',
                'description' => 'Semen serbaguna berkualitas tinggi untuk plesteran, acian, dan cor beton kuat.',
                'image_path' => null,
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => false,
            ]
        );

        // Besi beton (tanpa kalkulator)
        Material::updateOrCreate(
            ['slug' => 'besi-beton-polos-10mm-sni'],
            [
                'category_id' => $catBaja->id,
                'name' => 'Besi Beton Polos 10mm SNI (12m)',
                'price' => 74000,
                'sale_unit' => 'batang',
                'description' => 'Besi beton full SNI panjang standar 12 meter toleransi resmi.',
                'image_path' => null,
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => false,
            ]
        );
    }
}
