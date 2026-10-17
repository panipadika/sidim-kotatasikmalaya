<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('target', function (Blueprint $table): void {
            if (!Schema::hasColumn('target', 'nilai_target_range')) {
                $table->string('nilai_target_range')->nullable()->after('nilai_target');
            }
        });
    }

    public function down(): void
    {
        Schema::table('target', function (Blueprint $table): void {
            if (Schema::hasColumn('target', 'nilai_target_range')) {
                $table->dropColumn('nilai_target_range');
            }
        });
    }
};