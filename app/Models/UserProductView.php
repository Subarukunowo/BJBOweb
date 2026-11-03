<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProductView extends Model
{
    use HasFactory;

    protected $table = 'user_product_views';

    protected $fillable = [
        'user_id',
        'postingan_id',
        'view_count',
        'last_viewed_at',
    ];

    protected $casts = [
        'view_count' => 'integer',
        'last_viewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function postingan(): BelongsTo
    {
        return $this->belongsTo(Postingan::class);
    }
}
