<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<table>
    <tr>
        <td colspan="7" style="font-size:13pt;font-weight:bold;text-align:center;">
            MONITORING INDIKATOR &amp; TREN PENILAIAN — SIMA-MONEV
        </td>
    </tr>
    <tr>
        <td colspan="7" style="font-size:9pt;text-align:center;">
            Periode: {{ $periodeAktif?->nama ?? 'Semua Periode' }} | Cabang: {{ $cabangObj?->nama ?? 'Semua Cabang' }} | Divisi: {{ $divisiObj?->nama ?? 'Semua Divisi' }}
        </td>
    </tr>
    <tr><td colspan="7"></td></tr>

    <tr>
        <td colspan="7" style="font-weight:bold;background-color:#e2e8f0;border:1px solid #000000;">
            BAGIAN 1: TREN PENILAIAN PER PERIODE
        </td>
    </tr>
    <tr>
        <th style="border:1px solid #000000;background-color:#0d5c63;color:#ffffff;font-weight:bold;">No</th>
        <th colspan="3" style="border:1px solid #000000;background-color:#0d5c63;color:#ffffff;font-weight:bold;">Periode</th>
        <th style="border:1px solid #000000;background-color:#0d5c63;color:#ffffff;font-weight:bold;">Jumlah Dinilai</th>
        <th colspan="2" style="border:1px solid #000000;background-color:#0d5c63;color:#ffffff;font-weight:bold;">Rata-rata Nilai</th>
    </tr>
    @foreach($trendPeriodes as $i => $tp)
    <tr>
        <td style="border:1px solid #000000;">{{ $i + 1 }}</td>
        <td colspan="3" style="border:1px solid #000000;">{{ $tp->nama }}</td>
        <td style="border:1px solid #000000;">{{ $tp->total_penilaian }}</td>
        <td colspan="2" style="border:1px solid #000000;">{{ number_format($tp->avg_nilai ?? 0, 2) }}</td>
    </tr>
    @endforeach

    <tr><td colspan="7"></td></tr>

    <tr>
        <td colspan="7" style="font-weight:bold;background-color:#e2e8f0;border:1px solid #000000;">
            BAGIAN 2: INDIKATOR DIURUTKAN DARI NILAI RATA-RATA TERENDAH
        </td>
    </tr>
    <tr>
        <th style="border:1px solid #000000;background-color:#0d5c63;color:#ffffff;font-weight:bold;">No</th>
        <th style="border:1px solid #000000;background-color:#0d5c63;color:#ffffff;font-weight:bold;">Indikator</th>
        <th style="border:1px solid #000000;background-color:#0d5c63;color:#ffffff;font-weight:bold;">Kategori</th>
        <th style="border:1px solid #000000;background-color:#0d5c63;color:#ffffff;font-weight:bold;">Bobot</th>
        <th style="border:1px solid #000000;background-color:#0d5c63;color:#ffffff;font-weight:bold;">Rata-rata (1-4)</th>
        <th style="border:1px solid #000000;background-color:#0d5c63;color:#ffffff;font-weight:bold;">Capaian %</th>
        <th style="border:1px solid #000000;background-color:#0d5c63;color:#ffffff;font-weight:bold;">Catatan / Kendala Evaluator</th>
    </tr>
    @foreach($indikators as $idx => $ind)
    @php
        $catatanText = $ind->catatan_list->isNotEmpty()
            ? $ind->catatan_list->map(fn($c) => $c['catatan'] . ' (' . $c['karyawan'] . ' - Skor ' . $c['nilai'] . ')')->implode('; ')
            : '-';
    @endphp
    <tr>
        <td style="border:1px solid #000000;">{{ $idx + 1 }}</td>
        <td style="border:1px solid #000000;">{{ $ind->nama }}</td>
        <td style="border:1px solid #000000;">{{ $ind->kategori }}</td>
        <td style="border:1px solid #000000;">{{ $ind->bobot }}</td>
        <td style="border:1px solid #000000;">{{ $ind->avg_nilai !== null ? number_format($ind->avg_nilai, 2) : '-' }}</td>
        <td style="border:1px solid #000000;">{{ $ind->persentase !== null ? number_format($ind->persentase, 1) . '%' : '-' }}</td>
        <td style="border:1px solid #000000;">{{ $catatanText }}</td>
    </tr>
    @endforeach
</table>
</body>
</html>
