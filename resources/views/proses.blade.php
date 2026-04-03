<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Layanan - {{ $kunjungan->nomor_kunjungan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        .gradient-bg { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        .gradient-red { background: linear-gradient(135deg, #e11d48 0%, #be123c 100%); }
        .timeline-line { position: absolute; left: 11px; top: 24px; bottom: 0; width: 2px; background-color: #F1F5F9; }
        .animate-bounce-slow { animation: bounce 3s infinite; }
        @keyframes bounce { 0%, 100% { transform: translateY(-5%); } 50% { transform: translateY(0); } }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6 bg-[#F1F5F9]">

    <div class="max-w-md w-full space-y-6">

        {{-- CARD UTAMA --}}
        <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-slate-100">
            <div class="{{ $kunjungan->status_layanan == 'Ditolak' ? 'gradient-red' : 'gradient-bg' }} p-8 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12 blur-xl"></div>

                <div class="w-16 h-16 bg-white/20 rounded-full mx-auto flex items-center justify-center mb-4 backdrop-blur-md border border-white/30 shadow-inner">
                    @if($kunjungan->status_layanan == 'Selesai')
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    @elseif($kunjungan->status_layanan == 'Ditolak')
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    @else
                        <svg class="w-8 h-8 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight mb-2 uppercase">{{ $kunjungan->status_layanan }}</h1>
                <p class="text-white/70 text-[11px] font-medium mb-6 tracking-wide">Nomor Antrean Anda</p>

                <div class="bg-white/10 backdrop-blur-md py-4 rounded-3xl border border-white/20">
                    <p class="text-[3.5rem] font-black tracking-tighter leading-none mb-1">{{ $kunjungan->nomor_kunjungan }}</p>
                </div>

                {{-- INFO ESTIMASI SLA (SUDAH DIPERBAIKI MENJADI 'Diproses') --}}
                @if($kunjungan->status_layanan == 'Diproses' && $kunjungan->estimasi_sla)
                    <div class="mt-6 p-4 bg-white/20 backdrop-blur-sm rounded-2xl border border-white/30 animate-bounce-slow">
                        <p class="text-[10px] font-black uppercase tracking-widest text-white/80 mb-1">Estimasi Waktu Tunggu</p>
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xl font-black text-white">
                                {{ $kunjungan->estimasi_sla }} {{ $kunjungan->satuan_sla }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- DURASI PELAYANAN (SAAT SELESAI) --}}
            @if($kunjungan->status_layanan == 'Selesai')
            <div class="p-6 bg-emerald-50/40 border-b border-emerald-100 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Durasi Pelayanan</p>
                    <p class="text-xl font-extrabold text-emerald-700">
                        {{ $kunjungan->created_at->diffInMinutes($kunjungan->updated_at) }} Menit
                    </p>
                </div>
                <div class="px-4 py-1.5 bg-emerald-500 text-white text-[10px] font-black rounded-full uppercase tracking-tighter shadow-sm">
                    Selesai Tepat Waktu
                </div>
            </div>
            @endif

            <div class="px-8 py-6 flex justify-between border-b border-slate-50 bg-slate-50/30">
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Pengunjung</p>
                    <p class="text-sm font-bold text-slate-700">{{ $kunjungan->pengunjung->nama_lengkap }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Waktu Terbit</p>
                    <p class="text-sm font-bold text-slate-700">{{ $kunjungan->created_at->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>

        {{-- RIWAYAT STATUS --}}
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 mb-8">
                <div class="w-2 h-5 bg-indigo-600 rounded-full"></div>
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Riwayat Status</h2>
            </div>

            <div class="relative space-y-10">
                <div class="timeline-line"></div>

                {{-- STATUS 3: SELESAI --}}
                <div class="relative pl-10">
                    <div class="absolute left-0 top-1 w-6 h-6 rounded-full ring-4 ring-white z-10 flex items-center justify-center
                        {{ $kunjungan->status_layanan == 'Selesai' ? 'bg-emerald-500 shadow-lg shadow-emerald-100' : 'bg-slate-200' }}">
                        @if($kunjungan->status_layanan == 'Selesai')
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </div>
                    <h4 class="text-sm font-bold {{ $kunjungan->status_layanan == 'Selesai' ? 'text-slate-900' : 'text-slate-300' }}">Layanan Selesai</h4>
                    <p class="text-[11px] font-medium text-slate-400">
                        {{ $kunjungan->status_layanan == 'Selesai' ? 'Tuntas pada ' . $kunjungan->updated_at->format('H:i') . ' WIB' : 'Menunggu penyelesaian' }}
                    </p>
                </div>

                {{-- STATUS 2: PROSES (SUDAH DIPERBAIKI MENJADI 'Diproses') --}}
                <div class="relative pl-10">
                    <div class="absolute left-0 top-1 w-6 h-6 rounded-full ring-4 ring-white z-10 flex items-center justify-center
                        {{ in_array($kunjungan->status_layanan, ['Diproses', 'Selesai']) ? 'bg-blue-500 shadow-lg shadow-blue-100' : 'bg-slate-200' }}">
                        <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                    </div>
                    <h4 class="text-sm font-bold {{ in_array($kunjungan->status_layanan, ['Diproses', 'Selesai']) ? 'text-slate-900' : 'text-slate-300' }}">Sedang Diproses</h4>
                    @if($kunjungan->status_layanan == 'Diproses')
                        <p class="text-[11px] font-bold text-indigo-600">Petugas sedang melayani Anda (± {{ $kunjungan->estimasi_sla }} {{ $kunjungan->satuan_sla }})</p>
                    @else
                        <p class="text-[11px] font-medium text-slate-400">Petugas memproses keperluan Anda</p>
                    @endif
                </div>

                {{-- STATUS 1: TERDAFTAR --}}
                <div class="relative pl-10">
                    <div class="absolute left-0 top-1 w-6 h-6 rounded-full ring-4 ring-white z-10 bg-amber-400 shadow-lg shadow-amber-100 flex items-center justify-center">
                        <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">Tiket Terdaftar</h4>
                    <p class="text-[11px] font-medium text-amber-600 font-bold">Pukul {{ $kunjungan->created_at->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>

        {{-- AKSI --}}
        <div class="pt-2">
            @if($kunjungan->status_layanan == 'Selesai')
                <a href="{{ route('survey.form', $kunjungan->nomor_kunjungan) }}" class="w-full mb-4 flex items-center justify-center py-5 bg-indigo-600 text-white font-extrabold rounded-[2rem] shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">
    Beri Ulasan Layanan ⭐
</a>
            @endif

            <a href="{{ route('landing') }}" class="w-full flex items-center justify-center py-5 bg-slate-900 text-white font-extrabold rounded-[2rem] shadow-xl hover:bg-black transition-all active:scale-95 gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>

        <p class="text-center text-[10px] font-black text-slate-300 uppercase tracking-[0.6em]">Digital Gate System</p>
    </div>

    {{-- AUTO RELOAD JIKA BELUM SELESAI/DITOLAK --}}
    @if(!in_array($kunjungan->status_layanan, ['Selesai', 'Ditolak']))
    <script>
        // Reload halaman setiap 15 detik untuk update status otomatis
        setTimeout(function(){ window.location.reload(1); }, 15000);
    </script>
    @endif

</body>
</html>
