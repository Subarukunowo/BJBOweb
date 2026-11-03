<?php

namespace App\Services;

use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PesananLifecycleService
{
    /**
     * Seller confirms or rejects incoming order.
     * Status transitions:
     * - Accept: 'menunggu_konfirmasi' -> 'diproses'
     * - Reject: 'menunggu_konfirmasi' -> 'ditolak_penjual' (cancels order)
     */
    public function confirmOrder(Pesanan $pesanan, int $sellerId, bool $accept, ?string $rejectionReason = null): Pesanan
    {
        if ($pesanan->penjual_id !== $sellerId) {
            throw new InvalidArgumentException('Anda tidak berhak mengonfirmasi pesanan ini.');
        }

        if ($pesanan->shipping_status !== 'menunggu_konfirmasi') {
            throw new InvalidArgumentException('Status pesanan saat ini tidak dalam tahap menunggu konfirmasi.');
        }

        if ($accept) {
            $pesanan->update([
                'shipping_status' => 'diproses',
                'penjual_confirmation_at' => now(),
            ]);

            app(NotificationService::class)->sendNotification(
                $pesanan->user_id,
                'Pesanan Diterima Penjual',
                'Penjual telah menyetujui pesanan #'.$pesanan->order_number.'. Pesanan sedang dikemas dan diproses.',
                'pesanan',
                ['pesanan_id' => $pesanan->id, 'status' => 'diproses']
            );
        } else {
            $reason = $rejectionReason ?: 'Stok produk habis atau penjual berhalangan.';
            $pesanan->update([
                'shipping_status' => 'ditolak_penjual',
                'status' => 'cancelled',
                'penjual_confirmation_at' => now(),
                'alasan_penolakan' => $reason,
            ]);

            app(NotificationService::class)->sendNotification(
                $pesanan->user_id,
                'Pesanan Ditolak Penjual',
                'Penjual menolak pesanan #'.$pesanan->order_number.'. Alasan: '.$reason.'. Dana Anda aman dalam Rekber dan akan dikembalikan.',
                'pesanan',
                ['pesanan_id' => $pesanan->id, 'status' => 'ditolak_penjual']
            );
        }

        return $pesanan;
    }

    /**
     * Seller marks order as dispatched/shipped with courier and tracking number.
     * Sets coordinates for free Leaflet OpenStreetMap live tracking.
     */
    public function shipOrder(
        Pesanan $pesanan,
        int $sellerId,
        string $kurirName,
        string $nomorResi,
        array $coords = []
    ): Pesanan {
        if ($pesanan->penjual_id !== $sellerId) {
            throw new InvalidArgumentException('Anda tidak berhak mengirimkan pesanan ini.');
        }

        // Default koordinat Bengkalis untuk tracking jika tidak disediakan
        $originLat = $coords['origin_lat'] ?? 1.4877;
        $originLng = $coords['origin_lng'] ?? 102.1334;
        $originAddress = $coords['origin_address'] ?? 'Hub Penjual Bengkalis Kota';

        $destinationLat = $coords['destination_lat'] ?? 1.4725;
        $destinationLng = $coords['destination_lng'] ?? 102.1520;
        $destinationAddress = $coords['destination_address'] ?? ($pesanan->catatan ?? 'Alamat Pembeli');

        $currentLat = $coords['current_lat'] ?? 1.4820;
        $currentLng = $coords['current_lng'] ?? 102.1410;

        $pesanan->update([
            'shipping_status' => 'dikirim',
            'kurir_name' => $kurirName,
            'nomor_resi' => $nomorResi,
            'origin_lat' => $originLat,
            'origin_lng' => $originLng,
            'origin_address' => $originAddress,
            'destination_lat' => $destinationLat,
            'destination_lng' => $destinationLng,
            'destination_address' => $destinationAddress,
            'current_lat' => $currentLat,
            'current_lng' => $currentLng,
            'last_checkpoint' => 'Paket telah diserahkan ke '.$kurirName.' dan sedang menuju kota tujuan.',
        ]);

        app(NotificationService::class)->sendNotification(
            $pesanan->user_id,
            'Pesanan Sedang Dikirim',
            'Pesanan #'.$pesanan->order_number.' telah dikirim via '.$kurirName.' dengan Nomor Resi: '.$nomorResi.'. Anda dapat melacak posisi kurir di peta.',
            'pesanan',
            [
                'pesanan_id' => $pesanan->id,
                'kurir' => $kurirName,
                'resi' => $nomorResi,
            ]
        );

        return $pesanan;
    }

    /**
     * Update courier live GPS location on map.
     */
    public function updateCourierLocation(Pesanan $pesanan, float $lat, float $lng, string $checkpoint): Pesanan
    {
        $pesanan->update([
            'current_lat' => $lat,
            'current_lng' => $lng,
            'last_checkpoint' => $checkpoint,
        ]);

        return $pesanan;
    }

    /**
     * Mark order as delivered / fulfilled ('sampai_tujuan').
     * Automatically triggers Rekber escrow fund release to seller!
     */
    public function markAsDelivered(Pesanan $pesanan, int $actorUserId): Pesanan
    {
        if ($pesanan->user_id !== $actorUserId && $pesanan->penjual_id !== $actorUserId) {
            // Bisa diselesaikan oleh pembeli atau penjual/admin
        }

        DB::transaction(function () use ($pesanan) {
            $pesanan->update([
                'shipping_status' => 'sampai_tujuan',
                'delivered_at' => now(),
                'last_checkpoint' => 'Pesanan telah diterima oleh pembeli. Transaksi selesai.',
            ]);

            // Release escrow funds to seller's Rekber wallet
            app(RekberService::class)->releaseEscrowToSeller($pesanan);

            app(NotificationService::class)->sendNotification(
                $pesanan->user_id,
                'Pesanan Sampai Tujuan',
                'Pesanan #'.$pesanan->order_number.' telah tiba di tujuan. Terima kasih telah berbelanja di BJBO!',
                'pesanan',
                ['pesanan_id' => $pesanan->id]
            );
        });

        return $pesanan;
    }

    /**
     * Get tracking payload for Leaflet map and delivery timeline.
     */
    public function getTrackingData(Pesanan $pesanan): array
    {
        return [
            'order_number' => $pesanan->order_number,
            'shipping_status' => $pesanan->shipping_status,
            'kurir_name' => $pesanan->kurir_name ?? 'Kurir Lokal Bengkalis',
            'nomor_resi' => $pesanan->nomor_resi ?? 'RES-'.strtoupper(substr(md5((string) $pesanan->id), 0, 10)),
            'last_checkpoint' => $pesanan->last_checkpoint ?? 'Menunggu penjemputan paket.',
            'origin' => [
                'lat' => $pesanan->origin_lat ?? 1.4877,
                'lng' => $pesanan->origin_lng ?? 102.1334,
                'address' => $pesanan->origin_address ?? 'Gudang Penjual',
            ],
            'destination' => [
                'lat' => $pesanan->destination_lat ?? 1.4725,
                'lng' => $pesanan->destination_lng ?? 102.1520,
                'address' => $pesanan->destination_address ?? 'Alamat Penerima',
            ],
            'current' => [
                'lat' => $pesanan->current_lat ?? ($pesanan->origin_lat ?? 1.4877),
                'lng' => $pesanan->current_lng ?? ($pesanan->origin_lng ?? 102.1334),
            ],
            'delivered_at' => $pesanan->delivered_at ? $pesanan->delivered_at->format('d M Y H:i') : null,
        ];
    }
}
