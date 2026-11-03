<?php

namespace App\Services;

use App\Models\Promo;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PromoService
{
    /**
     * Get paginated promos for admin listing.
     * Loop-free and explicit column selection.
     */
    public function getAllPromos(int $perPage = 10): LengthAwarePaginator
    {
        return Promo::query()
            ->select([
                'id',
                'title',
                'slug',
                'type',
                'code',
                'discount_percentage',
                'discount_amount',
                'min_purchase',
                'banner_image',
                'start_date',
                'end_date',
                'is_active',
                'created_at',
            ])
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Get active advertisement banners for public carousel display.
     */
    public function getActiveBanners(): Collection
    {
        return Promo::query()
            ->select(['id', 'title', 'slug', 'type', 'banner_image', 'description'])
            ->where('is_active', true)
            ->whereIn('type', ['banner_iklan', 'flash_sale'])
            ->where(function ($q) {
                $now = now();
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) {
                $now = now();
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->latest('id')
            ->take(5)
            ->get();
    }

    /**
     * Get active vouchers and discount codes available for buyers.
     */
    public function getActiveVouchers(): Collection
    {
        return Promo::query()
            ->select(['id', 'title', 'code', 'type', 'discount_percentage', 'discount_amount', 'min_purchase', 'description', 'end_date'])
            ->where('is_active', true)
            ->whereNotNull('code')
            ->where(function ($q) {
                $now = now();
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) {
                $now = now();
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->get();
    }

    /**
     * Validate voucher code and compute discount amount.
     */
    public function validateAndCalculateDiscount(string $code, int $totalPrice): array
    {
        $promo = Promo::where('code', trim($code))->where('is_active', true)->first();

        if (! $promo) {
            return [
                'is_valid' => false,
                'discount' => 0,
                'promo' => null,
                'message' => 'Kode promo tidak ditemukan atau tidak aktif.',
            ];
        }

        if (! $promo->isValidNow()) {
            return [
                'is_valid' => false,
                'discount' => 0,
                'promo' => $promo,
                'message' => 'Masa berlaku promo telah berakhir.',
            ];
        }

        if ($totalPrice < $promo->min_purchase) {
            $formattedMin = 'Rp'.number_format($promo->min_purchase, 0, ',', '.');

            return [
                'is_valid' => false,
                'discount' => 0,
                'promo' => $promo,
                'message' => 'Minimal belanja untuk promo ini adalah '.$formattedMin.'.',
            ];
        }

        $discount = $promo->calculateDiscount($totalPrice);

        return [
            'is_valid' => true,
            'discount' => $discount,
            'promo' => $promo,
            'message' => 'Kode promo berhasil digunakan!',
        ];
    }

    /**
     * Store new promo created by admin.
     */
    public function createPromo(array $data, ?UploadedFile $bannerImage = null): Promo
    {
        $bannerPath = null;
        if ($bannerImage && $bannerImage->isValid()) {
            $bannerPath = $bannerImage->store('promos', 'public');
        }

        $slug = Str::slug($data['title']).'-'.Str::lower(Str::random(5));

        return Promo::create([
            'title' => $data['title'],
            'slug' => $slug,
            'type' => $data['type'] ?? 'diskon_persen',
            'code' => ! empty($data['code']) ? Str::upper(trim($data['code'])) : null,
            'discount_percentage' => $data['discount_percentage'] ?? null,
            'discount_amount' => $data['discount_amount'] ?? null,
            'min_purchase' => $data['min_purchase'] ?? 0,
            'banner_image' => $bannerPath,
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
    }

    /**
     * Update an existing promo.
     */
    public function updatePromo(Promo $promo, array $data, ?UploadedFile $bannerImage = null): Promo
    {
        $payload = [
            'title' => $data['title'] ?? $promo->title,
            'type' => $data['type'] ?? $promo->type,
            'code' => ! empty($data['code']) ? Str::upper(trim($data['code'])) : $promo->code,
            'discount_percentage' => $data['discount_percentage'] ?? $promo->discount_percentage,
            'discount_amount' => $data['discount_amount'] ?? $promo->discount_amount,
            'min_purchase' => $data['min_purchase'] ?? $promo->min_purchase,
            'description' => $data['description'] ?? $promo->description,
            'start_date' => $data['start_date'] ?? $promo->start_date,
            'end_date' => $data['end_date'] ?? $promo->end_date,
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $promo->is_active,
        ];

        if ($bannerImage && $bannerImage->isValid()) {
            $payload['banner_image'] = $bannerImage->store('promos', 'public');
        }

        $promo->update($payload);

        return $promo;
    }

    /**
     * Delete a promo.
     */
    public function deletePromo(Promo $promo): bool
    {
        return (bool) $promo->delete();
    }

    /**
     * Toggle active state.
     */
    public function togglePromoStatus(Promo $promo): Promo
    {
        $promo->update(['is_active' => ! $promo->is_active]);

        return $promo;
    }
}
