<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrasiUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan pengecekan Foreign Key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Bersihkan tabel tujuan
        DB::table('master_role')->truncate();
        DB::table('master_user')->truncate();

        // 3. Buat Role Default (ID 1)
        DB::table('master_role')->insert([
            'id' => 1,
            'nama_role' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Ambil semua data dari tabel 'users'
        $users = DB::table('users')->get();

        // 5. Pindahkan ke 'master_user'
        foreach ($users as $user) {
            DB::table('master_user')->insert([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'role_id' => 1, // Kita hubungkan ke role ID 1
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
        }

        // 6. Hidupkan kembali pengecekan Foreign Key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Data Users berhasil dipindahkan ke master_user!');
    }
}
