<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_nilais', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->comment('Contoh: Sangat Baik, Baik, Cukup, Kurang');
            $table->decimal('nilai_min', 5, 2)->comment('Nilai minimum untuk masuk kategori ini');
            $table->decimal('nilai_max', 5, 2)->comment('Nilai maksimum untuk masuk kategori ini');
            $table->string('warna', 20)->comment('Bootstrap color class: success, primary, warning, danger');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_nilais');
    }
};
