<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenilaianExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(protected Collection $penilaians) {}

    public function collection(): Collection
    {
        return $this->penilaians->map(function ($p, $i) {
            return [
                'No'           => $i + 1,
                'NIP'          => $p->karyawan->nip,
                'Nama'         => $p->karyawan->nama,
                'Divisi'       => $p->karyawan->divisi->nama ?? '-',
                'Jabatan'      => $p->karyawan->jabatan->nama ?? '-',
                'Periode'      => $p->periode->nama ?? '-',
                'Tgl Penilaian' => $p->tanggal_penilaian?->format('d/m/Y'),
                'Evaluator'    => $p->evaluator->name ?? '-',
                'Nilai Akhir'  => number_format($p->nilai_akhir ?? 0, 2),
                'Catatan'      => $p->catatan ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'NIP', 'Nama', 'Divisi', 'Jabatan', 'Periode', 'Tgl Penilaian', 'Evaluator', 'Nilai Akhir', 'Catatan'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
