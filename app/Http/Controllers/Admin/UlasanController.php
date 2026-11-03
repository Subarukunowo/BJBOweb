<?php

namespace App\Http\Controllers\Admin;

use App\Events\UlasanAdded;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ulasan\StoreUlasanRequest;
use App\Services\UlasanService;
use Illuminate\Support\Facades\Auth;

class UlasanController extends Controller
{
    protected UlasanService $ulasanService;

    public function __construct(UlasanService $ulasanService)
    {
        $this->ulasanService = $ulasanService;
    }

    public function store(StoreUlasanRequest $request)
    {
        $ulasan = $this->ulasanService->addUlasan($request->validated(), Auth::id());
        $ulasan->load('user');

        broadcast(new UlasanAdded($ulasan))->toOthers();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ulasan berhasil ditambahkan!',
                'ulasan' => [
                    'id' => $ulasan->id,
                    'rating' => (int) $ulasan->rating,
                    'komentar' => (string) $ulasan->komentar,
                    'user_name' => $ulasan->user->name ?? 'Warga',
                    'user_avatar' => $ulasan->user->profile_picture_url ?? asset('images/bjbo.png'),
                    'created_at' => $ulasan->created_at ? $ulasan->created_at->format('d M Y') : 'Baru saja',
                ],
            ]);
        }

        return back()->with('success', 'Ulasan berhasil ditambahkan!');
    }

    public function index()
    {
        $ulasans = $this->ulasanService->getAllUlasans();

        return view('pages.admin.ulasan.index', compact('ulasans'));
    }
}
