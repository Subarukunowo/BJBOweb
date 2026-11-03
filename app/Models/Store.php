<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';

    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'store_type',
        'phone_number',
        'email',
        'ktp_number',
        'ktp_image',
        'nib_number',
        'npwp_company',
        'npwp_image',
        'haki_certificate_number',
        'haki_certificate_file',
        'description',
        'address',
        'logo',
        'banner',
        'status',
        'rejected_reason',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOfficial(): bool
    {
        return $this->store_type === 'official';
    }

    public function isVerified(): bool
    {
        return $this->status === 'aktif';
    }
}
