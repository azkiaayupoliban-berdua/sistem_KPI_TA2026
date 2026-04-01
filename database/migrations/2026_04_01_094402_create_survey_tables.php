<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('survey', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->constrained('kunjungan')->onDelete('cascade');
            $table->text('kritik_saran')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_survey', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('survey')->onDelete('cascade');
            $table->tinyInteger('p1');
            $table->tinyInteger('p2');
            $table->tinyInteger('p3');
            $table->tinyInteger('p4');
            $table->tinyInteger('p5');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('detail_survey');
        Schema::dropIfExists('survey');
    }
};