<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring Indikator — SIMA-MONEV</title>
    <style>
        @page { size: A4 landscape; margin: 14mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9.5px; color: #1e293b; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #0d5c63; padding-bottom: 8px; margin-bottom: 12px; }
        .header h1 { font-size: 14px; color: #0d5c63; font-weight: 700; }
        .header p { font-size: 9px; color: #64748b; margin-top: 2px; }
        .section-title { font-size: 10.5px; font-weight: 700; color: #0d5c63; margin: 12px 0 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; vertical-align: top; }
        thead tr { background: #0d5c63; color: #ffffff; }
        thead th { font-size: 8.5px; font-weight: 700; text-align: left; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        .text-center { text-align: center; }
        .footer { margin-top: 10px; font-size: 8px; color: #64748b; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN MONITORING INDIKATOR &amp; TREN PENILAIAN — SIMA-MONEV</h1>
        <p>
            Periode: {{ $periodeAktif?->nama ?? 'Semua Periode' }} |
            Cabang: {{ $cabangObj?->nama ?? 'Semua Cabang' }} |
            Divisi: {{ $divisiObj?->nama ?? 'Semua Divisi' }} |
            Dicetak: {{ now()->format('d M Y, H:i') }} WIB
        </p>
    </div>

    <div class="section-title">1. Ringkasan Tren Penilaian Per Periode</div>
    <table>
        <thead>
            <tr>
                <th style="width:35px;" class="text-center">No</th>
                <th>Nama Periode</th>
                <th style="width:120px;" class="text-center">Jumlah Dinilai</th>
                <th style="width:120px;" class="text-center">Rata-rata Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trendPeriodes as $i => $tp)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $tp->nama }}</td>
                <td class="text-center">{{ $tp->total_penilaian }}</td>
                <td class="text-center" style="font-weight:700;">{{ number_format($tp->avg_nilai ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Belum ada data penilaian per periode.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. Daftar Indikator Diurutkan dari Nilai Rata-rata Terendah</div>
    <table>
        <thead>
            <tr>
                <th style="width:28px;" class="text-center">#</th>
                <th>Indikator &amp; Kategori</th>
                <th style="width:45px;" class="text-center">Bobot</th>
                <th style="width:70px;" class="text-center">Rata-rata</th>
                <th style="width:65px;" class="text-center">Capaian %</th>
                <th style="width:55px;" class="text-center">Evaluasi</th>
                <th style="width:240px;">Alasan / Catatan Kendala Evaluator</th>
            </tr>
        </thead>
        <tbody>
            @foreach($indikators as $idx => $ind)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>
                    <strong>{{ $ind->nama }}</strong><br>
                    <span style="font-size:8px;color:#64748b;">{{ $ind->kategori }}</span>
                </td>
                <td class="text-center">{{ $ind->bobot }}</td>
                <td class="text-center" style="font-weight:700;">{{ $ind->avg_nilai !== null ? number_format($ind->avg_nilai, 2) . ' / 4' : '-' }}</td>
                <td class="text-center">{{ $ind->persentase !== null ? number_format($ind->persentase, 1) . '%' : '-' }}</td>
                <td class="text-center">{{ $ind->total_evaluasi }}</td>
                <td>
                    @if($ind->catatan_list->isNotEmpty())
                        @foreach($ind->catatan_list as $c)
                            • {{ $c['catatan'] }} ({{ $c['karyawan'] }} - Skor {{ $c['nilai'] }})@if(!$loop->last)<br>@endif
                        @endforeach
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Dicetak oleh {{ auth()->user()->name }} pada {{ now()->format('d/m/Y H:i') }} WIB</div>
</body>
</html>
