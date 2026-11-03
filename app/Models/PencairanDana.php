<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $bank_account_id
 * @property int $amount
 * @property string $status
 * @property string $reference_number
 * @property \Carbon\Carbon|null $estimated_completion_at
 * @property string|null $catatan_admin
 * @property \App\Models\BankAccount|null $bankAccount
 * @property \App\Models\User|null $user
 */
class PencairanDana extends Model
{
    use HasFactory;

    protected $table = 'pencairan_danas';

    protected $fillable = [
        'user_id',
        'bank_account_id',
        'amount',
        'status',
        'reference_number',
        'estimated_completion_at',
        'catatan_admin',
    ];

    protected $casts = [
        'amount' => 'integer',
        'estimated_completion_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
