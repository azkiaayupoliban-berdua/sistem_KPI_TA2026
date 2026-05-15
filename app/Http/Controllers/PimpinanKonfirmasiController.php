<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PimpinanKonfirmasiController extends Controller
{
    
public function index()
{
    $user = Auth::user();

    $query = Kunjungan::with('pengunjung')
        ->where('is_forwarded', 1);

    // KAJUR
    if ($user->role_id == 3) {

        $query->where('tujuan_pimpinan', 'kajur');

    }

    // KAPRODI
    elseif ($user->role_id == 4) {

        $query->where('tujuan_pimpinan', 'kaprodi')
              ->where('prodi_id', $user->prodi_id);

    }

    $data_konfirmasi = $query
        ->orderBy('created_at', 'desc')
        ->get();

    return view('pimpinan.konfirmasi', compact('data_konfirmasi'));
}

public function tanggapan(Request $request, $id)
{
    $kunjungan = Kunjungan::findOrFail($id);

    // Ambil data dari input, jika kosong beri nilai default agar database tidak protes (NULL)
    $kunjungan->update([
        'status_pimpinan' => $request->status_pimpinan ?? 'Sudah Direspon',
        'catatan_pimpinan' => $request->catatan_pimpinan ?? '-'
    ]);

    return redirect()->back()->with('success', 'Konfirmasi berhasil dikirim.');
}
}