<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreModerationController extends Controller
{
    protected StoreService $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function index(): View
    {
        $stores = $this->storeService->getAllStoresForAdmin(15);

        return view('pages.admin.toko.index', compact('stores'));
    }

    public function moderate(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:aktif,ditolak,dinonaktifkan',
            'reason' => 'nullable|string|max:500',
        ]);

        $this->storeService->moderateStore($id, $validated['status'], $validated['reason'] ?? null);

        return back()->with('success', 'Status toko berhasil diperbarui.');
    }
}
