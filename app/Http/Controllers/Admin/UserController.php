<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PostinganService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected PostinganService $postinganService;

    public function __construct(PostinganService $postinganService)
    {
        $this->postinganService = $postinganService;
    }

    public function index()
    {
        $postingans = $this->postinganService->getApprovedPostings();
        $user = Auth::user();
        $recommendations = app(\App\Services\RecommendationEngineService::class)->getRecommendationsForUser(Auth::id(), 6);
        $banners = app(\App\Services\PromoService::class)->getActiveBanners();

        return view('pages.user.index', compact('postingans', 'user', 'recommendations', 'banners'));
    }

    public function show(int $id)
    {
        $detail = $this->postinganService->getPostinganDetail($id);
        $postingan = $detail['postingan'];
        $postingansama = $detail['related'];

        app(\App\Services\RecommendationEngineService::class)->logProductView(Auth::id(), $id);

        return view('pages.user.postingan.detail', compact('postingan', 'postingansama'));
    }

    public function about()
    {
        return view('about');
    }
}
