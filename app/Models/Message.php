<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'postingan_id',
        'message',
        'is_reported',
        'is_read',
    ];

    protected $casts = [
        'is_reported' => 'boolean',
        'is_read' => 'boolean',
    ];

    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function postingan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Postingan::class, 'postingan_id');
    }

    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ChatLaporan::class, 'message_id');
    }

    public function getDecryptedMessageAttribute(): string
    {
        try {
            return Crypt::decryptString($this->message);
        } catch (\Exception $e) {
            return $this->message;
        }
    }
}
