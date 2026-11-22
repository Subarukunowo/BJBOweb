<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $invoice_number
 * @property int $pesanan_id
 * @property int $user_id
 * @property int $seller_id
 * @property int $subtotal
 * @property int $ongkir
 * @property int $diskon_voucher
 * @property int $diskon_ongkir
 * @property int $total_bayar
 * @property string $metode_pembayaran
 * @property string $status
 * @property \Carbon\Carbon|null $paid_at
 * @property string $receipt_token
 * @property string|null $catatan
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Pesanan|null $pesanan
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\User|null $seller
 */
class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'invoice_number',
        'pesanan_id',
        'user_id',
        'seller_id',
        'subtotal',
        'ongkir',
        'diskon_voucher',
        'diskon_ongkir',
        'total_bayar',
        'metode_pembayaran',
        'status',
        'paid_at',
        'receipt_token',
        'catatan',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'ongkir' => 'integer',
        'diskon_voucher' => 'integer',
        'diskon_ongkir' => 'integer',
        'total_bayar' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp '.number_format($this->total_bayar, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp '.number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedOngkirAttribute(): string
    {
        return 'Rp '.number_format($this->ongkir, 0, ',', '.');
    }

    public function getFormattedDiskonAttribute(): string
    {
        $totalDiskon = $this->diskon_voucher + $this->diskon_ongkir;

        return 'Rp '.number_format($totalDiskon, 0, ',', '.');
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['lunas', 'selesai'], true);
    }
}
