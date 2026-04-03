<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem - Politeknik Negeri Banjarmasin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); }
        .gradient-btn { background: linear-gradient(90deg, #6366f1 0%, #f43f5e 100%); }
        .role-radio:checked + label { border-color: #6366f1; color: #6366f1; background-color: #f5f3ff; font-weight: 700; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-5xl w-full flex flex-col md:flex-row overflow-hidden rounded-[2.5rem] shadow-2xl shadow-slate-200 border border-white">

        <div class="md:w-5/12 relative bg-slate-900 p-12 flex flex-col justify-between text-white overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-600/20 rounded-full blur-3xl -mr-20 -mt-20"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center font-bold text-xl">IN</div>
                    <div>
                        <h2 class="font-bold text-lg leading-tight uppercase tracking-tight">Politeknik Negeri Banjarmasin</h2>
                        <p class="text-[10px] text-indigo-300 font-medium tracking-widest uppercase">Command Center</p>
                    </div>
                </div>

                <h1 class="text-4xl font-extrabold leading-tight mb-4">Sistem Informasi <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-rose-400 uppercase">Pelayanan Terpadu</span></h1>
                <p class="text-slate-400 text-sm leading-relaxed max-w-xs uppercase tracking-wide">Kelola antrean, pantau KPI, dan tingkatkan kualitas layanan institusi dalam satu dasbor.</p>
            </div>

            <div class="relative z-10 pt-10">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Digital Gate System v2.0</p>
            </div>
        </div>

        <div class="md:w-7/12 bg-white p-10 md:p-16 relative">
            <a href="{{ route('landing') }}" class="absolute top-8 right-8 text-xs font-bold text-slate-400 hover:text-indigo-600 flex items-center gap-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Beranda
            </a>

            <div class="mb-10">
                <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h2 class="text-3xl font-black text-slate-800 mb-2">Selamat Datang</h2>
                <p class="text-slate-400 text-sm font-medium">Silakan masuk dengan akun sesuai peran Anda.</p>
            </div>

         <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                    @if ($errors->any())
            <div class="bg-red-50 text-red-500 p-4 rounded-2xl mb-6 text-sm font-bold">
                {{ $errors->first() }}
            </div>
        @endif
                        @csrf
               <div>
    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Alamat Email</label>
    <input type="email" name="email" placeholder="nama_email@poliban.ac.id"
           class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-semibold text-slate-700"
           value="{{ old('email') }}" required>
</div>
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Password</label>
                        <a href="#" class="text-[10px] font-black text-rose-500 uppercase tracking-widest hover:underline">Lupa Password?</a>
                    </div>
                    <input type="password" name="password" placeholder="••••••••" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-semibold text-slate-700">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 uppercase">Pilih Role (Akses Sistem)</label>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <input type="radio" name="role" id="roleAdmin" value="admin" class="hidden role-radio" checked>
                            <label for="roleAdmin" class="flex items-center justify-center py-3 border border-slate-100 rounded-xl text-[10px] font-black uppercase tracking-tighter cursor-pointer hover:bg-slate-50 transition-all">Admin</label>
                        </div>
                        <div>
                            <input type="radio" name="role" id="roleLeader" value="leader" class="hidden role-radio">
                            <label for="roleLeader" class="flex items-center justify-center py-3 border border-slate-100 rounded-xl text-[10px] font-black uppercase tracking-tighter cursor-pointer hover:bg-slate-50 transition-all">Leader</label>
                        </div>
                        <div>
                            <input type="radio" name="role" id="roleSuper" value="super" class="hidden role-radio">
                            <label for="roleSuper" class="flex items-center justify-center py-3 border border-slate-100 rounded-xl text-[10px] font-black uppercase tracking-tighter cursor-pointer hover:bg-slate-50 transition-all">Super</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full gradient-btn text-white py-5 rounded-[1.5rem] font-extrabold text-sm uppercase tracking-widest shadow-lg shadow-indigo-200 hover:opacity-90 transition-all active:scale-95">
                    Masuk Sistem
                </button>
            </form>
        </div>
    </div>

</body>
</html>
