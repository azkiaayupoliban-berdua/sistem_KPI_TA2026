@extends('layouts.app')

@section('title', 'Manajemen Antrean')

@section('content')
    <div class="flex justify-between items-end mb-10">
        <div>
            <h2 class="text-4xl font-black text-gray-800 tracking-tight leading-none">Manajemen Antrean</h2>
            <p class="text-slate-400 text-sm font-medium mt-3">Monitor dan kelola riwayat antrean secara mendetail.</p>
        </div>
        <div class="flex gap-4">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                <input type="text" placeholder="Cari pengunjung..." class="pl-12 pr-6 py-3 bg-white border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm w-64 transition-all">
            </div>
            <select class="px-6 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 outline-none shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                <option>Semua Status</option>
                <option>Antre</option>
                <option>Diproses</option>
                <option>Selesai</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-gray-50 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Pengunjung</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Keperluan</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Masuk</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Penyelesaian (SLA)</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($data_kunjungan as $k)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-6 font-bold text-gray-800">#{{ $k->nomor_kunjungan }}</td>
                    <td class="px-8 py-6">
                        <p class="font-extrabold text-gray-800">{{ $k->pengunjung->nama_lengkap ?? 'Umum' }}</p>
                        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-tighter">{{ $k->pengunjung->instansi ?? '-' }}</p>
                    </td>
                    <td class="px-8 py-6 text-sm font-medium text-gray-500">{{ Str::limit($k->keperluan, 40) }}</td>

                    <td class="px-8 py-6">
                        <p class="font-bold text-gray-700 text-sm">{{ $k->created_at->format('H:i') }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">{{ $k->created_at->format('d/m/y') }}</p>
                    </td>

                    <td class="px-8 py-6 text-sm">
                        @php
                            $color = match($k->status_layanan) {
                                'Selesai' => 'bg-emerald-100 text-emerald-600',
                                'Diproses' => 'bg-indigo-100 text-indigo-600',
                                'Ditolak' => 'bg-rose-100 text-rose-600',
                                default => 'bg-amber-100 text-amber-600'
                            };
                        @endphp
                        <span class="px-4 py-1.5 {{ $color }} rounded-full text-[9px] font-black uppercase tracking-widest inline-block">
                            {{ $k->status_layanan }}
                        </span>
                    </td>

                    {{-- Kolom Penyelesaian & SLA --}}
                  <td class="px-8 py-6">
    @if($k->status_layanan == 'Diproses' && $k->estimasi_sla)
        <div class="flex flex-col gap-1">
            <div class="text-[10px] font-bold text-indigo-500 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                Target: {{ $k->estimasi_sla }} {{ $k->satuan_sla }}
            </div>
            <p class="text-[9px] text-gray-400 font-medium">Dimulai: {{ $k->created_at->format('H:i') }}</p>
        </div>

    @elseif($k->status_layanan == 'Selesai' && $k->waktu_selesai_layanan)
        <div class="flex flex-col gap-1">
            {{-- Info Tanggal & Jam Selesai --}}
            <div class="flex items-center gap-2 text-[10px] font-bold text-gray-700">
                <i class="fa-solid fa-calendar-check text-emerald-500/70"></i>
                {{ $k->waktu_selesai_layanan->format('d/m/y H:i') }}
            </div>

            <div class="flex flex-wrap gap-1 items-center">
                {{-- Durasi Realisasi --}}
                <span class="text-[9px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                    @php
                        // Menghitung selisih waktu secara dinamis
                        $selisihMenit = $k->created_at->diffInMinutes($k->waktu_selesai_layanan);
                        $selisihHari = $k->created_at->diffInDays($k->waktu_selesai_layanan);
                    @endphp

                    @if($k->satuan_sla == 'Hari')
                        {{ $selisihHari }} Hari
                    @else
                        {{ $selisihMenit }} Menit
                    @endif
                </span>

                {{-- Status SLA Badge --}}
                @if($k->status_sla)
                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md
                        {{ $k->status_sla == 'Tepat Waktu' ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-600' }}
                        text-[9px] font-black uppercase tracking-tighter">
                        <i class="fa-solid {{ $k->status_sla == 'Tepat Waktu' ? 'fa-circle-check' : 'fa-circle-exclamation' }} text-[8px]"></i>
                        {{ $k->status_sla }}
                    </div>
                @endif
            </div>
        </div>
    @else
        <span class="text-gray-300 text-[10px] italic font-medium">Belum diproses</span>
    @endif
</td>

                    <td class="px-8 py-6 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ url('/status/'.$k->nomor_kunjungan) }}" target="_blank" class="w-9 h-9 flex items-center justify-center bg-gray-50 text-gray-400 rounded-xl hover:bg-slate-800 hover:text-white transition-all shadow-sm">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </a>

                            @if($k->status_layanan == 'Antre')
                                <button type="button" onclick="bukaModalProses('{{ $k->nomor_kunjungan }}')" class="w-9 h-9 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-play text-xs ml-0.5"></i>
                                </button>
                            @elseif($k->status_layanan == 'Diproses')
                                <form action="{{ route('kunjungan.selesai', $k->nomor_kunjungan) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </button>
                                </form>
                            @else
                                <button class="w-9 h-9 flex items-center justify-center bg-gray-50 text-gray-300 rounded-xl cursor-not-allowed">
                                    <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center opacity-30">
                            <i class="fa-solid fa-folder-open text-5xl mb-4"></i>
                            <p class="font-bold uppercase tracking-widest text-xs">Data Antrean Tidak Ditemukan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
