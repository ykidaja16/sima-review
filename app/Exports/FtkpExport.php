<?php

namespace App\Exports;

use App\Models\Ketidaksesuaian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FtkpExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(protected Ketidaksesuaian $ketidaksesuaian) {}

    public function headings(): array
    {
        return ['Bagian / Field', 'Keterangan / Isi'];
    }

    public function collection(): Collection
    {
        $k  = $this->ketidaksesuaian;
        $tl = $k->tindaklanjut;
        $vm = $k->verifikasiManager;
        $vk = $k->verifikasiKacab;

        $jenisText = $k->jenis
            ? $k->jenis->nama
            : ($k->jenis_lainnya ? 'Lain-lain: ' . $k->jenis_lainnya : '-');

        $rows = [
            ['NOMOR FTKP', $k->nomor_ftkp],
            ['TANGGAL LAPORAN', $k->tanggal_laporan?->format('d/m/Y') ?? '-'],
            ['STATUS', $k->status_label],
            ['', ''],
            ['--- BAGIAN 1: PENGGAGAS ---', ''],
            ['Penggagas / Pelapor', $k->pelapor?->nama ?? '-'],
            ['Departemen / Bagian Penggagas', $k->divisiPelapor?->nama ?? '-'],
            ['Ditujukan ke Divisi', $k->divisiTujuan?->nama ?? '-'],
            ['Jenis Ketidaksesuaian', $jenisText],
            ['Kategori Temuan', $k->kategori_label],
            ['Penjelasan Temuan', $k->penjelasan_temuan],
            ['', ''],
            ['--- BAGIAN 2: TINDAKAN ---', ''],
            ['Diisi Oleh', $tl?->user?->name ?? 'Belum diisi'],
            ['Perkiraan Tanggal Selesai', $tl?->perkiraan_tanggal_selesai?->format('d/m/Y') ?? '-'],
            ['Akar Masalah / Analisa', $tl?->akar_masalah ?? '-'],
            ['Tindakan Korektif/Perbaikan', $tl?->tindakan_korektif ?? '-'],
            ['Tindakan Koreksi', $tl?->tindakan_pencegahan ?? '-'],
            ['', ''],
            ['--- BAGIAN 3: VERIFIKASI ---', ''],
            ['Verifikasi 1 (Manager Penggagas / Kacab)', $vm ? ($vm->tindakan_efektif ? 'Tindakan Efektif' : 'Tidak Efektif') : 'Belum diverifikasi'],
            ['Verifikator 1', $vm?->verifikator?->name ?? '-'],
            ['Catatan Verifikasi 1', $vm?->alasan ?? '-'],
            ['Verifikasi 2 (Kepala Cabang)', $vk ? ($vk->tindakan_efektif ? 'Tindakan Efektif' : 'Tidak Efektif') : 'Belum diverifikasi'],
            ['Verifikator 2', $vk?->verifikator?->name ?? '-'],
            ['Catatan Verifikasi 2', $vk?->alasan ?? '-'],
            ['No. FTKP Baru (Jika Ada)', $vk?->ftkp_baru_no ?? '-'],
        ];

        return collect($rows);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

