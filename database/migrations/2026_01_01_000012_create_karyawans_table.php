<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nip', 30)->unique();
            $table->string('nama');
            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->foreignId('divisi_id')->constrained('divisis')->restrictOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatans')->restrictOnDelete();
            $table->foreignId('atasan_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
