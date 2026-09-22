<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ketidaksesuaian_verifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ketidaksesuaian_id')->constrained('ketidaksesuaians')->cascadeOnDelete();
            $table->foreignId('verifikator_id')->constrained('users')->restrictOnDelete();
            $table->enum('level', ['manager', 'kacab']);
            $table->boolean('tindakan_efektif');
            $table->text('alasan')->nullable();
            $table->string('ftkp_baru_no', 50)->nullable();
            $table->timestamps();

            $table->unique(['ketidaksesuaian_id', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ketidaksesuaian_verifikasis');
    }
};
