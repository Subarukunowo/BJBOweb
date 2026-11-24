<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promos';

    protected $fillable = [
        'title',
        'slug',
        'type',
        'code',
        'discount_percentage',
        'discount_amount',
        'min_purchase',
        'banner_image',
        'description',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'integer',
        'discount_amount' => 'integer',
        'min_purchase' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function isValidNow(): bool
    {
        return $this->is_active
            && ($this->start_date === null || now()->gte($this->start_date))
            && ($this->end_date === null || now()->lte($this->end_date));
    }

    /**
     * @SuppressWarnings("php:S1142")
     */
    public function calculateDiscount(int $totalAmount): int
    {
        if ($totalAmount < $this->min_purchase) {
            return 0;
        }

        return match ($this->type) {
            'diskon_persen' => (int) round(($totalAmount * max(0, (float) $this->discount_percentage)) / 100),
            'diskon_nominal' => (int) min($totalAmount, max(0, (int) $this->discount_amount)),
            default => 0,
        };
    }
}
