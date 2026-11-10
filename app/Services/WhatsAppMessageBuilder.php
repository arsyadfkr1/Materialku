<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;

class WhatsAppMessageBuilder
{
    /**
     * Normalisasi nomor telepon ke format internasional (misal 0812 -> 62812)
     */
    public static function normalizePhoneNumber(string $phone): string
    {
        // Hilangkan spasi, strip, tanda tambah, titik
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali 08, ganti 0 dengan 62
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        }

        return $cleaned;
    }

    /**
     * Format rupiah helper
     */
    public static function formatRupiah(float $amount): string
    {
        return 'Rp' . number_format($amount, 0, ',', '.');
    }

    /**
     * Bangun pesan teks sesuai format PRD Bab 10
     */
    public static function buildOrderMessage(Order $order, ?Setting $setting = null): string
    {
        $setting = $setting ?? Setting::first();
        $storeName = $setting->store_name ?? 'MATERIALKU';

        $itemsText = [];
        $order->loadMissing('items');
        foreach ($order->items as $index => $item) {
            $num = $index + 1;
            $formattedPrice = self::formatRupiah((float) $item->price_snapshot);
            $formattedSubtotal = self::formatRupiah((float) $item->subtotal);
            $itemsText[] = "{$num}. {$item->name_snapshot} — {$item->quantity} {$item->unit_snapshot} × {$formattedPrice} = {$formattedSubtotal}";
        }
        $detailMaterial = implode("\n", $itemsText);

        $totalFormatted = self::formatRupiah((float) $order->material_total);

        $message = "Halo Admin {$storeName}, saya ingin mengonfirmasi pesanan berikut.\n\n"
            . "Kode: {$order->order_code}\n"
            . "Nama: {$order->customer_name}\n\n"
            . "Detail material:\n{$detailMaterial}\n\n"
            . "Estimasi biaya material: {$totalFormatted}\n"
            . "Ongkir belum termasuk.\n\n"
            . "Mohon konfirmasi ketersediaan, ongkir, dan cara pembayaran. Terima kasih.";

        return $message;
    }

    /**
     * Buat URL wa.me yang di-encode
     */
    public static function generateWhatsAppUrl(Order $order, ?Setting $setting = null): string
    {
        $setting = $setting ?? Setting::first();
        $targetPhone = self::normalizePhoneNumber($setting->store_whatsapp ?? '6281234567890');
        $message = self::buildOrderMessage($order, $setting);

        return "https://wa.me/{$targetPhone}?text=" . rawurlencode($message);
    }
}
