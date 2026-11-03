<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\FavoritService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritController extends Controller
{
    protected FavoritService $favoritService;

    public function __construct(FavoritService $favoritService)
    {
        $this->favoritService = $favoritService;
    }

    public function index()
    {
        $userId = Auth::id();
        $favorits = $this->favoritService->getUserFavorites($userId);

        return view('pages.user.favorit.index', compact('favorits'));
    }

    public function toggle(Request $request, int $id)
    {
        $userId = (int) Auth::id();
        $result = $this->favoritService->toggleFavorite($userId, $id);
        $favorited = (bool) ($result['favorited'] ?? false);

        broadcast(new \App\Events\PostinganFavoritToggled($userId, $id, $favorited))->toOthers();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'favorited' => $favorited,
                'postingan_id' => $id,
                'message' => $result['message'],
            ]);
        }

        toast($result['message'], 'success');

        return redirect()->back();
    }
}
