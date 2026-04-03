@extends('layouts.app') {{-- Sesuaikan dengan nama file layout di atas --}}

@section('title', 'Ulasan Layanan')

@section('content')
<div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-4xl font-black text-gray-800 tracking-tight text-left">Ulasan Layanan</h1>
        <p class="text-gray-500 mt-2 font-medium italic text-left">Pantau umpan balik pengunjung secara waktu nyata.</p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <div class="hidden lg:flex bg-white p-1.5 rounded-2xl border border-gray-100 shadow-sm">
            <button class="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 border-x border-gray-50">Bulan Ini</button>
            <button class="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400">Semua Rating</button>
        </div>
        <button class="bg-indigo-600 text-white px-8 py-4 rounded-[1.5rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center gap-3 active:scale-95">
            <i class="fa-solid fa-file-export text-sm"></i> Export Ulasan
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach($data_ulasan as $item)
        @php
            $detail = $item->survey->detail;
            $avgRating = ($detail->p1 + $detail->p2 + $detail->p3 + $detail->p4 + $detail->p5) / 5;
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
                    "{{ $item->survey->kritik_saran ?? 'Hanya memberikan rating bintang.' }}"
                </p>
            </div>

            <div class="mt-auto pt-8 border-t border-gray-50 flex flex-col text-left">
                <span class="text-gray-900 font-black text-base">{{ $item->pengunjung->nama }}</span>
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
        <p class="text-gray-400 font-bold">Belum ada ulasan yang masuk.</p>
    </div>
@endif
@endsection
