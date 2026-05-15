@extends('layouts.app')

@section('content')
{{-- Header Dashboard --}}
<div class="flex justify-between items-center mb-10">
    <div>
        <h2 class="text-4xl font-black text-gray-800 tracking-tight">Analytics Overview</h2>
        <p class="text-slate-400 text-sm font-medium mt-2">Ringkasan performa layanan institusi.</p>
    </div>
    <div class="flex gap-3">
        <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-50 text-xs font-bold text-gray-600 flex items-center gap-2">
            <span>Teknik Informatika</span>
            <i class="fa-solid fa-chevron-down text-[10px]"></i>
        </div>
        <button class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2.5 rounded-2xl text-xs font-black flex items-center gap-2 shadow-lg shadow-indigo-100 transition-all">
            <i class="fa-solid fa-download"></i> EXPORT LAPORAN
        </button>
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
        <div class="grid grid-cols-3 gap-4 mt-10 w-full">
            <div class="flex flex-col items-center">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-[9px] font-bold text-gray-400 uppercase">Puas</span>
                </div>
                <div class="font-black text-gray-700">{{ $skor_kepuasan['puas'] }}</div>
            </div>
            <div class="flex flex-col items-center border-x border-gray-100">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                    <span class="text-[9px] font-bold text-gray-400 uppercase">Cukup</span>
                </div>
                <div class="font-black text-gray-700">{{ $skor_kepuasan['cukup'] }}</div>
            </div>
            <div class="flex flex-col items-center">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                    <span class="text-[9px] font-bold text-gray-400 uppercase">Kurang</span>
                </div>
                <div class="font-black text-gray-700">{{ $skor_kepuasan['kurang'] }}</div>
            </div>
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
    // 1. Chart Skor Kepuasan (Doughnut)
    const ctxSat = document.getElementById('satisfactionChart').getContext('2d');
    new Chart(ctxSat, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [{{ $skor_kepuasan['puas'] }}, {{ $skor_kepuasan['cukup'] }}, {{ $skor_kepuasan['kurang'] }}],
                backgroundColor: ['#10b981', '#fbbf24', '#f43f5e'],
                borderWidth: 0,
                cutout: '82%',
                borderRadius: 15,
                spacing: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. Chart Distribusi (Bar)
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

// 3. Inisialisasi Chart Tren SLA (Line Chart dengan Data Dinamis)
const ctxSla = document.getElementById('slaChart').getContext('2d');

// Efek gradien ungu (indigo)
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
                tension: 0.4, // Melengkungkan garis
                borderWidth: 4,
                pointRadius: 4,
                pointBackgroundColor: '#6366f1'
            },
            {
                label: 'Terlambat',
                data: {!! json_encode($data_terlambat) !!},
                borderColor: '#f43f5e',
                fill: false,
                tension: 0.4, // Melengkungkan garis
                borderWidth: 2,
                borderDash: [5, 5], // Garis putus-putus untuk pembeda
                pointRadius: 4,
                pointBackgroundColor: '#f43f5e'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                suggestedMax: 5, // Agar grafik tidak terlihat "tenggelam" jika data sedikit
                ticks: {
                    stepSize: 1, // Angka bulat 1, 2, 3...
                    color: '#94a3b8'
                },
                grid: {
                    color: '#f1f5f9'
                }
            },
            x: {
                grid: { display: false }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                align: 'end'
            }
        }
    }
});
</script>
@endpush