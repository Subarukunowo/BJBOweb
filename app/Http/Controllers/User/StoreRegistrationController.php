<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\StoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class StoreRegistrationController extends Controller
{
    protected StoreService $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function show(): View
    {
        $user = auth()->user();
        $store = $this->storeService->getUserStore($user->id);

        return view('pages.user.toko.daftar', compact('store', 'user'));
    }

    public function storePerorangan(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'store_name' => 'required|string|min:3|max:100',
            'phone_number' => 'required|string|min:10|max:15',
            'email' => 'required|email|max:100',
            'ktp_number' => 'required|string|size:16',
            'ktp_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $this->storeService->registerPerorangan($user, $validated, $request->file('ktp_image'));

            return redirect()->route('user.profil.index')->with('success', 'Selamat! Toko Anda berhasil dibuka dan aktif. Anda sekarang dapat mulai menjual produk di BJBO.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['store' => $e->getMessage()]);
        }
    }

    public function storeOfficial(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'store_name' => 'required|string|min:3|max:100',
            'phone_number' => 'required|string|min:10|max:15',
            'email' => 'required|email|max:100',
            'nib_number' => 'required|string|min:9|max:20',
            'npwp_company' => 'required|string|min:15|max:25',
            'npwp_image' => 'required|image|mimes:jpeg,png,jpg,webp,pdf|max:3072',
            'haki_certificate_number' => 'required|string|min:5|max:50',
            'haki_certificate_file' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $this->storeService->registerOfficialStore(
                $user,
                $validated,
                $request->file('npwp_image'),
                $request->file('haki_certificate_file')
            );

            return redirect()->route('user.profil.index')->with('success', 'Pendaftaran Official Store berhasil diajukan dengan berkas NIB, NPWP, dan Sertifikat HAKI.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['store' => $e->getMessage()]);
        }
    }
}
