<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ketidaksesuaian_tindaklanjuts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ketidaksesuaian_id')->constrained('ketidaksesuaians')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->text('akar_masalah');
            $table->text('tindakan_korektif');
            $table->text('tindakan_pencegahan');
            $table->date('perkiraan_tanggal_selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ketidaksesuaian_tindaklanjuts');
    }
};
