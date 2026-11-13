<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan Role Direktur jika belum ada
        $direkturExists = DB::table('roles')->where('slug', 'direktur')->exists();
        if (!$direkturExists) {
            DB::table('roles')->insert([
                'nama'       => 'Direktur',
                'slug'       => 'direktur',
                'deskripsi'  => 'Direktur: memantau aktivitas seluruh cabang, review dan monitoring level eksekutif',
                'level'      => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Buat tabel role_menu_permissions jika belum ada
        if (!Schema::hasTable('role_menu_permissions')) {
            Schema::create('role_menu_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->string('menu_key', 60);
                $table->timestamps();

                $table->unique(['role_id', 'menu_key']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_menu_permissions');
        DB::table('roles')->where('slug', 'direktur')->delete();
    }
};

