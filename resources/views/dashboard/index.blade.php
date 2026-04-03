@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
    <div class="mb-10">
        <h2 class="text-4xl font-black text-gray-800 tracking-tight leading-none">Dashboard</h2>
        <div class="mt-4 flex items-center gap-3">
            <span class="px-4 py-1.5 bg-indigo-100 text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest">
                {{ $user->prodi ?? 'Seluruh Unit Kerja' }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="card-stat bg-white p-8 rounded-[32px] shadow-sm border border-gray-50 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Kunjungan</p>
                <h3 class="text-4xl font-black text-gray-800">{{ $data_kunjungan->count() }}</h3>
            </div>
            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="card-stat bg-white p-8 rounded-[32px] shadow-sm border border-gray-50 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status Diproses</p>
                <h3 class="text-4xl font-black text-gray-800">{{ $data_kunjungan->where('status_layanan', 'Diproses')->count() }}</h3>
            </div>
            <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-2xl">
                <i class="fa-solid fa-spinner"></i>
            </div>
        </div>

        <div class="card-stat bg-white p-8 rounded-[32px] shadow-sm border border-gray-50 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Selesai Hari Ini</p>
                <h3 class="text-4xl font-black text-gray-800">{{ $data_kunjungan->where('status_layanan', 'Selesai')->count() }}</h3>
            </div>
            <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-2xl">
                <i class="fa-solid fa-check-to-slot"></i>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3 mb-8">
        <h3 class="text-xl font-extrabold text-gray-800">Antrean Layanan (Front-Office)</h3>
        <div class="h-[2px] flex-1 bg-gray-100 rounded-full"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($data_kunjungan as $kunjungan)
        <div class="bg-white border {{ $kunjungan->status_layanan == 'Diproses' ? 'border-indigo-500 shadow-xl shadow-indigo-100' : 'border-gray-100 shadow-sm' }} rounded-[32px] p-8 relative overflow-hidden transition-all hover:scale-[1.02]">

            @if($kunjungan->status_layanan == 'Diproses')
            <div class="absolute top-0 right-0">
                <span class="bg-indigo-500 text-white text-[9px] font-black px-4 py-1 rounded-bl-xl uppercase tracking-tighter animate-pulse">Sedang Melayani</span>
            </div>
            @endif

            {{-- 1. BAGIAN ATAS: Label Status & Nomor Antrean --}}
            <div class="flex justify-between items-start mb-6">
                <span class="px-4 py-1 {{ match($kunjungan->status_layanan) {
                    'Antre' => 'bg-amber-100 text-amber-600',
                    'Diproses' => 'bg-indigo-100 text-indigo-600',
                    'Selesai' => 'bg-emerald-100 text-emerald-600',
                    'Ditolak' => 'bg-rose-100 text-rose-600',
                    default => 'bg-gray-100 text-gray-600'
                } }} rounded-full text-[10px] font-black uppercase tracking-widest">
                    {{ $kunjungan->status_layanan }}
                </span>

                {{-- NOMOR ANTREAN KEMBALI DIMUNCULKAN DI SINI --}}
                <div class="text-right">
                    <span class="block text-3xl font-black text-gray-800">{{ $kunjungan->nomor_kunjungan }}</span>
                </div>
            </div>

            {{-- 2. BAGIAN TENGAH: Nama & Instansi --}}
            <div class="mb-6">
                <h4 class="text-xl font-extrabold text-gray-800 leading-tight mb-1">{{ $kunjungan->pengunjung->nama_lengkap ?? 'Anonim' }}</h4>
                <p class="text-xs font-bold text-indigo-500 uppercase tracking-tighter">{{ $kunjungan->pengunjung->asal_instansi ?? 'Umum' }}</p>
            </div>

            {{-- 3. BAGIAN KEPERLUAN: Dropdown & Catatan (SUDAH DIPERBAIKI) --}}
            <div class="bg-gray-50 rounded-2xl p-4 mb-8">
                <p class="text-[9px] font-black text-gray-400 uppercase mb-1 tracking-widest">Keperluan:</p>

                {{-- Menampilkan Nama Keperluan dari Dropdown --}}
                <p class="text-sm font-bold text-gray-800">
                    {{ $kunjungan->keperluan_master->keterangan ?? 'Lainnya' }}
                </p>

                {{-- Menampilkan Catatan (Textarea) JIKA user mengisinya --}}
                @if($kunjungan->keperluan)
                    <p class="text-xs font-medium text-gray-500 italic mt-1 leading-relaxed">
                        "{{ $kunjungan->keperluan }}"
                    </p>
                @endif
            </div>

            {{-- 4. BAGIAN BAWAH: Tombol Aksi --}}
            <div class="flex gap-3">
                @if($kunjungan->status_layanan == 'Antre')
                    <button type="button" onclick="bukaModalProses('{{ $kunjungan->nomor_kunjungan }}')" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl text-[10px] font-black transition-all shadow-lg shadow-indigo-100 uppercase tracking-widest">
                        <i class="fa-solid fa-play mr-2"></i> Mulai Proses
                    </button>
                    <form id="form-tolak-{{ $kunjungan->nomor_kunjungan }}" action="{{ route('kunjungan.tolak', $kunjungan->nomor_kunjungan) }}" method="POST" class="inline">
                        @csrf
                        <button type="button" onclick="confirmTolak('form-tolak-{{ $kunjungan->nomor_kunjungan }}')" class="px-4 border border-rose-100 text-rose-400 hover:bg-rose-50 rounded-2xl transition-all h-full">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </form>
                @elseif($kunjungan->status_layanan == 'Diproses')
                    <form action="{{ route('kunjungan.selesai', $kunjungan->nomor_kunjungan) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-4 rounded-2xl text-[10px] font-black transition-all shadow-lg shadow-emerald-100 uppercase tracking-widest">
                            <i class="fa-solid fa-check-double mr-2"></i> Selesaikan Pelayanan
                        </button>
                    </form>
                @endif
            </div>
        </div>        @empty
        <div class="col-span-full py-20 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center text-gray-300 mb-4">
                <i class="fa-solid fa-inbox text-3xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-400">Belum ada antrean masuk</h4>
        </div>
        @endforelse
    </div>

    <div id="modalProsesSLA" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="tutupModal()"></div>
            <div class="relative bg-white rounded-[2.5rem] p-10 max-w-md w-full shadow-2xl border border-gray-100">
                <h3 class="text-2xl font-black text-slate-800 mb-2">Proses Antrean <span id="displayNoAntrean" class="text-indigo-600"></span></h3>
                <p class="text-sm text-slate-500 font-medium mb-8">Tentukan estimasi waktu pengerjaan untuk tamu.</p>

                <form action="" method="POST" id="formSLA">
                    @csrf
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block ml-1">Estimasi</label>
                            <input type="number" name="estimasi_sla" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-indigo-500" placeholder="Angka" required min="1">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block ml-1">Satuan</label>
                            <select name="satuan_sla" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-indigo-500">
                                <option value="Menit">Menit</option>
                                <option value="Hari">Hari</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black uppercase tracking-widest transition-all">
                        Konfirmasi & Mulai
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function bukaModalProses(noAntrean) {
        document.getElementById('displayNoAntrean').innerText = noAntrean;
        const form = document.getElementById('formSLA');
        form.action = "/dashboard/mulai-proses/" + noAntrean;
        document.getElementById('modalProsesSLA').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function tutupModal() {
        document.getElementById('modalProsesSLA').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmTolak(formId) {
        Swal.fire({
            title: 'Tolak Antrean?',
            text: "Data ini akan ditandai sebagai ditolak.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Ya, Tolak',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) { document.getElementById(formId).submit(); }
        })
    }
</script>
@endpush
