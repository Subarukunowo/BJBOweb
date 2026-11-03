<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\RekberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class RekberController extends Controller
{
    protected RekberService $rekberService;

    public function __construct(RekberService $rekberService)
    {
        $this->rekberService = $rekberService;
    }

    public function index(): View
    {
        $userId = (int) auth()->id();
        $balance = $this->rekberService->getBalance($userId);
        $bankAccounts = $this->rekberService->getUserBankAccounts($userId);
        $withdrawals = $this->rekberService->getUserWithdrawals($userId, 10);

        return view('pages.user.rekber.index', compact('balance', 'bankAccounts', 'withdrawals'));
    }

    public function addBank(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|in:BCA,Mandiri,BRI,BNI,BSI,Bank Riau Kepri Syariah,CIMB Niaga,Permata',
            'account_number' => 'required|string|min:8|max:25',
            'account_name' => 'required|string|min:3|max:100',
        ]);

        $userId = (int) auth()->id();
        $this->rekberService->registerBankAccount($userId, $validated);

        return back()->with('success', 'Rekening bank berhasil didaftarkan dan siap digunakan untuk pencairan.');
    }

    public function requestWithdrawal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:10000',
        ]);

        $userId = (int) auth()->id();

        try {
            $withdrawal = $this->rekberService->requestPencairan($userId, (int) $validated['bank_account_id'], (int) $validated['amount']);

            return back()->with('success', 'Permintaan pencairan dana sebesar Rp'.number_format($withdrawal->amount, 0, ',', '.').' berhasil diajukan. Estimasi proses 2x24 jam kerja.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }
    }
}
