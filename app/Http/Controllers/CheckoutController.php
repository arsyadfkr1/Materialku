<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function index(): Response
    {
        $setting = Setting::first();

        return Inertia::render('Checkout/Index', [
            'setting' => $setting,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'whatsapp' => ['required', 'string', 'min:9', 'max:20', 'regex:/^([0-9\+\s\-]+)$/'],
            'address' => 'required|string|max:500',
            'customer_note' => 'nullable|string|max:500',
            'idempotency_key' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.calculation_snapshot' => 'nullable|array',
        ]);

        try {
            $result = $this->checkoutService->processCheckout(
                [
                    'customer_name' => $validated['customer_name'],
                    'whatsapp' => $validated['whatsapp'],
                    'address' => $validated['address'],
                    'customer_note' => $validated['customer_note'] ?? null,
                    'idempotency_key' => $validated['idempotency_key'],
                ],
                $validated['items']
            );

            $order = $result['order'];
            $token = $result['tracking_token'];

            return redirect()->route('order.summary', [
                'orderCode' => $order->order_code,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['checkout_error' => $e->getMessage()]);
        }
    }
}
