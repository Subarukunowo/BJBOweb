<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatLaporan;
use App\Models\Laporan;
use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\Ulasan;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
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
            'recentLaporans'
        ));
    }
}
