@extends('layouts.app')

@section('title', $judul_dashboard)

@section('content')
<div class="min-h-screen bg-[#f6f7fb] px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6 mb-8">
        <div class="space-y-2">
            <h1 class="text-2xl md:text-4xl font-black text-slate-900 leading-tight">
                Dashboard Admin
            </h1>

            <div class="flex flex-wrap items-center gap-2">
                <span class="px-4 py-1.5 bg-emerald-100 text-emerald-600 rounded-full text-[10px] sm:text-[11px] font-black flex items-center gap-2 shadow-sm">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    @if($user->role_id==1 || $user->role_id==3)
                        Monitoring Active
                    @else
                        Petugas Aktif
                    @endif
                </span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full xl:w-auto">

            @php
                $isSuper=$user->role_id==1 || $user->role_id==3;
            @endphp

            <div class="relative w-full sm:w-auto">
                <select onchange="filterProdi(this.value)"
                    {{ !$isSuper ? 'disabled' : '' }}
                    class="w-full sm:w-[280px] bg-white border border-slate-200 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-100 outline-none appearance-none transition-all shadow-sm {{ !$isSuper ? 'bg-slate-100 cursor-not-allowed text-slate-500' : '' }}">

                    @if($isSuper)
                        <option value="">🌍 Seluruh Program Studi</option>
                        @foreach($daftar_prodi as $p)
                            <option value="{{ $p->id }}" {{ request('prodi_id')==$p->id ? 'selected' : '' }}>
                                🎓 {{ $p->nama }}
                            </option>
                        @endforeach
                    @else
                        <option selected>
                            🎓 {{ $user->prodi->nama ?? 'Prodi Tidak Ditemukan' }}
                        </option>
                    @endif

                </select>

                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 text-xs">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>

            {{-- TOMBOL EKSPOR --}}
            <button type="button" onclick="openExportModal()"
                class="bg-gradient-to-r from-indigo-500 via-purple-500 to-orange-400 text-white px-6 py-3 rounded-2xl font-black text-sm shadow-lg hover:scale-[1.02] transition-all">
                <i class="fa-solid fa-file-export mr-2"></i>
                Laporan Pengunjung
            </button>

        </div>
    </div>

    {{-- CARD STATISTIK --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 mb-10">

        {{-- TOTAL --}}
        <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-[10px] sm:text-[11px] uppercase font-black tracking-widest text-slate-400 mb-1">
                    Total Kunjungan
                </p>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900">
                    {{ $total_kunjungan }}
                </h2>
            </div>
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl sm:rounded-3xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl sm:text-2xl shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        {{-- SLA --}}
        <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-[10px] sm:text-[11px] uppercase font-black tracking-widest text-slate-400 mb-1">
                    Efektivitas (SLA)
                </p>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900">
                    {{ $efektivitas_persen }}%
                </h2>
            </div>
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl sm:rounded-3xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl sm:text-2xl shrink-0">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        {{-- SURVEI --}}
        <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow sm:col-span-2 xl:col-span-1">
            <div>
                <p class="text-[10px] sm:text-[11px] uppercase font-black tracking-widest text-slate-400 mb-1">
                    Kualitas (Survei)
                </p>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900">
                    {{ $kualitas_rating }}
                </h2>
            </div>
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl sm:rounded-3xl bg-amber-100 text-amber-500 flex items-center justify-center text-xl sm:text-2xl shrink-0">
                <i class="fa-solid fa-star"></i>
            </div>
        </div>

    </div>

    {{-- TITLE SECTION ANTREAN --}}
    <div class="flex items-center justify-between gap-3 mb-6 w-full">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-8 bg-indigo-600 rounded-full"></div>
            <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                Antrean Layanan
            </h3>
        </div>

        @if($data_kunjungan->count() > 6)
            <a href="{{ route('dashboard.antrean') }}" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-black text-xs uppercase tracking-widest rounded-2xl shadow-sm transition-all flex items-center gap-2 shrink-0">
                <span>Lihat semua antrean</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        @endif
    </div>

    {{-- LIST ANTREAN --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        @forelse($data_kunjungan->take(6) as $k)

            @php
                $isAntre = $k->status_layanan == 'Antre';
                $isDiproses = $k->status_layanan == 'Diproses';
                $isSelesai = $k->status_layanan == 'Selesai';
                $isDitolak = $k->status_layanan == 'Ditolak';

                $borderClass = $isDitolak 
                    ? 'border-rose-500 bg-rose-50/20' 
                    : ($isAntre 
                        ? 'border-amber-300' 
                        : ($isDiproses 
                            ? 'border-blue-300' 
                            : 'border-emerald-300'
                        )
                    );
                $badgeClass = $isAntre ? 'bg-amber-100 text-amber-600' : ($isDiproses ? 'bg-blue-100 text-blue-600' : 'bg-emerald-100 text-emerald-600');
            @endphp

            <div class="bg-white rounded-[2rem] border-2 {{ $borderClass }} p-4 sm:p-5 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group relative overflow-hidden">
                
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-slate-50 rounded-full -z-0 group-hover:scale-150 transition-transform duration-500"></div>

                <div class="relative z-10 flex flex-col h-full">
                    {{-- HEADER CARD --}}
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex-1">
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest
                                {{ $k->status_layanan == 'Ditolak' 
                                    ? 'bg-rose-100 text-rose-600 ring-1 ring-rose-200' 
                                    : $badgeClass }}">
                                {{ $k->status_layanan }}
                            </span>
                            <h4 class="mt-3 text-lg font-black text-slate-900 leading-tight">
                                {{ $k->pengunjung->nama_lengkap }}
                            </h4>
                            <p class="text-xs font-bold text-slate-400 mt-0.5">
                                <i class="fa-solid fa-building text-[10px] mr-1"></i> {{ $k->pengunjung->asal_instansi ?? '-' }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <h2 class="text-xs sm:text-sm font-black text-slate-800 tracking-tight bg-slate-100 px-2.5 py-1 rounded-xl inline-block">
                                {{ $k->nomor_kunjungan }}
                            </h2>
                            <p class="text-[10px] text-slate-400 font-black mt-1 pr-1">
                                <i class="fa-regular fa-clock mr-1"></i>{{ $k->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>

                    {{-- KEPERLUAN --}}
                    <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-100 mb-4 min-h-[100px] flex flex-col">
                        <p class="text-[9px] uppercase font-black tracking-widest text-indigo-500 mb-2">
                            Keperluan
                        </p>
                        <div class="mb-2">
                            <p class="text-[8px] uppercase font-black text-slate-400 tracking-widest">
                                Jenis
                            </p>
                            <p class="text-xs font-bold text-slate-700 italic">
                                {{ $k->keperluan_master->keterangan ?? '-' }}
                            </p>
                        </div>
                        @if(!empty($k->keperluan))
                            <div>
                                <p class="text-[8px] uppercase font-black text-slate-400 tracking-widest">
                                    Detail
                                </p>
                                <p class="text-xs font-medium text-slate-600 leading-relaxed italic line-clamp-2">
                                    "{{ $k->keperluan }}"
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- MODIFIKASI FOOTER BUTTONS: FULL SINKRON BULAT (ROUNDED-FULL) --}}
                    <div class="mt-auto flex items-center justify-between gap-2 pt-3 border-t border-slate-100">
                        
                        {{-- GRUP KIRI: AKSI UTAMA (MULAI / SELESAI / BADGE SELESAI) --}}
                        <div class="flex items-center gap-2">
                            @if($isAntre)
                                <div class="flex flex-row items-center gap-2 w-full">
                                {{-- Tombol Mulai dengan Teks Keterangan --}}
                                <button type="button" onclick="bukaModalProses('{{ $k->nomor_kunjungan }}')"
                                    class="flex-1 h-9 px-4 flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all shadow-sm active:scale-[0.98]">
                                    <i class="fa-solid fa-play text-[10px] pl-0.5"></i>
                                    <span class="text-[11px] font-black uppercase tracking-wider">Mulai</span>
                                </button>
                                
                                {{-- Tombol Tolak dengan Teks Keterangan --}}
                                <button type="button" onclick="bukaModalTolak('{{ $k->id }}')"
                                    class="flex-1 h-9 px-4 flex items-center justify-center gap-1.5 bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white rounded-xl transition-all active:scale-[0.98]">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                    <span class="text-[11px] font-black uppercase tracking-wider">Tolak</span>
                                </button>
                            </div>
                            @elseif($isDiproses)
                                {{-- Tombol Selesai (Bulat Hijau) --}}
                                <form id="form-selesai-{{ $k->id }}" action="{{ route('kunjungan.selesai', $k->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="button" onclick="konfirmasiSelesai('{{ $k->id }}', '{{ $k->nomor_kunjungan }}')" title="Selesaikan Antrean"
                                        class="w-9 h-9 flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white rounded-full transition-all shadow-sm active:scale-95 shrink-0">
                                        <i class="fa-solid fa-check text-sm"></i>
                                    </button>
                                </form>
                            @elseif($isSelesai)
                                {{-- Penanda Selesai Bulat Kecil --}}
                                <div title="Selesai" class="w-9 h-9 flex items-center justify-center bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-full shrink-0">
                                    <i class="fa-solid fa-check-double text-xs"></i>
                                </div>
                            @endif
                        </div>

                        {{-- GRUP KANAN: UTILITY BUTTONS (EMAIL, PIMPINAN, WHATSAPP) --}}
                        <div class="flex items-center gap-2">
                            {{-- Hanya Tampil Jika Statusnya Selesai --}}
                            @if($isSelesai)
                                {{-- Tombol Kirim Email (Bulat Biru Soft) --}}
                                <form action="{{ route('kunjungan.kirim-email', ['id' => $k->id]) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" title="Kirim Email" class="w-9 h-9 flex items-center justify-center bg-sky-50 hover:bg-sky-100 text-sky-600 rounded-full transition-all active:scale-95 shrink-0">
                                        <i class="fa-regular fa-envelope text-sm"></i>
                                    </button>
                                </form>

                                {{-- Tombol Teruskan ke Pimpinan (Bulat Amber Soft) --}}
                                <form action="{{ route('kunjungan.tanggapan', $k->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" title="Teruskan ke Pimpinan" class="w-9 h-9 flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-full transition-all active:scale-95 shrink-0">
                                        <i class="fa-solid fa-share-nodes text-sm"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-span-full bg-white border-2 border-dashed border-slate-200 rounded-[2.5rem] py-20 px-6 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-300 text-3xl mb-5">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <h3 class="text-xl font-black text-slate-500">Belum Ada Antrean</h3>
                <p class="text-slate-400 mt-2 text-sm max-w-xs mx-auto">
                    Antrean baru yang masuk akan muncul secara otomatis di sini.
                </p>
            </div>
        @endforelse

    </div>

    {{-- TITLE SECTION ULASAN TERBARU --}}
    <div class="flex items-center justify-between gap-3 mt-14 mb-6 w-full">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-8 bg-amber-500 rounded-full"></div>
            <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                Ulasan Terbaru
            </h3>
        </div>

        @if(($data_ulasan ?? collect())->count() > 3)
            <a href="{{ route('dashboard.ulasan') }}" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-black text-xs uppercase tracking-widest rounded-2xl shadow-sm transition-all flex items-center gap-2 shrink-0">
                <span>Lihat semua ulasan</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        @endif
    </div>

    {{-- LIST 3 ULASAN TERBARU --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
        @forelse(($data_ulasan ?? collect())->take(3) as $item)
            @php
                $detail = $item->survey->detail ?? null;
                $avgRating = $detail ? ($detail->p1 + $detail->p2 + $detail->p3 + $detail->p4 + $detail->p5) / 5 : 0;
                $ratingBulat = round($avgRating);
            @endphp
            <div class="bg-white p-6 sm:p-8 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all flex flex-col justify-between h-full group">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex gap-1 text-amber-400">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star text-xs {{ $i <= $ratingBulat ? '' : 'text-slate-100' }}"></i>
                            @endfor
                        </div>
                        <span class="bg-slate-50 text-slate-400 text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-widest border border-slate-100 group-hover:bg-indigo-50 group-hover:text-indigo-500 group-hover:border-indigo-100 transition-colors">
                            {{ $item->pengunjung->asal_instansi ?? 'UMUM' }}
                        </span>
                    </div>
                    <p class="text-slate-700 font-bold text-sm sm:text-base leading-relaxed mb-6 text-left italic">
                        "{{ $item->survey->kritik_saran ?? 'Hanya memberikan rating bintang.' }}"
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-50 flex flex-col text-left">
                    <span class="text-slate-900 font-black text-sm">
                        {{ $item->pengunjung->nama_lengkap ?? 'Pengunjung' }}
                    </span>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                            {{ $item->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white border-2 border-dashed border-slate-200 rounded-[2rem] py-10 px-6 text-center">
                <p class="text-slate-400 font-bold text-sm">Belum ada ulasan yang masuk.</p>
            </div>
        @endforelse
    </div>

</div>

{{-- MODAL EKSPOR PERIODE --}}
<div id="exportModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-[2rem] p-6 sm:p-8 shadow-2xl transform transition-all scale-95 opacity-0" id="modalContentExport">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">Periode Laporan Pengunjung</h2>
                <p class="text-xs text-slate-400 mt-1 font-medium">Tentukan tanggal penarikan data laporan</p>
            </div>
            <button onclick="closeExportModal()" class="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="space-y-4 mb-6">
            <div class="space-y-2">
                <label class="text-[10px] uppercase font-black text-slate-400 ml-2 tracking-widest">Tanggal Awal</label>
                <input type="date" id="exportStartDate" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 font-bold focus:ring-4 focus:ring-indigo-100 outline-none transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] uppercase font-black text-slate-400 ml-2 tracking-widest">Tanggal Akhir</label>
                <input type="date" id="exportEndDate" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 font-bold focus:ring-4 focus:ring-indigo-100 outline-none transition-all">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button onclick="downloadLaporan('xlsx')" class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-emerald-100">
                <i class="fa-regular fa-file-excel text-sm"></i> Excel
            </button>
            <button onclick="downloadLaporan('pdf')" class="flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-rose-100">
                <i class="fa-regular fa-file-pdf text-sm"></i> PDF
            </button>
        </div>
    </div>
</div>

{{-- MODAL TOLAK --}}
<div id="modalTolak" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[2rem] p-6 shadow-2xl">
        <div class="mb-5">
            <h2 class="text-xl font-black text-slate-900">Tolak Antrean</h2>
            <p class="text-sm text-slate-400 mt-1">Wajib isi alasan penolakan</p>
        </div>
        <form id="formTolak" method="POST">
            @csrf
            <textarea name="alasan_tolak" required
                class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-sm font-medium focus:ring-4 focus:ring-rose-100 outline-none"
                placeholder="Contoh: Dokumen tidak lengkap / data tidak valid"></textarea>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="tutupModalTolak()" class="flex-1 py-3 rounded-2xl bg-slate-100 text-slate-600 font-black text-xs uppercase">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-2xl bg-rose-600 text-white font-black text-xs uppercase shadow-lg">Kirim Penolakan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL SLA --}}
<div id="modalProsesSLA" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] p-6 sm:p-10 shadow-2xl transform transition-all scale-95 opacity-0" id="modalContentSLA">
        <div class="text-center mb-6">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl sm:text-3xl mx-auto mb-5 shadow-inner">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Estimasi Layanan</h2>
            <p class="text-slate-400 text-xs sm:text-sm mt-2 font-medium">Tentukan waktu pelayanan secara realistis</p>
        </div>
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200">
            <p class="text-[10px] uppercase font-black tracking-widest text-amber-600 mb-2">Perhatian</p>
            <p class="text-xs text-amber-700 font-semibold leading-relaxed">
                Estimasi hanya bisa diinput <b>1 kali</b>. Pastikan sudah sesuai dengan <b>jenis keperluan</b> dan perkiraan waktu pengerjaan layanan.
            </p>
        </div>
        <form id="formSLA" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-5 mb-8">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-black text-slate-400 ml-2 tracking-widest">Durasi Pelayanan</label>
                    <input type="number" name="estimasi_sla" required 
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold focus:ring-4 focus:ring-indigo-100 outline-none transition-all placeholder:text-slate-300"
                        placeholder="Contoh: 15">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-black text-slate-400 ml-2 tracking-widest">Satuan Waktu</label>
                    <div class="relative">
                        <select name="satuan_sla" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold focus:ring-4 focus:ring-indigo-100 outline-none appearance-none transition-all">
                            <option value="Menit">Menit</option>
                            <option value="Hari">Hari</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="tutupModal()" class="order-2 sm:order-1 flex-1 py-4 rounded-2xl bg-slate-100 text-slate-500 font-black uppercase text-[11px] tracking-widest hover:bg-slate-200 transition-colors">Kembali</button>
                <button type="submit" class="order-1 sm:order-2 flex-1 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase text-[11px] tracking-widest shadow-lg shadow-indigo-200 transition-all">Mulai Sekarang</button>
            </div>
        </form>
    </div>
</div>

{{-- JAVASCRIPT LOGIC --}}
<script>
    // LOGIKA MODAL EKSPOR LAPORAN
    function openExportModal() {
        document.getElementById('exportStartDate').value = '';
        document.getElementById('exportEndDate').value = '';
        
        const modal = document.getElementById('exportModal');
        const content = document.getElementById('modalContentExport');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeExportModal() {
        const modal = document.getElementById('exportModal');
        const content = document.getElementById('modalContentExport');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 200);
    }

    function downloadLaporan(type) {
        const startDate = document.getElementById('exportStartDate').value;
        const endDate = document.getElementById('exportEndDate').value;

        if (!startDate || !endDate) {
            alert('Silakan tentukan rentang tanggal awal dan akhir terlebih dahulu.');
            return;
        }

        const urlParams = new URLSearchParams(window.location.search);
        const prodiId = urlParams.get('prodi_id') || '';

        window.location = '/laporan/pengunjung' + 
                          '?type=' + type + 
                          '&start_date=' + startDate + 
                          '&end_date=' + endDate + 
                          '&prodi_id=' + prodiId;
    }

    // LOGIKA MODAL ESTIMASI SLA
    function bukaModalProses(nomor) {
        const modal = document.getElementById('modalProsesSLA');
        const content = document.getElementById('modalContentSLA');
        const form = document.getElementById('formSLA');
        
        form.action = `/dashboard/mulai-proses/${nomor}`;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function tutupModal() {
        const modal = document.getElementById('modalProsesSLA');
        const content = document.getElementById('modalContentSLA');
        
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 200);
    }

    function konfirmasiSelesai(id, nomor) {
        if(confirm(`Selesaikan antrean nomor ${nomor}?`)) {
            document.getElementById(`form-selesai-${id}`).submit();
        }
    }

    // LOGIKA MODAL TOLAK
    function bukaModalTolak(id) {
        const form = document.getElementById('formTolak');
        form.action = `/dashboard/tolak/${id}`;
        document.getElementById('modalTolak').classList.remove('hidden');
        document.getElementById('modalTolak').classList.add('flex');
    }

    function tutupModalTolak() {
        document.getElementById('modalTolak').classList.add('hidden');
        document.getElementById('modalTolak').classList.remove('flex');
    }

    // LOGIKA FILTER PRODI
    function filterProdi(prodiId){
        const url=new URL(window.location.href);
        if(prodiId){
            url.searchParams.set('prodi_id',prodiId);
        }else{
            url.searchParams.delete('prodi_id');
        }
        window.location.href=url.toString();
    }
    // Ambil jumlah total kunjungan awal saat halaman pertama kali dimuat
let currentTotalKunjungan = parseInt("{{ $total_kunjungan }}") || 0;

function cekDataBaru() {
    // Tetap tangkap parameter prodi_id jika admin sedang melakukan filter
    const urlParams = new URLSearchParams(window.location.search);
    const prodiId = urlParams.get('prodi_id') || '';

    // Request ke server di balik layar (background request)
    fetch(`/dashboard/cek-total?prodi_id=${prodiId}`)
        .then(response => response.json())
        .then(data => {
            // Jika jumlah di server lebih besar dari data sekarang, refresh halaman
            if (data.total_kunjungan > currentTotalKunjungan) {
                window.location.reload();
            }
        })
        .catch(error => console.error('Gagal mengecek data baru:', error));
}

// 15000 milidetik = 15 detik
setInterval(cekDataBaru, 15000);
</script>
@endsection