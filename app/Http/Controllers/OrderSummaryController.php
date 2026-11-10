<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use App\Services\WhatsAppMessageBuilder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderSummaryController extends Controller
{
    public function show(string $orderCode, Request $request): Response
    {
        $order = Order::with(['items.material', 'statusHistories'])
            ->where('order_code', $orderCode)
            ->firstOrFail();

        $token = $request->query('token');
        $isAuthorized = false;

        if ($token && hash('sha256', $token) === $order->tracking_token_hash) {
            $isAuthorized = true;
        } elseif (auth()->check()) {
            $isAuthorized = true;
        }

        $setting = Setting::first();
        $whatsappUrl = WhatsAppMessageBuilder::generateWhatsAppUrl($order, $setting);
        $whatsappMessage = WhatsAppMessageBuilder::buildOrderMessage($order, $setting);

        return Inertia::render('Order/Summary', [
            'order' => $order,
            'isAuthorized' => $isAuthorized,
            'whatsappUrl' => $whatsappUrl,
            'whatsappMessage' => $whatsappMessage,
            'setting' => $setting,
        ]);
    }
}
