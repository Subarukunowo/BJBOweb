<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\PesananService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Handle incoming payment notifications from Midtrans.
     */
    public function handle(Request $request, PesananService $pesananService): JsonResponse
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received: ', $payload);

        $orderId = $payload['order_id'] ?? null;
        $serverKey = (string) config('services.midtrans.server_key');

        if (! $this->isValidSignature($payload, $serverKey)) {
            Log::warning('Midtrans Invalid Signature for order: '.$orderId);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pesanan = Pesanan::where('order_number', $orderId)->first();
        if (! $pesanan) {
            Log::warning('Pesanan not found for Midtrans order_id: '.$orderId);

            return response()->json(['message' => 'Order not found'], 404);
        }

        $this->applyTransactionStatus($pesanan, $payload, $pesananService);

        return response()->json(['status' => 'success']);
    }

    /**
     * Validate signature key if present in Midtrans notification payload.
     */
    protected function isValidSignature(array $payload, string $serverKey): bool
    {
        $signatureKey = $payload['signature_key'] ?? null;
        $orderId = $payload['order_id'] ?? null;
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');

        if (! ($signatureKey && $serverKey && $orderId && $statusCode && $grossAmount)) {
            return true;
        }

        return $signatureKey === hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);
    }

    /**
     * Apply transaction status transition to order.
     */
    protected function applyTransactionStatus(Pesanan $pesanan, array $payload, PesananService $pesananService): void
    {
        $status = $payload['transaction_status'] ?? null;
        $fraud = $payload['fraud_status'] ?? null;
        $type = $payload['payment_type'] ?? 'midtrans';
        $trxId = $payload['transaction_id'] ?? null;

        if ($status === 'settlement' || ($status === 'capture' && $fraud === 'accept')) {
            $pesananService->markOrderAsPaid($pesanan->id, $type, $trxId);
        } elseif (in_array($status, ['cancel', 'deny', 'expire'], true)) {
            $pesanan->update(['status' => 'cancelled']);
        }
    }
}
