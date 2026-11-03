<?php

namespace App\Services;

use App\Models\Pesanan;
use Exception;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        $midtransPath = base_path('vendor/midtrans/midtrans-php/Midtrans.php');
        if (file_exists($midtransPath)) {
            require_once $midtransPath;
        }

        $this->applyConfig();
    }

    /**
     * Apply current Midtrans configurations.
     */
    protected function applyConfig(): void
    {
        if (class_exists(\Midtrans\Config::class)) {
            \Midtrans\Config::$serverKey = $this->getServerKey();
            \Midtrans\Config::$clientKey = $this->getClientKey();
            \Midtrans\Config::$isProduction = $this->isProduction();
            \Midtrans\Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', true);
            \Midtrans\Config::$is3ds = (bool) config('services.midtrans.is_3ds', true);
        }
    }

    /**
     * Check if running in production mode.
     * Mengikuti konfigurasi MIDTRANS_IS_PRODUCTION dari .env.
     */
    public function isProduction(): bool
    {
        return (bool) config('services.midtrans.is_production', false);
    }

    /**
     * Get Server Key from config.
     */
    public function getServerKey(): string
    {
        return trim((string) config('services.midtrans.server_key', ''));
    }

    /**
     * Get Client Key for frontend Snap JS.
     */
    public function getClientKey(): string
    {
        return trim((string) config('services.midtrans.client_key', ''));
    }

    /**
     * Generate Midtrans Snap token with QRIS, VA, and e-wallets enabled.
     */
    public function getSnapToken(Pesanan $pesanan): string
    {
        $this->applyConfig();

        $serverKey = $this->getServerKey();
        $isDummyKey = empty($serverKey) || str_contains($serverKey, 'sandbox-test') || str_contains($serverKey, 'demo');

        if ($isDummyKey || ! class_exists(\Midtrans\Snap::class)) {
            return 'SIMULASI-QRIS-'.$pesanan->order_number;
        }

        $grossAmount = (int) $pesanan->getEffectivePrice();
        if ($grossAmount <= 0) {
            $grossAmount = (int) $pesanan->total_price;
        }

        $params = [
            'transaction_details' => [
                'order_id' => $pesanan->order_number,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $pesanan->pembeli->name ?? 'Pembeli BJBO',
                'email' => $pesanan->pembeli->email ?? 'buyer@bjbo.local',
                'phone' => $pesanan->pembeli->nomor_hp ?? '081234567890',
            ],
            'item_details' => [
                [
                    'id' => (string) ($pesanan->postingan->id ?? $pesanan->id),
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => substr($pesanan->postingan->name ?? 'Barang BJBO', 0, 50),
                ],
            ],
        ];

        try {
            return \Midtrans\Snap::getSnapToken($params);
        } catch (Exception $e) {
            Log::warning('Midtrans Snap error for order '.$pesanan->order_number.': '.$e->getMessage());
            // Fallback ke token simulasi jika kredensial belum aktif / akun sedang aktivasi
            return 'SIMULASI-QRIS-'.$pesanan->order_number;
        }
    }
}
