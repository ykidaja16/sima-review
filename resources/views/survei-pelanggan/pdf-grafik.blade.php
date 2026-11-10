<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analisa Survei Pelanggan Interen - Tingkat Kesesuaian & Kuadran IPA</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 12mm 12mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1f2937;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 14px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 10pt;
            font-weight: bold;
            margin: 0 0 3px 0;
            color: #374151;
        }
        .header p {
            font-size: 8.5pt;
            color: #4b5563;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th {
            background-color: #fed7aa;
            color: #9a3412;
            font-weight: bold;
            font-size: 8pt;
            text-align: center;
            padding: 6px 4px;
            border: 1px solid #000;
        }
        td {
            padding: 6px 5px;
            font-size: 8pt;
            border: 1px solid #000;
        }
        .tfoot-avg {
            background-color: #fef08a;
            font-weight: bold;
        }
        .badge-a { background-color: #fee2e2; color: #991b1b; padding: 2px 6px; font-weight: bold; }
        .badge-b { background-color: #dcfce7; color: #166534; padding: 2px 6px; font-weight: bold; }
        .badge-c { background-color: #f3f4f6; color: #374151; padding: 2px 6px; font-weight: bold; }
        .badge-d { background-color: #fef3c7; color: #92400e; padding: 2px 6px; font-weight: bold; }

        .kuadran-box {
            margin-top: 14px;
            display: table;
            width: 100%;
            font-size: 8pt;
        }
        .kuadran-col {
            display: table-cell;
            width: 25%;
            padding: 6px 10px;
            vertical-align: top;
            border: 1px solid #d1d5db;
            background: #fafafa;
        }
        .footer {
            margin-top: 14px;
            font-size: 7.5pt;
            color: #6b7280;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Analisa Survey Pelanggan Interen</h1>
        <h2>Penilaian Tingkat Kesesuaian dan Rata-Rata Variabel Persepsi dan Harapan pada Pelayanan</h2>
        <p>Laboratorium Medis dan Klinik SIMA Malang - Jl. Ciliwung No.51 Malang &bull; <strong>{{ $periodeNama }}</strong> (Total Responden: {{ $jumlahResponden }})</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:25px;">NO</th>
                <th style="text-align:left;">URAIAN KRITERIA PELAYANAN</th>
                <th style="width:70px;">NILAI KINERJA</th>
                <th style="width:75px;">NILAI KEPENTINGAN</th>
                <th style="width:75px;">TINGKAT KESESUAIAN</th>
                <th style="width:55px;">SUMB X</th>
                <th style="width:55px;">SUMB Y</th>
                <th style="width:65px;">KUADRAN</th>
                <th style="width:130px;text-align:left;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td style="text-align:center;">{{ $item['no'] }}</td>
                <td>{{ $item['uraian'] }}</td>
                <td style="text-align:center;">{{ number_format($item['nilai_kinerja'], 2, ',', '.') }}</td>
                <td style="text-align:center;">{{ number_format($item['nilai_kepentingan'], 2, ',', '.') }}</td>
                <td style="text-align:center;font-weight:bold;">{{ number_format($item['kesesuaian'], 2, ',', '.') }}%</td>
                <td style="text-align:center;">{{ number_format($item['sumb_x'], 2, ',', '.') }}</td>
                <td style="text-align:center;">{{ number_format($item['sumb_y'], 2, ',', '.') }}</td>
                <td style="text-align:center;">
                    <span class="badge-{{ strtolower($item['kuadran']) }}">Kuadran {{ $item['kuadran'] }}</span>
                </td>
                <td>
                    @if($item['kuadran'] === 'A') Prioritas Utama
                    @elseif($item['kuadran'] === 'B') Pertahankan Prestasi
                    @elseif($item['kuadran'] === 'C') Prioritas Rendah
                    @else Berlebihan
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tfoot-avg">
                <td colspan="5" style="text-align:right;">Rata-rata Nilai X dan Y :</td>
                <td style="text-align:center;">{{ number_format($avgX, 2, ',', '.') }}</td>
                <td style="text-align:center;">{{ number_format($avgY, 2, ',', '.') }}</td>
                <td colspan="2" style="font-size:7.5pt;font-style:italic;">Garis Potong Sumbu X dan Sumbu Y</td>
            </tr>
        </tfoot>
    </table>

    <div class="kuadran-box">
        <div class="kuadran-col">
            <strong style="color:#991b1b;">Kuadran A (Prioritas Utama)</strong><br>
            Harapan tinggi, kinerja belum optimal. Menjadi fokus perbaikan utama.
        </div>
        <div class="kuadran-col">
            <strong style="color:#166534;">Kuadran B (Pertahankan Prestasi)</strong><br>
            Harapan tinggi, kinerja dinilai memuaskan. Harus terus dipertahankan.
        </div>
        <div class="kuadran-col">
            <strong style="color:#374151;">Kuadran C (Prioritas Rendah)</strong><br>
            Harapan rendah, kinerja dinilai cukup. Bukan prioritas utama perbaikan.
        </div>
        <div class="kuadran-col">
            <strong style="color:#92400e;">Kuadran D (Berlebihan)</strong><br>
            Harapan rendah, namun kinerja sangat tinggi. Sumber daya dapat dialihkan.
        </div>
    </div>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }} &bull; Sistem SIMA Review
    </div>
</body>
</html>

