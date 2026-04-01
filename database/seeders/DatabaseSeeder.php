<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterProdiInstansi;
use App\Models\MasterKeperluan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Mengisi Data Master Prodi
        MasterProdiInstansi::create([
            'nama' => 'Teknik Informatika',
            'jenis' => 'Prodi'
        ]);
        
        MasterProdiInstansi::create([
            'nama' => 'Teknik Elektro',
            'jenis' => 'Prodi'
        ]);

        MasterProdiInstansi::create([
            'nama' => 'Sistem Informasi Kota Cerdas',
            'jenis' => 'Prodi'
        ]);

        // 2. Mengisi Data Master Keperluan
        MasterKeperluan::create([
            'keterangan' => 'Legalisir Ijazah'
        ]);

        MasterKeperluan::create([
            'keterangan' => 'Konsultasi Tugas Akhir'
        ]);

        MasterKeperluan::create([
            'keterangan' => 'Penyerahan Berkas / Laporan'
        ]);

        MasterKeperluan::create([
            'keterangan' => 'Lainnya'
        ]);
    }
}