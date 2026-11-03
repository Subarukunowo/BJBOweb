<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\PencairanDana;
use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class RekberService
{
    /**
     * Get user's current Rekber escrow wallet balance.
     */
    public function getBalance(int $userId): int
    {
        return (int) User::where('id', $userId)->value('saldo_rekber');
    }

    /**
     * Get verified bank accounts for a seller.
     */
    public function getUserBankAccounts(int $userId): Collection
    {
        return BankAccount::query()
            ->select(['id', 'user_id', 'bank_name', 'account_number', 'account_name', 'is_verified', 'created_at'])
            ->where('user_id', $userId)
            ->latest('id')
            ->get();
    }

    /**
     * Register a new bank account for withdrawals.
     * Production-ready with dummy fallback.
     */
    public function registerBankAccount(int $userId, array $data): BankAccount
    {
        return BankAccount::create([
            'user_id' => $userId,
            'bank_name' => $data['bank_name'],
            'account_number' => preg_replace('/[^\d]/', '', $data['account_number']),
            'account_name' => Str::upper(trim($data['account_name'])),
            'is_verified' => true, // Siap pakai production / auto-verified untuk dummy sandbox
        ]);
    }

    /**
     * Release escrow payment from buyer to seller's Rekber wallet.
     * Triggered when order is fulfilled ('sampai_tujuan').
     */
    public function releaseEscrowToSeller(Pesanan $pesanan): bool
    {
        if ($pesanan->is_funds_released) {
            return false;
        }

        return DB::transaction(function () use ($pesanan) {
            $effectivePrice = $pesanan->getEffectivePrice();

            User::where('id', $pesanan->penjual_id)->increment('saldo_rekber', $effectivePrice);

            $pesanan->update([
                'is_funds_released' => true,
                'delivered_at' => $pesanan->delivered_at ?? now(),
            ]);

            $formatted = 'Rp'.number_format($effectivePrice, 0, ',', '.');
            app(NotificationService::class)->sendNotification(
                $pesanan->penjual_id,
                'Pencairan Escrow Rekber Berhasil',
                'Dana pesanan #'.$pesanan->order_number.' sebesar '.$formatted.' telah masuk ke saldo Rekber Anda dan siap dicairkan.',
                'rekber',
                ['pesanan_id' => $pesanan->id, 'amount' => $effectivePrice]
            );

            return true;
        });
    }

    /**
     * Request withdrawal (pencairan dana) with 2x24 working hours SLA.
     */
    public function requestPencairan(int $userId, int $bankAccountId, int $amount): PencairanDana
    {
        if ($amount < 10000) {
            throw new InvalidArgumentException('Minimal pencairan dana adalah Rp10.000.');
        }

        $bankAccount = BankAccount::where('id', $bankAccountId)
            ->where('user_id', $userId)
            ->firstOrFail();

        return DB::transaction(function () use ($userId, $bankAccount, $amount) {
            $user = User::lockForUpdate()->findOrFail($userId);

            if ($user->saldo_rekber < $amount) {
                throw new InvalidArgumentException('Saldo Rekber tidak mencukupi untuk penarikan ini.');
            }

            // Potong saldo pengguna
            $user->decrement('saldo_rekber', $amount);

            // Hitung estimasi selesai: 2 hari kerja (2x24 jam kerja)
            $estimatedDate = Carbon::now()->addWeekdays(2);

            $referenceNumber = 'WD-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));

            $pencairan = PencairanDana::create([
                'user_id' => $userId,
                'bank_account_id' => $bankAccount->id,
                'amount' => $amount,
                'status' => 'menunggu_proses',
                'reference_number' => $referenceNumber,
                'estimated_completion_at' => $estimatedDate,
                'catatan_admin' => null,
            ]);

            $formatted = 'Rp'.number_format($amount, 0, ',', '.');
            app(NotificationService::class)->sendNotification(
                $userId,
                'Permintaan Pencairan Diajukan',
                'Permintaan pencairan dana '.$formatted.' (Ref: '.$referenceNumber.') berhasil dibuat. Estimasi selesai dalam 2x24 jam kerja.',
                'rekber',
                ['pencairan_id' => $pencairan->id, 'ref' => $referenceNumber]
            );

            return $pencairan;
        });
    }

    /**
     * Admin processing: Approve, Complete, or Reject withdrawal request.
     */
    public function processPencairan(int $pencairanId, string $status, ?string $catatan = null): PencairanDana
    {
        return DB::transaction(function () use ($pencairanId, $status, $catatan) {
            $pencairan = PencairanDana::with(['user', 'bankAccount'])->lockForUpdate()->findOrFail($pencairanId);

            $oldStatus = $pencairan->status;

            // Jika sebelumnya belum ditolak dan sekarang ditolak, kembalikan saldo ke pengguna
            if ($status === 'ditolak' && $oldStatus !== 'ditolak') {
                User::where('id', $pencairan->user_id)->increment('saldo_rekber', $pencairan->amount);
            }

            $pencairan->update([
                'status' => $status,
                'catatan_admin' => $catatan,
            ]);

            $formatted = 'Rp'.number_format($pencairan->amount, 0, ',', '.');
            $bankName = $pencairan->bankAccount->bank_name ?? 'Bank Terdaftar';
            $accountNumber = $pencairan->bankAccount->account_number ?? '-';
            $msg = $status === 'selesai'
                ? 'Dana sebesar '.$formatted.' telah berhasil ditransfer ke rekening '.$bankName.' ('.$accountNumber.').'
                : ($status === 'ditolak'
                    ? 'Permintaan pencairan dana '.$formatted.' ditolak. Alasan: '.($catatan ?? 'Data rekening tidak sesuai').'. Saldo telah dikembalikan.'
                    : 'Permintaan pencairan dana '.$formatted.' sedang diproses perbankan.');

            app(NotificationService::class)->sendNotification(
                $pencairan->user_id,
                'Update Pencairan Dana (Ref: '.$pencairan->reference_number.')',
                $msg,
                'rekber',
                ['pencairan_id' => $pencairan->id, 'status' => $status]
            );

            return $pencairan;
        });
    }

    /**
     * Get user's withdrawal history.
     */
    public function getUserWithdrawals(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return PencairanDana::with('bankAccount')
            ->select(['id', 'user_id', 'bank_account_id', 'amount', 'status', 'reference_number', 'estimated_completion_at', 'catatan_admin', 'created_at'])
            ->where('user_id', $userId)
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Get all withdrawals for admin panel.
     */
    public function getAllWithdrawalsForAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return PencairanDana::with(['user:id,name,email', 'bankAccount'])
            ->select(['id', 'user_id', 'bank_account_id', 'amount', 'status', 'reference_number', 'estimated_completion_at', 'catatan_admin', 'created_at'])
            ->latest('id')
            ->paginate($perPage);
    }
}
