<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analisa Data Survei Pelanggan - {{ $periodeBulan }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 8.5pt; color: #1e293b; margin: 0; padding: 10px; }
        .kop { text-align: center; border-bottom: 2px solid #334155; padding-bottom: 8px; margin-bottom: 12px; }
        .kop h2 { margin: 0 0 3px 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .kop h3 { margin: 0 0 3px 0; font-size: 10.5pt; text-transform: uppercase; color: #475569; }
        .kop .periode { font-size: 9.5pt; font-weight: bold; color: #0284c7; }
        .meta { margin-bottom: 10px; font-size: 8pt; }
        .meta table { width: 100%; border: none; }
        .meta td { border: none; padding: 2px 0; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th, table.data td { border: 1px solid #94a3b8; padding: 5px 4px; text-align: left; }
        table.data thead th { background-color: #e2e8f0; font-weight: bold; text-align: center; font-size: 7.5pt; }
        table.data thead th.dark { background-color: #cbd5e1; }
        table.data thead th.gray { background-color: #94a3b8; color: #0f172a; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .section-row td { background-color: #fef2f2; color: #dc2626; font-weight: bold; }
        .bg-gray { background-color: #94a3b8; font-weight: bold; text-align: center; }
        .bg-green { background-color: #22c55e; color: #ffffff; font-weight: bold; text-align: center; }
        .bg-total { background-color: #64748b; color: #ffffff; font-weight: bold; text-align: center; }
        .bg-total-green { background-color: #16a34a; color: #ffffff; font-weight: bold; text-align: center; }
        .keterangan { margin-top: 14px; font-size: 7pt; line-height: 1.4; border-top: 1px dashed #cbd5e1; padding-top: 6px; color: #475569; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>ANALISA DATA SURVEY PELANGGAN</h2>
        <h3>LABORATORIUM MEDIS DAN KLINIK SIMA CILIWUNG MALANG</h3>
        <div class="periode">PERIODE BULAN {{ $periodeBulan }}</div>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td style="width:50%;">Data Survey Pelanggan: <strong>{{ $periodeNama }}</strong></td>
                <td style="width:50%;text-align:right;">Jumlah Responden: <strong>{{ $jumlahResponden }}</strong> responden</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th rowspan="2" class="dark" style="width:24px;">NO</th>
                <th rowspan="2" class="dark" style="text-align:left;">HAL / KRITERIA</th>
                <th colspan="5">Kinerja</th>
                <th colspan="5">Kepentingan</th>
                <th rowspan="2" class="dark" style="width:50px;">TP %</th>
            </tr>
            <tr>
                <th style="width:28px;">SP</th>
                <th style="width:28px;">P</th>
                <th style="width:28px;">TP</th>
                <th style="width:28px;">STP</th>
                <th class="gray" style="width:48px;">Nilai</th>
                <th style="width:28px;">SP'</th>
                <th style="width:28px;">P'</th>
                <th style="width:28px;">TP'</th>
                <th style="width:28px;">STP'</th>
                <th class="gray" style="width:48px;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr class="section-row">
                <td colspan="13">KUALITAS LAYANAN</td>
            </tr>
            @foreach($analysisData as $row)
            <tr>
                <td class="center">{{ $row['no'] }}</td>
                <td>{{ $row['label'] }}</td>
                <td class="center">{{ $row['k_sp'] }}</td>
                <td class="center">{{ $row['k_p'] }}</td>
                <td class="center">{{ $row['k_tp'] }}</td>
                <td class="center">{{ $row['k_stp'] }}</td>
                <td class="bg-gray">{{ number_format($row['k_nilai'], 2, ',', '.') }}</td>
                <td class="center">{{ $row['i_sp'] }}</td>
                <td class="center">{{ $row['i_p'] }}</td>
                <td class="center">{{ $row['i_tp'] }}</td>
                <td class="center">{{ $row['i_stp'] }}</td>
                <td class="bg-gray">{{ number_format($row['i_nilai'], 2, ',', '.') }}</td>
                <td class="bg-green">{{ number_format($row['tp_persen'], 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" class="center bold" style="background:#f1f5f9;">Total / Rata-rata</td>
                <td colspan="4" style="background:#f8fafc;"></td>
                <td class="bg-total">{{ number_format($totalNilaiKinerja, 2, ',', '.') }}</td>
                <td colspan="4" style="background:#f8fafc;"></td>
                <td class="bg-total">{{ number_format($totalNilaiKepentingan, 2, ',', '.') }}</td>
                <td class="bg-total-green">{{ number_format($avgTpPersen, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="keterangan">
        <strong>Keterangan Bobot:</strong> SP / SP' = 4 | P / P' = 3 | TP / TP' = 2 | STP / STP' = 1 |
        <strong>Rumus:</strong> Nilai = (SP&times;4 + P&times;3 + TP&times;2 + STP&times;1) / 4 &nbsp;|&nbsp;
        <strong>TP %:</strong> (Nilai Kinerja / Nilai Kepentingan) &times; 100%
    </div>
</body>
</html>

