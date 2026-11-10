<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Material;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
                'store_name' => 'TB. Mandiri Sejahtera Abadi',
                'store_whatsapp' => '6281234567890',
                'store_address' => 'Jl. Raya Industri Timur No. 45, Kawasan Niaga Utama',
                'message_template' => "Halo Admin MATERIALKU, saya ingin mengonfirmasi pesanan berikut.\n\nKode: {ORDER_CODE}\nNama: {CUSTOMER_NAME}\n\nDetail material:\n{ORDER_ITEMS}\n\nEstimasi biaya material: {MATERIAL_TOTAL}\nOngkir belum termasuk.\n\nMohon konfirmasi ketersediaan, ongkir, dan cara pembayaran. Terima kasih.",
            ]
        );

        // 3. Kategori Awal (Keramik, Cat & Pelapis, Semen & Mortar, Besi & Baja)
        $catKeramik = Category::firstOrCreate(['slug' => 'keramik'], ['name' => 'Keramik']);
        $catCat = Category::firstOrCreate(['slug' => 'cat-pelapis'], ['name' => 'Cat & Pelapis']);
        $catSemen = Category::firstOrCreate(['slug' => 'semen-mortar'], ['name' => 'Semen & Mortar']);
        $catBaja = Category::firstOrCreate(['slug' => 'besi-baja'], ['name' => 'Besi & Baja']);

        // 4. Produk Utama Sesuai PRD & Yang Sebelumnya Dicoba
        $items = [
            // 1. Keramik Granit Motif Marmer 60x60 (Sesuai UAT-01 PRD 8.1)
            [
                'category_id' => $catKeramik->id,
                'name' => 'Keramik Granit Motif Marmer 60x60',
                'slug' => 'keramik-granit-motif-marmer-60x60',
                'price' => 85000,
                'sale_unit' => 'dus',
                'spec_badge' => '1 Dus = 1.44 m²',
                'unit_detail' => 'per Dus (isi 4 pcs)',
                'sub_price_badge' => '~ Rp 59.027 / m²',
                'stock_badge' => 'Tersedia',
                'description' => 'Keramik lantai motif marmer putih mewah ukuran 60x60 cm. 1 dus isi 4 keping (1.44 m2). Tekstur polished kilap anti gores.',
                'image_path' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => true,
                'calculation_type' => 'ceramic',
                'coverage_per_box' => 1.44,
                'waste_ratio' => 0.10,
                'calculation_note' => 'Disarankan menambah cadangan 10% untuk pemotongan sudut dan cadangan renovasi.',
            ],

            // 2. Keramik Dinding Subway Glossy 10x20
            [
                'category_id' => $catKeramik->id,
                'name' => 'Keramik Dinding Subway Glossy 10x20',
                'slug' => 'keramik-dinding-dapur-subway-10x20',
                'price' => 95000,
                'sale_unit' => 'dus',
                'spec_badge' => '1 Dus = 1.00 m²',
                'unit_detail' => 'per Dus (isi 50 pcs)',
                'sub_price_badge' => 'Rp 95.000 / m²',
                'stock_badge' => 'Tersedia',
                'description' => 'Keramik dinding model subway tile bevel gloss. Cocok untuk backsplash dapur atau kamar mandi. 1 dus mengcover 1.0 m2.',
                'image_path' => 'https://images.unsplash.com/photo-1502005229762-ee1b2da973e6?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => true,
                'calculation_type' => 'ceramic',
                'coverage_per_box' => 1.00,
                'waste_ratio' => 0.10,
                'calculation_note' => 'Untuk motif zig-zag / herringbone cadangan disarankan 15%.',
            ],

            // 3. Cat Tembok Interior Premium Putih 5L (Sesuai UAT-02 PRD 8.2)
            [
                'category_id' => $catCat->id,
                'name' => 'Cat Tembok Interior Premium Putih 5L',
                'slug' => 'cat-tembok-interior-premium-putih-5l',
                'price' => 150000,
                'sale_unit' => 'kaleng',
                'spec_badge' => '1 Galon = 5 L',
                'unit_detail' => 'per Kaleng (5 Liter)',
                'sub_price_badge' => '~ 25 m² (2 lapis)',
                'stock_badge' => 'Tersedia',
                'description' => 'Cat interior anti jamur, mudah dibersihkan, daya tutup optimal dengan aroma harum segar. Kemasan galon 5 Liter.',
                'image_path' => 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => true,
                'calculation_type' => 'paint',
                'spread_per_liter_per_coat' => 10.00,
                'package_volume_liters' => 5.00,
                'default_coats' => 2,
                'waste_ratio' => 0.10,
                'calculation_note' => 'Standar pengecatan tembok baru membutuhkan 2 lapisan cat.',
            ],

            // 4. Cat Eksterior Weather Shield 20L
            [
                'category_id' => $catCat->id,
                'name' => 'Cat Eksterior Weather Shield 20L',
                'slug' => 'cat-eksterior-weather-shield-20l',
                'price' => 580000,
                'sale_unit' => 'pail',
                'spec_badge' => '1 Pail = 20 L',
                'unit_detail' => 'per Pail (20 Liter)',
                'sub_price_badge' => '~ 120 m² (2 lapis)',
                'stock_badge' => 'Tersedia',
                'description' => 'Cat pelindung cuaca luar tahan lumut, panas ekstrem, dan hujan lebat. Kemasan pail 20 Liter.',
                'image_path' => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => true,
                'calculation_type' => 'paint',
                'spread_per_liter_per_coat' => 12.00,
                'package_volume_liters' => 20.00,
                'default_coats' => 2,
                'waste_ratio' => 0.10,
                'calculation_note' => 'Pastikan dinding sudah diplester dan diaci sempurna sebelum dilapisi.',
            ],

            // 5. Semen Portland Composite (PCC) 50kg
            [
                'category_id' => $catSemen->id,
                'name' => 'Semen Portland Composite (PCC) 50kg',
                'slug' => 'semen-portland-composite-50kg',
                'price' => 68000,
                'sale_unit' => 'sak',
                'spec_badge' => '1 Sak = 50 Kg',
                'unit_detail' => 'per Sak (50kg)',
                'sub_price_badge' => 'Min. 5 sak kirim',
                'stock_badge' => 'Tersedia',
                'description' => 'Semen serbaguna berkualitas tinggi untuk plesteran, acian, dan cor beton kuat.',
                'image_path' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => false,
            ],

            // 6. Besi Beton Polos 10mm SNI (12m)
            [
                'category_id' => $catBaja->id,
                'name' => 'Besi Beton Polos 10mm SNI (12m)',
                'slug' => 'besi-beton-polos-10mm-sni',
                'price' => 74000,
                'sale_unit' => 'batang',
                'spec_badge' => '1 Batang = 12 M',
                'unit_detail' => 'per Batang (12 meter)',
                'sub_price_badge' => '~ Rp 6.166 / m',
                'stock_badge' => 'Stok Menipis',
                'description' => 'Besi beton full SNI panjang standar 12 meter toleransi resmi.',
                'image_path' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => false,
            ],

            // 7. Keramik Lantai 40x40 Abu Rustic
            [
                'category_id' => $catKeramik->id,
                'name' => 'Keramik Lantai 40x40 Abu Rustic Anti Selip',
                'slug' => 'keramik-lantai-40x40-abu-rustic',
                'price' => 65000,
                'sale_unit' => 'dus',
                'spec_badge' => '1 Dus = 1.00 m²',
                'unit_detail' => 'per Dus (isi 6 pcs)',
                'sub_price_badge' => 'Rp 65.000 / m²',
                'stock_badge' => 'Tersedia',
                'description' => 'Keramik motif batu alam kasar anti licin untuk garasi carport, teras depan, dan area jemuran.',
                'image_path' => 'https://images.unsplash.com/photo-1502005229762-ee1b2da973e6?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => true,
                'calculation_type' => 'ceramic',
                'coverage_per_box' => 1.00,
                'waste_ratio' => 0.10,
            ],

            // 8. Mortar Perekat Bata Ringan 40kg
            [
                'category_id' => $catSemen->id,
                'name' => 'Mortar Perekat Bata Ringan / Hebel 40kg',
                'slug' => 'mortar-perekat-bata-ringan-40kg',
                'price' => 78000,
                'sale_unit' => 'sak',
                'spec_badge' => '1 Sak = 40 Kg',
                'unit_detail' => 'per Sak (40kg)',
                'sub_price_badge' => '~ 10 m² dinding',
                'stock_badge' => 'Tersedia',
                'description' => 'Lem mortar tipis 3mm berkekuatan tinggi untuk pemasangan bata hebel presisi tanpa boros pasir.',
                'image_path' => 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => false,
            ],

            // 9. Baja Ringan Kanal C75 0.75mm
            [
                'category_id' => $catBaja->id,
                'name' => 'Baja Ringan Truss Kanal C75 Ketebalan 0.75mm',
                'slug' => 'baja-ringan-truss-kanal-c75-075mm',
                'price' => 98000,
                'sale_unit' => 'batang',
                'spec_badge' => '1 Batang = 6 M',
                'unit_detail' => 'per Batang (6 meter)',
                'sub_price_badge' => '~ Rp 16.333 / m',
                'stock_badge' => 'Tersedia',
                'description' => 'Kanal C galvalum anti-karat untuk rangka kuda-kuda atap genteng metal atau spandek rumah.',
                'image_path' => 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => false,
            ],

            // 10. Cat Dasar Anti Alkali Primer 20L
            [
                'category_id' => $catCat->id,
                'name' => 'Cat Dasar Alkali Resisting Primer Sealer 20L',
                'slug' => 'cat-dasar-alkali-resisting-primer-sealer-20l',
                'price' => 520000,
                'sale_unit' => 'pail',
                'spec_badge' => '1 Pail = 20 L',
                'unit_detail' => 'per Pail (20 Liter)',
                'sub_price_badge' => '~ 140 m² (1 lapis)',
                'stock_badge' => 'Tersedia',
                'description' => 'Mencegah garam alkali semen merusak warna cat akhir dan merekatkan cat baru ke acian dinding.',
                'image_path' => 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => true,
                'calculation_type' => 'paint',
                'spread_per_liter_per_coat' => 14.00,
                'package_volume_liters' => 20.00,
                'default_coats' => 1,
                'waste_ratio' => 0.10,
            ],

            // 11. Semen Instan Acian Halus 40kg
            [
                'category_id' => $catSemen->id,
                'name' => 'Semen Instan Acian Dinding Halus 40kg',
                'slug' => 'semen-instan-acian-dinding-halus-40kg',
                'price' => 82000,
                'sale_unit' => 'sak',
                'spec_badge' => '1 Sak = 40 Kg',
                'unit_detail' => 'per Sak (40kg)',
                'sub_price_badge' => '~ 12 m² acian',
                'stock_badge' => 'Tersedia',
                'description' => 'Hasil acian dinding halus siap cat tanpa plamir. Mencegah retak rambut dan tidak berdebu saat diamplas.',
                'image_path' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => false,
            ],

            // 12. Besi Beton Ulir D13 SNI (12m)
            [
                'category_id' => $catBaja->id,
                'name' => 'Besi Beton Ulir D13 SNI TS420B (Panjang 12m)',
                'slug' => 'besi-beton-ulir-d13-sni-ts420b-12m',
                'price' => 135000,
                'sale_unit' => 'batang',
                'spec_badge' => '1 Batang = 12 M',
                'unit_detail' => 'per Batang (12 meter)',
                'sub_price_badge' => '~ Rp 11.250 / m',
                'stock_badge' => 'Tersedia',
                'description' => 'Besi beton standar konstruksi tahan gempa, toleransi presisi untuk pembesian kolom dan sloof balok.',
                'image_path' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
                'availability_status' => 'available',
                'calculator_enabled' => false,
            ],
        ];

        foreach ($items as $item) {
            Material::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
