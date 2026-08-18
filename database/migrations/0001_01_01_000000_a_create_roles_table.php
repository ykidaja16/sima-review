<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->comment('Nama tampilan, contoh: Super Admin');
            $table->string('slug', 30)->unique()->comment('Slug unik, contoh: super_admin');
            $table->string('deskripsi')->nullable();
            $table->integer('level')->default(1)->comment('Level hierarki: semakin besar semakin tinggi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
