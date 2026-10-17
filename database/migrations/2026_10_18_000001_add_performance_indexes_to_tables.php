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
        Schema::table('capaian', function (Blueprint $table) {
            $table->index('tahun', 'capaian_tahun_index');
        });

        Schema::table('target', function (Blueprint $table) {
            $table->index('tahun', 'target_tahun_index');
            $table->index('jenis_target', 'target_jenis_target_index');
            $table->index(['jenis_target', 'tahun'], 'target_jenis_tahun_index');
        });

        Schema::table('indikator', function (Blueprint $table) {
            $table->index('jenis', 'indikator_jenis_index');
            $table->index('nama_indikator', 'indikator_nama_indikator_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikator', function (Blueprint $table) {
            $table->dropIndex('indikator_nama_indikator_index');
            $table->dropIndex('indikator_jenis_index');
        });

        Schema::table('target', function (Blueprint $table) {
            $table->dropIndex('target_jenis_tahun_index');
            $table->dropIndex('target_jenis_target_index');
            $table->dropIndex('target_tahun_index');
        });

        Schema::table('capaian', function (Blueprint $table) {
            $table->dropIndex('capaian_tahun_index');
        });
    }
};
