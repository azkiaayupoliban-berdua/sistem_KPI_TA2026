<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('pengunjung', function (Blueprint $table) {
            $table->id();
            $table->string('identitas_no', 30)->nullable();
            $table->string('nama_lengkap', 50);
            $table->string('asal_instansi', 50);
            $table->string('no_telepon', 15);
            $table->timestamps();
        });

        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kunjungan', 20)->unique();
            $table->foreignId('pengunjung_id')->constrained('pengunjung')->onDelete('cascade');
            $table->foreignId('prodi_id')->constrained('master_prodi_instansi');
            $table->foreignId('keperluan_id')->constrained('master_keperluan');
            $table->foreignId('user_id')->nullable()->constrained('master_user'); 
            
            $table->string('keperluan', 100)->nullable(); 
            $table->string('hari_kunjungan', 50);
            $table->date('tanggal');
            
            $table->enum('status_layanan', ['Antre', 'Diproses', 'Selesai', 'Ditolak'])->default('Antre');
            $table->dateTime('waktu_selesai_layanan')->nullable();
            $table->enum('status_sla', ['Tepat Waktu', 'Terlambat'])->nullable();
            $table->dateTime('waktu_notif_pimpinan')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('kunjungan');
        Schema::dropIfExists('pengunjung');
    }
};