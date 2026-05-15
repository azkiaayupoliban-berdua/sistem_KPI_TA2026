<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - @yield('title')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        *{
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body{
            background: #f6f7fb;
        }

        ::-webkit-scrollbar{
            width:6px;
        }

        ::-webkit-scrollbar-thumb{
            background:#dbe1ea;
            border-radius:20px;
        }

        .sidebar-scroll::-webkit-scrollbar{
            display:none;
        }

        .sidebar-scroll{
            -ms-overflow-style:none;
            scrollbar-width:none;
        }

        .menu-active{
            background: linear-gradient(90deg,#f3e8ff 0%, #ede9fe 100%);
            color:#7c3aed;
            font-weight:800;
            position:relative;
        }

        .menu-active::before{
            content:'';
            position:absolute;
            left:0;
            top:14px;
            bottom:14px;
            width:4px;
            border-radius:999px;
            background:#9333ea;
        }

        .menu-hover:hover{
            background:#f8fafc;
            color:#0f172a;
        }

        .glass{
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(14px);
        }
    </style>
</head>

<body class="h-screen overflow-hidden">

<div class="flex h-screen overflow-hidden">

    {{-- MOBILE OVERLAY --}}
    <div id="sidebarOverlay"
        onclick="toggleSidebar()"
        class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden">
    </div>

    {{-- SIDEBAR --}}
    <aside id="sidebar"
        class="fixed lg:relative z-50 lg:z-0 top-0 left-0 h-screen w-[290px] bg-white border-r border-slate-100 flex flex-col transition-all duration-300 -translate-x-full lg:translate-x-0">

        {{-- LOGO --}}
        <div class="h-24 px-6 flex items-center border-b border-slate-100">

            <div class="w-12 h-12 rounded-2xl overflow-hidden flex items-center justify-center bg-indigo-50 shadow-sm">
                <img src="{{ asset('img/logo-poliban.png') }}"
                    alt="Logo"
                    class="w-9 h-9 object-contain">
            </div>

            <div class="ml-4">
                <h1 class="font-black text-slate-900 text-sm leading-tight">
                    Jurusan Teknik Elektro
                </h1>

                <p class="text-[10px] uppercase tracking-[0.25em] text-slate-400 font-bold mt-1">
                    Admin Panel
                </p>
            </div>

        </div>

        {{-- MENU --}}
        <div class="flex-1 overflow-y-auto sidebar-scroll px-4 py-6">

            <nav class="space-y-2">

                {{-- DASHBOARD --}}
                @if(in_array(Auth::user()->role_id, [1,2]))
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-200
                    {{ request()->routeIs('dashboard') ? 'menu-active' : 'menu-hover text-slate-400' }}">

                    <i class="fa-solid fa-chart-pie text-lg"></i>

                    <span class="font-bold text-sm">
                        Dashboard
                    </span>

                </a>
                @endif

                {{-- ANTREAN --}}
                @if(in_array(Auth::user()->role_id, [1,2]))
                <a href="{{ route('dashboard.antrean') }}"
                    class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-200
                    {{ request()->routeIs('dashboard.antrean') ? 'menu-active' : 'menu-hover text-slate-400' }}">

                    <i class="fa-solid fa-users-viewfinder text-lg"></i>

                    <span class="font-bold text-sm">
                        Manajemen Antrean
                    </span>

                </a>
                @endif

                {{-- ANALYTICS --}}
                <a href="{{ route('dashboard.analytics') }}"
                    class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-200
                    {{ request()->routeIs('dashboard.analytics') ? 'menu-active' : 'menu-hover text-slate-400' }}">

                    <i class="fa-solid fa-chart-simple text-lg"></i>

                    <span class="font-bold text-sm">
                        Analytics KPI
                    </span>

                </a>

                {{-- LAPORAN --}}
                <a href="{{ route('dashboard.laporan') }}"
                    class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-200
                    {{ request()->routeIs('dashboard.laporan') ? 'menu-active' : 'menu-hover text-slate-400' }}">

                    <i class="fa-solid fa-file-export text-lg"></i>

                    <span class="font-bold text-sm">
                        Laporan Ekspor
                    </span>

                </a>

                {{-- ULASAN --}}
                <a href="{{ route('dashboard.ulasan') }}"
                    class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-200
                    {{ request()->routeIs('dashboard.ulasan') ? 'menu-active' : 'menu-hover text-slate-400' }}">

                    <i class="fa-solid fa-comment-dots text-lg"></i>

                    <span class="font-bold text-sm">
                        Ulasan Pengunjung
                    </span>

                </a>

                {{-- PIMPINAN --}}
                @if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2)

                <div class="pt-5 mt-5 border-t border-slate-100">

                    <p class="px-4 mb-3 text-[10px] uppercase tracking-[0.3em] text-slate-300 font-black">
                        Tugas Pimpinan
                    </p>

                    <a href="{{ route('pimpinan.konfirmasi') }}"
                        class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-200 relative
                        {{ request()->routeIs('pimpinan.konfirmasi') ? 'bg-amber-50 text-amber-600 font-black' : 'menu-hover text-slate-400' }}">

                        <i class="fa-solid fa-file-signature text-lg"></i>

                        <span class="font-bold text-sm">
                            Konfirmasi Masuk
                        </span>

                        <span class="absolute right-5 w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>

                    </a>

                </div>

                @endif

                {{-- SUPER ADMIN --}}
                @if(Auth::user()->role_id == 1)

                <div class="pt-5 mt-5 border-t border-slate-100">

                    <p class="px-4 mb-3 text-[10px] uppercase tracking-[0.3em] text-slate-300 font-black">
                        System Admin
                    </p>

                    <a href="{{ route('dashboard.control_panel') }}"
                        class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-200
                        {{ request()->routeIs('dashboard.control_panel') ? 'menu-active' : 'menu-hover text-slate-400' }}">

                        <i class="fa-solid fa-gears text-lg"></i>

                        <span class="font-bold text-sm">
                            Sistem Control
                        </span>

                    </a>

                </div>

                @endif

            </nav>

        </div>

        {{-- LOGOUT --}}
        <div class="p-5 border-t border-slate-100">

            <form action="{{ route('logout') }}"
                method="POST"
                id="logout-form">

                @csrf

                <button type="button"
                    onclick="confirmLogout()"
                    class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl text-rose-500 hover:bg-rose-50 transition-all">

                    <i class="fa-solid fa-arrow-right-from-bracket"></i>

                    <span class="font-bold text-sm">
                        Keluar
                    </span>

                </button>

            </form>

        </div>

    </aside>

    {{-- MAIN --}}
    <main class="flex-1 overflow-y-auto">

        {{-- TOPBAR --}}
        <header class="h-24 px-4 sm:px-6 lg:px-8 border-b border-slate-100 glass sticky top-0 z-30">

            <div class="h-full flex items-center justify-between">

                {{-- LEFT --}}
                <div class="flex items-center gap-4">

                    {{-- MOBILE BUTTON --}}
                    <button
                        onclick="toggleSidebar()"
                        class="lg:hidden w-11 h-11 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-600 shadow-sm">

                        <i class="fa-solid fa-bars"></i>

                    </button>

                    <div class="hidden sm:flex items-center gap-3 px-4 py-2 rounded-full bg-emerald-50 border border-emerald-100">

                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>

                        <span class="text-[11px] uppercase tracking-widest text-emerald-600 font-black">
                            Auto-Refresh: ON
                        </span>

                    </div>

                </div>

                {{-- RIGHT --}}
                <div class="flex items-center gap-4">

                    {{-- NOTIF --}}
                    <button class="relative w-11 h-11 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 shadow-sm hover:bg-slate-50 transition-all">

                        <i class="fa-regular fa-bell"></i>

                        <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full"></span>

                    </button>

                    {{-- USER --}}
                    <div class="flex items-center gap-3">

                        <div class="hidden sm:block text-right">

                            <h3 class="text-sm font-black text-slate-900 leading-tight">
                                {{ Auth::user()->name }}
                            </h3>

                            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mt-1">

                                @if(Auth::user()->role_id == 1)
                                    Master Administrator
                                @elseif(Auth::user()->email === 'kajur.elektro@poliban.ac.id')
                                    Ketua Jurusan
                                @elseif(Auth::user()->role_id == 2)
                                    Admin Prodi
                                @else
                                    Ketua Program Studi
                                @endif

                            </p>

                        </div>

                        <div class="w-12 h-12 rounded-full bg-indigo-500 text-white flex items-center justify-center font-black shadow-lg">

                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}

                        </div>

                    </div>

                </div>

            </div>

        </header>

        {{-- CONTENT --}}
        <div class="p-4 sm:p-6 lg:p-8">
            @yield('content')
        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    function toggleSidebar() {

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');

    }

    function confirmLogout() {

        Swal.fire({
            title: 'Keluar dari sistem?',
            text: 'Sesi login akan diakhiri.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#e2e8f0',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-[2rem]'
            }
        }).then((result) => {

            if(result.isConfirmed){
                document.getElementById('logout-form').submit();
            }

        });

    }

</script>

@stack('scripts')

</body>
</html>