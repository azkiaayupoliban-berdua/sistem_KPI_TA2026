@extends('layouts.app')

@section('title', 'Ulasan Layanan')

@section('content')

@php
    $isSuper = $user->role_id == 1 || $user->role_id == 3;
@endphp

<div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">

    <div>
        <h1 class="text-4xl font-black text-gray-800 tracking-tight text-left">
            Ulasan Layanan
        </h1>

        <p class="text-gray-500 mt-2 font-medium italic text-left">
            Pantau umpan balik pengunjung secara waktu nyata.
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">

        {{-- FILTER PRODI --}}
        <form action="{{ route('dashboard.ulasan') }}" method="GET">

            <div class="relative w-full sm:w-auto">

                <select name="prodi_id"
                    onchange="this.form.submit()"
                    {{ !$isSuper ? 'disabled' : '' }}
                    class="w-full sm:w-[280px] bg-white border border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-100 outline-none appearance-none transition-all shadow-sm {{ !$isSuper ? 'bg-slate-100 cursor-not-allowed text-slate-500' : '' }}">

                    @if($isSuper)

                        <option value="">🌍 Seluruh Program Studi</option>

                        @foreach($daftar_prodi as $p)
                            <option value="{{ $p->id }}"
                                {{ request('prodi_id') == $p->id ? 'selected' : '' }}>
                                🎓 {{ $p->nama }}
                            </option>
                        @endforeach

                    @else
                        {{-- Menambahkan atribut value dengan ID asli milik user agar tidak mengirim teks emoji --}}
                        <option value="{{ $user->prodi_id }}" selected>
                            🎓 {{ $user->prodi->nama ?? 'Prodi Tidak Ditemukan' }}
                        </option>
                    @endif

                </select>

                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 text-xs">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>

            </div>

        </form>

        <div class="hidden lg:flex bg-white p-1.5 rounded-2xl border border-gray-100 shadow-sm">

            <button class="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 border-x border-gray-50">
                Bulan Ini
            </button>

            <button class="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400">
                Semua Rating
            </button>

        </div>

        <button type="button" onclick="openExportModal()"
        class="bg-gradient-to-r from-indigo-500 via-purple-500 to-orange-400 text-white px-6 py-3 rounded-2xl font-black text-sm shadow-lg hover:scale-[1.02] transition-all">
        <i class="fa-solid fa-file-export mr-2"></i>
            Laporan Ulasan
        </button>

    </div>

</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

    @foreach($data_ulasan as $item)

        @php
            $detail = $item->survey->detail;

            $avgRating =
                ($detail->p1 +
                $detail->p2 +
                $detail->p3 +
                $detail->p4 +
                $detail->p5) / 5;

            $ratingBulat = round($avgRating);
        @endphp

        <div class="bg-white p-10 rounded-[3rem] border border-gray-50 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group">

            <div class="flex justify-between items-start mb-8">

                <div class="flex gap-1 text-amber-400">

                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star text-sm {{ $i <= $ratingBulat ? '' : 'text-gray-100' }}"></i>
                    @endfor

                </div>

                <span class="bg-slate-50 text-slate-400 text-[9px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest border border-slate-100 group-hover:bg-indigo-50 group-hover:text-indigo-500 group-hover:border-indigo-100 transition-colors">
                    {{ $item->pengunjung->asal_instansi ?? 'UMUM' }}
                </span>

            </div>

            <div class="flex-grow">

                <p class="text-gray-800 font-bold text-xl leading-relaxed mb-10 text-left">
                    "{!! e($item->survey->kritik_saran ?? 'Hanya memberikan rating bintang.') !!}"
                </p>

            </div>

            <div class="mt-auto pt-8 border-t border-gray-50 flex flex-col text-left">

                <span class="text-gray-900 font-black text-base">
                    {{ $item->pengunjung->nama_lengkap ?? 'Pengunjung' }}
                </span>

                <div class="flex items-center gap-2 mt-1">

                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>

                    <span class="text-gray-400 text-[11px] font-bold uppercase tracking-wider">
                        {{ $item->created_at->diffForHumans() }}
                    </span>

                </div>

            </div>

        </div>

    @endforeach

</div>

@if($data_ulasan->isEmpty())

    <div class="py-20 text-center">

        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
            <i class="fa-solid fa-comment-slash text-2xl"></i>
        </div>

        <p class="text-gray-400 font-bold">
            Belum ada ulasan yang masuk.
        </p>

    </div>

@endif

{{-- MODAL EKSPOR PERIODE (Khusus Laporan Ulasan) --}}
<div id="exportModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden border border-slate-100 transform transition-all animate-in fade-in zoom-in-95 duration-150">
        
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800">Periode Laporan Ulasan</h3>
            <button onclick="closeExportModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Awal</label>
                <input type="date" id="exportStartDate" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Akhir</label>
                <input type="date" id="exportEndDate" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 grid grid-cols-2 gap-3">
            <button id="btnExcel" class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-xl font-bold text-sm transition-colors shadow-sm shadow-emerald-100">
                <i class="fa-regular fa-file-excel"></i> Excel
            </button>
            <button id="btnPdf" class="flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white py-2.5 rounded-xl font-bold text-sm transition-colors shadow-sm shadow-rose-100">
                <i class="fa-regular fa-file-pdf"></i> PDF
            </button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
let exportRoute = '';

function openExportModal(laporan){
    exportRoute = laporan; // Menyimpan konteks data 'ulasan'
    document.getElementById('exportStartDate').value = '';
    document.getElementById('exportEndDate').value = '';
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal(){
    document.getElementById('exportModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnExcel').addEventListener('click', function(){
        downloadLaporan('xlsx');
    });

    document.getElementById('btnPdf').addEventListener('click', function(){
        downloadLaporan('pdf');
    });
});

function downloadLaporan(type){
    const startDate = document.getElementById('exportStartDate').value;
    const endDate = document.getElementById('exportEndDate').value;

    if(!startDate || !endDate){
        alert('Silakan pilih rentang tanggal terlebih dahulu.');
        return;
    }

    // Ambil elemen select prodi
    const prodiSelect = document.querySelector('[name=prodi_id]');
    let prodi = '';

    if (prodiSelect) {
        // JIKA select sedang disabled (User biasa/bukan super), gunakan properti dataset atau fallback dari backend
        // Untuk amannya, kita ambil value-nya langsung. Jika value kosong/berisi teks nama, Laravel yang akan membersihkan.
        prodi = prodiSelect.value;
    }

    window.location = '/laporan/' + exportRoute + 
                      '?type=' + type + 
                      '&start_date=' + startDate + 
                      '&end_date=' + endDate + 
                      '&prodi_id=' + encodeURIComponent(prodi);
}
</script>
@endpush