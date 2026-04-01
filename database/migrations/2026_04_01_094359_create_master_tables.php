<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('master_role', function (Blueprint $table) {
            $table->id();
            $table->string('nama_role', 30);
            $table->timestamps();
        });

        Schema::create('master_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('master_role')->onDelete('cascade');
            $table->string('name', 50);
            $table->string('email', 50)->unique();
            $table->string('password', 255); 
            $table->string('foto', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('master_prodi_instansi', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->enum('jenis', ['Prodi', 'Instansi']);
            $table->timestamps();
        });

        Schema::create('master_keperluan', function (Blueprint $table) {
            $table->id();
            $table->string('keterangan', 100);
            $table->timestamps();
        });

        Schema::create('master_aspek_survey', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aspek', 100);
            $table->timestamps();
        });

        Schema::create('master_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspek_id')->constrained('master_aspek_survey')->onDelete('cascade');
            $table->string('pertanyaan', 150);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('master_pertanyaan');
        Schema::dropIfExists('master_aspek_survey');
        Schema::dropIfExists('master_keperluan');
        Schema::dropIfExists('master_prodi_instansi');
        Schema::dropIfExists('master_user');
        Schema::dropIfExists('master_role');
    }
};