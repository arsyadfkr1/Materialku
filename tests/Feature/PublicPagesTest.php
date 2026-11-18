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
                'store_name' => 'TB. Mandiri Sejahtera Abadi',
                'store_whatsapp' => '6281234567890',
                'store_address' => 'Jl. Soekarno-Hatta No. 88, By Pass, Kota Bandar Lampung',
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

    public function test_catalog_page_supports_advanced_filters(): void
    {
        $response = $this->get('/katalog?price_range=under_100k&unit=sak&sort=price_asc&available_only=1');
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

    public function test_shipping_info_page_returns_successful_response(): void
    {
        $response = $this->get('/informasi-pengiriman');
        $response->assertStatus(200);
    }

    public function test_cart_page_returns_successful_response(): void
    {
        $response = $this->get('/keranjang');
        $response->assertStatus(200);
    }

    public function test_search_suggestions_api_returns_json_results(): void
    {
        $response = $this->getJson('/api/search/suggestions?q=semen');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'materials',
            'total_count'
        ]);
    }
}
