<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\MasterUser; // Pastikan nama model sesuai dengan tabel master_user Anda
use App\Models\MasterRole;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        // 1. PASTIKAN ROLE TERSEDIA (Mencegah error Foreign Key)
        $roles = [
            ['id' => 1, 'nama_role' => 'Super Admin'],
            ['id' => 2, 'nama_role' => 'Admin Prodi'],
            ['id' => 3, 'nama_role' => 'Ketua Jurusan'],
            ['id' => 4, 'nama_role' => 'Ketua Prodi'],
        ];

        foreach ($roles as $role) {
            MasterRole::updateOrCreate(['id' => $role['id']], $role);
        }

        // 2. SUPER ADMIN (Role ID: 1)
        MasterUser::updateOrCreate(
            ['email' => 'super@poliban.ac.id'],
            [
                'name'      => 'Super Administrator',
                'password'  => $password,
                'role_id'   => 1,
            ]
        );

        // 3. KETUA JURUSAN ELEKTRO (Role ID: 3)
        MasterUser::updateOrCreate(
            ['email' => 'kajur.elektro@poliban.ac.id'],
            [
                'name'      => 'Ketua Jurusan Elektro',
                'password'  => $password,
                'role_id'   => 3,
            ]
        );

        // DAFTAR PRODI UNTUK LOOPING
        $daftar_prodi = [
            'Teknik Informatika' => 'ti',
            'Elektronika' => 'elka',
            'Teknik Listrik' => 'listrik',
            'Teknologi Rekayasa Pembangkit Energi' => 'trpe',
            'Sistem Informasi Kota Cerdas' => 'sikc',
            'Teknologi Rekayasa Otomasi' => 'tro',
        ];

        foreach ($daftar_prodi as $nama_prodi => $slug) {

            // 4. KAPRODI (Role ID: 4)
            MasterUser::updateOrCreate(
                ['email' => 'kaprodi.' . $slug . '@poliban.ac.id'],
                [
                    'name'      => 'Kaprodi ' . $nama_prodi,
                    'password'  => $password,
                    'role_id'   => 4,
                ]
            );

            // 5. ADMIN PRODI (Role ID: 2)
            MasterUser::updateOrCreate(
                ['email' => 'admin.' . $slug . '@poliban.ac.id'],
                [
                    'name'      => 'Admin ' . $nama_prodi,
                    'password'  => $password,
                    'role_id'   => 2,
                ]
            );
        }
    }
}
