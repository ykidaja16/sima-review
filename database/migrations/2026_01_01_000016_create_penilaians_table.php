<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->restrictOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('periode_id')->constrained('periode_penilaians')->restrictOnDelete();
            $table->decimal('nilai_akhir', 5, 2)->nullable()->comment('Rata-rata berbobot semua parameter');
            $table->text('catatan')->nullable();
            $table->date('tanggal_penilaian');
            $table->timestamps();
            $table->softDeletes();

            // Satu karyawan hanya boleh dinilai sekali per periode
            $table->unique(['karyawan_id', 'periode_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
