<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\User;
use App\Repositories\PesananRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PesananService
{
    protected PesananRepository $pesananRepository;

    protected MidtransService $midtransService;

    public function __construct(PesananRepository $pesananRepository, MidtransService $midtransService)
    {
        $this->pesananRepository = $pesananRepository;
        $this->midtransService = $midtransService;
    }

    /**
     * Create a new order for a posting and obtain a snap token.
     * Supports optional promo voucher discount, COD, and BJPay wallet payments.
     */
    public function createOrder(
        int $buyerId,
        int $postinganId,
        ?string $catatan = null,
        ?string $promoCode = null,
        string $metodePembayaran = 'transfer'
    ): Pesanan {
        $postingan = Postingan::findOrFail($postinganId);
        $buyer = User::findOrFail($buyerId);

        $totalPrice = $postingan->price;
        $ongkir = 10000;
        $diskonOngkir = $metodePembayaran === 'bjpay' ? 5000 : 0;
        [$promoId, $discountAmount] = $this->resolvePromoDiscount($promoCode, $totalPrice);
        $finalPrice = max(0, $totalPrice + $ongkir - $discountAmount - $diskonOngkir);

        [$status, $paymentType] = $this->resolvePayment($buyer, $metodePembayaran, $finalPrice);

        $pesanan = $this->pesananRepository->create([
            'order_number' => 'BJBO-'.date('Ymd').'-'.strtoupper(Str::random(6)),
            'user_id' => $buyerId,
            'postingan_id' => $postingan->id,
            'penjual_id' => $postingan->user_id,
            'total_price' => $totalPrice,
            'ongkir' => $ongkir,
            'diskon_ongkir' => $diskonOngkir,
            'promo_id' => $promoId,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'metode_pembayaran' => $metodePembayaran,
            'catatan' => $catatan,
            'status' => $status,
            'shipping_status' => 'menunggu_konfirmasi',
            'payment_type' => $paymentType,
            'settlement_time' => $status === 'paid' ? now() : null,
        ]);

        $this->handlePostOrder($pesanan, $buyer, $metodePembayaran, $finalPrice);

        return $pesanan;
    }

    /**
     * Resolve promo voucher and discount amount.
     *
     * @return array{0: int|null, 1: int}
     */
    protected function resolvePromoDiscount(?string $promoCode, int $totalPrice): array
    {
        if (! $promoCode) {
            return [null, 0];
        }

        $promoRes = app(PromoService::class)->validateAndCalculateDiscount($promoCode, $totalPrice);
        if ($promoRes['is_valid'] && $promoRes['promo']) {
            return [(int) $promoRes['promo']->id, (int) $promoRes['discount']];
        }

        return [null, 0];
    }

    /**
     * Process payment rules for BJPay, COD, or transfer.
     *
     * @return array{0: string, 1: string}
     */
    protected function resolvePayment(User $buyer, string $metodePembayaran, int $finalPrice): array
    {
        if ($metodePembayaran === 'bjpay') {
            $currentSaldo = (int) ($buyer->saldo_bjpay ?? 0);
            if ($currentSaldo < $finalPrice) {
                throw new \InvalidArgumentException('Saldo BJPay Anda tidak mencukupi (Tersedia: Rp '.number_format($currentSaldo, 0, ',', '.').', Dibutuhkan: Rp '.number_format($finalPrice, 0, ',', '.').'). Silakan top up saldo BJPay terlebih dahulu.');
            }

            $buyer->decrement('saldo_bjpay', $finalPrice);

            return ['paid', 'bjpay'];
        }

        if ($metodePembayaran === 'cod') {
            return ['pending', 'cod'];
        }

        return ['pending', 'qris'];
    }

    /**
     * Handle invoice creation, snap token generation, and payment notifications.
     */
    protected function handlePostOrder(Pesanan $pesanan, User $buyer, string $metodePembayaran, int $finalPrice): void
    {
        app(InvoiceService::class)->generateInvoice($pesanan);

        if ($metodePembayaran === 'transfer') {
            $snapToken = $this->midtransService->getSnapToken($pesanan);
            $pesanan->update(['snap_token' => $snapToken]);
        } elseif ($metodePembayaran === 'bjpay') {
            try {
                app(NotificationService::class)->notifyPaymentReceived(
                    $pesanan->penjual_id,
                    $pesanan->order_number,
                    $finalPrice,
                    $buyer->name
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Failed sending payment notification to seller: '.$e->getMessage());
            }
        }
    }

    /**
     * Get order details.
     */
    public function getOrder(int $id): ?Pesanan
    {
        return $this->pesananRepository->findById($id);
    }

    /**
     * Get all orders related to a user.
     */
    public function getUserOrders(int $userId): Collection
    {
        return $this->pesananRepository->getOrdersByUserId($userId);
    }

    /**
     * Mark an order as paid (via Midtrans callback or QRIS simulation).
     * Triggers real-time notification to seller via Reverb.
     */
    public function markOrderAsPaid(int $orderId, ?string $paymentType = 'qris', ?string $transactionId = null): bool
    {
        $result = $this->pesananRepository->updateStatus($orderId, 'paid', $paymentType, $transactionId);

        if ($result) {
            try {
                $pesanan = $this->pesananRepository->findById($orderId);
                if ($pesanan) {
                    app(InvoiceService::class)->generateInvoice($pesanan);
                    $buyerName = $pesanan->pembeli ? $pesanan->pembeli->name : 'Pembeli';
                    app(NotificationService::class)->notifyPaymentReceived(
                        $pesanan->penjual_id,
                        $pesanan->order_number,
                        $pesanan->getEffectivePrice(),
                        $buyerName
                    );
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Failed sending payment notification to seller: '.$e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Refresh order snap token if pending.
     */
    public function refreshSnapToken(Pesanan $pesanan): ?string
    {
        $snapToken = $this->midtransService->getSnapToken($pesanan);
        if ($snapToken && ! str_starts_with($snapToken, 'SIMULASI-QRIS-')) {
            $this->pesananRepository->updateSnapToken($pesanan->id, $snapToken);

            return $snapToken;
        }

        return null;
    }

    /**
     * Get Midtrans Client Key for frontend.
     */
    public function getMidtransClientKey(): string
    {
        return $this->midtransService->getClientKey();
    }

    /**
     * Check if Midtrans is in production.
     */
    public function isProduction(): bool
    {
        return $this->midtransService->isProduction();
    }
}
