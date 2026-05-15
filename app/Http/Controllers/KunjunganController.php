<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengunjung;
use App\Models\Kunjungan;
use App\Models\MasterProdiInstansi;
use App\Models\MasterKeperluan;
use App\Models\MasterAspekSurvey;
use App\Models\MasterUser;
use App\Models\Survey;
use App\Models\DetailSurvey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KunjunganController extends Controller
{
    public function create()
    {
       // Tambahkan ->query()
        $prodi = MasterProdiInstansi::query()->where('jenis', 'Prodi')->get();

        $keperluan = DB::table('master_keperluan')
                        ->select('keterangan', DB::raw('MIN(id) as id'))
                        ->groupBy('keterangan')
                        ->get();

        return view('landing', compact('prodi', 'keperluan'));
    }

public function store(Request $request)
{
    // 1. Validasi Input
    $request->validate([
        'nama_lengkap'   => 'required|string|max:50',
        'no_telepon'     => 'required|string|max:15',
        'asal_instansi'  => 'required|string|max:50',
        'prodi_id'       => 'required|integer',
        'keperluan_id'   => 'required|integer'
    ]);

    try {

        // =========================================
        // TRANSACTION
        // =========================================
        $kunjungan = DB::transaction(function () use ($request) {

            // =========================================
            // SIMPAN / AMBIL PENGUNJUNG
            // =========================================
            $pengunjung = Pengunjung::firstOrCreate(
                [
                    'no_telepon' => $request->no_telepon
                ],
                [
                    'nama_lengkap' => $request->nama_lengkap,
                    'identitas_no' => $request->identitas_no,
                    'asal_instansi'=> $request->asal_instansi
                ]
            );

            // =========================================
            // AMBIL DATA MASTER KEPERLUAN
            // =========================================
            $masterKeperluan = MasterKeperluan::find($request->keperluan_id);

            // =========================================
            // GABUNGKAN DROPDOWN + DETAIL
            // =========================================
            $keperluanGabungan = $masterKeperluan->keterangan ?? '-';

            if (!empty($request->catatan_keperluan)) {

                $keperluanGabungan .= ' - ' . $request->catatan_keperluan;
            }

            // =========================================
            // BUAT NOMOR ANTREAN
            // =========================================
            $nomor_kunjungan = 'IN-' . date('ymd') . '-' . rand(100, 999);

            // =========================================
            // SIMPAN KUNJUNGAN
            // =========================================
return Kunjungan::create([

    'nomor_kunjungan' => $nomor_kunjungan,

    'pengunjung_id'   => $pengunjung->id,

    'prodi_id'        => $request->prodi_id,

    'keperluan_id'    => $request->keperluan_id,

    // HANYA DETAIL TAMBAHAN
    'keperluan'       => $request->catatan_keperluan ?? '-',

    'hari_kunjungan'  => Carbon::now()->isoFormat('dddd'),

    'tanggal'         => Carbon::now()->toDateString(),

    'status_layanan'  => 'Antre',

    'status_pimpinan' => 'Menunggu',
]);
        });

        // =========================================
        // KIRIM EMAIL PIMPINAN
        // =========================================
        try {

            $pimpinan = MasterUser::query()
    ->where(function ($query) use ($request) {

        $query->where('role_id', 4)
              ->where('prodi_id', $request->prodi_id);

    })
    ->orWhere('role_id', 3)
    ->get();

            foreach ($pimpinan as $user) {

                $dataEmail = [
                    'kunjungan' => $kunjungan,
                    'url_login' => url('/login')
                ];

                Mail::send(
                    'emails.notifikasi_kunjungan',
                    $dataEmail,
                    function($message) use ($user) {

                        $message->to($user->email)
                                ->subject('Notifikasi Antrean Baru');
                    }
                );
            }

        } catch (\Exception $e) {

            Log::warning(
                "Email pimpinan gagal: " . $e->getMessage()
            );
        }

        // =========================================
        // REDIRECT SUKSES
        // =========================================
        return redirect()->route(
            'kunjungan.status',
            ['kunjungan' => $kunjungan->nomor_kunjungan]
        )->with(
            'success',
            'Pendaftaran antrean berhasil!'
        );

    } catch (\Exception $e) {

        Log::error(
            "Proses pendaftaran gagal: " . $e->getMessage()
        );

        return back()
            ->withInput()
            ->with(
                'error',
                'Gagal mendaftar antrean. Silakan coba lagi.'
            );
    }
}

    // Fungsi lainnya (cekStatus, formSurvey, storeSurvey) tetap sama...
   public function cekStatus(Kunjungan $kunjungan)
{
    // WAJIB: Muat relasi survey agar bisa dicek di Blade
    $kunjungan->load('survey');

    $durasi_menit = 0;
    if ($kunjungan->waktu_selesai_layanan) {
        $durasi_menit = round($kunjungan->created_at->diffInMinutes($kunjungan->waktu_selesai_layanan));
    }

    return view('proses', compact('kunjungan', 'durasi_menit'));
}

   public function formSurvey($id)
{
    // Cari data kunjungan berdasarkan nomor_kunjungan
   $kunjungan = Kunjungan::query()->where('nomor_kunjungan', $id)->firstOrFail();
    if ($kunjungan->survey) {
        return redirect()->route('kunjungan.status', $id)
                         ->with('error', 'Anda sudah mengisi survey untuk antrean ini.');
    }

    $nama_tamu = $kunjungan->pengunjung->nama_lengkap ?? session('nama_tamu', 'Tamu');
    $aspek_survey = MasterAspekSurvey::with('pertanyaan')->get();

    $durasi_menit = 0;
    if ($kunjungan->waktu_selesai_layanan) {
        $durasi_menit = round($kunjungan->created_at->diffInMinutes($kunjungan->waktu_selesai_layanan));
    }

    return view('guest.form-survey', compact('kunjungan', 'aspek_survey', 'nama_tamu', 'durasi_menit'));
}

public function storeSurvey(Request $request)
{
    // 1. Validasi Input
    $request->validate([
        'nomor_kunjungan' => 'required',
        'jawaban' => 'required|array',
        'catatan' => 'nullable|string',
    ]);

    $kunjungan = \App\Models\Kunjungan::query()
        ->where('nomor_kunjungan', $request->nomor_kunjungan)
        ->first();

    if (!$kunjungan) {
        return back()->with('error', 'Data kunjungan tidak ditemukan.');
    }

    // 3. Logika Hitungan (Total Bintang * 4 untuk Skala 100)
    // Tetap dipertahankan sesuai rumus Poin 3 / Tabel 3.7 Anda
    $p1 = $request->jawaban[1] ?? 0;
    $p2 = $request->jawaban[2] ?? 0;
    $p3 = $request->jawaban[3] ?? 0;
    $p4 = $request->jawaban[4] ?? 0;
    $p5 = $request->jawaban[5] ?? 0;

    $skor_total_y = ($p1 + $p2 + $p3 + $p4 + $p5) * 4;

    // 4. Simpan ke Database dengan Transaction
    DB::transaction(function () use ($request, $kunjungan, $skor_total_y, $p1, $p2, $p3, $p4, $p5) {

        // Simpan ke tabel Survey (Skor skala 100)
        $survey = \App\Models\Survey::create([
            'kunjungan_id' => $kunjungan->id,
            'kritik_saran' => $request->catatan,
            'skor_total'   => $skor_total_y,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Simpan detail per pertanyaan (Bintang 1-5)
        \App\Models\DetailSurvey::create([
            'survey_id' => $survey->id,
            'p1' => $p1,
            'p2' => $p2,
            'p3' => $p3,
            'p4' => $p4,
            'p5' => $p5,
        ]);
    });

    return back()->with('success', 'Terima kasih atas ulasan Anda!');
}

    // =========================================
    // KIRIM MASSAL
    // =========================================
    public function kirimMassal(Request $request)
    {
        $ids = $request->ids;

        $tujuan = $request->tujuan_pimpinan;

        if (empty($ids)) {

            return back()->with(
                'error',
                'Tidak ada data yang dipilih.'
            );
        }

        $statusTujuan = $tujuan == 'kajur'
            ? 'Menunggu Kajur'
            : 'Menunggu Kaprodi';

        Kunjungan::whereIn('id', $ids)->update([
            'is_forwarded'    => 1,
            'tujuan_pimpinan' => $tujuan,
            'status_pimpinan' => $statusTujuan
        ]);

        return back()->with(
            'success',
            count($ids) .
            ' data berhasil diteruskan ke ' .
            ($tujuan == 'kajur'
                ? 'Kajur'
                : 'Kaprodi')
        );
    }

}