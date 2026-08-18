<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaians')->cascadeOnDelete();
            $table->foreignId('parameter_id')->constrained('parameter_sops')->restrictOnDelete();
            $table->decimal('nilai', 5, 2)->comment('Nilai 0-100 untuk parameter ini');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['penilaian_id', 'parameter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_details');
    }
};
