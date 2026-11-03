<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\PesananLifecycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class PesananFulfillmentController extends Controller
{
    protected PesananLifecycleService $lifecycleService;

    public function __construct(PesananLifecycleService $lifecycleService)
    {
        $this->lifecycleService = $lifecycleService;
    }

    /**
     * Seller accepts or rejects the incoming paid order.
     */
    public function confirm(Request $request, int $id): RedirectResponse
    {
        $pesanan = Pesanan::findOrFail($id);
        $sellerId = (int) auth()->id();

        $action = $request->input('action'); // 'accept' or 'reject'
        $accept = $action === 'accept';
        $reason = $request->input('alasan_penolakan');

        try {
            $this->lifecycleService->confirmOrder($pesanan, $sellerId, $accept, $reason);

            $msg = $accept ? 'Pesanan berhasil disetujui dan sedang diproses.' : 'Pesanan telah ditolak dan dibatalkan.';

            return back()->with('success', $msg);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['fulfillment' => $e->getMessage()]);
        }
    }

    /**
     * Seller marks order as shipped with courier name and tracking code.
     */
    public function ship(Request $request, int $id): RedirectResponse
    {
        $pesanan = Pesanan::findOrFail($id);
        $sellerId = (int) auth()->id();

        $validated = $request->validate([
            'kurir_name' => 'required|string|max:50',
            'nomor_resi' => 'required|string|max:100',
            'origin_address' => 'nullable|string|max:255',
            'destination_address' => 'nullable|string|max:255',
        ]);

        try {
            $this->lifecycleService->shipOrder(
                $pesanan,
                $sellerId,
                $validated['kurir_name'],
                $validated['nomor_resi'],
                $validated
            );

            return back()->with('success', 'Pesanan telah ditandai sedang dikirim. Nomor resi berhasil dicatat.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['fulfillment' => $e->getMessage()]);
        }
    }

    /**
     * Buyer or seller confirms order delivered / fulfilled.
     */
    public function deliver(int $id): RedirectResponse
    {
        $pesanan = Pesanan::findOrFail($id);
        $userId = (int) auth()->id();

        if ($pesanan->user_id !== $userId && $pesanan->penjual_id !== $userId) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $this->lifecycleService->markAsDelivered($pesanan, $userId);

        return back()->with('success', 'Pesanan selesai! Dana telah diteruskan ke saldo Rekber penjual.');
    }

    /**
     * View live interactive tracking page using Leaflet + OpenStreetMap.
     */
    public function track(int $id): View
    {
        $pesanan = Pesanan::with(['pembeli:id,name', 'penjual:id,name', 'postingan:id,name,image,price,category'])->findOrFail($id);
        $userId = (int) auth()->id();

        if ($pesanan->user_id !== $userId && $pesanan->penjual_id !== $userId) {
            abort(403, 'Anda tidak berhak melihat pelacakan pesanan ini.');
        }

        $trackingData = $this->lifecycleService->getTrackingData($pesanan);

        return view('pages.user.pesanan.lacak', compact('pesanan', 'trackingData'));
    }

    /**
     * JSON API for map polling/real-time courier location updates.
     */
    public function trackingApi(int $id): JsonResponse
    {
        $pesanan = Pesanan::findOrFail($id);

        return response()->json($this->lifecycleService->getTrackingData($pesanan));
    }
}
