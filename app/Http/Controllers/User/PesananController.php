<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Postingan;
use App\Services\PesananService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    protected PesananService $pesananService;

    public function __construct(PesananService $pesananService)
    {
        $this->pesananService = $pesananService;
    }

    /**
     * Show checkout preview page.
     */
    public function checkout(int $postingan_id)
    {
        $postingan = Postingan::with('user')->findOrFail($postingan_id);
        $user = Auth::user();

        if ($postingan->user_id === $user->id) {
            toast('Anda tidak dapat membeli barang dagangan Anda sendiri.', 'warning');

            return redirect()->back();
        }

        return view('pages.user.pesanan.checkout', [
            'postingan' => $postingan,
            'user' => $user,
            'saldoBjpay' => $user->saldo_bjpay ?? 0,
        ]);
    }

    /**
     * Process checkout and create order with Snap token, COD, or BJPay wallet.
     */
    public function store(Request $request, int $postingan_id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
            'promo_code' => 'nullable|string|max:50',
            'metode_pembayaran' => 'nullable|string|in:transfer,cod,bjpay',
        ]);

        $userId = Auth::id();
        $catatan = $request->input('catatan');
        $promoCode = $request->input('promo_code');
        $metodePembayaran = $request->input('metode_pembayaran', 'transfer');

        try {
            $pesanan = $this->pesananService->createOrder($userId, $postingan_id, $catatan, $promoCode, $metodePembayaran);

            if ($metodePembayaran === 'bjpay') {
                toast('Pembayaran menggunakan saldo BJPay berhasil! Pesanan langsung diproses penjual.', 'success');
            } elseif ($metodePembayaran === 'cod') {
                toast('Pesanan COD (Bayar di Tempat) berhasil dibuat. Siapkan uang pas saat kurir tiba.', 'success');
            } else {
                toast('Pesanan berhasil dibuat. Silakan lakukan pembayaran.', 'success');
            }

            return redirect()->route('user.pesanan.detail', $pesanan->id);
        } catch (\InvalidArgumentException $e) {
            toast($e->getMessage(), 'error');

            return redirect()->back()->withInput();
        }
    }

    /**
     * Menampilkan struk digital dan invoice pesanan.
     */
    public function invoice(int $id)
    {
        $userId = Auth::id();
        $pesanan = $this->pesananService->getOrder($id);
        if (! $pesanan) {
            abort(404, 'Pesanan tidak ditemukan.');
        }

        if ($pesanan->user_id !== $userId && $pesanan->penjual_id !== $userId) {
            abort(403, 'Anda tidak berhak melihat struk pesanan ini.');
        }

        $invoice = app(\App\Services\InvoiceService::class)->generateInvoice($pesanan);
        $invoice = app(\App\Services\InvoiceService::class)->getInvoiceByNumber($invoice->invoice_number);

        return view('pages.user.pesanan.invoice', [
            'pesanan' => $pesanan,
            'invoice' => $invoice,
        ]);
    }

    /**
     * Menampilkan struk digital berdasarkan token unik.
     */
    public function invoiceByToken(string $token)
    {
        $invoice = app(\App\Services\InvoiceService::class)->getInvoiceByToken($token);
        if (! $invoice) {
            abort(404, 'Struk digital tidak ditemukan atau token tidak valid.');
        }

        $pesanan = $invoice->pesanan;

        return view('pages.user.pesanan.invoice', [
            'pesanan' => $pesanan,
            'invoice' => $invoice,
        ]);
    }

    /**
     * Show order detail and payment interface with Midtrans Snap Popup.
     */
    public function detail(int $id)
    {
        $pesanan = $this->pesananService->getOrder($id);
        if (! $pesanan) {
            abort(404, 'Pesanan tidak ditemukan.');
        }

        $userId = Auth::id();
        if ($pesanan->user_id !== $userId && $pesanan->penjual_id !== $userId) {
            abort(403, 'Anda tidak berhak melihat pesanan ini.');
        }

        if ($pesanan->status === 'pending' && (! $pesanan->snap_token || str_starts_with($pesanan->snap_token, 'SIMULASI-QRIS-'))) {
            $freshToken = $this->pesananService->refreshSnapToken($pesanan);
            if ($freshToken) {
                $pesanan->snap_token = $freshToken;
            }
        }

        $clientKey = $this->pesananService->getMidtransClientKey();
        $isProduction = $this->pesananService->isProduction();

        return view('pages.user.pesanan.detail', compact('pesanan', 'clientKey', 'isProduction'));
    }

    /**
     * List all orders of the user.
     */
    public function index()
    {
        $userId = Auth::id();
        $pesanans = $this->pesananService->getUserOrders($userId);

        return view('pages.user.pesanan.index', compact('pesanans'));
    }

    /**
     * Finish payment callback from Midtrans Snap.
     */
    public function finish(Request $request, int $id)
    {
        $paymentType = $request->input('payment_type', 'qris');
        $transactionId = $request->input('transaction_id');

        $this->pesananService->markOrderAsPaid($id, $paymentType, $transactionId);

        toast('Pembayaran berhasil dikonfirmasi! Terima kasih.', 'success');

        return redirect()->route('user.pesanan.detail', $id);
    }

    /**
     * Free sandbox QRIS simulator endpoint for zero-cost instant testing.
     */
    public function simulateQris(int $id)
    {
        $this->pesananService->markOrderAsPaid($id, 'qris_gratis_simulasi', 'SIM-'.time());

        toast('Pembayaran simulasi QRIS berhasil diverifikasi tanpa biaya!', 'success');

        return redirect()->route('user.pesanan.detail', $id);
    }
}
