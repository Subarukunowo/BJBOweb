<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'sender' => 'required|string',
            'message' => 'required|string',
        ]);

        // Simpan pesan dengan user_id dari pengguna yang sedang login
        Message::create([
            'user_id' => auth()->id(),
            'sender' => $request->sender,
            'message' => $request->message,
        ]);

        return response()->json(['message' => 'Pesan berhasil dikirim']);
    }
}
