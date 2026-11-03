<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Laporan\StoreLaporanRequest;
use App\Models\ChatLaporan;
use App\Services\LaporanService;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    protected LaporanService $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function submit(StoreLaporanRequest $request)
    {
        $this->laporanService->submitLaporan($request->validated(), Auth::id());

        return redirect()->back()->with('success', 'Laporan berhasil dikirim dan akan diverifikasi!');
    }

    public function index()
    {
        $laporans = $this->laporanService->getAllLaporan();
        $chatLaporans = ChatLaporan::with(['message', 'reporter', 'reportedUser', 'postingan'])->latest()->get();

        return view('pages.admin.laporan.index', compact('laporans', 'chatLaporans'));
    }
}
