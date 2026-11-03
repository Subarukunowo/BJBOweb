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
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? 'midtrans';
        $transactionId = $payload['transaction_id'] ?? null;

        $serverKey = (string) config('services.midtrans.server_key');

        // Validasi signature key jika dikirim oleh Midtrans
        if ($signatureKey && $serverKey && $orderId && $statusCode && $grossAmount) {
            $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);
            if ($signatureKey !== $expectedSignature) {
                Log::warning('Midtrans Invalid Signature for order: '.$orderId);

                return response()->json(['message' => 'Invalid signature'], 403);
            }
        }

        $pesanan = Pesanan::where('order_number', $orderId)->first();
        if (! $pesanan) {
            Log::warning('Pesanan not found for Midtrans order_id: '.$orderId);

            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $pesananService->markOrderAsPaid($pesanan->id, $paymentType, $transactionId);
            }
        } elseif ($transactionStatus === 'settlement') {
            $pesananService->markOrderAsPaid($pesanan->id, $paymentType, $transactionId);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $pesanan->update(['status' => 'cancelled']);
        }

        return response()->json(['status' => 'success']);
    }
}

