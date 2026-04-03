@extends('layouts.app')

@section('title', 'Sistem Control Panel')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-10">
        <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Sistem Control Panel</h2>
        <p class="text-gray-500 mt-1 font-medium">Pusat kendali manajemen pengguna dan konfigurasi data master sistem.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-600 rounded-2xl font-bold flex items-center gap-3">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-7 bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-50">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Manajemen Pengguna</h3>
                    <p class="text-sm text-gray-400 font-medium">Total {{ count($data_users) }} akun terdaftar</p>
                </div>
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-lg shadow-indigo-100">
                    <i class="fa-solid fa-user-plus mr-2"></i> Tambah User
                </button>
            </div>

            <div class="space-y-4">
                @foreach($data_users as $u)
                <div class="group flex items-center justify-between p-5 bg-gray-50 hover:bg-white hover:shadow-xl hover:shadow-gray-100 rounded-[2rem] border border-transparent hover:border-gray-100 transition-all">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-100 group-hover:scale-110 transition-transform">
                            <span class="text-lg font-black">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-lg">{{ $u->name }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold px-2 py-0.5 bg-indigo-100 text-indigo-600 rounded-md uppercase tracking-wider">
                                    Role ID: {{ $u->role_id }}
                                </span>
                                <span class="text-gray-400 text-sm">•</span>
                                <span class="text-gray-400 text-sm font-medium">{{ $u->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-gray-400 hover:text-indigo-600 shadow-sm border border-gray-100 transition-all">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-gray-400 hover:text-rose-600 shadow-sm border border-gray-100 transition-all">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-5 space-y-8">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-50">
                <h3 class="text-xl font-bold text-gray-800 mb-2">Master Keperluan</h3>
                <p class="text-sm text-gray-400 font-medium mb-6">Kelola opsi tujuan kunjungan tamu.</p>

                <form action="{{ route('keperluan.store') }}" method="POST" class="relative mb-8">
                    @csrf
                    <input type="text" name="keterangan" required
                           class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl px-5 py-4 focus:bg-white focus:border-indigo-500 focus:ring-0 transition-all font-semibold text-gray-700"
                           placeholder="Ketik keperluan baru...">
                    <button type="submit" class="absolute right-2 top-2 bottom-2 bg-gray-900 hover:bg-black text-white px-5 rounded-xl font-bold text-sm transition-all">
                        Simpan
                    </button>
                </form>

                <div class="flex flex-wrap gap-3">
                    @foreach($data_keperluan as $k)
                    <div class="flex items-center gap-3 pl-5 pr-3 py-3 bg-indigo-50/50 hover:bg-indigo-50 text-indigo-700 rounded-2xl border border-indigo-100/50 transition-all group">
                        <span class="font-bold text-sm">{{ $k->keterangan }}</span>
                        <form action="{{ route('keperluan.destroy', $k->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg bg-white text-indigo-300 hover:text-rose-500 hover:shadow-md transition-all">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-100 relative overflow-hidden">
                <i class="fa-solid fa-shield-halved absolute -right-4 -bottom-4 text-8xl opacity-10 rotate-12"></i>
                <h4 class="text-lg font-bold mb-2 relative z-10">Keamanan Sistem</h4>
                <p class="text-indigo-100 text-sm leading-relaxed relative z-10">
                    Perubahan pada halaman ini berdampak langsung pada database master. Pastikan data yang dimasukkan sudah benar.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
