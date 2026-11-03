<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        ];
    }

    public function postingan(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Postingan::class);
    }

    public function sentMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    public function bankAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function pencairanDanas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PencairanDana::class);
    }

    public function store(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Store::class);
    }

    public function hasActiveStore(): bool
    {
        return $this->store !== null && $this->store->status === 'aktif';
    }

    public function hasStore(): bool
    {
        return $this->store !== null;
    }

    public function wishlists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function searchLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserSearchLog::class);
    }

    public function productViews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserProductView::class);
    }

    public function laporan(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Laporan::class);
    }

    public function ulasan(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ulasan::class);
    }

    public function favorits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Favorit::class);
    }

    public function favoritePostings(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Postingan::class, 'favorits');
    }

    public function pesanans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pesanan::class, 'user_id');
    }

    public function penjualPesanans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pesanan::class, 'penjual_id');
    }

    /**
     * Get dynamic avatar URL from storage, OAuth, or avatar generator.
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if (! empty($this->profile_picture)) {
            if (str_starts_with($this->profile_picture, 'http://') || str_starts_with($this->profile_picture, 'https://')) {
                return $this->profile_picture;
            }
            if (file_exists(public_path('storage/'.$this->profile_picture))) {
                return asset('storage/'.$this->profile_picture);
            }

            return asset('storage/'.$this->profile_picture);
        }

        $formattedName = urlencode($this->name ?: 'Warga BJBO');

        return 'https://ui-avatars.com/api/?name='.$formattedName.'&background=1E3A8A&color=ffffff&bold=true&size=200';
    }
}
