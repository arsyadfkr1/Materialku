<?php

namespace Tests\Feature;

use App\Models\Setting;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Setting::firstOrCreate(
            ['id' => 1],
            [
                'store_name' => 'MATERIALKU',
                'store_whatsapp' => '6281234567890',
                'store_address' => 'Jl. Merdeka No. 123',
            ]
        );
    }

    public function test_home_page_returns_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_catalog_page_returns_successful_response(): void
    {
        $response = $this->get('/katalog');
        $response->assertStatus(200);
    }

    public function test_calculator_page_returns_successful_response(): void
    {
        $response = $this->get('/kalkulator');
        $response->assertStatus(200);
    }

    public function test_checkout_page_returns_successful_response(): void
    {
        $response = $this->get('/checkout');
        $response->assertStatus(200);
    }
}
