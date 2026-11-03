<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Postingan;

class PostingController extends Controller
{
    public function index()
    {
        $postingans = Postingan::with('user')->latest()->get();
        confirmDelete('Hapus Data!', 'Apakah Anda yakin ingin menghapus postingan ini?');

        return view('pages.admin.postingan.index', compact('postingans'));
    }

    public function delete(int $id)
    {
        $postingan = Postingan::findOrFail($id);
        $postingan->delete();

        return redirect()->route('admin.postings')->with('success', 'Postingan berhasil dihapus.');
    }

    public function updateStatus(int $id)
    {
        $postingan = Postingan::findOrFail($id);

        if ($postingan->status === 'disetujui' && ! $postingan->is_auto_blocked) {
            $postingan->status = 'belum disetujui';
        } else {
            $postingan->status = 'disetujui';
            $postingan->is_auto_blocked = false;
            $postingan->block_reason = null;
        }

        $postingan->save();

        return redirect()->route('admin.postings')->with('success', 'Status postingan berhasil diperbarui.');
    }
}
