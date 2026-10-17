<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('target', 'periode')) {
            Schema::table('target', function (Blueprint $table): void {
                $table->string('periode')->nullable()->after('jenis_target');
            });
        }

        if (!Schema::hasColumn('target', 'sumber')) {
            Schema::table('target', function (Blueprint $table): void {
                $table->string('sumber')->nullable()->after('nilai');
            });
        }

        if (Schema::hasColumn('target', 'nilai') && !Schema::hasColumn('target', 'nilai_target')) {
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                Schema::table('target', function (Blueprint $table): void {
                    $table->renameColumn('nilai', 'nilai_target');
                });
            } else {
                DB::statement('ALTER TABLE `target` CHANGE `nilai` `nilai_target` DECIMAL(20,6) NULL');
            }
        }

        if (!Schema::getColumnType('target', 'tahun') || Schema::getColumnType('target', 'tahun') === 'smallint') {
            Schema::table('target', function (Blueprint $table): void {
                $table->unsignedSmallInteger('tahun')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('target', 'nilai_target')) {
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                Schema::table('target', function (Blueprint $table): void {
                    $table->renameColumn('nilai_target', 'nilai');
                });
            } else {
                DB::statement('ALTER TABLE `target` CHANGE `nilai_target` `nilai` DECIMAL(20,6) NULL');
            }
        }

        if (Schema::hasColumn('target', 'periode') || Schema::hasColumn('target', 'sumber')) {
            Schema::table('target', function (Blueprint $table): void {
                $columns = [];
                if (Schema::hasColumn('target', 'periode')) {
                    $columns[] = 'periode';
                }
                if (Schema::hasColumn('target', 'sumber')) {
                    $columns[] = 'sumber';
                }
                if ($columns) {
                    $table->dropColumn($columns);
                }
            });
        }

        Schema::table('target', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun')->nullable(false)->change();
        });
    }
};
