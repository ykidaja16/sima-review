<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ketidaksesuaians', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_ftkp', 30)->unique();
            $table->date('tanggal_laporan');
            $table->foreignId('pelapor_id')->constrained('karyawans')->restrictOnDelete();
            $table->foreignId('divisi_pelapor_id')->constrained('divisis')->restrictOnDelete();
            $table->foreignId('jenis_id')->nullable()->constrained('jenis_ketidaksesuaians')->nullOnDelete();
            $table->string('jenis_lainnya')->nullable();
            $table->text('penjelasan_temuan');
            $table->enum('kategori_temuan', ['ok', 'observasi', 'nc'])->default('nc');
            $table->foreignId('divisi_tujuan_id')->constrained('divisis')->restrictOnDelete();
            $table->enum('status', ['open', 'in_progress', 'verifikasi_manager', 'verifikasi_kacab', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ketidaksesuaians');
    }
};
