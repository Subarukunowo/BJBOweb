<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\BankAccount;
use App\Models\Invoice;
use App\Models\User;
use App\Services\RekberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class WalletController extends BaseApiController
{
    public function __construct(
        protected RekberService $rekberService
    ) {}

    public function getBjpay(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $invoices = Invoice::with(['pesanan:id,order_number', 'seller:id,name'])
            ->where('user_id', $user->id)
            ->where('metode_pembayaran', 'bjpay')
            ->latest('id')
            ->take(20)
            ->get();

        return $this->successResponse([
            'saldo' => $user->saldo_bjpay ?? 0,
            'formatted_saldo' => $user->formatted_saldo_bjpay,
            'history' => $invoices,
        ], 'Data dompet BJPay berhasil diambil.');
    }

    public function topupBjpay(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'nominal' => 'required|integer|min:10000|max:10000000',
        ]);

        $nominal = (int) $validated['nominal'];
        $user->increment('saldo_bjpay', $nominal);
        Cache::forget("user_saldo_bjpay_{$user->id}");

        return $this->successResponse([
            'saldo' => $user->fresh()->saldo_bjpay,
            'formatted_saldo' => $user->fresh()->formatted_saldo_bjpay,
            'added' => $nominal,
        ], 'Top-up saldo BJPay berhasil.');
    }

    public function getRekber(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $balance = $this->rekberService->getBalance($user->id);
        $bankAccounts = $this->rekberService->getUserBankAccounts($user->id);
        $withdrawals = $this->rekberService->getUserWithdrawals($user->id, 20);

        return $this->successResponse([
            'saldo_rekber' => $balance,
            'bank_accounts' => $bankAccounts,
            'withdrawals' => $withdrawals,
        ], 'Data dompet Rekber berhasil diambil.');
    }

    public function withdrawRekber(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'amount' => 'required|integer|min:10000',
            'bank_account_id' => 'nullable|integer|exists:bank_accounts,id',
        ]);

        $bankAccountId = $validated['bank_account_id'] ?? BankAccount::where('user_id', $user->id)->value('id');
        if (! $bankAccountId) {
            return $this->errorResponse('Silakan daftarkan rekening bank terlebih dahulu.', 422);
        }

        try {
            $pencairan = $this->rekberService->requestPencairan(
                $user->id,
                (int) $bankAccountId,
                (int) $validated['amount']
            );

            return $this->successResponse(
                $pencairan,
                'Permintaan penarikan dana berhasil diajukan. Estimasi pencairan 2x24 jam kerja.',
                201
            );
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function addBankAccount(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'bank_name' => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        $account = $this->rekberService->registerBankAccount($user->id, $validated);

        return $this->successResponse($account, 'Rekening bank berhasil disimpan.', 201);
    }

    public function deleteBankAccount(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $account = BankAccount::where('id', $id)->where('user_id', $user->id)->first();

        if (! $account) {
            return $this->errorResponse('Rekening bank tidak ditemukan.', 404);
        }

        $account->delete();

        return $this->successResponse(null, 'Rekening bank berhasil dihapus.');
    }
}
