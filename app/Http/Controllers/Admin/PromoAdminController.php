<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Services\PromoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromoAdminController extends Controller
{
    protected PromoService $promoService;

    public function __construct(PromoService $promoService)
    {
        $this->promoService = $promoService;
    }

    public function index(): View
    {
        $promos = $this->promoService->getAllPromos(12);

        return view('pages.admin.promo.index', compact('promos'));
    }

    public function create(): View
    {
        return view('pages.admin.promo.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:banner_iklan,diskon_persen,diskon_nominal,flash_sale',
            'code' => 'nullable|string|max:50|unique:promos,code',
            'discount_percentage' => 'nullable|numeric|min:1|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $this->promoService->createPromo($validated, $request->file('banner_image'));

        return redirect()->route('admin.promo.index')->with('success', 'Promo / Iklan berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $promo = Promo::findOrFail($id);

        return view('pages.admin.promo.edit', compact('promo'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $promo = Promo::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:banner_iklan,diskon_persen,diskon_nominal,flash_sale',
            'code' => 'nullable|string|max:50|unique:promos,code,'.$promo->id,
            'discount_percentage' => 'nullable|numeric|min:1|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $this->promoService->updatePromo($promo, $validated, $request->file('banner_image'));

        return redirect()->route('admin.promo.index')->with('success', 'Promo berhasil diperbarui.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $promo = Promo::findOrFail($id);
        $this->promoService->togglePromoStatus($promo);

        return back()->with('success', 'Status promo berhasil diubah.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $promo = Promo::findOrFail($id);
        $this->promoService->deletePromo($promo);

        return redirect()->route('admin.promo.index')->with('success', 'Promo berhasil dihapus.');
    }
}
