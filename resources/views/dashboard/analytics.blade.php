@extends('layouts.app')

@section('content')
{{-- Header Dashboard --}}
<div class="flex justify-between items-center mb-10">
    <div>
        <h2 class="text-4xl font-black text-gray-800 tracking-tight">Analytics Overview</h2>
        <p class="text-slate-400 text-sm font-medium mt-2">Ringkasan performa layanan institusi.</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3">

    @if($user->role_id == 1 || $user->role_id == 3)

    <form action="{{ route('dashboard.analytics') }}" method="GET">

        <div class="relative">
            <select name="prodi_id"
                onchange="this.form.submit()"
                class="bg-white border border-slate-200 rounded-2xl px-5 py-3 pr-10 text-xs font-black text-slate-700 shadow-sm outline-none appearance-none">

                <option value="">🌍 Semua Prodi</option>

                @foreach($daftar_prodi as $p)
                <option value="{{ $p->id }}"
                    {{ request('prodi_id') == $p->id ? 'selected' : '' }}>
                    🎓 {{ $p->nama }}
                </option>
                @endforeach

            </select>

            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]">
                <i class="fa-solid fa-chevron-down"></i>
            </div>
        </div>

    </form>

    @else

    <div class="bg-white px-5 py-3 rounded-2xl border border-slate-200 text-xs font-black text-slate-700 shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-building-columns text-indigo-500"></i>
        🎓 {{ $user->prodi->nama ?? 'Program Studi' }}
    </div>

    @endif

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
                </div>
            </div>

</div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    {{-- Grafik Tren Waktu Layanan (SLA) --}}
    <div class="lg:col-span-2 bg-white p-8 rounded-[3rem] border border-gray-50 shadow-sm relative">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-xl font-black text-gray-800 tracking-tight">Tren Waktu Layanan (SLA)</h3>
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mt-1">Rata-rata menit per hari</p>
            </div>
            <div class="bg-emerald-50 text-emerald-500 text-[10px] font-black px-4 py-1.5 rounded-full uppercase">Stabil</div>
        </div>
        <div class="h-[320px]">
            <canvas id="slaChart"></canvas>
        </div>
    </div>

    {{-- Grafik Skor Kepuasan --}}
    <div class="bg-white p-8 rounded-[3rem] border border-gray-50 shadow-sm flex flex-col items-center relative">
        <h3 class="text-xl font-black text-gray-800 mb-10 self-start tracking-tight">Skor Kepuasan</h3>
        
        <div class="relative w-full aspect-square max-w-[240px]">
            <canvas id="satisfactionChart"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-5xl font-black text-gray-800 tracking-tighter">{{ $skor_kepuasan['persen'] }}%</span>
                <span class="text-[10px] font-black text-indigo-500 uppercase mt-1 tracking-widest">Puas</span>
            </div>
        </div>

        {{-- Legenda Skor Kepuasan --}}
        <div class="grid grid-cols-4 gap-4 mt-8 w-full">
        {{-- Sangat Puas --}}
        <div class="flex flex-col items-center">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-indigo-600"></div>
                <span class="text-[9px] font-bold text-gray-400 uppercase">Sangat Puas</span>
            </div>
            <div class="font-black text-gray-700">{{ $skor_kepuasan['sangat_puas'] }}</div>
        </div>

        {{-- Puas --}}
        <div class="flex flex-col items-center border-l border-gray-100">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <span class="text-[9px] font-bold text-gray-400 uppercase">Puas</span>
            </div>
            <div class="font-black text-gray-700">{{ $skor_kepuasan['puas'] }}</div>
        </div>

        {{-- Kurang Puas --}}
        <div class="flex flex-col items-center border-l border-gray-100">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                <span class="text-[9px] font-bold text-gray-400 uppercase">Kurang Puas</span>
            </div>
            <div class="font-black text-gray-700">{{ $skor_kepuasan['kurang_puas'] }}</div>
        </div>

        {{-- Tidak Puas --}}
        <div class="flex flex-col items-center border-l border-gray-100">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                <span class="text-[9px] font-bold text-gray-400 uppercase">Tidak Puas</span>
            </div>
            <div class="font-black text-gray-700">{{ $skor_kepuasan['tidak_puas'] }}</div>
        </div>
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


{{-- Bar Chart Bawah --}}
<div class="bg-white p-10 rounded-[3rem] border border-gray-50 shadow-sm">
    <h3 class="text-xl font-black text-gray-800 tracking-tight mb-10">Distribusi Kunjungan per Keperluan</h3>
    <div class="h-[350px] w-full">
        <canvas id="keperluanChart"></canvas>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // ==========================================
    // 1. CHART KEPUASAN (DOUGHNUT)
    // ==========================================
    const ctxSat = document.getElementById('satisfactionChart').getContext('2d');
    new Chart(ctxSat, {
        type: 'doughnut',
        data: {
            labels: ['Sangat Puas', 'Puas', 'Kurang Puas', 'Tidak Puas'],
            datasets: [{
                data: [
                    {{ $skor_kepuasan['sangat_puas'] }}, 
                    {{ $skor_kepuasan['puas'] }}, 
                    {{ $skor_kepuasan['kurang_puas'] }}, 
                    {{ $skor_kepuasan['tidak_puas'] }}
                ],
                backgroundColor: ['#4f46e5', '#10b981', '#fbbf24', '#f43f5e'], 
                borderWidth: 0,
                cutout: '82%',
                borderRadius: 15,
                spacing: 4
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // ==========================================
    // 2. CHART DISTRIBUSI (BAR)
    // ==========================================
    const ctxKep = document.getElementById('keperluanChart').getContext('2d');
    new Chart(ctxKep, {
        type: 'bar',
        data: {
            labels: {!! json_encode($distribusi_label) !!},
            datasets: [{
                data: {!! json_encode($distribusi_data) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 12,
                barThickness: 45,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: '#f8fafc', drawBorder: false }, ticks: { color: '#cbd5e1', font: { size: 10, weight: '600' } } },
                x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 10, weight: '700' } } }
            }
        }
    });

    // ==========================================
    // 3. CHART TREN SLA (LINE)
    // ==========================================
    const ctxSla = document.getElementById('slaChart').getContext('2d');
    const gradientIndigo = ctxSla.createLinearGradient(0, 0, 0, 400);
    gradientIndigo.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
    gradientIndigo.addColorStop(1, 'rgba(99, 102, 241, 0)');

    new Chart(ctxSla, {
        type: 'line',
        data: {
            labels: {!! json_encode($label_sla) !!},
            datasets: [
                {
                    label: 'Tepat Waktu',
                    data: {!! json_encode($data_tepat_waktu) !!},
                    borderColor: '#6366f1',
                    backgroundColor: gradientIndigo,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 4,
                    pointBackgroundColor: '#6366f1'
                },
                {
                    label: 'Terlambat',
                    data: {!! json_encode($data_terlambat) !!},
                    borderColor: '#f43f5e',
                    fill: false,
                    tension: 0.4,
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 4,
                    pointBackgroundColor: '#f43f5e'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, suggestedMax: 5, ticks: { stepSize: 1, color: '#94a3b8' }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: true, position: 'top', align: 'end' } }
        }
    });

    // ==========================================
    // 4. CLOSING TRIGGER UNTUK DROPDOWN OUTSIDE CLICK
    // ==========================================
    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('exportDropdownMenu');
        const trigger = document.getElementById('btnDropdownTrigger');
        if (dropdown && trigger && !trigger.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
            document.getElementById('dropdownArrow').classList.remove('rotate-180');
        }
    });
});

// ======================================================
// 5. KONTROL INTERAKSI JAVASCRIPT DROPDOWN & MODAL
// ======================================================
let exportRoute = '';

function toggleExportDropdown() {
    const dropdown = document.getElementById('exportDropdownMenu');
    const arrow = document.getElementById('dropdownArrow');
    dropdown.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

function triggerExport(laporan) {
    toggleExportDropdown(); // Tutup dropdown menu setelah dipilih
    openExportModal(laporan); // Buka modal pengisian tanggal
}

function openExportModal(laporan) {
    exportRoute = laporan; // Isi dengan 'kunjungan', 'pengunjung', 'kinerja', 'penolakan', atau 'ulasan'
    document.getElementById('exportStartDate').value = '';
    document.getElementById('exportEndDate').value = '';
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
}

// Handler event click pada tombol modal ekspor
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnExcel').addEventListener('click', function() {
        downloadLaporan('xlsx');
    });

    document.getElementById('btnPdf').addEventListener('click', function() {
        downloadLaporan('pdf');
    });
});

function downloadLaporan(type) {
    const startDate = document.getElementById('exportStartDate').value;
    const endDate = document.getElementById('exportEndDate').value;

    if (!startDate || !endDate) {
        alert('Silakan pilih rentang tanggal terlebih dahulu.');
        return;
    }

    // Tangkap data prodi_id yang sedang terfilter di halaman Analytics
    const prodi = document.querySelector('[name=prodi_id]')?.value ?? '';

    // Redirect langsung menuju target endpoint controller masing-masing laporan
    window.location = '/laporan/' + exportRoute + 
                      '?type=' + type + 
                      '&start_date=' + startDate + 
                      '&end_date=' + endDate + 
                      '&prodi_id=' + prodi;
}
</script>
@endpush