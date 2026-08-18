<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parameter_sops', function (Blueprint $table) {
            $table->id();
            $table->string('kategori')->comment('Kategori/Kelompok parameter SOP');
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0)->comment('Urutan tampil di form penilaian');
            $table->integer('bobot')->default(1)->comment('Bobot/weight parameter dalam perhitungan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parameter_sops');
    }
};
