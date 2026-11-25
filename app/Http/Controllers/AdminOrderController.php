<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminOrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan masuk untuk dashboard admin.
     */
    public function index(Request $request): Response
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Order::with(['items.material', 'statusHistories.changer'])
            ->latest('created_at');

        // Filter status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Pencarian kode pesanan, nama pembeli, atau nomor WhatsApp
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        // Statistik pesanan untuk KPI cards
        $stats = [
            'total'               => Order::count(),
            'menunggu_konfirmasi' => Order::where('status', 'Menunggu Konfirmasi')->count(),
            'diproses'            => Order::where('status', 'Diproses')->count(),
            'dikirim'             => Order::where('status', 'Dikirim')->count(),
            'selesai'             => Order::where('status', 'Selesai')->count(),
            'dibatalkan'          => Order::where('status', 'Dibatalkan')->count(),
            'total_revenue'       => (float) Order::where('status', 'Selesai')->sum('material_total'),
        ];

        $setting = Setting::first();

        return Inertia::render('Dashboard', [
            'orders'   => $orders,
            'filters'  => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
            ],
            'stats'    => $stats,
            'setting'  => $setting,
        ]);
    }

    /**
     * Memperbarui status pesanan dan mencatat riwayat perubahannya.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status'     => 'required|string|in:Menunggu Konfirmasi,Diproses,Dikirim,Selesai,Dibatalkan',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $fromStatus = $order->status;
        $toStatus   = $validated['status'];

        $order->update([
            'status'     => $toStatus,
            'admin_note' => $validated['admin_note'] ?? $order->admin_note,
        ]);

        // Catat ke riwayat perubahan status jika status berubah atau ada catatan
        if ($fromStatus !== $toStatus || !empty($validated['admin_note'])) {
            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'from_status' => $fromStatus,
                'to_status'   => $toStatus,
                'changed_by'  => auth()->id(),
                'note'        => $validated['admin_note'] ?? null,
                'created_at'  => now(),
            ]);
        }

        return back()->with('success', "Status pesanan {$order->order_code} berhasil diperbarui menjadi '{$toStatus}'.");
    }
}
