<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penilaian — {{ $periode->nama ?? 'Semua' }} — SIMA-MONEV</title>
    <style>
        @page { size: A3 landscape; margin: 10mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 8.5px; color: #1e293b; background: white; }
        .header { text-align: center; padding-bottom: 10px; border-bottom: 2px solid #1e40af; margin-bottom: 10px; }
        .header h1 { font-size: 14px; font-weight: 700; color: #1e40af; }
        .header h2 { font-size: 11px; font-weight: 600; color: #374151; margin-top: 2px; }
        .header p { font-size: 9px; color: #64748b; margin-top: 2px; }
        .meta { margin-bottom: 10px; font-size: 9px; color: #334155; }
        table.data-table { width: 100%; border-collapse: collapse; table-layout: auto; }
        table.data-table thead tr { background: #1e40af; color: #ffffff; }
        table.data-table th { border: 1px solid #cbd5e1; padding: 4px 3px; text-align: left; font-size: 7px; font-weight: 700; vertical-align: middle; }
        table.data-table td { border: 1px solid #cbd5e1; padding: 4px 3px; font-size: 7.5px; vertical-align: top; }
        table.data-table tbody tr:nth-child(even) { background: #f8fafc; }
        .text-center { text-align: center !important; }
        .nilai { font-weight: 700; }
        .ind-col { text-align: center !important; width: 28px; }
        .footer { margin-top: 12px; font-size: 8px; color: #64748b; text-align: right; }
    </style>
</head>
<body>
    @php
        $canSeeEvaluator = auth()->user()?->isKacab() || auth()->user()?->isSuperAdmin();
    @endphp

    <div class="header">
        <h1>SIMA-MONEV — LAPORAN EVALUASI SERVICE EXCELLENCE</h1>
        <h2>Periode: {{ $periode->nama ?? 'Semua Periode' }} | Divisi: {{ $divisi->nama ?? 'Semua Divisi' }}</h2>
        <p>Dicetak: {{ now()->format('d M Y, H:i') }} WIB &nbsp;|&nbsp; Oleh: {{ auth()->user()->name }}</p>
    </div>

    <div class="meta">
        <strong>Total Penilaian:</strong> {{ $penilaians->count() }} &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Rata-rata Nilai:</strong> {{ number_format($penilaians->avg('nilai_akhir') ?? 0, 2) }} &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Tertinggi:</strong> {{ number_format($penilaians->max('nilai_akhir') ?? 0, 2) }} &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Terendah:</strong> {{ number_format($penilaians->min('nilai_akhir') ?? 0, 2) }}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Divisi</th>
                <th>Jabatan</th>
                <th>Periode</th>
                <th>Tgl</th>
                @if($canSeeEvaluator)
                <th>Evaluator</th>
                @endif
                <th class="text-center">Nilai</th>
                <th>Catatan</th>
                @foreach($parameters as $param)
                <th class="ind-col" title="{{ $param->nama }}">{{ $param->nama }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($penilaians as $i => $p)
            @php $detailsMap = $p->details->keyBy('parameter_id'); @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $p->karyawan?->nip ?? '-' }}</td>
                <td style="font-weight:600;">{{ $p->karyawan?->nama ?? '-' }}</td>
                <td>{{ $p->karyawan?->divisi?->nama ?? '-' }}</td>
                <td>{{ $p->karyawan?->jabatan?->nama ?? '-' }}</td>
                <td>{{ $p->periode?->nama ?? '-' }}</td>
                <td>{{ $p->tanggal_penilaian?->format('d/m/y') ?? '-' }}</td>
                @if($canSeeEvaluator)
                <td>{{ $p->evaluator?->name ?? '-' }}</td>
                @endif
                <td class="text-center nilai">{{ number_format($p->nilai_akhir ?? 0, 2) }}</td>
                <td>{{ $p->catatan ?? '' }}</td>
                @foreach($parameters as $param)
                @php $detail = $detailsMap->get($param->id); @endphp
                <td class="text-center">{{ $detail ? (int) $detail->nilai : '-' }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Keterangan Indikator: Skor 4 = Sangat Baik, 3 = Baik, 2 = Cukup, 1 = Kurang Baik &nbsp;|&nbsp; SIMA-MONEV
    </div>
</body>
</html>
