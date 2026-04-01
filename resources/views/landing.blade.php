<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pelayanan Publik & KPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 font-sans text-slate-800">

    <div class="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-12 gap-0 bg-white rounded-[2rem] shadow-2xl overflow-hidden">
        
        <div class="lg:col-span-5 bg-gradient-to-br from-blue-600 to-purple-700 p-10 text-white flex flex-col justify-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-xs font-bold mb-8">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Sistem Beroperasi
                </div>
                <h1 class="text-4xl font-black leading-tight mb-4">Layanan Publik Digital.</h1>
                <p class="text-blue-100 mb-10 font-medium leading-relaxed">Sistem antrean cepat, transparan, dan terukur. Pantau estimasi waktu layanan Anda secara real-time.</p>
                
                <div class="bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/20 shadow-xl">
                    <h3 class="font-bold mb-3 text-sm tracking-wide uppercase text-blue-100">Cek Nomor Antrean</h3>
                    <form action="#" class="flex gap-2">
                        <input type="text" placeholder="Misal: IN-260401-123" class="w-full px-4 py-3 rounded-xl bg-white/90 text-slate-800 font-bold outline-none uppercase tracking-widest placeholder:font-normal placeholder:normal-case placeholder:tracking-normal">
                        <button type="button" class="bg-white text-purple-700 font-black px-6 py-3 rounded-xl hover:bg-slate-100 transition shadow-lg">Lacak</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-7 p-10 lg:p-12 bg-white">
            <h2 class="text-2xl font-black mb-2 text-slate-900">Buku Tamu Digital</h2>
            <p class="text-sm text-slate-500 mb-8 font-medium">Silakan lengkapi formulir di bawah ini untuk mengambil antrean layanan.</p>

            @if(session('success'))
            <div class="mb-8 bg-emerald-50 text-emerald-700 p-5 rounded-2xl font-bold border-2 border-emerald-200 flex items-center gap-3 shadow-sm">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('kunjungan.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_lengkap" required class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-purple-500 focus:bg-white transition-colors outline-none font-medium placeholder:font-normal" placeholder="Jhon Doe">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider">NIK/NIM <span class="font-medium normal-case text-slate-400">(Opsional)</span></label>
                        <input type="text" name="identitas_no" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-purple-500 focus:bg-white transition-colors outline-none font-medium placeholder:font-normal" placeholder="C0303...">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider">Asal Instansi <span class="text-rose-500">*</span></label>
                        <input type="text" name="asal_instansi" required class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-purple-500 focus:bg-white transition-colors outline-none font-medium placeholder:font-normal" placeholder="Mis: Poliban">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider">No. WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_telepon" required class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-purple-500 focus:bg-white transition-colors outline-none font-medium placeholder:font-normal" placeholder="0812...">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider">Program Studi Tujuan <span class="text-rose-500">*</span></label>
                    <select name="prodi_id" required class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-purple-500 focus:bg-white transition-colors outline-none font-bold text-slate-700 cursor-pointer appearance-none">
                        <option value="" disabled selected>Pilih Tujuan...</option>
                        @foreach($prodi as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider">Kategori Keperluan <span class="text-rose-500">*</span></label>
                    <select name="keperluan_id" required class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-purple-500 focus:bg-white transition-colors outline-none font-bold text-slate-700 cursor-pointer appearance-none">
                        <option value="" disabled selected>Pilih Keperluan...</option>
                        @foreach($keperluan as $k)
                            <option value="{{ $k->id }}">{{ $k->keterangan }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider">Catatan Tambahan <span class="font-medium normal-case text-slate-400">(Opsional)</span></label>
                    <textarea name="catatan_keperluan" rows="2" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-purple-500 focus:bg-white transition-colors outline-none font-medium resize-none placeholder:font-normal" placeholder="Tuliskan detail keperluan Anda..."></textarea>
                </div>

                <button type="submit" class="w-full py-4 mt-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-black text-lg rounded-xl hover:shadow-lg hover:shadow-purple-500/30 transition-all hover:-translate-y-1">
                    Ambil Nomor Antrean
                </button>
            </form>
        </div>
    </div>

</body>
</html>