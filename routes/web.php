<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PencairanAdminController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\PostinganController;
use App\Http\Controllers\Admin\PostingController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Admin\PromoAdminController;
use App\Http\Controllers\Admin\StoreModerationController;
use App\Http\Controllers\Admin\UlasanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\FavoritController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\PesananController;
use App\Http\Controllers\User\PesananFulfillmentController;
use App\Http\Controllers\User\RekberController;
use App\Http\Controllers\User\StoreRegistrationController;
use App\Http\Controllers\User\WishlistController;
use Illuminate\Support\Facades\Route;

// Halaman Publik & Autentikasi Pengunjung
Route::group(['middleware' => 'guest'], function () {
    Route::get('/', [AuthController::class, 'splash'])->name('splash');
    Route::get('/layout', [AuthController::class, 'layout'])->name('layout');
    Route::get('/about', [AuthController::class, 'about'])->name('about');

    // Halaman Blade Khusus Login & Register
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('post.login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'post_register'])->middleware('throttle:5,1')->name('post.register');
    Route::post('/post-register', [AuthController::class, 'post_register'])->middleware('throttle:5,1')->name('post.register.legacy');

    // Verifikasi Email OTP dengan jeda resend 60 detik
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('otp.verify.show');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('otp.verify.submit');
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->middleware('throttle:5,1')->name('otp.resend');
});

// Autentikasi Sosial (Google, Facebook, Instagram)
Route::prefix('auth')->group(function () {
    Route::get('/google', [SocialAuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('google.callback');

    Route::get('/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('facebook.redirect');
    Route::get('/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback'])->name('facebook.callback');

    Route::get('/instagram', [SocialAuthController::class, 'redirectToInstagram'])->name('instagram.redirect');
    Route::get('/instagram/callback', [SocialAuthController::class, 'handleInstagramCallback'])->name('instagram.callback');
});

// Admin Route (100% Tailwind CSS)
Route::group(['middleware' => 'admin'], function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin-logout', [AuthController::class, 'admin_logout'])->name('admin.logout');
    Route::get('/admin/pengguna', [PenggunaController::class, 'index'])->name('admin.users');
    Route::get('/admin/posting', [PostingController::class, 'index'])->name('admin.postings');
    Route::get('/admin/ulasan', [UlasanController::class, 'index'])->name('admin.ulasans');
    Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporans');

    Route::get('/admin/pengguna/block/{id}', [PenggunaController::class, 'blockUser'])->name('pengguna.block');
    Route::get('/admin/pengguna/unblock/{id}', [PenggunaController::class, 'unblockUser'])->name('pengguna.unblock');
    Route::delete('/admin/pengguna/delete/{id}', [PenggunaController::class, 'delete'])->name('pengguna.delete');

    Route::post('/admin/posting/update-status/{id}', [PostingController::class, 'updateStatus'])->name('posting.updateStatus');

    // Admin Promo, Iklan & Diskon
    Route::get('/admin/promo', [PromoAdminController::class, 'index'])->name('admin.promo.index');
    Route::get('/admin/promo/create', [PromoAdminController::class, 'create'])->name('admin.promo.create');
    Route::post('/admin/promo', [PromoAdminController::class, 'store'])->name('admin.promo.store');
    Route::get('/admin/promo/{id}/edit', [PromoAdminController::class, 'edit'])->name('admin.promo.edit');
    Route::put('/admin/promo/{id}', [PromoAdminController::class, 'update'])->name('admin.promo.update');
    Route::post('/admin/promo/{id}/toggle', [PromoAdminController::class, 'toggle'])->name('admin.promo.toggle');
    Route::delete('/admin/promo/{id}', [PromoAdminController::class, 'destroy'])->name('admin.promo.destroy');

    // Admin Pencairan Rekber (2x24 Jam Kerja)
    Route::get('/admin/pencairan', [PencairanAdminController::class, 'index'])->name('admin.pencairan.index');
    Route::post('/admin/pencairan/{id}/proses', [PencairanAdminController::class, 'process'])->name('admin.pencairan.process');

    // Admin Verifikasi Toko (Perorangan vs Official Store)
    Route::get('/admin/toko', [StoreModerationController::class, 'index'])->name('admin.toko.index');
    Route::post('/admin/toko/{id}/moderasi', [StoreModerationController::class, 'moderate'])->name('admin.toko.moderate');
});

// Midtrans Webhook Notification Callback (Unauthenticated & CSRF-exempt)
Route::post('/midtrans/notification', [\App\Http\Controllers\MidtransWebhookController::class, 'handle'])->name('midtrans.notification');

// User Route (Terautentikasi)
Route::group(['middleware' => 'auth'], function () {
    // Dashboard User
    Route::get('/user', [UserController::class, 'index'])->name('user.dashboard');
    Route::get('/user-logout', [AuthController::class, 'user_logout'])->name('user.logout');

    Route::prefix('user')->group(function () {
        // Postingan
        Route::get('/postingan/create', [PostinganController::class, 'create'])->name('user.postingan.create');
        Route::post('/postingan', [PostinganController::class, 'store'])->name('user.postingan.store');
        Route::get('/postingan/detail/{id}', [UserController::class, 'show'])->name('user.detail.postingan');
        Route::get('/postingan/cari', [PostinganController::class, 'cari'])->name('postingan.cari');
        Route::get('/postingan/kategori/{kategori}', [PostinganController::class, 'filterByCategory'])->name('postingan.kategori');
        Route::post('/ulasan', [UlasanController::class, 'store'])->name('ulasan.store');
        Route::get('/laporan', fn () => redirect()->route('user.dashboard'))->name('laporan.form');
        Route::post('/laporan', [LaporanController::class, 'submit'])->name('laporan.submit');

        // Notifikasi Pengguna & Realtime Reverb
        Route::get('/notifikasi', [NotificationController::class, 'index'])->name('user.notifikasi.index');
        Route::post('/notifikasi/{id}/baca', [NotificationController::class, 'markAsRead'])->name('user.notifikasi.read');
        Route::post('/notifikasi/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('user.notifikasi.readAll');
        Route::get('/api/notifikasi/unread-count', [NotificationController::class, 'unreadCount'])->name('user.notifikasi.unreadCount');

        // Fitur Chat Sedagho (Tanya Stok, Anti Nomor Telepon, E2E, Lapor Chat)
        Route::get('/chat', [ChatController::class, 'index'])->name('user.chat.index');
        Route::get('/chat/start/{postinganId}', [ChatController::class, 'startFromProduct'])->name('user.chat.start');
        Route::get('/chat/{otherUserId}/{postinganId?}', [ChatController::class, 'room'])->name('user.chat.room');
        Route::post('/chat/send', [ChatController::class, 'send'])->name('user.chat.send');
        Route::post('/chat/report/{messageId}', [ChatController::class, 'report'])->name('user.chat.report');

        // Favorit Barang & Wishlist
        Route::get('/favorit', [FavoritController::class, 'index'])->name('user.favorit');
        Route::post('/favorit/toggle/{id}', [FavoritController::class, 'toggle'])->name('user.favorit.toggle');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('user.wishlist.index');
        Route::post('/wishlist/toggle/{id}', [WishlistController::class, 'toggle'])->name('user.wishlist.toggle');
        Route::post('/api/promo/check', [WishlistController::class, 'checkPromo'])->name('user.promo.check');

        // Buka Toko (Toko Perorangan vs Official Store)
        Route::get('/buka-toko', [StoreRegistrationController::class, 'show'])->name('user.toko.daftar');
        Route::post('/buka-toko/perorangan', [StoreRegistrationController::class, 'storePerorangan'])->name('user.toko.storePerorangan');
        Route::post('/buka-toko/official', [StoreRegistrationController::class, 'storeOfficial'])->name('user.toko.storeOfficial');

        // Rekber Escrow Wallet & Pencairan Bank 2x24 Jam Kerja
        Route::get('/rekber', [RekberController::class, 'index'])->name('user.rekber.index');
        Route::post('/rekber/rekening', [RekberController::class, 'addBank'])->name('user.rekber.addBank');
        Route::post('/rekber/tarik-dana', [RekberController::class, 'requestWithdrawal'])->name('user.rekber.tarikDana');

        // Pesanan & Pembayaran Midtrans Snap (Tanpa Redirect) / QRIS Gratis
        Route::get('/pesanan', [PesananController::class, 'index'])->name('user.pesanan.index');
        Route::get('/pesanan/checkout/{postingan_id}', [PesananController::class, 'checkout'])->name('user.pesanan.checkout');
        Route::post('/pesanan/store/{postingan_id}', [PesananController::class, 'store'])->name('user.pesanan.store');
        Route::get('/pesanan/detail/{id}', [PesananController::class, 'detail'])->name('user.pesanan.detail');
        Route::post('/pesanan/finish/{id}', [PesananController::class, 'finish'])->name('user.pesanan.finish');
        Route::post('/pesanan/simulate-qris/{id}', [PesananController::class, 'simulateQris'])->name('user.pesanan.simulate-qris');

        // Lifecycle Pesanan & Pelacakan Kurir (Leaflet OpenStreetMap)
        Route::post('/pesanan/{id}/konfirmasi', [PesananFulfillmentController::class, 'confirm'])->name('user.pesanan.confirm');
        Route::post('/pesanan/{id}/kirim', [PesananFulfillmentController::class, 'ship'])->name('user.pesanan.ship');
        Route::post('/pesanan/{id}/selesai', [PesananFulfillmentController::class, 'deliver'])->name('user.pesanan.deliver');
        Route::get('/pesanan/{id}/lacak', [PesananFulfillmentController::class, 'track'])->name('user.pesanan.track');
        Route::get('/api/pesanan/{id}/tracking', [PesananFulfillmentController::class, 'trackingApi'])->name('user.pesanan.trackingApi');

        // Profil & Edit Foto Akun
        Route::get('/profil', [ProfilController::class, 'index'])->name('user.profil');
        Route::get('/profil-saya', [ProfilController::class, 'index'])->name('user.profil.index');
        Route::post('/profil/update-akun', [ProfilController::class, 'updateAccount'])->name('user.profil.updateAccount');
        Route::delete('/profil/hapus-foto', [ProfilController::class, 'removeAvatar'])->name('user.profil.removeAvatar');
        Route::post('/profil/toggle-sold/{id}', [ProfilController::class, 'toggleSold'])->name('user.profil.toggleSold');
        Route::get('/profil/edit/{id}', [ProfilController::class, 'edit'])->name('user.profil.edit');
        Route::post('/profil/update/{id}', [ProfilController::class, 'update'])->name('profil.update');
        Route::delete('/profil/delete/{id}', [ProfilController::class, 'delete'])->name('user.profil.delete');
    });
});
