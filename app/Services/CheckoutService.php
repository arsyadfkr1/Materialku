<?php

namespace App\Services;

use App\Models\Material;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    /**
     * Proses pembuatan pesanan baru tamu
     *
     * @param array $customerData [customer_name, whatsapp, address, customer_note, idempotency_key]
     * @param array $cartItems [ ['material_id' => int, 'quantity' => int, 'calculation_snapshot' => ?array] ]
     * @return array [ 'order' => Order, 'tracking_token' => string ]
     */
    public function processCheckout(array $customerData, array $cartItems): array
    {
        $idempotencyKey = $customerData['idempotency_key'] ?? Str::uuid()->toString();

        // 1. Cek duplikasi idempotency key
        $existingOrder = Order::with('items')->where('idempotency_key', $idempotencyKey)->first();
        if ($existingOrder) {
            return [
                'order' => $existingOrder,
                'tracking_token' => null, // Tidak dapat menampilkan token mentah yang telah lewat
                'is_duplicate' => true,
            ];
        }

        if (empty($cartItems)) {
            throw new \InvalidArgumentException('Keranjang belanja kosong tidak dapat diproses.');
        }

        // 2. Kunci transaksi database
        return DB::transaction(function () use ($customerData, $cartItems, $idempotencyKey) {
            $materialIds = array_column($cartItems, 'material_id');
            $materials = Material::whereIn('id', $materialIds)
                ->where('is_active', true)
                ->get()
                ->keyBy('id');

            $orderItemsData = [];
            $totalAmount = 0;

            foreach ($cartItems as $item) {
                $materialId = $item['material_id'];
                $quantity = (int) ($item['quantity'] ?? 1);

                if ($quantity <= 0) {
                    throw new \InvalidArgumentException('Jumlah pembelian harus berupa bilangan bulat positif.');
                }

                if (!isset($materials[$materialId])) {
                    throw new \Exception("Produk dengan ID {$materialId} tidak ditemukan atau sedang tidak aktif.");
                }

                $mat = $materials[$materialId];

                if ($mat->availability_status !== 'available') {
                    throw new \Exception("Produk '{$mat->name}' saat ini tidak tersedia.");
                }

                $price = (float) $mat->price;
                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'material_id' => $mat->id,
                    'name_snapshot' => $mat->name,
                    'unit_snapshot' => $mat->sale_unit,
                    'price_snapshot' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                    'calculation_snapshot' => $item['calculation_snapshot'] ?? null,
                ];
            }

            // 3. Generate Order Code Unik (Format PRD: MK-YYYYMMDD-XXXX)
            $datePart = now()->format('Ymd');
            do {
                $randomCode = strtoupper(Str::random(4));
                $orderCode = "MK-{$datePart}-{$randomCode}";
            } while (Order::where('order_code', $orderCode)->exists());

            // 4. Generate Kriptografis Tracking Token
            $rawTrackingToken = Str::random(40);
            $tokenHash = hash('sha256', $rawTrackingToken);

            // 5. Buat entitas Order
            $order = Order::create([
                'order_code' => $orderCode,
                'idempotency_key' => $idempotencyKey,
                'tracking_token_hash' => $tokenHash,
                'customer_name' => $customerData['customer_name'],
                'whatsapp' => WhatsAppMessageBuilder::normalizePhoneNumber($customerData['whatsapp']),
                'address' => $customerData['address'],
                'customer_note' => $customerData['customer_note'] ?? null,
                'material_total' => $totalAmount,
                'status' => 'Menunggu Konfirmasi',
            ]);

            // 6. Simpan item pesanan
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // 7. Simpan Riwayat Status Awal
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => 'Menunggu Konfirmasi',
                'changed_by' => null,
                'note' => 'Pesanan baru dibuat oleh pelanggan.',
                'created_at' => now(),
            ]);

            return [
                'order' => $order->fresh(['items']),
                'tracking_token' => $rawTrackingToken,
                'is_duplicate' => false,
            ];
        });
    }
}
