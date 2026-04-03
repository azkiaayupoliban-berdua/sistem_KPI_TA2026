@extends('layouts.app') @section('content')
<div class="px-8 py-6 max-w-7xl mx-auto">

    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 mb-1">Laporan & Ekspor</h1>
            <p class="text-slate-500 text-sm">Unduh laporan performa layanan secara mendetail.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">

            <form action="{{ route('dashboard.laporan') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="flex items-center bg-white border border-slate-200 rounded-full overflow-hidden shadow-sm">
                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="text-sm border-none focus:ring-0 px-4 py-2.5 text-slate-600 bg-transparent outline-none">
                    <span class="text-slate-300">-</span>
                    <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="text-sm border-none focus:ring-0 px-4 py-2.5 text-slate-600 bg-transparent outline-none">
                </div>

                <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-slate-700 transition-colors shadow-sm">
                    Filter
                </button>

                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('dashboard.laporan') }}" class="text-slate-400 hover:text-rose-500 text-sm transition-colors px-2">
                        Reset
                    </a>
                @endif
            </form>

            <div class="w-px h-8 bg-slate-200 hidden sm:block mx-1"></div>

            <button class="bg-white border border-slate-200 px-4 py-2.5 rounded-full flex items-center gap-2 text-sm text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <i class="fa-regular fa-file-excel"></i> Ekspor Excel
            </button>
            <button class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-6 py-2.5 rounded-full flex items-center gap-2 text-sm font-semibold hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all">
                <i class="fa-solid fa-download"></i> Ekspor PDF
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
            <h3 class="text-slate-400 text-xs font-bold tracking-widest uppercase mb-4">Total Layanan Selesai</h3>
            <div class="text-5xl font-black text-slate-800 mb-2">{{ number_format($totalSelesai, 0, ',', '.') }}</div>
            <p class="text-emerald-500 text-sm font-medium flex items-center gap-1">
                <i class="fa-solid fa-check-circle"></i> Data layanan berhasil
            </p>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
            <h3 class="text-slate-400 text-xs font-bold tracking-widest uppercase mb-4">Rata-Rata SLA</h3>
            <div class="text-5xl font-black text-slate-800 mb-2">
                {{ $rataRataSla }} <span class="text-2xl text-slate-500 font-bold">menit</span>
            </div>
            <p class="text-emerald-500 text-sm font-medium flex items-center gap-1">
                <i class="fa-solid fa-clock"></i> Waktu rata-rata proses
            </p>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
            <h3 class="text-slate-400 text-xs font-bold tracking-widest uppercase mb-4">Tingkat Penolakan</h3>
            <div class="text-5xl font-black text-slate-800 mb-2">{{ $tingkatPenolakan }}%</div>
            <p class="text-rose-500 text-sm font-medium flex items-center gap-1">
                <i class="fa-solid fa-xmark-circle"></i> Persentase layanan ditolak
            </p>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
        <div class="mb-8">
            <h3 class="text-xl font-bold text-slate-800 mb-1">Perbandingan Kinerja Layanan</h3>
            <p class="text-slate-500 text-sm">
                @if(request('start_date') && request('end_date'))
                    Jumlah layanan diselesaikan berdasarkan filter tanggal.
                @else
                    Jumlah layanan diselesaikan per hari dalam minggu ini.
                @endif
            </p>
        </div>
        <div class="relative h-72 w-full">
            <canvas id="kinerjaChart"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('kinerjaChart').getContext('2d');

        // Data dari Controller
        const labels = {!! json_encode($labelGrafik) !!};
        const data = {!! json_encode($dataGrafik) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Layanan Selesai',
                    data: data,
                    backgroundColor: '#4f46e5', // Warna Indigo (menyesuaikan tema Figma)
                    borderRadius: 6, // Ujung bar agak membulat
                    barThickness: 16, // Ketebalan bar
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }, // Sembunyikan tulisan legend default
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
                        grid: {
                            color: '#f1f5f9', // Garis grid horizontal samar
                            drawBorder: false,
                        },
                        ticks: { color: '#94a3b8', stepSize: 1 }
                    },
                    x: {
                        grid: { display: false }, // Sembunyikan garis grid vertikal
                        ticks: { color: '#64748b', font: { weight: '600' } }
                    }
                }
            }
        });
    });
</script>
@endsection
