<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\CartController;
use App\Http\Controllers\Api\v1\ChatController;
use App\Http\Controllers\Api\v1\NotificationController;
use App\Http\Controllers\Api\v1\OrderController;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\PromoController;
use App\Http\Controllers\Api\v1\ReviewController;
use App\Http\Controllers\Api\v1\StoreController;
use App\Http\Controllers\Api\v1\WalletController;
use App\Http\Controllers\Api\v1\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile REST API Routes (v1)
|--------------------------------------------------------------------------
|
| Base URL: /api/v1/...
| Format data respons: JSON standar BJBO
|
*/

Route::prefix('v1')->group(function () {
    // Autentikasi Publik
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Katalog Produk & Promosi Publik
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/categories', [ProductController::class, 'categories']);
    Route::get('/products/{id}', [ProductController::class, 'show'])->whereNumber('id');
    Route::get('/products/{id}/reviews', [ReviewController::class, 'index'])->whereNumber('id');
    Route::get('/promos/banners', [PromoController::class, 'banners']);
    Route::get('/promos/vouchers', [PromoController::class, 'vouchers']);
    Route::get('/shipping/couriers', [OrderController::class, 'couriers']);

    // Rute Terproteksi Bearer Token (api.auth)
    Route::middleware('api.auth')->group(function () {
        // Profil & Sesi
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::match(['put', 'post'], '/auth/profile', [AuthController::class, 'updateProfile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Rekomendasi & Manajemen Produk Penjual
        Route::get('/products/recommendations', [ProductController::class, 'recommendations']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::match(['put', 'post'], '/products/{id}', [ProductController::class, 'update'])->whereNumber('id');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->whereNumber('id');
        Route::post('/products/{id}/toggle-sold', [ProductController::class, 'toggleSold'])->whereNumber('id');
        Route::get('/seller/metrics', [ProductController::class, 'sellerMetrics']);
        Route::get('/seller/products', [ProductController::class, 'sellerProducts']);

        // Keranjang Belanja (Cart)
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::put('/cart/{id}', [CartController::class, 'update'])->whereNumber('id');
        Route::delete('/cart/{id}', [CartController::class, 'destroy'])->whereNumber('id');

        // Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);

        // Transaksi & Pesanan
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show'])->whereNumber('id');
        Route::post('/orders/checkout', [OrderController::class, 'checkout']);
        Route::get('/orders/{id}/tracking', [OrderController::class, 'tracking'])->whereNumber('id');
        Route::post('/orders/{id}/pay', [OrderController::class, 'pay'])->whereNumber('id');
        Route::post('/orders/{id}/confirm', [OrderController::class, 'confirm'])->whereNumber('id');
        Route::post('/orders/{id}/ship', [OrderController::class, 'ship'])->whereNumber('id');
        Route::post('/orders/{id}/deliver', [OrderController::class, 'deliver'])->whereNumber('id');
        Route::get('/orders/{id}/invoice', [OrderController::class, 'invoice'])->whereNumber('id');

        // Dompet Digital & Escrow (BJPay & Rekber)
        Route::get('/wallet/bjpay', [WalletController::class, 'getBjpay']);
        Route::post('/wallet/bjpay/topup', [WalletController::class, 'topupBjpay']);
        Route::get('/wallet/rekber', [WalletController::class, 'getRekber']);
        Route::post('/wallet/rekber/withdraw', [WalletController::class, 'withdrawRekber']);
        Route::post('/wallet/rekber/bank-accounts', [WalletController::class, 'addBankAccount']);
        Route::delete('/wallet/rekber/bank-accounts/{id}', [WalletController::class, 'deleteBankAccount'])->whereNumber('id');

        // Chat Real-Time & Sensor Anti-HP
        Route::get('/chats', [ChatController::class, 'conversations']);
        Route::get('/chats/{otherUserId}', [ChatController::class, 'messages'])->whereNumber('otherUserId');
        Route::post('/chats/send', [ChatController::class, 'send']);
        Route::post('/chats/report/{messageId}', [ChatController::class, 'report'])->whereNumber('messageId');

        // Validasi Voucher
        Route::post('/promos/validate', [PromoController::class, 'validateCode']);

        // Buka Toko
        Route::get('/store/my-store', [StoreController::class, 'myStore']);
        Route::post('/store/register', [StoreController::class, 'register']);

        // Ulasan Produk
        Route::post('/reviews', [ReviewController::class, 'store']);

        // Notifikasi Pengguna
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->whereNumber('id');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    });
});
