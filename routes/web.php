<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KunjunganController;

Route::get('/', [KunjunganController::class, 'create'])->name('landing');
Route::post('/kunjungan', [KunjunganController::class, 'store'])->name('kunjungan.store');