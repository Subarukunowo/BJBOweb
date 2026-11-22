<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class BjPayController extends Controller
{
    /**
     * Tampilkan halaman dashboard dompet digital BJPay.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Riwayat transaksi menggunakan BJPay (dioptimasi tanpa SELECT * dan tanpa N+1)
        $invoices = Invoice::with(['pesanan:id,order_number', 'seller:id,name'])
            ->where('user_id', $user->id)
            ->where('metode_pembayaran', 'bjpay')
            ->latest('id')
            ->take(10)
            ->get();

        return view('pages.user.bjpay.index', [
            'user' => $user,
            'invoices' => $invoices,
            'saldo' => $user->saldo_bjpay ?? 0,
        ]);
    }

    /**
     * Proses pengisian saldo (Top Up) BJPay secara instan.
     */
    public function topup(Request $request): RedirectResponse
    {
        $request->validate([
            'nominal' => 'required|integer|min:10000|max:10000000',
        ], [
            'nominal.min' => 'Nominal pengisian saldo BJPay minimal Rp 10.000.',
            'nominal.max' => 'Nominal pengisian saldo BJPay maksimal Rp 10.000.000 per transaksi.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $nominal = (int) $request->input('nominal');

        $user->increment('saldo_bjpay', $nominal);

        // Invalidate cache user jika ada
        Cache::forget("user_saldo_bjpay_{$user->id}");

        app(NotificationService::class)->sendNotification(
            $user->id,
            'Top Up BJPay Berhasil',
            'Selamat! Pengisian saldo BJPay sebesar Rp '.number_format($nominal, 0, ',', '.').' telah berhasil ditambahkan ke akun Anda.',
            'bjpay',
            ['nominal' => $nominal, 'saldo_akhir' => $user->saldo_bjpay]
        );

        toast('Top Up BJPay sebesar Rp '.number_format($nominal, 0, ',', '.').' berhasil!', 'success');

        return redirect()->route('user.bjpay.index');
    }
}
