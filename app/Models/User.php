<?php

namespace App\Models;

use App\Models\Traits\HasApiTokens;
use App\Models\Traits\UserRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $avatar
 * @property int $saldo_rekber
 * @property int $saldo_bjpay
 * @property bool $is_blocked
 * @property-read string $profile_picture_url
 * @property-read \App\Models\Store|null $store
 *
 * @SuppressWarnings("php:S1448")
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, UserRelations {
        UserRelations::notifications insteadof Notifiable;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'facebook_id',
        'instagram_id',
        'profile_picture',
        'is_blocked',
        'saldo_rekber',
        'saldo_bjpay',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_blocked' => 'boolean',
            'saldo_rekber' => 'integer',
            'saldo_bjpay' => 'integer',
        ];
    }

    public function getFormattedSaldoBjpayAttribute(): string
    {
        return 'Rp '.number_format($this->saldo_bjpay ?? 0, 0, ',', '.');
    }

    public function hasActiveStore(): bool
    {
        return $this->store !== null && $this->store->status === 'aktif';
    }

    public function hasStore(): bool
    {
        return $this->store !== null;
    }

    /**
     * Get dynamic avatar URL from storage, OAuth, or avatar generator.
     *
     * @SuppressWarnings("php:S1142")
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if (! empty($this->profile_picture)) {
            if (str_starts_with($this->profile_picture, 'http://') || str_starts_with($this->profile_picture, 'https://')) {
                return $this->profile_picture;
            }

            return asset('storage/'.$this->profile_picture);
        }

        $formattedName = urlencode($this->name ?: 'Warga BJBO');

        return 'https://ui-avatars.com/api/?name='.$formattedName.'&background=1E3A8A&color=ffffff&bold=true&size=200';
    }
}
