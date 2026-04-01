<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengunjung;
use App\Models\Kunjungan;
use App\Models\MasterProdiInstansi;
use App\Models\MasterKeperluan;
use Carbon\Carbon;

class KunjunganController extends Controller
{
    public function create()
    {
        // Mengambil data master untuk Dropdown di Landing Page
        $prodi = MasterProdiInstansi::where('jenis', 'Prodi')->get();
        $keperluan = MasterKeperluan::all();
        
        return view('landing', compact('prodi', 'keperluan'));
    }

    public function store(Request $request)
    {
        // Validasi Form
        $request->validate([
            'nama_lengkap' => 'required|string|max:50',
            'no_telepon' => 'required|string|max:15',
            'asal_instansi' => 'required|string|max:50',
            'prodi_id' => 'required|integer',
            'keperluan_id' => 'required|integer'
        ]);

        // 1. Simpan/Cek Pengunjung
        $pengunjung = Pengunjung::firstOrCreate(
            ['no_telepon' => $request->no_telepon],
            [
                'nama_lengkap' => $request->nama_lengkap,
                'identitas_no' => $request->identitas_no,
                'asal_instansi' => $request->asal_instansi
            ]
        );

        // 2. Generate Nomor Kunjungan Unik (Misal: IN-260401-123)
        $nomor_kunjungan = 'IN-' . date('ymd') . '-' . rand(100, 999);

        // 3. Simpan Kunjungan
        Kunjungan::create([
            'nomor_kunjungan' => $nomor_kunjungan,
            'pengunjung_id' => $pengunjung->id,
            'prodi_id' => $request->prodi_id,
            'keperluan_id' => $request->keperluan_id,
            'keperluan' => $request->catatan_keperluan,
            'hari_kunjungan' => Carbon::now()->isoFormat('dddd'),
            'tanggal' => Carbon::now()->toDateString(),
            'status_layanan' => 'Antre'
        ]);

        return redirect()->back()->with('success', 'Pendaftaran Berhasil! Nomor Antrean Anda: ' . $nomor_kunjungan);
    }
}