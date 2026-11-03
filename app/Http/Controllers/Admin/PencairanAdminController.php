<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RekberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PencairanAdminController extends Controller
{
    protected RekberService $rekberService;

    public function __construct(RekberService $rekberService)
    {
        $this->rekberService = $rekberService;
    }

    public function index(): View
    {
        $withdrawals = $this->rekberService->getAllWithdrawalsForAdmin(15);

        return view('pages.admin.pencairan.index', compact('withdrawals'));
    }

    public function process(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:diproses,selesai,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $this->rekberService->processPencairan($id, $validated['status'], $validated['catatan'] ?? null);

        return back()->with('success', 'Status pencairan dana berhasil diperbarui.');
    }
}
