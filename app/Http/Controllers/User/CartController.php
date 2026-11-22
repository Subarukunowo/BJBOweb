<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Postingan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Tampilkan halaman keranjang belanja pengguna.
     */
    public function index(): View
    {
        $userId = (int) Auth::id();
        $cartItems = Cart::query()
            ->where('user_id', $userId)
            ->with(['postingan' => function ($query) {
                $query->select([
                    'id',
                    'name',
                    'price',
                    'image',
                    'category',
                    'kondisi',
                    'lokasi',
                    'status',
                    'is_sold',
                    'user_id',
                ])->with(['user:id,name']);
            }])
            ->latest('id')
            ->get();

        $totalBelanja = (int) $cartItems->sum(function (Cart $item): int {
            return $item->subtotal;
        });

        $totalItem = (int) $cartItems->sum('quantity');

        return view('pages.user.cart.index', [
            'cartItems' => $cartItems,
            'totalBelanja' => $totalBelanja,
            'totalItem' => $totalItem,
        ]);
    }

    /**
     * Tambahkan barang ke keranjang belanja.
     */
    public function add(Request $request, int $postinganId): JsonResponse|RedirectResponse
    {
        $userId = (int) Auth::id();
        $postingan = Postingan::select(['id', 'name', 'price', 'status', 'is_sold', 'user_id'])
            ->where('id', $postinganId)
            ->first();

        if (! $postingan || $postingan->status !== 'disetujui' || $postingan->is_sold) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak tersedia atau sudah terjual.',
                ], 422);
            }

            return back()->with('error', 'Barang tidak tersedia atau sudah terjual.');
        }

        if ($postingan->user_id === $userId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menambahkan barang milik sendiri ke keranjang.',
                ], 422);
            }

            return back()->with('error', 'Anda tidak dapat menambahkan barang milik sendiri ke keranjang.');
        }

        $cart = Cart::firstOrNew([
            'user_id' => $userId,
            'postingan_id' => $postinganId,
        ]);

        $cart->quantity = $cart->exists ? ($cart->quantity + 1) : 1;
        $cart->save();

        $totalCount = (int) Cart::where('user_id', $userId)->sum('quantity');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil ditambahkan ke keranjang!',
                'cart_count' => $totalCount,
            ]);
        }

        return back()->with('success', 'Barang berhasil ditambahkan ke keranjang!');
    }

    /**
     * Perbarui jumlah item di keranjang.
     */
    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $userId = (int) Auth::id();
        $cart = Cart::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $cart->update([
            'quantity' => (int) $validated['quantity'],
        ]);

        if ($request->expectsJson()) {
            $totalCount = (int) Cart::where('user_id', $userId)->sum('quantity');

            return response()->json([
                'success' => true,
                'subtotal' => $cart->formatted_subtotal,
                'cart_count' => $totalCount,
            ]);
        }

        return back()->with('success', 'Jumlah barang berhasil diperbarui.');
    }

    /**
     * Hapus barang dari keranjang belanja.
     */
    public function remove(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $userId = (int) Auth::id();
        $cart = Cart::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $cart->delete();

        $totalCount = (int) Cart::where('user_id', $userId)->sum('quantity');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Barang dihapus dari keranjang.',
                'cart_count' => $totalCount,
            ]);
        }

        return back()->with('success', 'Barang dihapus dari keranjang.');
    }

    /**
     * Dapatkan jumlah item di keranjang untuk lencana navbar.
     */
    public function count(): JsonResponse
    {
        $userId = (int) Auth::id();
        $count = (int) Cart::where('user_id', $userId)->sum('quantity');

        return response()->json([
            'count' => $count,
        ]);
    }
}
