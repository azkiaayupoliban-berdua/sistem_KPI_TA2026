@extends('layouts.app')

@section('content')
{{-- Baris 1: Grafik SLA dan Skor Kepuasan --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    {{-- Grafik Tren Waktu Layanan (SLA) --}}
    <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] border border-gray-50 shadow-sm">
        <h3 class="text-xl font-black text-gray-800">Tren Waktu Layanan (SLA)</h3>
        <div class="h-[320px]">
            <canvas id="slaChart"></canvas>
        </div>
    </div>

    {{-- Grafik Skor Kepuasan --}}
    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-50 shadow-sm flex flex-col items-center">
        <h3 class="text-xl font-black text-gray-800 mb-8 self-start">Skor Kepuasan</h3>
        <div class="relative w-full aspect-square max-w-[240px]">
            <canvas id="satisfactionChart"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-5xl font-black text-gray-800">{{ $skor_kepuasan['persen'] }}%</span>
                <span class="text-[10px] font-black text-indigo-500 uppercase mt-1">Puas</span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-10 w-full text-center">
            <div>
                <div class="text-[10px] font-bold text-gray-400 uppercase">Puas</div>
                <div class="font-black text-emerald-500">{{ $skor_kepuasan['puas'] }}</div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-400 uppercase">Cukup</div>
                <div class="font-black text-amber-500">{{ $skor_kepuasan['cukup'] }}</div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-400 uppercase">Kurang</div>
                <div class="font-black text-rose-500">{{ $skor_kepuasan['kurang'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Baris 2: Tambahan Grafik Distribusi Kunjungan per Keperluan --}}
<div class="bg-white p-10 rounded-[2.5rem] border border-gray-50 shadow-sm">
    <h3 class="text-xl font-black text-gray-800 tracking-tight mb-8 text-left">Distribusi Kunjungan per Keperluan</h3>
    <div class="h-[300px] w-full">
        <canvas id="keperluanChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Inisialisasi Chart Skor Kepuasan
    const ctxSat = document.getElementById('satisfactionChart').getContext('2d');
    new Chart(ctxSat, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [{{ $skor_kepuasan['puas'] }}, {{ $skor_kepuasan['cukup'] }}, {{ $skor_kepuasan['kurang'] }}],
                backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'], // Emerald, Amber, Rose
                borderWidth: 0,
                cutout: '82%',
                borderRadius: 15
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. Inisialisasi Chart Distribusi Keperluan
    const ctxKep = document.getElementById('keperluanChart').getContext('2d');
    new Chart(ctxKep, {
        type: 'bar',
        data: {
            labels: {!! json_encode($distribusi_label) !!},
            datasets: [{
                label: 'Jumlah Kunjungan',
                data: {!! json_encode($distribusi_data) !!},
                backgroundColor: '#3b82f6', // Warna biru (blue-500) sesuai desain
                borderRadius: {topLeft: 12, topRight: 12, bottomLeft: 12, bottomRight: 12}, // Efek rounded yang konsisten
                barThickness: 40,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: true, drawBorder: false, color: '#f8fafc', borderDash: [5, 5] },
                    ticks: { color: '#94a3b8', font: { weight: '600', size: 10 }, stepSize: 15 }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { weight: '700', size: 11 } }
                }
            }
        }
    });

    // 3. Inisialisasi Chart Tren SLA
    const ctxSla = document.getElementById('slaChart').getContext('2d');
    new Chart(ctxSla, {
        type: 'line',
        data: {
            labels: {!! json_encode($label_sla) !!},
            datasets: [
                {
                    label: 'Tepat Waktu',
                    data: {!! json_encode($data_tepat_waktu) !!},
                    borderColor: '#10b981', // Warna Emerald untuk Tepat Waktu
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4, // Membuat garisnya melengkung halus (curved)
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 4
                },
                {
                    label: 'Terlambat',
                    data: {!! json_encode($data_terlambat) !!},
                    borderColor: '#f43f5e', // Warna Rose untuk Terlambat
                    backgroundColor: 'rgba(244, 63, 94, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#f43f5e',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: { usePointStyle: true, boxWidth: 8, font: { weight: 'bold', size: 11 }, color: '#64748b' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#94a3b8', font: { weight: '600' } },
                    grid: { color: '#f8fafc', drawBorder: false }
                },
                x: {
                    ticks: { color: '#64748b', font: { weight: '700' } },
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
