<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $postingan_id
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Postingan $postingan
 * @property-read int $subtotal
 * @property-read string $formatted_subtotal
 */
class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'postingan_id',
        'quantity',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'postingan_id' => 'integer',
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function postingan(): BelongsTo
    {
        return $this->belongsTo(Postingan::class);
    }

    public function getSubtotalAttribute(): int
    {
        return (int) (($this->postingan->price ?? 0) * $this->quantity);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp '.number_format($this->subtotal, 0, ',', '.');
    }
}
