@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-8 py-6 max-w-7xl mx-auto">

    {{-- HEADER & UTILITY SECTION --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8 pb-6 border-b border-slate-100">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight mb-1">Laporan & Ekspor</h1>
            <p class="text-slate-500 text-sm">Unduh rekapitulasi performa dan analisis data layanan.</p>
        </div>

        {{-- ACTIONS ROW --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
            
            {{-- FORM FILTER PRODI --}}
            <form action="{{ route('dashboard.laporan') }}" method="GET" class="w-full sm:w-auto">
                @php
                    $isSuper = $user->role_id == 1 || $user->role_id == 3;
                @endphp

                <div class="relative w-full sm:w-64">
                    <select name="prodi_id"
                        onchange="this.form.submit()"
                        {{ !$isSuper ? 'disabled' : '' }}
                        class="w-full bg-white border border-slate-200 rounded-2xl pl-4 pr-10 py-3 text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none appearance-none transition-all shadow-sm {{ !$isSuper ? 'bg-slate-50 cursor-not-allowed text-slate-400 border-slate-200' : '' }}">
                        
                        @if($isSuper)
                            <option value="">🌍 Seluruh Program Studi</option>
                            @foreach($daftar_prodi as $p)
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
            </form>

            {{-- DROPDOWN EKSPOR DENGAN DESAIN GRADASI PREMIUM --}}
            <div class="relative w-full sm:w-auto text-left">
                <button type="button" onclick="toggleExportDropdown()" id="btnDropdownTrigger"
                    class="inline-flex justify-center items-center gap-2 w-full sm:w-auto bg-gradient-to-r from-indigo-500 via-purple-500 to-orange-400 text-white px-6 py-3 rounded-2xl font-black text-sm shadow-lg hover:scale-[1.02] transition-all duration-300">
                    <i class="fa-solid fa-file-export text-base"></i>
                    <span>Ekspor Laporan</span>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200 ml-1" id="dropdownArrow"></i>
                </button>

                {{-- MENU DROPDOWN --}}
                <div id="exportDropdownMenu" 
                    class="hidden absolute right-0 mt-2 w-full sm:w-56 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 py-2 origin-top-right transition-all">
                    
                    <button onclick="triggerExport('kunjungan')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors text-left">
                        <i class="fa-regular fa-file-excel text-emerald-500 text-base w-5"></i> Laporan Kunjungan
                    </button>
                    <button onclick="triggerExport('pengunjung')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors text-left">
                        <i class="fa-solid fa-users text-blue-500 text-base w-5"></i> Laporan Pengunjung
                    </button>
                    <button onclick="triggerExport('kinerja')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors text-left">
                        <i class="fa-solid fa-chart-line text-violet-500 text-base w-5"></i> Laporan Kinerja
                    </button>
                    <button onclick="triggerExport('penolakan')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors text-left">
                        <i class="fa-solid fa-ban text-amber-500 text-base w-5"></i> Laporan Penolakan
                    </button>
                    <button onclick="triggerExport('ulasan')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors text-left">
                        <i class="fa-solid fa-star text-indigo-500 text-base w-5"></i> Laporan Ulasan
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- KARTU STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <h3 class="text-slate-400 text-xs font-bold tracking-wider uppercase mb-3">Total Layanan Selesai</h3>
            <div>
                <div class="text-4xl font-black text-slate-800 tracking-tight mb-1">
                    {{ number_format($totalSelesai, 0, ',', '.') }}
                </div>
                <p class="text-emerald-500 text-xs font-bold flex items-center gap-1">
                    <i class="fa-solid fa-circle-check"></i> Layanan berhasil diproses
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <h3 class="text-slate-400 text-xs font-bold tracking-wider uppercase mb-3">Rata-Rata SLA</h3>
            <div>
                <div class="text-4xl font-black text-slate-800 tracking-tight mb-1">
                    {{ $rataRataSla }}
                </div>
                <p class="text-blue-500 text-xs font-bold flex items-center gap-1">
                    <i class="fa-solid fa-clock"></i> Kecepatan waktu respons
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <h3 class="text-slate-400 text-xs font-bold tracking-wider uppercase mb-3">Tingkat Penolakan</h3>
            <div>
                <div class="text-4xl font-black text-slate-800 tracking-tight mb-1">
                    {{ $tingkatPenolakan }}%
                </div>
                <p class="{{ $tingkatPenolakan > 10 ? 'text-rose-500' : 'text-amber-500' }} text-xs font-bold flex items-center gap-1">
                    <i class="fa-solid fa-circle-xmark"></i> Persentase berkas ditolak
                </p>
            </div>
        </div>
    </div>

    {{-- GRAFIK KINERJA LAYANAN --}}
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-800 mb-1">Perbandingan Kinerja Layanan</h3>
            <p class="text-slate-500 text-sm">Jumlah berkas layanan yang diselesaikan.</p>
        </div>
        <div class="relative h-64 sm:h-72 w-full">
            <canvas id="kinerjaChart"></canvas>
        </div>
    </div>

</div>

{{-- MODAL EKSPOR PERIODE --}}
<div id="exportModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden border border-slate-100 transform transition-all animate-in fade-in zoom-in-95 duration-150">
        
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800">Tentukan Periode</h3>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function(){
    // INTEGRASI CHART JS
    const ctx = document.getElementById('kinerjaChart').getContext('2d');
    const labels = {!! json_encode($labelGrafik) !!};
    const data = {!! json_encode($dataGrafik) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Layanan Selesai',
                data: data,
                backgroundColor: '#4f46e5',
                borderRadius: 6,
                barThickness: window.innerWidth < 640 ? 12 : 18,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: 'bold' },
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { color: '#94a3b8', stepSize: 1 }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { weight: '600', size: window.innerWidth < 640 ? 10 : 12 } }
                }
            }
        }
    });

    // MENUTUP DROPDOWN KETIKA KLIK DI LUAR ELEMEN
    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('exportDropdownMenu');
        const trigger = document.getElementById('btnDropdownTrigger');
        if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
            document.getElementById('dropdownArrow').classList.remove('rotate-180');
        }
    });
});

// LOGIKA JAVASCRIPT DROPDOWN EXPORT
function toggleExportDropdown() {
    const dropdown = document.getElementById('exportDropdownMenu');
    const arrow = document.getElementById('dropdownArrow');
    
    dropdown.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

function triggerExport(laporan) {
    toggleExportDropdown(); // tutup dropdown menu
    openExportModal(laporan); // buka modal pengisian tanggal
}

let exportRoute = '';

function openExportModal(laporan){
    exportRoute = laporan;
    document.getElementById('exportStartDate').value = '';
    document.getElementById('exportEndDate').value = '';
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal(){
    document.getElementById('exportModal').classList.add('hidden');
}

document.getElementById('btnExcel').addEventListener('click', function(){
    downloadLaporan('xlsx');
});

document.getElementById('btnPdf').addEventListener('click', function(){
    downloadLaporan('pdf');
});

function downloadLaporan(type){
    const startDate = document.getElementById('exportStartDate').value;
    const endDate = document.getElementById('exportEndDate').value;

    if(!startDate || !endDate){
        alert('Silakan pilih rentang tanggal terlebih dahulu.');
        return;
    }

    const prodi = document.querySelector('[name=prodi_id]')?.value ?? '';

    window.location = '/laporan/' + exportRoute + 
                      '?type=' + type + 
                      '&start_date=' + startDate + 
                      '&end_date=' + endDate + 
                      '&prodi_id=' + prodi;
}
</script>
@endsection