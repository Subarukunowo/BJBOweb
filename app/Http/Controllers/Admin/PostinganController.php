<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Postingan\StorePostinganRequest;
use App\Services\PostinganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class PostinganController extends Controller
{
    protected PostinganService $postinganService;

    public function __construct(PostinganService $postinganService)
    {
        $this->postinganService = $postinganService;
    }

    public function index()
    {
        return redirect()->route('user.dashboard');
    }

    public function create()
    {
        $user = Auth::user();
        if (! $user || ! $user->hasActiveStore()) {
            Alert::warning(
                'Buka Toko Terlebih Dahulu',
                'Anda harus mendaftar dan memiliki toko aktif sebelum dapat menambahkan produk untuk dijual di BJBO Sedagho.'
            );

            return redirect()->route('user.toko.daftar');
        }

        return view('pages.user.postingan.create');
    }

    public function store(StorePostinganRequest $request)
    {
        $user = Auth::user();

        if (! $user || ! $user->hasActiveStore()) {
            Alert::error(
                'Akses Ditolak',
                'Anda harus mendaftarkan dan memiliki toko aktif sebelum dapat mengunggah produk ke etalase.'
            );

            return redirect()->route('user.toko.daftar');
        }

        // 1. Periksa moderasi konten barang terlarang sebelum menyimpan ke etalase
        $moderation = app(\App\Services\ContentModerationService::class)->inspect(
            $request->input('name', ''),
            $request->input('category', ''),
            $request->input('description', '')
        );

        if ($moderation['is_blocked']) {
            // Catat otomatis ke Laporan Admin sebagai notifikasi pelanggaran hukum
            \App\Models\Laporan::create([
                'jenis_laporan' => 'Percobaan Penjualan Barang Terlarang (Otomatis Sistem)',
                'teks_laporan' => 'Pengguna "'.$user->name.'" (ID: #'.$user->id.', Email: '.$user->email.') terdeteksi mencoba mengunggah barang terlarang: '.$moderation['reason'].' dengan judul produk: "'.$request->input('name').'".',
                'user_id' => $user->id,
                'status' => 'Pending',
                'postingan_id' => null,
            ]);

            // Tampilkan peringatan yang sopan tanpa kata-kata eksplisit di profil
            Alert::error(
                'Unggahan Ditolak!',
                'Produk tidak dapat diunggah karena memuat indikasi barang atau istilah yang dilarang oleh hukum dan kebijakan komunitas BJBO Sedagho.'
            );

            return redirect()->back()->withInput()->with('error', 'Produk tidak dapat diunggah karena terindikasi melanggar ketentuan hukum barang terlarang dan telah dilaporkan ke Admin.');
        }

        try {
            $postingan = $this->postinganService->createPostingan(
                $request->validated(),
                $request->file('image'),
                $user->id,
                $user->name
            );

            Alert::success(
                'Berhasil Diterbitkan!',
                'Produk Anda lolos verifikasi otomatis BJBO dan langsung aktif di etalase tanpa menunggu ACC admin!'
            );

            return redirect()->route('user.profil');
        } catch (\Exception $e) {
            Alert::error('Gagal!', 'Terjadi kesalahan saat menyimpan data!');

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function cari(Request $request)
    {
        $keyword = (string) $request->input('keyword', '');
        $postingan = $this->postinganService->searchPostings($keyword);

        if (! empty($keyword)) {
            app(\App\Services\RecommendationEngineService::class)->logSearch(
                \Illuminate\Support\Facades\Auth::id(),
                $keyword,
                null,
                $request->ip()
            );
        }

        return view('pages.user.cari', compact('postingan', 'keyword'));
    }

    public function filterByCategory(string $kategori)
    {
        $postingan = $this->postinganService->getPostingsByCategory($kategori);
        $category = ucfirst($kategori);

        app(\App\Services\RecommendationEngineService::class)->logSearch(
            \Illuminate\Support\Facades\Auth::id(),
            $kategori,
            $kategori,
            request()->ip()
        );

        return view('pages.user.postingan.kategori.index', compact('postingan', 'category'));
    }
}
