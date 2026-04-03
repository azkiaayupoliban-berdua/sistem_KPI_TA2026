<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengunjung;
use App\Models\Kunjungan;
use App\Models\MasterProdiInstansi;
use App\Models\MasterKeperluan;
// Tambahkan Model Survey & Aspek agar bisa digunakan di fungsi baru
use App\Models\MasterAspekSurvey;
use App\Models\Survey;
use App\Models\DetailSurvey;
use Carbon\Carbon;

class KunjunganController extends Controller
{
    public function create()
    {
        $prodi = MasterProdiInstansi::where('jenis', 'Prodi')->get();

        // HAPUS ATAU KOMENTARI BARIS LAMA INI:
        // $keperluan = MasterKeperluan::all();

        // GANTI DENGAN BARIS BARU INI (Filter Duplikat):
        $keperluan = \Illuminate\Support\Facades\DB::table('master_keperluan')
                        ->select('keterangan', \Illuminate\Support\Facades\DB::raw('MIN(id) as id'))
                        ->groupBy('keterangan')
                        ->get();

        return view('landing', compact('prodi', 'keperluan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:50',
            'no_telepon' => 'required|string|max:15',
            'asal_instansi' => 'required|string|max:50',
            'prodi_id' => 'required|integer',
            'keperluan_id' => 'required|integer'
        ]);

        $pengunjung = Pengunjung::firstOrCreate(
            ['no_telepon' => $request->no_telepon],
            [
                'nama_lengkap' => $request->nama_lengkap,
                'identitas_no' => $request->identitas_no,
                'asal_instansi' => $request->asal_instansi
            ]
        );

        // Generate Nomor Kunjungan Unik
        $nomor_kunjungan = 'IN-' . date('ymd') . '-' . rand(100, 999);

        Kunjungan::create([
            'nomor_kunjungan' => $nomor_kunjungan,
            'pengunjung_id' => $pengunjung->id,
            'prodi_id' => $request->prodi_id,
            'keperluan_id' => $request->keperluan_id,
            'keperluan' => $request->catatan_keperluan,
            'hari_kunjungan' => Carbon::now()->isoFormat('dddd'),
            'tanggal' => Carbon::now()->toDateString(),
            'status_layanan' => 'Antre' // Status awal
        ]);

        return redirect()->route('kunjungan.status', ['kunjungan' => $nomor_kunjungan]);
    }

    public function cekStatus(Kunjungan $kunjungan)
    {
        // PERBAIKAN: Menghitung durasi pelayanan dalam menit (dibulatkan)
        // agar tidak muncul angka desimal panjang di view
        $durasi_menit = 0;
        if ($kunjungan->waktu_selesai_layanan) {
            $durasi_menit = round($kunjungan->created_at->diffInMinutes($kunjungan->waktu_selesai_layanan));
        }

        return view('proses', compact('kunjungan', 'durasi_menit'));
    }

    /**
     * FUNGSI BARU: Menampilkan Form Survey Dinamis dari Database
     */
 public function formSurvey($id)
{
    // Ambil data kunjungan berdasarkan nomor_kunjungan (identifikasi tamu)
    $kunjungan = Kunjungan::where('nomor_kunjungan', $id)->firstOrFail();

    // Ambil nama pengunjung dari relasi, jika tidak ada gunakan session atau default 'Tamu'
    $nama_tamu = $kunjungan->pengunjung->nama_lengkap ?? session('nama_tamu', 'Tamu');

    // AMBIL DATA DARI DATABASE:
    // Gunakan Eager Loading 'with' untuk mengambil aspek beserta pertanyaannya sekaligus
    $aspek_survey = MasterAspekSurvey::with('pertanyaan')->get();

    // Perbaikan durasi agar tidak desimal panjang (seperti 2.2833... di gambar)
    $durasi_menit = 0;
    if ($kunjungan->waktu_selesai_layanan) {
        $durasi_menit = round($kunjungan->created_at->diffInMinutes($kunjungan->waktu_selesai_layanan));
    }

    return view('guest.form-survey', compact('kunjungan', 'aspek_survey', 'nama_tamu', 'durasi_menit'));
}

    /**
     * FUNGSI BARU: Menyimpan hasil survey ke tabel Survey dan DetailSurvey
     */
public function storeSurvey(Request $request)
{
    $request->validate([
        'nomor_kunjungan' => 'required',
        'jawaban' => 'required|array',
        'catatan' => 'nullable|string', // Validasi untuk input textarea dari form
    ]);

    $kunjungan = Kunjungan::where('nomor_kunjungan', $request->nomor_kunjungan)->firstOrFail();

    // 1. Simpan Header Survey ke tabel 'survey'
    // Kolom 'kritik_saran' digunakan sesuai struktur image_34eded.png
    $survey = Survey::create([
        'kunjungan_id' => $kunjungan->id,
        'kritik_saran' => $request->catatan, // Data kritik masuk ke sini
        'created_at'   => now(),
        'updated_at'   => now(),
    ]);

    // 2. Simpan Detail Jawaban ke tabel 'detail_survey' (p1-p5)
    // Sesuai struktur kolom pada image_34d45e.png
    DetailSurvey::create([
        'survey_id' => $survey->id,
        'p1' => $request->jawaban[1] ?? 0,
        'p2' => $request->jawaban[2] ?? 0,
        'p3' => $request->jawaban[3] ?? 0,
        'p4' => $request->jawaban[4] ?? 0,
        'p5' => $request->jawaban[5] ?? 0,
    ]);

    return back()->with('success', 'Terima kasih atas ulasan Anda!');
}
}
