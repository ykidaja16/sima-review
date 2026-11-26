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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sidebar_theme')) {
                $table->string('sidebar_theme', 50)->nullable()->default('sima_emerald')->after('is_dinilai');
            }
        });

        Schema::table('parameter_sops', function (Blueprint $table) {
            if (!Schema::hasColumn('parameter_sops', 'metode_penilaian')) {
                $table->json('metode_penilaian')->nullable()->after('deskripsi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sidebar_theme')) {
                $table->dropColumn('sidebar_theme');
            }
        });

        Schema::table('parameter_sops', function (Blueprint $table) {
            if (Schema::hasColumn('parameter_sops', 'metode_penilaian')) {
                $table->dropColumn('metode_penilaian');
            }
        });
    }
};
