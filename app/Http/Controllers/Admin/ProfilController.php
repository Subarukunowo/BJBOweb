<?php

namespace App\Http\Controllers\Admin;

use App\Events\PostinganStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Postingan\UpdatePostinganRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\Postingan;
use App\Services\PostinganService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProfilController extends Controller
{
    protected PostinganService $postinganService;

    protected UserService $userService;

    public function __construct(PostinganService $postinganService, UserService $userService)
    {
        $this->postinganService = $postinganService;
        $this->userService = $userService;
    }

    public function index()
    {
        $userId = (int) Auth::id();
        $postingan = $this->postinganService->getUserPostings($userId);
        $user = Auth::user();
        $metrics = $this->postinganService->getSellerMetrics($userId);

        $store = app(\App\Services\StoreService::class)->getUserStore($userId);
        $saldoRekber = app(\App\Services\RekberService::class)->getBalance($userId);
        $bankAccounts = app(\App\Services\RekberService::class)->getUserBankAccounts($userId);
        $withdrawals = app(\App\Services\RekberService::class)->getUserWithdrawals($userId, 5);
        $wishlistsCount = \App\Models\Wishlist::where('user_id', $userId)->count();

        return view('pages.user.profil.index', compact(
            'postingan',
            'user',
            'metrics',
            'store',
            'saldoRekber',
            'bankAccounts',
            'withdrawals',
            'wishlistsCount'
        ));
    }

    public function toggleSold(Request $request, int $id)
    {
        $userId = (int) Auth::id();
        $success = $this->postinganService->toggleSoldStatus($id, $userId);
        $postingan = Postingan::find($id);
        $isSold = $postingan ? (bool) $postingan->is_sold : false;

        if ($success) {
            broadcast(new PostinganStatusChanged($id, $isSold))->toOthers();
            $message = 'Status ketersediaan barang berhasil diperbarui!';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'is_sold' => $isSold,
                    'postingan_id' => $id,
                    'message' => $message,
                ]);
            }

            Alert::success('Berhasil!', $message);
        } else {
            $message = 'Anda tidak memiliki hak akses mengubah produk ini.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 403);
            }

            Alert::error('Gagal!', $message);
        }

        return redirect()->back();
    }

    public function updateAccount(UpdateProfileRequest $request)
    {
        try {
            $this->userService->updateProfile(
                Auth::id(),
                $request->validated(),
                $request->file('profile_picture')
            );

            Alert::success('Berhasil!', 'Profil dan foto akun berhasil diperbarui!');

            return redirect()->route('user.profil');
        } catch (\Exception $e) {
            Alert::error('Gagal!', 'Terjadi kesalahan saat memperbarui profil: '.$e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    public function removeAvatar()
    {
        try {
            $this->userService->removeAvatar(Auth::id());
            Alert::success('Berhasil!', 'Foto profil berhasil dihapus dan dikembalikan ke avatar default.');

            return redirect()->route('user.profil');
        } catch (\Exception $e) {
            Alert::error('Gagal!', 'Gagal menghapus foto profil: '.$e->getMessage());

            return redirect()->back();
        }
    }

    public function edit(int $id)
    {
        $detail = $this->postinganService->getPostinganDetail($id);
        $postingan = $detail['postingan'];

        return view('pages.user.profil.edit', compact('postingan'));
    }

    public function update(UpdatePostinganRequest $request, int $id)
    {
        $user = Auth::user();

        // 1. Periksa moderasi konten barang terlarang sebelum menyimpan perubahan
        $moderation = app(\App\Services\ContentModerationService::class)->inspect(
            $request->input('name', ''),
            $request->input('category', ''),
            $request->input('description', '')
        );

        if ($moderation['is_blocked']) {
            // Catat otomatis ke Laporan Admin sebagai notifikasi pelanggaran hukum
            \App\Models\Laporan::create([
                'jenis_laporan' => 'Percobaan Perubahan Produk Menjadi Barang Terlarang (Otomatis Sistem)',
                'teks_laporan' => 'Pengguna "'.$user->name.'" (ID: #'.$user->id.', Email: '.$user->email.') terdeteksi mencoba mengubah produk (ID: #'.$id.') menjadi barang terlarang: '.$moderation['reason'].' dengan judul produk: "'.$request->input('name').'".',
                'user_id' => $user->id,
                'status' => 'Pending',
                'postingan_id' => $id,
            ]);

            // Tampilkan peringatan yang sopan tanpa kata-kata eksplisit di profil
            Alert::error(
                'Perubahan Ditolak!',
                'Perubahan produk tidak dapat disimpan karena memuat indikasi barang atau istilah yang dilarang oleh hukum dan kebijakan komunitas BJBO Sedagho.'
            );

            return redirect()->back()->withInput()->with('error', 'Perubahan produk tidak dapat disimpan karena terindikasi melanggar ketentuan hukum barang terlarang dan telah dilaporkan ke Admin.');
        }

        try {
            $this->postinganService->updatePostingan(
                $id,
                $request->validated(),
                $request->file('image')
            );

            Alert::success('Berhasil!', 'Iklan berhasil diperbarui dan aktif di pasar!');

            return redirect()->route('user.profil');
        } catch (\Exception $e) {
            Alert::error('Gagal!', 'Terjadi kesalahan saat memperbarui data!');

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete(int $id)
    {
        $this->postinganService->deletePostingan($id);
        Alert::success('Berhasil!', 'Iklan berhasil dihapus!');

        return redirect()->back();
    }
}
