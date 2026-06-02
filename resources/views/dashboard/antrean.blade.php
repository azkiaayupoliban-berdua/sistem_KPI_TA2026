@extends('layouts.app')

@section('title', 'Manajemen Antrean')

@section('content')
    {{-- SISTEM NOTIFIKASI TOAST POP-UP --}}
    <div id="toast-container" class="fixed bottom-4 md:bottom-10 left-4 right-4 md:left-auto md:right-10 z-[999] flex flex-col gap-4">
        @if(session('success'))
            <div class="toast-item bg-emerald-500 text-white px-6 md:px-8 py-4 md:py-5 rounded-2xl md:rounded-[2rem] shadow-xl flex items-center gap-4 animate-toast-in">
                <div class="flex-shrink-0 w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-check text-sm md:text-lg"></i>
                </div>
                <div>
                    <p class="text-[9px] md:text-[10px] font-black uppercase tracking-widest opacity-70">Berhasil</p>
                    <p class="font-bold text-xs md:text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast-item bg-rose-500 text-white px-8 py-5 rounded-[2rem] shadow-[0_20px_50px_rgba(244,63,94,0.3)] flex items-center gap-4 animate-toast-in">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-70">Gagal</p>
                    <p class="font-bold text-sm">{{ session('error') }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- HEADER SECTION & FILTERS --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-6 md:mb-10 gap-4">
        <div>
            <h2 class="text-2xl md:text-4xl font-black text-gray-800 tracking-tight leading-none">Manajemen Antrean</h2>
            <p class="text-slate-400 text-xs md:text-sm font-medium mt-2 md:mt-3">Monitor dan kelola riwayat antrean secara mendetail.</p>
        </div>
        
{{-- FORM PENCARIAN DAN FILTER --}}
<form action="{{ url()->current() }}" method="GET" class="w-full lg:w-auto flex flex-col sm:flex-row gap-3 items-center">
    
    @php
        $isSuper = $user->role_id == 1 || $user->role_id == 3;
    @endphp

            {{-- Filter Prodi dengan Validasi Role Admin/Petugas --}}
            <div class="w-full sm:w-64 relative">
                <select name="prodi_id"
                    onchange="this.form.submit()"
                    {{ !$isSuper ? 'disabled' : '' }}
                    class="w-full bg-white border border-slate-200 rounded-2xl pl-4 pr-10 py-3 text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none appearance-none transition-all shadow-sm {{ !$isSuper ? 'bg-slate-50 cursor-not-allowed text-slate-400 border-slate-200' : '' }}">
                    
                    @if($isSuper)
                        <option value="">🌍 Seluruh Program Studi</option>
                        @foreach($daftar_prodi ?? [] as $p)
                            <option value="{{ $p->id }}" {{ request('prodi_id') == $p->id ? 'selected' : '' }}>
                                🎓 {{ $p->nama }}
                            </option>
                        @endforeach
                    @else
                        <option selected>
                            🎓 {{ $user->prodi->nama ?? 'Prodi Tidak Ditemukan' }}
                        </option>
                    @endif
                </select>
                
                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 text-xs">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>

            {{-- Input Pencarian Nama / Nomor Kunjungan --}}
            <div class="w-full sm:w-64 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari nama / no. kunjungan..." 
                    class="w-full pl-12 pr-10 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none shadow-sm transition-all text-slate-700">
                
                @if(request('search') || request('prodi_id'))
                    <a href="{{ url()->current() }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500 transition-colors" title="Clear Filter">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </a>
                @endif
            </div>
            
            {{-- Tombol Submit Cari (Tampil di Desktop) --}}
            <button type="submit" 
                class="hidden sm:inline-block bg-gradient-to-r from-indigo-500 via-purple-500 to-orange-400 text-white px-6 py-3 rounded-2xl font-black text-sm shadow-lg hover:scale-[1.02] transition-all">
                <i class="fa-solid fa-magnifying-glass mr-2"></i>
                Cari
            </button>
        </form>
    </div>

    {{-- TABLE CONTAINER --}}
    <div class="bg-white rounded-2xl md:rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 md:px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID</th>
                        <th class="px-6 md:px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Pengunjung</th>
                        <th class="px-6 md:px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status Layanan</th>
                        <th class="px-6 md:px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Estimasi SLA</th>
                        <th class="px-6 md:px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status SLA</th>
                        <th class="px-6 md:px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tanggal</th>
                        <th class="px-6 md:px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data_kunjungan as $k)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 md:px-8 py-4 md:py-6 font-bold text-gray-800 text-sm md:text-base">#{{ $k->nomor_kunjungan }}</td>
                        <td class="px-6 md:px-8 py-4 md:py-6">
                            <p class="font-extrabold text-gray-800 text-sm md:text-base">{{ $k->pengunjung->nama_lengkap ?? 'Umum' }}</p>
                            
                            {{-- JENIS KEPERLUAN --}}
                            <div class="mb-2 mt-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Jenis</p>
                                <p class="text-sm font-bold text-slate-700 italic leading-relaxed">
                                    {{ $k->keperluan_master->keterangan ?? '-' }}
                                </p>
                            </div>
                            
                            {{-- DETAIL --}}
                            @if(!empty($k->keperluan) && $k->keperluan != '-')
                                <div class="mb-2">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Detail</p>
                                    <p class="text-sm font-medium text-slate-600 italic leading-relaxed">
                                        "{{ Str::limit($k->keperluan, 120) }}"
                                    </p>
                                </div>
                            @endif

                            @if($k->catatan_pimpinan)
                                <div class="mt-3 p-3 rounded-xl shadow-sm {{ $k->status_pimpinan == 'Setuju' ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200' }}">
                                    <p class="text-[9px] font-black uppercase tracking-widest mb-1 {{ $k->status_pimpinan == 'Setuju' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        <i class="fa-solid fa-comment-medical mr-1"></i> Respon Pimpinan : {{ $k->status_pimpinan }}
                                    </p>
                                    <p class="text-[11px] font-bold italic leading-relaxed {{ $k->status_pimpinan == 'Setuju' ? 'text-emerald-900' : 'text-rose-900' }}">
                                        "{{ $k->catatan_pimpinan }}"
                                    </p>
                                </div>
                            @endif

                            @if($k->status_layanan == 'Selesai')
                                <div class="mt-3 p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                                    <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Durasi Pelayanan</p>
                                    <p class="text-sm font-extrabold text-emerald-700">{{ $k->durasi_layanan }}</p>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 md:px-8 py-4 md:py-6 text-center">
                            @php
                                $color = match($k->status_layanan) {
                                    'Selesai' => 'bg-emerald-100 text-emerald-600',
                                    'Diproses' => 'bg-indigo-100 text-indigo-600',
                                    'Ditolak' => 'bg-rose-100 text-rose-600',
                                    default => 'bg-amber-100 text-amber-600'
                                };
                            @endphp
                            <span class="px-3 md:px-4 py-1 {{ $color }} rounded-full text-[8px] md:text-[9px] font-black uppercase tracking-widest inline-block whitespace-nowrap">
                                {{ $k->status_layanan }}
                            </span>
                        </td>
                        <td class="px-6 md:px-8 py-4 md:py-6 text-center text-sm font-bold text-gray-600">
                            {{ $k->estimasi_sla ?? '-' }} {{ $k->satuan_sla ?? '' }}
                        </td>
                        <td class="px-8 py-6 text-center">
                            @php
                                $status_sla = strtolower(trim($k->status_sla ?? ''));
                                $status_layanan = strtolower(trim($k->status_layanan ?? ''));
                            @endphp

                            @if($status_layanan == 'selesai')
                                @if($status_sla == 'tepat waktu')
                                    <span class="text-emerald-500 font-black text-[10px] flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-circle-check"></i> TEPAT WAKTU
                                    </span>
                                @elseif($status_sla == 'terlambat')
                                    <span class="text-rose-500 font-black text-[10px] flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i> TERLAMBAT
                                    </span>
                                @else
                                    <span class="text-gray-400 text-[10px] italic">Data SLA: "{{ $k->status_sla ?? 'Null/Kosong' }}"</span>
                                @endif
                            @elseif($status_layanan == 'ditolak')
                                <span class="text-rose-600 font-black text-[10px] flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-ban"></i> GAGAL
                                </span>
                            @else
                                <span class="text-indigo-400 text-[9px] font-black uppercase italic tracking-tighter">Sedang Berjalan</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-center">
                            <p class="text-gray-800 font-bold text-sm">{{ \Carbon\Carbon::parse($k->tanggal)->translatedFormat('d M Y') }}</p>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ $k->hari_kunjungan }}</p>
                        </td>
                        <td class="px-6 md:px-8 py-4 md:py-6 text-center">
                            @php $layananBelumDimulai = $k->status_layanan == 'Antre'; @endphp
                            <div class="flex justify-center gap-1.5 md:gap-2 items-center">
                                <a href="{{ url('/status/'.$k->nomor_kunjungan) }}" target="_blank" class="flex-shrink-0 w-8 h-8 md:w-9 md:h-9 flex items-center justify-center bg-gray-50 text-gray-400 rounded-lg md:rounded-xl hover:bg-slate-800 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-eye text-[10px] md:text-xs"></i>
                                </a>

                                @if($user->role_id == 2)
                                    @if($k->status_layanan != 'Selesai' && !$k->is_email_sent)
                                        <button type="button" {{ $layananBelumDimulai ? 'disabled' : '' }} onclick="bukaModalEmail('{{ $k->id }}', '{{ $k->pengunjung->nama_lengkap ?? 'Umum' }}', '{{ addslashes($k->keperluan) }}')" class="w-9 h-9 flex items-center justify-center rounded-xl shadow-sm transition-all {{ $layananBelumDimulai ? 'bg-gray-100 text-gray-300 cursor-not-allowed' : 'bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white' }}" title="{{ $layananBelumDimulai ? 'Mulai layanan terlebih dahulu' : 'Kirim Email ke Pimpinan' }}">
                                            <i class="fa-solid fa-envelope text-xs"></i>
                                        </button>
                                    @endif

                                    @if($k->status_layanan != 'Selesai' && !$k->is_forwarded)
                                        <button type="button" {{ $layananBelumDimulai ? 'disabled' : '' }} onclick="bukaModalForward('{{ $k->id }}', '{{ $k->pengunjung->nama_lengkap ?? 'Umum' }}')" class="w-9 h-9 flex items-center justify-center rounded-xl shadow-sm transition-all {{ $layananBelumDimulai ? 'bg-gray-100 text-gray-300 cursor-not-allowed' : 'bg-violet-50 text-violet-600 hover:bg-violet-600 hover:text-white' }}" title="{{ $layananBelumDimulai ? 'Mulai layanan terlebih dahulu' : 'Teruskan ke Pimpinan' }}">
                                            <i class="fa-solid fa-share-nodes text-xs"></i>
                                        </button>
                                    @endif

                                    @if($k->is_forwarded && !$k->is_email_sent)
                                        <button type="button" onclick="alert('Data sudah diteruskan ke pimpinan.\n\nAdmin wajib mengirim email konfirmasi ulang.')" class="w-9 h-9 flex items-center justify-center bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Wajib Email Konfirmasi">
                                            <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                        </button>
                                    @endif

                                    @if($k->is_email_sent)
                                        <button type="button" disabled class="w-9 h-9 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-xl cursor-not-allowed shadow-sm" title="Email Sudah Terkirim">
                                            <i class="fa-solid fa-envelope-circle-check text-xs"></i>
                                        </button>
                                    @endif

                                    @if($k->status_layanan == 'Antre')
                                        <button type="button" onclick="bukaModalProsesSLA('{{ $k->nomor_kunjungan }}')" class="w-9 h-9 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Mulai Proses">
                                            <i class="fa-solid fa-play text-xs"></i>
                                        </button>
                                    @elseif(strtolower($k->status_layanan) == 'diproses')
                                        <form action="{{ route('kunjungan.selesai', $k->id) }}" method="POST" class="m-0" onsubmit="return confirm('Selesaikan layanan?')">
                                            @csrf
                                            <button type="submit" class="w-9 h-9 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Selesai">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                        </form>

                                        @if(empty($k->file_surat))
                                            <button type="button" onclick="bukaModalUpload('{{ $k->id }}', '{{ $k->pengunjung->nama_lengkap ?? 'Umum' }}')" class="w-9 h-9 flex items-center justify-center bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Upload File">
                                                <i class="fa-solid fa-paperclip text-xs"></i>
                                            </button>
                                        @else
                                            <div class="w-9 h-9 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-xl shadow-sm" title="File Sudah Upload">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-8 py-20 text-center text-gray-400">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL ESTIMASI SLA --}}
    <div id="modalProsesSLA" class="fixed inset-0 z-[100] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2.5rem] p-6 md:p-10 max-w-md w-full shadow-2xl animate-modal-up relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl md:text-2xl font-black text-gray-800 tracking-tight">Estimasi Waktu</h3>
                <button type="button" onclick="tutupModalSLA()" class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="formSLA" method="POST">
                @csrf
                <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200">
                    <p class="text-[10px] uppercase font-black tracking-widest text-amber-600 mb-2">Perhatian</p>
                    <p class="text-xs text-amber-700 font-semibold leading-relaxed">
                        Estimasi hanya bisa diinput <b>1 kali</b>. Pastikan sudah sesuai dengan <b>jenis keperluan</b> dan perkiraan waktu pengerjaan layanan.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-5 mb-8">
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Angka</label>
                        <input type="number" name="estimasi_sla" required class="bg-gray-50 border-2 border-transparent rounded-2xl p-4 font-bold text-gray-800 focus:bg-white focus:border-indigo-500 outline-none transition-all">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Satuan</label>
                        <select name="satuan_sla" class="bg-gray-50 border-2 border-transparent rounded-2xl p-4 font-bold text-gray-800 focus:bg-white focus:border-indigo-500 outline-none transition-all">
                            <option value="Menit">Menit</option>
                            <option value="Hari">Hari</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-[1.5rem] font-black uppercase tracking-widest shadow-xl hover:bg-indigo-700 transition-all">
                    Konfirmasi & Mulai
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL EMAIL PIMPINAN --}}
    <div id="modalEmailPimpinan" class="fixed inset-0 z-[100] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-modal-up">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-black text-gray-800">Teruskan ke Pimpinan</h3>
                <button type="button" onclick="tutupModalEmail()" class="text-gray-400 hover:text-rose-500 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('kunjungan.kirim-email') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="kunjungan_id" id="modal_kunjungan_id">
                <div class="mb-5 bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100/50">
                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1">Informasi Kunjungan</p>
                    <p class="font-bold text-gray-800 text-sm" id="modal_nama_pengunjung"></p>
                    <p class="text-xs text-gray-500 mt-1 italic" id="modal_keperluan_pengunjung"></p>
                </div>
                <div class="mb-6">
                    <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Email Pimpinan</label>
                    <div class="relative">
                        <i class="fa-solid fa-at absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                        <input type="email" name="email_pimpinan" id="email_pimpinan" required placeholder="pimpinan@poliban.ac.id" class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="tutupModalEmail()" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane text-xs"></i> Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL FORWARD PIMPINAN --}}
    <div id="modalForwardPimpinan" class="fixed inset-0 z-[120] hidden bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden animate-modal-up">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-gray-800">Teruskan ke Pimpinan</h3>
                    <p class="text-xs text-gray-400 mt-1">Pilih tujuan disposisi layanan</p>
                </div>
                <button onclick="tutupModalForward()" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-rose-100 text-gray-400 hover:text-rose-500 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formForwardPimpinan" action="{{ route('kunjungan.kirim-massal') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="ids[]" id="forward_kunjungan_id">
                <div class="mb-6">
                    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-500 mb-1">Pengunjung</p>
                        <p id="forward_nama_pengunjung" class="font-bold text-gray-800 text-sm"></p>
                    </div>
                </div>

                <div class="space-y-3 mb-8">
                    <label class="flex items-center gap-4 p-4 rounded-2xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 transition-all cursor-pointer">
                        <input type="radio" name="tujuan_pimpinan" value="kajur" required class="w-5 h-5 text-indigo-600">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <p class="font-black text-gray-800 text-sm">Ketua Jurusan</p>
                                <p class="text-xs text-gray-400">Kirim ke Kajur Elektro</p>
                            </div>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-4 rounded-2xl border border-gray-200 hover:border-violet-500 hover:bg-violet-50 transition-all cursor-pointer">
                        <input type="radio" name="tujuan_pimpinan" value="kaprodi" required class="w-5 h-5 text-violet-600">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <div>
                                <p class="font-black text-gray-800 text-sm">Ketua Program Studi</p>
                                <p class="text-xs text-gray-400">Kirim ke Kaprodi terkait</p>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="tutupModalForward()" class="flex-1 py-3 rounded-2xl bg-gray-100 text-gray-600 font-bold text-sm hover:bg-gray-200 transition-all">Batal</button>
                    <button type="submit" onclick="return confirm('Teruskan data ini ke pimpinan?')" class="flex-1 py-3 rounded-2xl bg-violet-600 hover:bg-violet-700 text-white font-black text-sm shadow-lg shadow-violet-200 transition-all">Teruskan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL UPLOAD FILE --}}
    <div id="modalUploadFile" class="fixed inset-0 z-[100] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-modal-up">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-amber-50/50">
                <h3 class="text-lg font-black text-amber-800">Upload File Layanan</h3>
                <button type="button" onclick="tutupModalUpload()" class="text-gray-400 hover:text-rose-500 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="formUploadSelesai" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="mb-5 bg-amber-50 p-4 rounded-2xl border border-amber-100">
                    <p class="font-bold text-gray-800 text-sm" id="upload_nama_pengunjung"></p>
                    <p class="text-xs text-amber-700 mt-2 leading-relaxed">
                        Upload dokumen pendukung layanan. Status layanan tetap diproses sampai admin menekan tombol selesai.
                    </p>
                </div>

                <div class="mb-6">
                    <input type="file" name="file_surat" accept=".pdf" required class="w-full text-sm file:mr-4 file:py-2 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-amber-100 file:text-amber-700">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="tutupModalUpload()" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow-lg">Upload File</button>
                </div>
            </form>
        </div>
    </div>

    <style>
    /* Agar scrollbar lebih estetik di mobile */
    .overflow-x-auto::-webkit-scrollbar {
        height: 4px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background-color: #e2e8f0;
        border-radius: 10px;
    }
    
    @keyframes toast-in { 
        from { transform: translateY(100px); opacity: 0; } 
        to { transform: translateY(0); opacity: 1; } 
    }
    .animate-toast-in { animation: toast-in 0.5s ease forwards; }
    
    @keyframes modal-up { 
        from { transform: translateY(20px); opacity: 0; } 
        to { transform: translateY(0); opacity: 1; } 
    }
    .animate-modal-up { animation: modal-up 0.3s ease-out forwards; }
</style>

<script>
    // ==========================================
    // MODAL PROSES SLA
    // ==========================================
    function bukaModalProsesSLA(nomorKunjungan) {
        const form = document.getElementById('formSLA');
        form.action = "{{ url('/dashboard/mulai-proses') }}/" + nomorKunjungan;
        document.getElementById('modalProsesSLA').classList.remove('hidden');
    }

    function tutupModalSLA() {
        document.getElementById('modalProsesSLA').classList.add('hidden');
    }

    // ==========================================
    // MODAL EMAIL PIMPINAN
    // ==========================================
    function bukaModalEmail(id, nama, keperluan) {
        document.getElementById('modal_kunjungan_id').value = id;
        document.getElementById('modal_nama_pengunjung').innerText = nama;
        document.getElementById('modal_keperluan_pengunjung').innerText = keperluan ? `"${keperluan}"` : '-';
        document.getElementById('modalEmailPimpinan').classList.remove('hidden');
    }

    function tutupModalEmail() {
        document.getElementById('modalEmailPimpinan').classList.add('hidden');
    }

    // ==========================================
    // MODAL UPLOAD FILE
    // ==========================================
    function bukaModalUpload(id, nama) {
        document.getElementById('upload_nama_pengunjung').innerText = nama;
        document.getElementById('formUploadSelesai').action = `/dashboard/upload-file/${id}`;
        document.getElementById('modalUploadFile').classList.remove('hidden');
    }

    function tutupModalUpload() {
        document.getElementById('modalUploadFile').classList.add('hidden');
    }

    // ==========================================
    // MODAL FORWARD PIMPINAN
    // ==========================================
    function bukaModalForward(id, nama) {
        console.log('Modal Forward Jalan');
        document.getElementById('forward_kunjungan_id').value = id;
        document.getElementById('forward_nama_pengunjung').innerText = nama;
        document.getElementById('modalForwardPimpinan').classList.remove('hidden');
    }

    function tutupModalForward() {
        document.getElementById('modalForwardPimpinan').classList.add('hidden');
    }

    // ==========================================
    // CLOSE MODAL OUTSIDE CLICK
    // ==========================================
    window.onclick = function(event) {
        // Deteksi klik di luar modal (wrapper background hitam/transparan)
        if (event.target.id && event.target.id.startsWith('modal')) {
            event.target.classList.add('hidden');
        }
    }
</script>

<script>
    // Auto-reload halaman setiap 3 menit (180000 ms) jika tidak ada modal yang terbuka
    setInterval(() => {
        const modalSLA = document.getElementById('modalProsesSLA');
        const modalEmail = document.getElementById('modalEmailPimpinan');
        const modalForward = document.getElementById('modalForwardPimpinan');
        const modalUpload = document.getElementById('modalUploadFile');

        // Pengecekan aman (null-safe) untuk memastikan element modalnya ada di DOM
        const isModalOpen = 
            (modalSLA && !modalSLA.classList.contains('hidden')) ||
            (modalEmail && !modalEmail.classList.contains('hidden')) ||
            (modalForward && !modalForward.classList.contains('hidden')) ||
            (modalUpload && !modalUpload.classList.contains('hidden'));

        if (!isModalOpen) {
            window.location.reload();
        }
    }, 180000);
</script>

@endsection