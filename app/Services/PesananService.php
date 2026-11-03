<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Postingan;
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
     * Supports optional promo voucher discount.
     */
    public function createOrder(int $buyerId, int $postinganId, ?string $catatan = null, ?string $promoCode = null): Pesanan
    {
        $postingan = Postingan::findOrFail($postinganId);
        $orderNumber = 'BJBO-'.date('Ymd').'-'.strtoupper(Str::random(6));

        $totalPrice = $postingan->price;
        $discountAmount = 0;
        $promoId = null;

        if ($promoCode) {
            $promoRes = app(PromoService::class)->validateAndCalculateDiscount($promoCode, $totalPrice);
            if ($promoRes['is_valid'] && $promoRes['promo']) {
                $promoId = $promoRes['promo']->id;
                $discountAmount = $promoRes['discount'];
            }
        }

        $finalPrice = max(0, $totalPrice - $discountAmount);

        $pesanan = $this->pesananRepository->create([
            'order_number' => $orderNumber,
            'user_id' => $buyerId,
            'postingan_id' => $postingan->id,
            'penjual_id' => $postingan->user_id,
            'total_price' => $totalPrice,
            'promo_id' => $promoId,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'catatan' => $catatan,
            'status' => 'pending',
            'shipping_status' => 'menunggu_konfirmasi',
            'payment_type' => 'qris',
        ]);

        $snapToken = $this->midtransService->getSnapToken($pesanan);
        $pesanan->update(['snap_token' => $snapToken]);

        return $pesanan;
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
