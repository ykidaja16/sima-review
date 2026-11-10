<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Survei Kepuasan Pelanggan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 10mm 12mm 10mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8pt;
            color: #1f2937;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
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
        }
        .header p {
            font-size: 8pt;
            color: #4b5563;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        th {
            background-color: #f3f4f6;
            color: #111827;
            font-weight: bold;
            font-size: 7pt;
            text-align: center;
            padding: 5px 3px;
            border: 1px solid #9ca3af;
        }
        td {
            padding: 4px 3px;
            font-size: 7pt;
            border: 1px solid #d1d5db;
        }
        tr:nth-child(even) td {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 10px;
            font-size: 7.5pt;
            color: #6b7280;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Mentah Survei Kepuasan Pelanggan</h1>
        <h2>Laboratorium Medis dan Klinik SIMA Ciliwung Malang</h2>
        <p>
            @if($search)
                Filter: "{{ $search }}" &bull; Total Ditemukan: {{ count($rows) }} data
            @else
                Total Data: {{ count($rows) }} data
            @endif
            @if(count($rows) > 300)
                <span style="color:#b91c1c;">(Menampilkan 300 data pertama untuk optimasi cetak PDF, gunakan Export Excel untuk data lengkap)</span>
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:25px;">#</th>
                <th style="width:75px;">Timestamp</th>
                <th style="width:65px;">Front Office</th>
                <th>Nama Responden</th>
                <th style="width:40px;">JK</th>
                <th style="width:45px;">Pendidikan</th>
                <th style="width:30px;">Usia</th>
                <th style="width:65px;">Pekerjaan</th>
                <th style="width:65px;">No Telp</th>
                @for($k = 1; $k <= 9; $k++)
                <th style="width:16px;">K{{ $k }}</th>
                @endfor
                @for($p = 1; $p <= 9; $p++)
                <th style="width:16px;">P{{ $p }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach(array_slice($rows, 0, 300) as $idx => $r)
            <tr>
                <td style="text-align:center;">{{ $idx + 1 }}</td>
                <td style="text-align:center;">{{ $r[0] ?? '' }}</td>
                <td>{{ $r[1] ?? '' }}</td>
                <td>{{ $r[2] ?? '' }}</td>
                <td style="text-align:center;">{{ $r[3] ?? '' }}</td>
                <td style="text-align:center;">{{ $r[4] ?? '' }}</td>
                <td style="text-align:center;">{{ $r[5] ?? '' }}</td>
                <td>{{ $r[6] ?? '' }}</td>
                <td>{{ $r[7] ?? '' }}</td>
                @for($k = 1; $k <= 9; $k++)
                <td style="text-align:center;font-weight:bold;">{{ $r[8 + ($k - 1) * 2] ?? '' }}</td>
                @endfor
                @for($p = 1; $p <= 9; $p++)
                <td style="text-align:center;">{{ $r[9 + ($p - 1) * 2] ?? '' }}</td>
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }} &bull; Sistem SIMA Review
    </div>
</body>
</html>

