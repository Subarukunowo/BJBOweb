<?php

namespace App\Models\Traits;

use App\Models\AppNotification;
use App\Models\BankAccount;
use App\Models\Cart;
use App\Models\Favorit;
use App\Models\Invoice;
use App\Models\Laporan;
use App\Models\Message;
use App\Models\PencairanDana;
use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\Store;
use App\Models\Ulasan;
use App\Models\UserProductView;
use App\Models\UserSearchLog;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait UserRelations
{
    public function postingan(): HasMany
    {
        return $this->hasMany(Postingan::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function pencairanDanas(): HasMany
    {
        return $this->hasMany(PencairanDana::class);
    }

    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'user_id');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function searchLogs(): HasMany
    {
        return $this->hasMany(UserSearchLog::class);
    }

    public function productViews(): HasMany
    {
        return $this->hasMany(UserProductView::class);
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }

    public function ulasan(): HasMany
    {
        return $this->hasMany(Ulasan::class);
    }

    public function favorits(): HasMany
    {
        return $this->hasMany(Favorit::class);
    }

    public function favoritePostings(): BelongsToMany
    {
        return $this->belongsToMany(Postingan::class, 'favorits');
    }

    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'user_id');
    }

    public function penjualPesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'penjual_id');
    }
}
