<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\PromoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromoController extends BaseApiController
{
    public function __construct(
        protected PromoService $promoService
    ) {}

    public function banners(): JsonResponse
    {
        $banners = $this->promoService->getActiveBanners();

        return $this->successResponse($banners, 'Banner promosi berhasil dimuat.');
    }

    public function vouchers(): JsonResponse
    {
        $vouchers = $this->promoService->getActiveVouchers();

        return $this->successResponse($vouchers, 'Voucher promo berhasil dimuat.');
    }

    public function validateCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'total_price' => 'required|numeric|min:0',
        ]);

        $res = $this->promoService->validateAndCalculateDiscount(
            $validated['code'],
            (int) $validated['total_price']
        );

        if (! $res['valid']) {
            return $this->errorResponse($res['message'], 422, $res);
        }

        return $this->successResponse($res, $res['message']);
    }
}
