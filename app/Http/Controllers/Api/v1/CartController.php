<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Cart;
use App\Models\Postingan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $cartItems = Cart::query()
            ->where('user_id', $user->id)
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

        $totalBelanja = (int) $cartItems->sum(fn (Cart $item) => $item->subtotal);
        $totalItems = (int) $cartItems->sum('quantity');

        return $this->successResponse([
            'items' => $cartItems,
            'total_price' => $totalBelanja,
            'total_items' => $totalItems,
        ], 'Keranjang belanja berhasil dimuat.');
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'postingan_id' => 'required|integer|exists:postingans,id',
            'quantity' => 'nullable|integer|min:1|max:99',
        ]);

        $postingan = Postingan::find($validated['postingan_id']);

        if (! $postingan || $postingan->status !== 'disetujui' || $postingan->is_sold) {
            return $this->errorResponse('Barang tidak tersedia atau sudah terjual.', 422);
        }

        if ($postingan->user_id === $user->id) {
            return $this->errorResponse('Anda tidak dapat menambahkan barang milik sendiri ke keranjang.', 422);
        }

        $cart = Cart::firstOrNew([
            'user_id' => $user->id,
            'postingan_id' => $postingan->id,
        ]);

        $qtyToAdd = $validated['quantity'] ?? 1;
        $cart->quantity = $cart->exists ? ($cart->quantity + $qtyToAdd) : $qtyToAdd;
        $cart->save();

        $totalCount = (int) Cart::where('user_id', $user->id)->sum('quantity');

        return $this->successResponse([
            'cart_item' => $cart->load('postingan'),
            'cart_count' => $totalCount,
        ], 'Barang berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = Cart::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $cart) {
            return $this->errorResponse('Item keranjang tidak ditemukan.', 404);
        }

        $cart->update(['quantity' => (int) $validated['quantity']]);

        $totalCount = (int) Cart::where('user_id', $user->id)->sum('quantity');

        return $this->successResponse([
            'cart_item' => $cart,
            'cart_count' => $totalCount,
        ], 'Jumlah barang di keranjang berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $cart = Cart::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $cart) {
            return $this->errorResponse('Item keranjang tidak ditemukan.', 404);
        }

        $cart->delete();

        $totalCount = (int) Cart::where('user_id', $user->id)->sum('quantity');

        return $this->successResponse([
            'cart_count' => $totalCount,
        ], 'Item keranjang berhasil dihapus.');
    }
}
