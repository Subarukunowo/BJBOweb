<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatLaporan;
use App\Models\Laporan;
use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\Ulasan;
use App\Models\User;
use App\Services\CategoryAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(CategoryAnalyticsService $categoryAnalyticsService)
    {
        $users = User::count();
        $postings = Postingan::count();
        $ulasans = Ulasan::count();
        $laporans = Laporan::count();
        $chatReports = ChatLaporan::count();
        $blockedPostings = Postingan::where('is_auto_blocked', true)->count();
        $activePostings = Postingan::where('status', 'disetujui')->where('is_auto_blocked', false)->count();
        $transactions = Pesanan::count();

        $recentPostings = Postingan::with('user')->latest()->take(6)->get();
        $recentLaporans = Laporan::with(['user', 'postingan'])->latest()->take(5)->get();

        $categoryAnalyticsData = $categoryAnalyticsService->getAllPeriodsData();

        return view('pages.admin.index', compact(
            'users',
            'postings',
            'ulasans',
            'laporans',
            'chatReports',
            'blockedPostings',
            'activePostings',
            'transactions',
            'recentPostings',
            'recentLaporans',
            'categoryAnalyticsData'
        ));
    }

    public function categoryAnalytics(Request $request, CategoryAnalyticsService $categoryAnalyticsService): JsonResponse
    {
        $period = (string) $request->get('period', 'all_time');
        $data = $categoryAnalyticsService->getCategorySalesData($period);

        return response()->json($data);
    }
}
