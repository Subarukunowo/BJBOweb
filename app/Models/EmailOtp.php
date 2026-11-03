<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailOtp extends Model
{
    use HasFactory;

    protected $table = 'email_otps';

    protected $fillable = [
        'email',
        'otp',
        'registration_data',
        'last_sent_at',
        'expires_at',
    ];

    protected $casts = [
        'last_sent_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    public function canResend(): bool
    {
        if (! $this->last_sent_at) {
            return true;
        }

        return now()->diffInSeconds($this->last_sent_at) >= 60;
    }

    public function remainingCooldownSeconds(): int
    {
        if (! $this->last_sent_at) {
            return 0;
        }

        $elapsed = now()->diffInSeconds($this->last_sent_at);

        return max(0, 60 - $elapsed);
    }
}
