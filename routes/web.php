<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminStoreController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DokuWebhookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentGroupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerProductController;
use App\Http\Controllers\Seller\SellerRegisterController;
use App\Http\Controllers\Seller\SellerStoreController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// Katalog & Produk Pembeli (Publik)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/kategori/{slug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/toko/{slug}', [StoreController::class, 'show'])->name('stores.show');

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

    // Pendaftaran Penjual (Bagi User Login yang Belum Punya Toko)
    Route::get('/penjual/daftar', [SellerRegisterController::class, 'create'])->name('seller.register');
    Route::post('/penjual/daftar', [SellerRegisterController::class, 'store'])->name('seller.register.store');
});

// Area Khusus Penjual (Hanya Seller Aktif dengan Toko)
Route::middleware(['auth', 'seller'])->prefix('penjual')->name('seller.')->group(function () {
    // 1. Dashboard Penjual
    Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');

    // 2. Pengaturan Profil Toko
    Route::get('/toko', [SellerStoreController::class, 'edit'])->name('store.edit');
    Route::put('/toko', [SellerStoreController::class, 'update'])->name('store.update');

    // 3. Manajemen Produk Toko
    Route::get('/produk', [SellerProductController::class, 'index'])->name('products.index');
    Route::get('/produk/tambah', [SellerProductController::class, 'create'])->name('products.create');
    Route::post('/produk', [SellerProductController::class, 'store'])->name('products.store');
    Route::get('/produk/{product}/edit', [SellerProductController::class, 'edit'])->name('products.edit');
    Route::put('/produk/{product}', [SellerProductController::class, 'update'])->name('products.update');
    Route::delete('/produk/{product}', [SellerProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('/produk/{product}/toggle-status', [SellerProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::delete('/produk/{product}/foto/{image}', [SellerProductController::class, 'deleteImage'])->name('products.images.destroy');
    Route::patch('/produk/{product}/foto/{image}/primary', [SellerProductController::class, 'setPrimaryImage'])->name('products.images.primary');

    // 4. Manajemen Pesanan Toko
    Route::get('/pesanan', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order_number}', [SellerOrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order_number}/kirim', [SellerOrderController::class, 'ship'])->name('orders.ship');
});

// Area Khusus Administrator
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // 1. Dashboard Admin
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 2. Kelola Pengguna
    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/pengguna/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::patch('/pengguna/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

    // 3. Kelola Toko
    Route::get('/toko', [AdminStoreController::class, 'index'])->name('stores.index');
    Route::get('/toko/{store}', [AdminStoreController::class, 'show'])->name('stores.show');
    Route::patch('/toko/{store}/toggle-status', [AdminStoreController::class, 'toggleStatus'])->name('stores.toggle-status');
});

require __DIR__.'/auth.php';
