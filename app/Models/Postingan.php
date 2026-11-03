<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null $affinity_score
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Postingan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'tipe_penawaran',
        'category',
        'kondisi',
        'description',
        'image',
        'lokasi',
        'user_id',
        'username',
        'status',
        'is_sold',
        'views_count',
        'is_auto_blocked',
        'block_reason',
    ];

    protected $casts = [
        'is_sold' => 'boolean',
        'views_count' => 'integer',
        'price' => 'integer',
        'is_auto_blocked' => 'boolean',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ulasan(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ulasan::class);
    }

    public function laporan(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Laporan::class);
    }

    public function favorits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Favorit::class);
    }

    public function pesanans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function wishlists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function productViews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserProductView::class);
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->favorits()->where('user_id', $user->id)->exists();
    }

    public function isWishlistedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->wishlists()->where('user_id', $user->id)->exists();
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('images/bjbo.png');
        }
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        if (file_exists(public_path('storage/'.$this->image))) {
            return asset('storage/'.$this->image);
        }
        if (file_exists(public_path('images/'.$this->image))) {
            return asset('images/'.$this->image);
        }

        return asset('storage/'.$this->image);
    }

    public function getRatingScoreAttribute(): ?float
    {
        $avg = $this->ulasan()->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp'.number_format($this->price, 0, ',', '.');
    }
}
