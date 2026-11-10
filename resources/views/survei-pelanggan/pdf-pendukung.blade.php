<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pendukung - Profil Responden Survei Pelanggan</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 14mm 14mm 14mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1f2937;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 10pt;
            font-weight: bold;
            margin: 0 0 4px 0;
            color: #374151;
        }
        .header p {
            font-size: 8.5pt;
            color: #4b5563;
            margin: 0;
        }
        .section-title {
            background-color: #fed7aa;
            color: #9a3412;
            font-weight: bold;
            padding: 5px 8px;
            font-size: 8.5pt;
            text-transform: uppercase;
            border: 1px solid #000;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th {
            background-color: #f3f4f6;
            color: #111827;
            font-weight: bold;
            font-size: 8pt;
            padding: 5px 6px;
            border: 1px solid #000;
        }
        td {
            padding: 4.5px 6px;
            font-size: 8pt;
            border: 1px solid #000;
        }
        .tfoot-total {
            background-color: #e5e7eb;
            font-weight: bold;
        }
        .table-split {
            width: 100%;
        }
        .table-split td {
            border: none;
            padding: 0 6px;
            vertical-align: top;
        }
        .footer {
            margin-top: 18px;
            font-size: 7.5pt;
            color: #6b7280;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Pendukung - Profil Responden Survei</h1>
        <h2>Laboratorium Medis dan Klinik SIMA Ciliwung Malang</h2>
        <p><strong>{{ $semesterLabel }}</strong> &bull; Total Responden: <strong>{{ $jumlahResponden }}</strong> orang</p>
    </div>

    <table class="table-split">
        <tr>
            {{-- 1. JENIS KELAMIN --}}
            <td style="width:50%;">
                <div class="section-title">1. Jenis Kelamin</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width:25px;text-align:center;">NO</th>
                            <th style="text-align:left;">Profil Responden</th>
                            <th style="width:65px;text-align:center;">Jumlah</th>
                            <th style="width:50px;text-align:right;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demografi['gender'] as $idx => $g)
                        <tr>
                            <td style="text-align:center;">{{ $idx + 1 }}</td>
                            <td>{{ $g['label'] }}</td>
                            <td style="text-align:center;font-weight:bold;">{{ $g['count'] }}</td>
                            <td style="text-align:right;">{{ $g['pct'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="tfoot-total">
                            <td colspan="2">Total</td>
                            <td style="text-align:center;">{{ $jumlahResponden }}</td>
                            <td style="text-align:right;">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </td>

            {{-- 2. USIA --}}
            <td style="width:50%;">
                <div class="section-title">2. Usia</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width:25px;text-align:center;">NO</th>
                            <th style="text-align:left;">Rentang Usia</th>
                            <th style="width:65px;text-align:center;">Jumlah</th>
                            <th style="width:50px;text-align:right;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demografi['usia'] as $idx => $u)
                        <tr>
                            <td style="text-align:center;">{{ $idx + 1 }}</td>
                            <td>{{ $u['label'] }}</td>
                            <td style="text-align:center;font-weight:bold;">{{ $u['count'] }}</td>
                            <td style="text-align:right;">{{ $u['pct'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="tfoot-total">
                            <td colspan="2">Total</td>
                            <td style="text-align:center;">{{ $jumlahResponden }}</td>
                            <td style="text-align:right;">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        <tr>
            {{-- 3. PENDIDIKAN --}}
            <td style="width:50%;">
                <div class="section-title">3. Pendidikan</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width:25px;text-align:center;">NO</th>
                            <th style="text-align:left;">Pendidikan</th>
                            <th style="width:65px;text-align:center;">Jumlah</th>
                            <th style="width:50px;text-align:right;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demografi['pendidikan'] as $idx => $p)
                        <tr>
                            <td style="text-align:center;">{{ $idx + 1 }}</td>
                            <td>{{ $p['label'] }}</td>
                            <td style="text-align:center;font-weight:bold;">{{ $p['count'] }}</td>
                            <td style="text-align:right;">{{ $p['pct'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="tfoot-total">
                            <td colspan="2">Total</td>
                            <td style="text-align:center;">{{ $jumlahResponden }}</td>
                            <td style="text-align:right;">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </td>

            {{-- 4. PEKERJAAN --}}
            <td style="width:50%;">
                <div class="section-title">4. Pekerjaan</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width:25px;text-align:center;">NO</th>
                            <th style="text-align:left;">Pekerjaan</th>
                            <th style="width:65px;text-align:center;">Jumlah</th>
                            <th style="width:50px;text-align:right;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demografi['pekerjaan'] as $idx => $k)
                        <tr>
                            <td style="text-align:center;">{{ $idx + 1 }}</td>
                            <td>{{ $k['label'] }}</td>
                            <td style="text-align:center;font-weight:bold;">{{ $k['count'] }}</td>
                            <td style="text-align:right;">{{ $k['pct'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="tfoot-total">
                            <td colspan="2">Total</td>
                            <td style="text-align:center;">{{ $jumlahResponden }}</td>
                            <td style="text-align:right;">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }} &bull; Sistem SIMA Review
    </div>
</body>
</html>

