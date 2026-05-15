@extends('layouts.app')

@section('title', 'Konfirmasi Pimpinan')

@section('content')
<div class="mb-10">
    <h2 class="text-4xl font-black text-gray-800 tracking-tight">Konfirmasi Layanan</h2>
    <p class="text-slate-400 text-sm font-medium mt-3">Daftar permintaan persetujuan atau tanggapan pimpinan yang perlu diproses.</p>
</div>

<div class="bg-white rounded-[2.5rem] border border-gray-50 shadow-sm overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50/50">
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu Masuk</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pengunjung</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Keperluan</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @php $count = 0; @endphp
            @foreach($data_konfirmasi as $item)
                {{-- HANYA MENERIMA DATA YANG DITERUSKAN --}}
                @if($item->is_forwarded == 1 || $item->is_forwarded == true)
                @php $count++; @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-8 py-6">
                        <p class="text-sm font-bold text-gray-800">{{ $item->created_at->format('H:i') }}</p>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $item->created_at->format('d M Y') }}</p>
                    </td>
                    <td class="px-8 py-6">

    {{-- NAMA --}}
    <p class="font-extrabold text-gray-800">
        {{ $item->pengunjung->nama_lengkap ?? 'Umum' }}
    </p>

    {{-- INSTANSI --}}
    <p class="text-[10px] font-bold text-indigo-500 uppercase">
        {{ $item->pengunjung->instansi ?? '-' }}
    </p>

    {{-- ID PENGUNJUNG --}}
    <div class="mt-2 flex flex-wrap gap-2">

        <span class="px-3 py-1 bg-slate-100 text-slate-600
                     rounded-lg text-[9px] font-black uppercase tracking-widest">

            ID: #{{ $item->id }}
        </span>

        <span class="px-3 py-1 bg-indigo-100 text-indigo-600
                     rounded-lg text-[9px] font-black uppercase tracking-widest">

            Antrean: {{ $item->nomor_kunjungan }}
        </span>

    </div>

</td>
                    <td class="px-8 py-6">
                        <p class="text-sm text-gray-600 italic">"{{ $item->keperluan }}"</p>
                    </td>
<td class="px-8 py-6 text-center">

@if(
    !$item->status_pimpinan ||
    str_contains(strtolower($item->status_pimpinan), 'menunggu')
)

    <button
        onclick="bukaModalTanggapan('{{ $item->id }}')"
        class="px-6 py-2 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">

        Beri Tanggapan
    </button>

@else

    {{-- STATUS SUDAH DITANGGAPI --}}
    <div class="flex flex-col items-center gap-2">

        <span class="px-4 py-1.5
            {{ $item->status_pimpinan == 'Setuju'
                ? 'bg-emerald-100 text-emerald-600'
                : 'bg-rose-100 text-rose-600'
            }}
            text-[10px] font-black uppercase rounded-lg">

            {{ $item->status_pimpinan }}
        </span>

    </div>

@endif

</td>
                </tr>
                @endif
            @endforeach

            {{-- TAMPILKAN PESAN KOSONG JIKA TIDAK ADA DATA YANG DITERUSKAN --}}
            @if($count == 0)
            <tr>
                <td colspan="4" class="px-8 py-20 text-center">
                    <div class="flex flex-col items-center opacity-30">
                        <i class="fa-solid fa-inbox text-5xl mb-4"></i>
                        <p class="font-bold uppercase tracking-widest text-sm">Belum ada data diteruskan</p>
                    </div>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

{{-- MODAL TANGGAPAN --}}
<div id="modalTanggapan" class="fixed inset-0 z-[100] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] p-10 max-w-md w-full shadow-2xl">
        <h3 class="text-2xl font-black text-gray-800 mb-2">Konfirmasi Layanan</h3>
        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-8">Pilih keputusan anda</p>
        
        <form id="formTanggapan" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-6">
                <label class="cursor-pointer">
                    <input type="radio" name="status_pimpinan" value="Setuju" class="peer hidden" required checked>
                    <div class="flex items-center justify-center gap-2 p-4 bg-gray-50 border-2 border-transparent rounded-2xl font-black text-gray-400 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-600 transition-all text-sm uppercase">
                        <i class="fa-solid fa-circle-check"></i> Setuju
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="status_pimpinan" value="Ditolak" class="peer hidden" required>
                    <div class="flex items-center justify-center gap-2 p-4 bg-gray-50 border-2 border-transparent rounded-2xl font-black text-gray-400 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-600 transition-all text-sm uppercase">
                        <i class="fa-solid fa-circle-xmark"></i> Tolak
                    </div>
                </label>
            </div>

            <div class="mb-8">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Pesan/Instruksi Tambahan</label>
                <textarea name="catatan_pimpinan" rows="4" required 
                    class="w-full bg-gray-50 border-2 border-transparent rounded-2xl p-4 font-medium text-gray-800 focus:bg-white focus:border-indigo-500 outline-none transition-all shadow-inner" 
                    placeholder="Contoh: Temui saya di ruangan atau lengkapi berkas..."></textarea>
            </div>
            
            <div class="flex flex-col gap-2">
                <button type="submit" class="w-full bg-slate-900 text-white py-5 rounded-[1.5rem] font-black uppercase tracking-widest shadow-xl hover:bg-black transition-all">
                    Kirim Keputusan
                </button>
                <button type="button" onclick="tutupModal()" class="w-full py-3 font-bold text-gray-400 text-xs uppercase tracking-widest">
                    Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalTanggapan(id) {
        const form = document.getElementById('formTanggapan');
        form.action = `/dashboard/pimpinan/konfirmasi/${id}/tanggapan`;
        document.getElementById('modalTanggapan').classList.remove('hidden');
    }
    function tutupModal() {
        document.getElementById('modalTanggapan').classList.add('hidden');
    }
</script>
@endsection