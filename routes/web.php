<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\PimpinanKonfirmasiController;
// Import Middleware Manual yang dibuat
use App\Http\Middleware\CekSessionLogin;

/*
|--------------------------------------------------------------------------
| 1. BAGIAN PENGUNJUNG (Public)
|--------------------------------------------------------------------------
*/
Route::get('/', 'App\Http\Controllers\KunjunganController@create')->name('landing');
Route::post('/kunjungan', 'App\Http\Controllers\KunjunganController@store')->name('kunjungan.store');

// UBAH: {kunjungan} menjadi parameter biasa {id} agar Laravel tidak mencari ke MySQL
Route::get('/status/{id}', 'App\Http\Controllers\KunjunganController@cekStatus')->name('kunjungan.status');
Route::get('/survei/{id}', 'App\Http\Controllers\KunjunganController@formSurvey')->name('survey.form');
Route::post('/survei/simpan', 'App\Http\Controllers\KunjunganController@storeSurvey')->name('survey.store');

/*
|--------------------------------------------------------------------------
| 2. BAGIAN AUTHENTICATION
|--------------------------------------------------------------------------
*/
// UBAH: Hapus middleware 'guest' karena pengecekan session lama sudah 
// dilakukan langsung di dalam AuthController@showLogin
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');


/*
|--------------------------------------------------------------------------
| 3. BAGIAN DASHBOARD (Wajib Login)
|--------------------------------------------------------------------------
*/
// UBAH: Hapus middleware 'auth' bawaan Laravel dan panggil middleware session manual kita
Route::middleware([CekSessionLogin::class])->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/dashboard/antrean/kirim-massal', 'App\Http\Controllers\KunjunganController@kirimMassal')->name('kunjungan.kirim-massal');
    
    // Halaman Khusus Pimpinan
    Route::get('/dashboard/pimpinan/konfirmasi', [PimpinanKonfirmasiController::class, 'index'])->name('pimpinan.konfirmasi');
    Route::post('/dashboard/pimpinan/konfirmasi/{id}/tanggapan', [PimpinanKonfirmasiController::class, 'tanggapan'])->name('pimpinan.tanggapan');

    /**
     * ROUTE UTAMA
     */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /**
     * HALAMAN UMUM (Semua Role)
     */
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('/dashboard/ulasan', [DashboardController::class, 'ulasanLayanan'])->name('dashboard.ulasan');
    Route::get('/dashboard/laporan', [DashboardController::class, 'laporan'])->name('dashboard.laporan');

    /**
     * HALAMAN OPERASIONAL (Admin & Super Admin)
     */
    Route::get('/dashboard/manajemen-antrean', [DashboardController::class, 'manajemenAntrean'])->name('dashboard.antrean');
    
    // UBAH: Binding parameter disesuaikan dengan isi Controller
    Route::post('/dashboard/mulai-proses/{nomor_kunjungan}', [DashboardController::class, 'mulaiProses'])->name('kunjungan.mulaiProses');
    Route::post('/dashboard/tolak/{id}', [DashboardController::class, 'tolak'])->name('kunjungan.tolak');
    Route::post('/dashboard/antrean/{id}/selesai', [DashboardController::class, 'selesai'])->name('kunjungan.selesai');

    /**
     * SISTEM TANGGAPAN & EMAIL
     */
    Route::post('/dashboard/antrean/{id}/tanggapan', [DashboardController::class, 'tanggapanPimpinan'])->name('kunjungan.tanggapan');
    Route::post('/dashboard/kirim-email', [DashboardController::class, 'kirimEmailPimpinan'])->name('kunjungan.kirim-email');

    /**
     * --- CONTROL PANEL (Hanya Super Admin) ---
     */
    Route::get('/dashboard/control-panel', [DashboardController::class, 'controlPanel'])->name('dashboard.control_panel');
    Route::post('/dashboard/keperluan', [DashboardController::class, 'storeKeperluan'])->name('keperluan.store');
    Route::delete('/dashboard/keperluan/{id}', [DashboardController::class, 'destroyKeperluan'])->name('keperluan.destroy');
    Route::post('/dashboard/users', [DashboardController::class, 'storeUser'])->name('users.store');

});