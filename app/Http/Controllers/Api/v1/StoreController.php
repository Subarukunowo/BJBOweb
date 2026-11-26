<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use App\Services\StoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class StoreController extends BaseApiController
{
    public function __construct(
        protected StoreService $storeService
    ) {}

    public function myStore(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $store = $this->storeService->getUserStore($user->id);

        if (! $store) {
            return $this->successResponse(null, 'Pengguna belum memiliki toko terdaftar.');
        }

        return $this->successResponse($store, 'Profil toko berhasil dimuat.');
    }

    public function register(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $type = $request->input('store_type', 'perorangan');

        try {
            if ($type === 'official') {
                $validated = $request->validate([
                    'store_name' => 'required|string|max:100',
                    'phone_number' => 'required|string|max:20',
                    'email' => 'required|email|max:100',
                    'nib_number' => 'required|string|max:30',
                    'npwp_company' => 'required|string|max:30',
                    'haki_certificate_number' => 'nullable|string|max:50',
                    'description' => 'required|string|max:500',
                    'address' => 'required|string|max:255',
                    'npwp_image' => 'nullable|file|mimes:jpeg,png,pdf|max:3072',
                    'haki_certificate_file' => 'nullable|file|mimes:jpeg,png,pdf|max:3072',
                    'logo' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
                ]);

                $store = $this->storeService->registerOfficialStore(
                    $user,
                    $validated,
                    $request->file('npwp_image'),
                    $request->file('haki_certificate_file'),
                    $request->file('logo')
                );
            } else {
                $validated = $request->validate([
                    'store_name' => 'required|string|max:100',
                    'phone_number' => 'required|string|max:20',
                    'email' => 'required|email|max:100',
                    'ktp_number' => 'required|string|size:16',
                    'description' => 'required|string|max:500',
                    'address' => 'required|string|max:255',
                    'ktp_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
                    'logo' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
                ]);

                $store = $this->storeService->registerPerorangan(
                    $user,
                    $validated,
                    $request->file('ktp_image'),
                    $request->file('logo')
                );
            }

            return $this->successResponse(
                $store,
                'Pendaftaran toko berhasil diajukan dan sedang dalam proses peninjauan.',
                201
            );
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
}
