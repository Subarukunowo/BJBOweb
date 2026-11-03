<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $order_number
 * @property int $user_id
 * @property int $postingan_id
 * @property int $penjual_id
 * @property int $total_price
 * @property string|null $catatan
 * @property string $status
 * @property string|null $snap_token
 * @property string|null $payment_type
 * @property string|null $transaction_id
 * @property string|null $pdf_url
 * @property \Carbon\Carbon|null $settlement_time
 * @property \Carbon\Carbon|null $delivered_at
 * @property \Carbon\Carbon|null $penjual_confirmation_at
 * @property string $shipping_status
 * @property string|null $kurir_name
 * @property string|null $nomor_resi
 * @property string|null $alasan_penolakan
 * @property float|null $origin_lat
 * @property float|null $origin_lng
 * @property string|null $origin_address
 * @property float|null $destination_lat
 * @property float|null $destination_lng
 * @property string|null $destination_address
 * @property float|null $current_lat
 * @property float|null $current_lng
 * @property string|null $last_checkpoint
 * @property int|null $promo_id
 * @property int|null $discount_amount
 * @property int|null $final_price
 * @property bool $is_funds_released
 * @property-read \App\Models\User|null $pembeli
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\User|null $penjual
 * @property-read \App\Models\Postingan|null $postingan
 */
class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanans';

    protected $fillable = [
        'order_number',
        'user_id',
        'postingan_id',
        'penjual_id',
        'total_price',
        'catatan',
        'status',
        'snap_token',
        'payment_type',
        'transaction_id',
        'pdf_url',
        'settlement_time',
        'shipping_status',
        'kurir_name',
        'nomor_resi',
        'penjual_confirmation_at',
        'alasan_penolakan',
        'origin_lat',
        'origin_lng',
        'origin_address',
        'destination_lat',
        'destination_lng',
        'destination_address',
        'current_lat',
        'current_lng',
        'last_checkpoint',
        'promo_id',
        'discount_amount',
        'final_price',
        'delivered_at',
        'is_funds_released',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'integer',
            'discount_amount' => 'integer',
            'final_price' => 'integer',
            'settlement_time' => 'datetime',
            'penjual_confirmation_at' => 'datetime',
            'delivered_at' => 'datetime',
            'is_funds_released' => 'boolean',
            'origin_lat' => 'float',
            'origin_lng' => 'float',
            'destination_lat' => 'float',
            'destination_lng' => 'float',
            'current_lat' => 'float',
            'current_lng' => 'float',
        ];
    }

    public function pembeli(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function penjual(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'penjual_id');
    }

    public function postingan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Postingan::class);
    }

    public function promo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function getEffectivePrice(): int
    {
        return $this->final_price ?? ($this->total_price - ($this->discount_amount ?? 0));
    }
}
