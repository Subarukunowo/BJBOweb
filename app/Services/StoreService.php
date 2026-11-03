<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class StoreService
{
    /**
     * Get existing store for a user.
     */
    public function getUserStore(int $userId): ?Store
    {
        return Store::query()
            ->select([
                'id',
                'user_id',
                'store_name',
                'store_slug',
                'store_type',
                'phone_number',
                'email',
                'ktp_number',
                'ktp_image',
                'nib_number',
                'npwp_company',
                'npwp_image',
                'haki_certificate_number',
                'haki_certificate_file',
                'description',
                'address',
                'logo',
                'banner',
                'status',
                'rejected_reason',
                'verified_at',
                'created_at',
            ])
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Register Toko Perorangan:
     * Requires active phone number, verified email, and KTP (number & photo).
     */
    public function registerPerorangan(User $user, array $data, ?UploadedFile $ktpFile = null): Store
    {
        if (Store::where('user_id', $user->id)->exists()) {
            throw new InvalidArgumentException('Anda sudah memiliki pendaftaran toko.');
        }

        $ktpPath = null;
        if ($ktpFile && $ktpFile->isValid()) {
            $ktpPath = $ktpFile->store('stores/ktp', 'public');
        }

        $slug = Str::slug($data['store_name']).'-'.Str::lower(Str::random(4));

        $store = Store::create([
            'user_id' => $user->id,
            'store_name' => $data['store_name'],
            'store_slug' => $slug,
            'store_type' => 'perorangan',
            'phone_number' => $data['phone_number'],
            'email' => $data['email'] ?? $user->email,
            'ktp_number' => preg_replace('/[^\d]/', '', $data['ktp_number'] ?? ''),
            'ktp_image' => $ktpPath,
            'description' => $data['description'] ?? null,
            'address' => $data['address'] ?? null,
            'status' => 'aktif',
            'verified_at' => now(),
        ]);

        app(NotificationService::class)->sendNotification(
            $user->id,
            'Toko Berhasil Dibuka!',
            'Selamat! Toko "'.$store->store_name.'" berhasil dibuka dengan verifikasi KTP. Anda sekarang dapat mulai mengunggah dan menjual produk di BJBO Sedagho.',
            'toko',
            ['store_id' => $store->id]
        );

        return $store;
    }

    /**
     * Register Official Store:
     * Requires NIB, Company NPWP, and HAKI certificate.
     */
    public function registerOfficialStore(
        User $user,
        array $data,
        ?UploadedFile $npwpFile = null,
        ?UploadedFile $hakiFile = null
    ): Store {
        if (Store::where('user_id', $user->id)->exists()) {
            throw new InvalidArgumentException('Anda sudah memiliki pendaftaran toko.');
        }

        $npwpPath = null;
        if ($npwpFile && $npwpFile->isValid()) {
            $npwpPath = $npwpFile->store('stores/npwp', 'public');
        }

        $hakiPath = null;
        if ($hakiFile && $hakiFile->isValid()) {
            $hakiPath = $hakiFile->store('stores/haki', 'public');
        }

        $slug = Str::slug($data['store_name']).'-official-'.Str::lower(Str::random(4));

        $store = Store::create([
            'user_id' => $user->id,
            'store_name' => $data['store_name'],
            'store_slug' => $slug,
            'store_type' => 'official',
            'phone_number' => $data['phone_number'],
            'email' => $data['email'] ?? $user->email,
            'nib_number' => preg_replace('/[^\d]/', '', $data['nib_number'] ?? ''),
            'npwp_company' => $data['npwp_company'] ?? null,
            'npwp_image' => $npwpPath,
            'haki_certificate_number' => $data['haki_certificate_number'] ?? null,
            'haki_certificate_file' => $hakiPath,
            'description' => $data['description'] ?? null,
            'address' => $data['address'] ?? null,
            'status' => 'menunggu_verifikasi',
        ]);

        app(NotificationService::class)->sendNotification(
            $user->id,
            'Pendaftaran Official Store Berhasil Diajukan',
            'Pendaftaran Official Store "'.$store->store_name.'" dengan berkas NIB, NPWP & HAKI telah diterima dan masuk antrean verifikasi kurator.',
            'toko',
            ['store_id' => $store->id]
        );

        return $store;
    }

    /**
     * Admin store moderation (Approve / Reject).
     */
    public function moderateStore(int $storeId, string $status, ?string $reason = null): Store
    {
        $store = Store::with('user:id,name,email')->findOrFail($storeId);

        $store->update([
            'status' => $status,
            'rejected_reason' => $reason,
            'verified_at' => $status === 'aktif' ? now() : null,
        ]);

        $statusMsg = $status === 'aktif'
            ? 'Selamat! Toko "'.$store->store_name.'" telah disetujui dan kini berstatus aktif.'
            : 'Pendaftaran toko "'.$store->store_name.'" ditolak. Alasan: '.($reason ?? 'Dokumen tidak memenuhi syarat.');

        app(NotificationService::class)->sendNotification(
            $store->user_id,
            'Status Pendaftaran Toko: '.Str::headline($status),
            $statusMsg,
            'toko',
            ['store_id' => $store->id, 'status' => $status]
        );

        return $store;
    }

    /**
     * Get paginated stores for admin review.
     */
    public function getAllStoresForAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return Store::with('user:id,name,email')
            ->select([
                'id',
                'user_id',
                'store_name',
                'store_slug',
                'store_type',
                'phone_number',
                'email',
                'nib_number',
                'npwp_company',
                'haki_certificate_number',
                'status',
                'rejected_reason',
                'verified_at',
                'created_at',
            ])
            ->latest('id')
            ->paginate($perPage);
    }
}
