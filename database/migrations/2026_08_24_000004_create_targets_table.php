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
        Schema::create('target', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikator')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->string('jenis_target');
            $table->decimal('nilai', 20, 6)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['indikator_id', 'tahun', 'jenis_target']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target');
    }
};
