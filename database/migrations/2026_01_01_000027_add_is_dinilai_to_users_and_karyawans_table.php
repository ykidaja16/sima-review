<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_dinilai')) {
                $table->boolean('is_dinilai')->default(true)->after('is_active');
            }
        });

        Schema::table('karyawans', function (Blueprint $table) {
            if (!Schema::hasColumn('karyawans', 'is_dinilai')) {
                $table->boolean('is_dinilai')->default(true)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_dinilai')) {
                $table->dropColumn('is_dinilai');
            }
        });

        Schema::table('karyawans', function (Blueprint $table) {
            if (Schema::hasColumn('karyawans', 'is_dinilai')) {
                $table->dropColumn('is_dinilai');
            }
        });
    }
};

