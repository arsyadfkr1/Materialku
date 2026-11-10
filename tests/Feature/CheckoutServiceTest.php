<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Material;
use App\Models\Order;
use App\Services\CheckoutService;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    private CheckoutService $checkoutService;
    private Material $material;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checkoutService = new CheckoutService();

        $category = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Kategori Test']);
        $this->material = Material::firstOrCreate(
            ['slug' => 'material-test-resmi'],
            [
                'category_id' => $category->id,
                'name' => 'Material Uji Harga Resmi',
                'price' => 85000,
                'sale_unit' => 'dus',
                'is_active' => true,
                'availability_status' => 'available',
            ]
        );
    }

    /**
     * UAT-04: Server selalu memakai harga resmi, bukan hasil manipulasi browser
     */
    public function test_uat_04_server_enforces_official_price(): void
    {
        $customerData = [
            'customer_name' => 'Budi Santoso',
            'whatsapp' => '08123456789',
            'address' => 'Jl. Mawar No. 10',
            'idempotency_key' => 'key-test-04-' . uniqid(),
        ];

        $cartItems = [
            [
                'material_id' => $this->material->id,
                'quantity' => 2,
                // User coba manipulasi harga fiktif di client
                'price' => 1000, 
            ]
        ];

        $result = $this->checkoutService->processCheckout($customerData, $cartItems);
        $order = $result['order'];

        // Total harus 2 * 85000 = 170000, bukan 2000
        $this->assertEquals(170000, (float) $order->material_total);
        $this->assertEquals(85000, (float) $order->items->first()->price_snapshot);
    }

    /**
     * UAT-05: Checkout dua kali dengan kunci sama menghasilkan satu pesanan (idempotency)
     */
    public function test_uat_05_idempotency_prevents_duplicate_orders(): void
    {
        $customerData = [
            'customer_name' => 'Ahmad',
            'whatsapp' => '08129876543',
            'address' => 'Jl. Melati No. 5',
            'idempotency_key' => 'unique-idem-key-' . uniqid(),
        ];

        $cartItems = [
            [
                'material_id' => $this->material->id,
                'quantity' => 1,
            ]
        ];

        $firstResult = $this->checkoutService->processCheckout($customerData, $cartItems);
        $secondResult = $this->checkoutService->processCheckout($customerData, $cartItems);

        $this->assertEquals($firstResult['order']->id, $secondResult['order']->id);
        $this->assertEquals($firstResult['order']->order_code, $secondResult['order']->order_code);
        $this->assertTrue($secondResult['is_duplicate']);
    }
}
