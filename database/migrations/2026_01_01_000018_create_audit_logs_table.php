<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aktivitas')->comment('Contoh: LOGIN, CREATE_PENILAIAN, UPDATE_KARYAWAN');
            $table->string('model')->nullable()->comment('Nama model yang dipengaruhi');
            $table->unsignedBigInteger('model_id')->nullable()->comment('ID record yang dipengaruhi');
            $table->json('data_lama')->nullable()->comment('Data sebelum perubahan');
            $table->json('data_baru')->nullable()->comment('Data sesudah perubahan');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
