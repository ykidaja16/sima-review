<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom tanggapan pada tabel penilaians
        Schema::table('penilaians', function (Blueprint $table) {
            if (!Schema::hasColumn('penilaians', 'tanggapan')) {
                $table->text('tanggapan')->nullable()->after('catatan');
            }
            if (!Schema::hasColumn('penilaians', 'tanggal_tanggapan')) {
                $table->timestamp('tanggal_tanggapan')->nullable()->after('tanggapan');
            }
        });

        // 2. Tambah kolom kategori_temuan (JSON) pada tabel jenis_ketidaksesuaians
        Schema::table('jenis_ketidaksesuaians', function (Blueprint $table) {
            if (!Schema::hasColumn('jenis_ketidaksesuaians', 'kategori_temuan')) {
                $table->json('kategori_temuan')->nullable()->after('deskripsi');
            }
        });

        // 3. Ubah kolom kategori_temuan di ketidaksesuaians dari enum menjadi string(100)
        DB::statement("ALTER TABLE ketidaksesuaians MODIFY COLUMN kategori_temuan VARCHAR(100) NOT NULL DEFAULT 'nc'");

        // 4. Isi default kategori_temuan untuk data jenis_ketidaksesuaians yang sudah ada
        $jenisList = DB::table('jenis_ketidaksesuaians')->get();
        foreach ($jenisList as $jenis) {
            $namaUpper = strtoupper($jenis->nama);
            if (str_contains($namaUpper, 'AMI')) {
                $kategoris = ['AMI 1', 'AMI 2', 'AMI 3'];
            } elseif (str_contains($namaUpper, 'AME')) {
                $kategoris = ['Observasi', 'NC Minor', 'NC Mayor', 'OFI'];
            } else {
                $kategoris = ['Observasi', 'NC Minor', 'NC Mayor', 'OFI'];
            }
            DB::table('jenis_ketidaksesuaians')
                ->where('id', $jenis->id)
                ->whereNull('kategori_temuan')
                ->update(['kategori_temuan' => json_encode($kategoris)]);
        }
    }

    public function down(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            $table->dropColumn(['tanggapan', 'tanggal_tanggapan']);
        });

        Schema::table('jenis_ketidaksesuaians', function (Blueprint $table) {
            $table->dropColumn('kategori_temuan');
        });
    }
};

