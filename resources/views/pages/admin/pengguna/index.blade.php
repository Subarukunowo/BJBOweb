@extends('layouts.admin.main')

@section('title', 'Manajemen Pengguna - Admin BJBO')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Daftar warga terdaftar, status akun, dan kontrol pemblokiran.</p>
        </div>
        <div class="text-xs font-mono text-gray-500 bg-white border border-bjbo-border px-3 py-1.5 rounded-xl shadow-2xs">
            Total: <span class="font-bold text-gray-900">{{ count($users) }}</span> Warga
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white border border-bjbo-border rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-bjbo-surface-subtle border-b border-bjbo-border text-gray-500 font-mono uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4 font-semibold">Pengguna</th>
                        <th class="py-3.5 px-4 font-semibold">Email</th>
                        <th class="py-3.5 px-4 font-semibold">Status Akun</th>
                        <th class="py-3.5 px-4 font-semibold">Terdaftar Pada</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bjbo-border text-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/80 transition">
                            <!-- Pengguna Info -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->profile_picture_url ?? asset('images/bjbo.png') }}"
                                         alt="{{ $user->name }}"
                                         class="w-9 h-9 rounded-full object-cover border border-bjbo-border shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 text-xs truncate">{{ $user->name }}</p>
                                        <span class="text-[10px] text-gray-400 font-mono">ID: #{{ $user->id }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="py-3 px-4 font-mono text-gray-600">
                                {{ $user->email }}
                                @if($user->email_verified_at)
                                    <span class="ml-1 text-emerald-600" title="Email Terverifikasi"><i class="bi bi-patch-check-fill"></i></span>
                                @endif
                            </td>

                            <!-- Status Blokir -->
                            <td class="py-3 px-4">
                                @if($user->is_blocked)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                        <i class="bi bi-slash-circle-fill"></i> Diblokir
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="bi bi-check-circle-fill"></i> Aktif
                                    </span>
                                @endif
                            </td>

                            <!-- Tanggal -->
                            <td class="py-3 px-4 font-mono text-gray-500">
                                {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    @if($user->is_blocked)
                                        <a href="{{ route('pengguna.unblock', $user->id) }}"
                                           class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition"
                                           title="Buka Blokir">
                                            <i class="bi bi-unlock mr-1"></i> Buka Blokir
                                        </a>
                                    @else
                                        <a href="{{ route('pengguna.block', $user->id) }}"
                                           class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition"
                                           title="Blokir Akun">
                                            <i class="bi bi-shield-slash mr-1"></i> Blokir
                                        </a>
                                    @endif

                                    <form action="{{ route('pengguna.delete', $user->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus Pengguna">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">Belum ada pengguna terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
