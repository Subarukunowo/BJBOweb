<?php

namespace App\Repositories;

use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Collection;

class PesananRepository
{
    /**
     * Create a new order.
     */
    public function create(array $data): Pesanan
    {
        return Pesanan::create($data);
    }

    /**
     * Find order by ID.
     */
    /**
     * Find order by ID with optimized columns and eager loading.
     */
    public function findById(int $id): ?Pesanan
    {
        return Pesanan::query()
            ->select([
                'id', 'order_number', 'user_id', 'postingan_id', 'penjual_id',
                'total_price', 'catatan', 'status', 'shipping_status', 'kurir_name',
                'nomor_resi', 'penjual_confirmation_at', 'alasan_penolakan',
                'origin_lat', 'origin_lng', 'origin_address',
                'destination_lat', 'destination_lng', 'destination_address',
                'current_lat', 'current_lng', 'last_checkpoint',
                'promo_id', 'discount_amount', 'final_price', 'delivered_at',
                'is_funds_released', 'snap_token', 'payment_type', 'transaction_id',
                'settlement_time', 'created_at',
            ])
            ->with([
                'pembeli:id,name,email,profile_picture',
                'penjual:id,name,email,profile_picture',
                'postingan:id,name,price,category,image,lokasi,user_id',
                'promo:id,title,code,type,discount_amount,discount_percentage',
            ])
            ->find($id);
    }

    /**
     * Find order by order number.
     */
    public function findByOrderNumber(string $orderNumber): ?Pesanan
    {
        return Pesanan::query()
            ->select([
                'id', 'order_number', 'user_id', 'postingan_id', 'penjual_id',
                'total_price', 'catatan', 'status', 'shipping_status', 'kurir_name',
                'nomor_resi', 'penjual_confirmation_at', 'alasan_penolakan',
                'promo_id', 'discount_amount', 'final_price', 'delivered_at',
                'is_funds_released', 'snap_token', 'payment_type', 'transaction_id',
                'settlement_time', 'created_at',
            ])
            ->with([
                'pembeli:id,name,email,profile_picture',
                'penjual:id,name,email,profile_picture',
                'postingan:id,name,price,category,image,lokasi,user_id',
            ])
            ->where('order_number', $orderNumber)
            ->first();
    }

    /**
     * Get all orders for a user as buyer or seller with optimized columns.
     */
    public function getOrdersByUserId(int $userId): Collection
    {
        return Pesanan::query()
            ->select([
                'id', 'order_number', 'user_id', 'postingan_id', 'penjual_id',
                'total_price', 'catatan', 'status', 'shipping_status', 'kurir_name',
                'nomor_resi', 'promo_id', 'discount_amount', 'final_price',
                'delivered_at', 'is_funds_released', 'payment_type', 'created_at',
            ])
            ->with([
                'postingan:id,name,price,category,image,lokasi',
                'penjual:id,name',
                'pembeli:id,name',
            ])
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhere('penjual_id', $userId);
            })
            ->latest('id')
            ->get();
    }

    /**
     * Update order payment status and transition shipping status to menunggu_konfirmasi.
     */
    public function updateStatus(int $id, string $status, ?string $paymentType = null, ?string $transactionId = null): bool
    {
        $pesanan = Pesanan::find($id);
        if (! $pesanan) {
            return false;
        }

        $data = ['status' => $status];
        if ($paymentType) {
            $data['payment_type'] = $paymentType;
        }
        if ($transactionId) {
            $data['transaction_id'] = $transactionId;
        }
        if ($status === 'paid') {
            $data['settlement_time'] = now();
            $data['shipping_status'] = 'menunggu_konfirmasi';
        }

        return $pesanan->update($data);
    }

    /**
     * Update order snap token.
     */
    public function updateSnapToken(int $id, string $snapToken): bool
    {
        $pesanan = Pesanan::find($id);
        if (! $pesanan) {
            return false;
        }

        return $pesanan->update(['snap_token' => $snapToken]);
    }
}
