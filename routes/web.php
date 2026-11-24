<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DokuWebhookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentGroupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// Katalog & Produk Pembeli (Publik)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/kategori/{slug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');

// Gateway Pembayaran DOKU (Publik & Webhook)
Route::post('/api/doku/notification', [DokuWebhookController::class, 'handle'])->name('doku.notification');
Route::get('/pembayaran/kembali', [PaymentGroupController::class, 'callback'])->name('payment-groups.callback');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Fitur Pembeli (Harus Login)
Route::middleware('auth')->group(function () {
    // 1. Keranjang Belanja
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/keranjang/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/keranjang/{cartItem}/toggle-select', [CartController::class, 'toggleSelect'])->name('cart.toggle-select');
    Route::post('/keranjang/select-all', [CartController::class, 'selectAll'])->name('cart.select-all');

    // 2. Checkout & Pembayaran
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/voucher', [CheckoutController::class, 'applyVoucher'])->name('checkout.voucher');
    Route::delete('/checkout/voucher', [CheckoutController::class, 'removeVoucher'])->name('checkout.remove-voucher');

    // 3. Riwayat & Detail Pesanan
    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order}/batal', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/pesanan/{order}/selesai', [OrderController::class, 'complete'])->name('orders.complete');

    // 4. Wishlist & Favorit
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // 5. Alamat Pengiriman (CRUD Lengkap)
    Route::resource('alamat', AddressController::class)->parameters(['alamat' => 'address'])->names('addresses');
    Route::patch('/alamat/{address}/set-primary', [AddressController::class, 'setPrimary'])->name('addresses.set-primary');

    // 6. Ulasan Produk Pesanan Selesai
    Route::get('/pesanan/{order}/ulasan/{orderItem}', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/pesanan/{order}/ulasan/{orderItem}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/ulasan/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/ulasan/{review}', [ReviewController::class, 'update'])->name('reviews.update');

    // 7. Grup Pembayaran DOKU Multi-Order
    Route::get('/pembayaran/{paymentGroup}', [PaymentGroupController::class, 'show'])->name('payment-groups.show');
    Route::post('/pembayaran/{paymentGroup}/bayar', [PaymentGroupController::class, 'pay'])->name('payment-groups.pay');
    Route::post('/pembayaran/{paymentGroup}/cek-status', [PaymentGroupController::class, 'check'])->name('payment-groups.check');

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
