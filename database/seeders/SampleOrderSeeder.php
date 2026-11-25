<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@materialku.com')->first();
        $adminId = $admin?->id ?? 1;

        $materials = Material::all()->keyBy('id');
        if ($materials->isEmpty()) {
            return;
        }

        $sampleOrders = [
            [
                'order_code' => 'MK-20261125-A101',
                'customer_name' => 'Budi Santoso',
                'whatsapp' => '081298765432',
                'address' => 'Jl. Pangeran Antasari No. 42, Kedamaian, Bandar Lampung (Dekat Masjid Al-Huda)',
                'customer_note' => 'Mohon dikirim pagi jam 09.00 saat tukang sudah siap. Akses jalan masuk muat truk engkel.',
                'status' => 'Menunggu Konfirmasi',
                'admin_note' => null,
                'created_at' => now()->subHours(2),
                'items' => [
                    ['material_id' => 1, 'qty' => 25], // Keramik Granit 60x60 (85.000 x 25 = 2.125.000)
                    ['material_id' => 4, 'qty' => 15], // Semen PCC 50kg (65.000 x 15 = 975.000)
                ],
            ],
            [
                'order_code' => 'MK-20261125-B202',
                'customer_name' => 'Hendra Wijaya (CV. Bangun Jaya)',
                'whatsapp' => '085211223344',
                'address' => 'Proyek Ruko Sukarame Blok C-7, Jl. Ryacudu, Bandar Lampung',
                'customer_note' => 'Besi beton jangan ditekuk, pakai armada panjang.',
                'status' => 'Diproses',
                'admin_note' => 'DP 50% sudah diterima via BCA atas nama Hendra. Armada truk engkel disiapkan berangkat jam 13.00.',
                'created_at' => now()->subHours(5),
                'items' => [
                    ['material_id' => 12, 'qty' => 40], // Besi Ulir D13 (135.000 x 40 = 5.400.000)
                    ['material_id' => 9, 'qty' => 30],  // Baja Ringan C75 (98.000 x 30 = 2.940.000)
                ],
            ],
            [
                'order_code' => 'MK-20261124-C303',
                'customer_name' => 'Siti Nurhaliza',
                'whatsapp' => '081377889900',
                'address' => 'Perumahan Villa Citra 2 Blok E No. 12, Rajabasa, Bandar Lampung',
                'customer_note' => 'Rumah cat pagar hitam. Mohon telepon dulu sebelum sampai.',
                'status' => 'Dikirim',
                'admin_note' => 'Armada Pickup Toko (Sopir: Pak Joko) berangkat pukul 10.30 WIB membawa pesanan.',
                'created_at' => now()->subDay(),
                'items' => [
                    ['material_id' => 3, 'qty' => 4],   // Cat Tembok Interior 5L (150.000 x 4 = 600.000)
                    ['material_id' => 10, 'qty' => 1],  // Cat Dasar Alkali 20L (520.000 x 1 = 520.000)
                ],
            ],
            [
                'order_code' => 'MK-20261123-D404',
                'customer_name' => 'Rizki Anugrah',
                'whatsapp' => '081955667788',
                'address' => 'Jl. Teuku Umar No. 105, Kedaton, Bandar Lampung',
                'customer_note' => 'Untuk renovasi lantai 2.',
                'status' => 'Selesai',
                'admin_note' => 'Barang sudah diterima di lokasi oleh Pak Bambang (mandor). Pembayaran lunas di tempat (COD).',
                'created_at' => now()->subDays(2),
                'items' => [
                    ['material_id' => 6, 'qty' => 8],   // Hebel (650.000 x 8 = 5.200.000)
                    ['material_id' => 8, 'qty' => 10],  // Mortar (78.000 x 10 = 780.000)
                ],
            ],
            [
                'order_code' => 'MK-20261122-E505',
                'customer_name' => 'Agus Setiawan',
                'whatsapp' => '085712348899',
                'address' => 'Jl. ZA Pagar Alam No. 18, Gedong Meneng, Bandar Lampung',
                'customer_note' => 'Kirim hari ini jika bisa.',
                'status' => 'Dibatalkan',
                'admin_note' => 'Dibatalkan oleh pembeli via WhatsApp karena jadwal renovasi diundur bulan depan.',
                'created_at' => now()->subDays(3),
                'items' => [
                    ['material_id' => 2, 'qty' => 10], // Keramik Subway (95.000 x 10 = 950.000)
                ],
            ],
        ];

        foreach ($sampleOrders as $data) {
            $existing = Order::where('order_code', $data['order_code'])->first();
            if ($existing) {
                continue;
            }

            $rawToken = Str::random(32);
            $hashToken = hash('sha256', $rawToken);

            $order = Order::create([
                'order_code' => $data['order_code'],
                'idempotency_key' => 'seed_' . Str::uuid(),
                'tracking_token_hash' => $hashToken,
                'customer_name' => $data['customer_name'],
                'whatsapp' => $data['whatsapp'],
                'address' => $data['address'],
                'customer_note' => $data['customer_note'],
                'material_total' => 0,
                'status' => $data['status'],
                'admin_note' => $data['admin_note'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['created_at'],
            ]);

            $total = 0;
            foreach ($data['items'] as $itemData) {
                $mat = $materials->get($itemData['material_id']) ?? $materials->first();
                $subtotal = $mat->price * $itemData['qty'];
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'material_id' => $mat->id,
                    'name_snapshot' => $mat->name,
                    'unit_snapshot' => $mat->sale_unit,
                    'price_snapshot' => $mat->price,
                    'quantity' => $itemData['qty'],
                    'subtotal' => $subtotal,
                    'calculation_snapshot' => null,
                ]);
            }

            $order->update(['material_total' => $total]);

            // Add history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => 'Menunggu Konfirmasi',
                'changed_by' => null,
                'note' => 'Pesanan diajukan oleh pembeli via web',
                'created_at' => $data['created_at'],
            ]);

            if ($data['status'] !== 'Menunggu Konfirmasi') {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'from_status' => 'Menunggu Konfirmasi',
                    'to_status' => $data['status'],
                    'changed_by' => $adminId,
                    'note' => $data['admin_note'],
                    'created_at' => $data['created_at']->copy()->addMinutes(30),
                ]);
            }
        }
    }
}
