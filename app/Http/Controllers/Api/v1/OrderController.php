<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\PesananLifecycleService;
use App\Services\PesananService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class OrderController extends BaseApiController
{
    public function __construct(
        protected PesananService $pesananService,
        protected PesananLifecycleService $lifecycleService,
        protected InvoiceService $invoiceService
    ) {}

    public function couriers(): JsonResponse
    {
        return $this->successResponse($this->pesananService->getCouriers(), 'Daftar opsi pengiriman berhasil dimuat.');
    }

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $role = $request->query('role', 'buyer');

        $orders = match ($role) {
            'seller' => $this->pesananService->getUserSales($user->id),
            default => $this->pesananService->getUserOrders($user->id),
        };

        return $this->successResponse($orders, 'Daftar pesanan berhasil dimuat.');
    }

    public function show(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $pesanan = Pesanan::with([
            'postingan',
            'pembeli:id,name,email,phone,profile_picture',
            'penjual:id,name,email,phone,profile_picture',
            'invoice',
        ])->find($id);

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan.', 404);
        }

        if ($pesanan->user_id !== $user->id && $pesanan->penjual_id !== $user->id) {
            return $this->errorResponse('Anda tidak berhak mengakses pesanan ini.', 403);
        }

        return $this->successResponse($pesanan, 'Detail pesanan berhasil diambil.');
    }

    public function checkout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'postingan_id' => 'required|integer|exists:postingans,id',
            'catatan' => 'nullable|string|max:500',
            'promo_code' => 'nullable|string|max:50',
            'metode_pembayaran' => 'nullable|string|in:transfer,cod,bjpay',
            'kurir_code' => 'nullable|string|in:sedagho,jnt,jne,pickup',
        ]);

        $postingan = Postingan::find($validated['postingan_id']);

        if (! $postingan || $postingan->status !== 'disetujui' || $postingan->is_sold) {
            return $this->errorResponse('Barang tidak tersedia atau sudah terjual.', 422);
        }

        if ($postingan->user_id === $user->id) {
            return $this->errorResponse('Anda tidak dapat membeli barang dagangan Anda sendiri.', 422);
        }

        $metodePembayaran = $validated['metode_pembayaran'] ?? 'transfer';
        $kurirCode = $validated['kurir_code'] ?? 'sedagho';

        try {
            $pesanan = $this->pesananService->createOrder(
                $user->id,
                $postingan->id,
                $validated['catatan'] ?? null,
                $validated['promo_code'] ?? null,
                $metodePembayaran,
                $kurirCode
            );

            return $this->successResponse(
                $pesanan->load(['postingan', 'invoice']),
                'Pesanan berhasil dibuat.',
                201
            );
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function tracking(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $pesanan = Pesanan::find($id);

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan.', 404);
        }

        if ($pesanan->user_id !== $user->id && $pesanan->penjual_id !== $user->id) {
            return $this->errorResponse('Anda tidak berhak melihat pelacakan pesanan ini.', 403);
        }

        $trackingData = $this->lifecycleService->getTrackingData($pesanan);

        return $this->successResponse($trackingData, 'Data pelacakan pengiriman berhasil dimuat.');
    }

    public function confirm(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $pesanan = Pesanan::find($id);

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'action' => 'required|in:accept,reject',
            'alasan_penolakan' => 'nullable|string|max:255',
        ]);

        try {
            $isAccept = $validated['action'] === 'accept';
            $updated = $this->lifecycleService->confirmOrder(
                $pesanan,
                $user->id,
                $isAccept,
                $validated['alasan_penolakan'] ?? null
            );

            $msg = $isAccept
                ? 'Pesanan berhasil dikonfirmasi dan sedang diproses.'
                : 'Pesanan telah ditolak oleh penjual.';

            return $this->successResponse($updated, $msg);
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function ship(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $pesanan = Pesanan::find($id);

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'kurir_name' => 'required|string|max:100',
            'nomor_resi' => 'required|string|max:100',
        ]);

        try {
            $updated = $this->lifecycleService->shipOrder(
                $pesanan,
                $user->id,
                $validated['kurir_name'],
                $validated['nomor_resi']
            );

            return $this->successResponse($updated, 'Pesanan berhasil diubah ke status pengiriman.');
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function deliver(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $pesanan = Pesanan::find($id);

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan.', 404);
        }

        if ($pesanan->user_id !== $user->id && $pesanan->penjual_id !== $user->id) {
            return $this->errorResponse('Anda tidak berhak menyelesaikan pesanan ini.', 403);
        }

        try {
            $updated = $this->lifecycleService->markAsDelivered($pesanan, $user->id);

            return $this->successResponse($updated, 'Pesanan telah diterima dan dana diteruskan ke saldo penjual.');
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function pay(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $pesanan = Pesanan::find($id);

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan.', 404);
        }

        if ($pesanan->user_id !== $user->id) {
            return $this->errorResponse('Anda tidak berhak membayar pesanan ini.', 403);
        }

        $validated = $request->validate([
            'payment_type' => 'nullable|string|max:50',
            'transaction_id' => 'nullable|string|max:120',
        ]);

        $paymentType = $validated['payment_type'] ?? ($pesanan->metode_pembayaran ?: 'midtrans_inapp');
        $transactionId = $validated['transaction_id'] ?? ('MID-INAPP-'.$pesanan->order_number);

        $this->pesananService->markOrderAsPaid($pesanan->id, $paymentType, $transactionId);

        $pesanan->refresh();
        if ($pesanan->shipping_status === 'menunggu_konfirmasi') {
            $pesanan->update([
                'shipping_status' => 'diproses',
                'last_checkpoint' => 'Pembayaran terverifikasi via '.$paymentType.'. Sedang disiapkan untuk '.($pesanan->kurir_name ?? 'Kurir Lokal').'.',
            ]);
        }

        return $this->successResponse($pesanan->fresh(['postingan', 'invoice']), 'Pembayaran berhasil diselesaikan.');
    }

    public function invoice(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $pesanan = Pesanan::with('postingan:id,name,image,price,lokasi')->find($id);

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan.', 404);
        }

        if ($pesanan->user_id !== $user->id && $pesanan->penjual_id !== $user->id) {
            return $this->errorResponse('Anda tidak berhak mengakses faktur pesanan ini.', 403);
        }

        $invoice = $this->invoiceService->generateInvoice($pesanan);

        return $this->successResponse([
            'invoice_number' => $invoice->invoice_number,
            'verification_token' => $invoice->receipt_token,
            'order_id' => $pesanan->id,
            'order_number' => $pesanan->order_number,
            'total_amount' => (int) $invoice->total_bayar,
            'discount_amount' => (int) ($invoice->diskon_voucher + $invoice->diskon_ongkir),
            'shipping_fee' => (int) $invoice->ongkir,
            'payment_method' => strtoupper($invoice->metode_pembayaran ?? 'transfer'),
            'kurir_name' => $pesanan->kurir_name ?? 'Kurir Lokal Sedagho Bengkalis',
            'nomor_resi' => $pesanan->nomor_resi ?? 'BKS-'.$pesanan->id,
            'status' => $pesanan->shipping_status ?: $invoice->status,
            'created_at' => $invoice->created_at ? $invoice->created_at->format('d M Y, H:i WIB') : now()->format('d M Y, H:i WIB'),
        ], 'Data faktur dan struk resmi berhasil dimuat.');
    }
}
