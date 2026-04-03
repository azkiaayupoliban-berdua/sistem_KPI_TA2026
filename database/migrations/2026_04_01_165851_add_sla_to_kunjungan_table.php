<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('kunjungan', function (Blueprint $table) {
        // Kita tambahkan setelah status_layanan
        $table->integer('estimasi_sla')->nullable()->after('status_layanan');
        $table->enum('satuan_sla', ['Menit', 'Hari'])->nullable()->after('estimasi_sla');
    });
}

public function down(): void
{
    Schema::table('kunjungan', function (Blueprint $table) {
        $table->dropColumn(['estimasi_sla', 'satuan_sla']);
    });
}
};
