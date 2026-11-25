<?php

use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderSummaryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ShippingInfoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Halaman Publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/api/search/suggestions', [CatalogController::class, 'suggestions'])->name('api.search.suggestions');
Route::get('/informasi-pengiriman', [ShippingInfoController::class, 'index'])->name('shipping.info');
Route::get('/hubungi-toko', [ContactController::class, 'index'])->name('contact.index');
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');

// Kalkulator Material
Route::get('/kalkulator', [CalculatorController::class, 'index'])->name('calculator.index');
Route::post('/api/calculate/ceramic', [CalculatorController::class, 'calculateCeramic'])->name('api.calculate.ceramic');
Route::post('/api/calculate/paint', [CalculatorController::class, 'calculatePaint'])->name('api.calculate.paint');

// Checkout & Ringkasan Pesanan (Tamu)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/pesanan/{orderCode}', [OrderSummaryController::class, 'show'])->name('order.summary');

// Dashboard & Manajemen Pesanan Admin (Wajib Login)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [AdminOrderController::class, 'index'])->name('dashboard');
    Route::patch('/admin/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.update-status');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
