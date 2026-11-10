<table>
    <tr>
        <th colspan="4" style="font-size:14pt;font-weight:bold;text-align:center;">
            DATA PENDUKUNG - PROFIL RESPONDEN SURVEI PELANGGAN
        </th>
    </tr>
    <tr>
        <th colspan="4" style="font-size:11pt;font-weight:bold;text-align:center;">
            Laboratorium Medis dan Klinik SIMA Malang - Jl. Ciliwung No.51 Malang
        </th>
    </tr>
    <tr>
        <th colspan="4" style="font-size:11pt;font-weight:bold;text-align:center;">
            {{ $semesterLabel }} &bull; Total Responden: {{ $jumlahResponden }}
        </th>
    </tr>
    <tr>
        <th colspan="4"></th>
    </tr>

    {{-- 1. JENIS KELAMIN --}}
    <tr style="background-color:#fed7aa;">
        <th colspan="4" style="font-weight:bold;font-size:11pt;border:1px solid #000000;background-color:#fed7aa;">
            1. JENIS KELAMIN
        </th>
    </tr>
    <tr>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;width:40px;text-align:center;">NO</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;width:220px;">PROFIL RESPONDEN</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;width:100px;text-align:center;">JUMLAH</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;width:80px;text-align:right;">%</th>
    </tr>
    @foreach($demografi['gender'] as $idx => $g)
    <tr>
        <td style="text-align:center;border:1px solid #000000;">{{ $idx + 1 }}</td>
        <td style="border:1px solid #000000;">{{ $g['label'] }}</td>
        <td style="text-align:center;font-weight:bold;border:1px solid #000000;">{{ $g['count'] }}</td>
        <td style="text-align:right;border:1px solid #000000;">{{ $g['pct'] }}%</td>
    </tr>
    @endforeach
    <tr style="font-weight:bold;background-color:#e5e7eb;">
        <td colspan="2" style="border:1px solid #000000;font-weight:bold;">Total</td>
        <td style="text-align:center;border:1px solid #000000;font-weight:bold;">{{ $jumlahResponden }}</td>
        <td style="text-align:right;border:1px solid #000000;font-weight:bold;">100%</td>
    </tr>

    <tr><th colspan="4"></th></tr>

    {{-- 2. USIA --}}
    <tr style="background-color:#fed7aa;">
        <th colspan="4" style="font-weight:bold;font-size:11pt;border:1px solid #000000;background-color:#fed7aa;">
            2. USIA
        </th>
    </tr>
    <tr>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;text-align:center;">NO</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;">RENTANG USIA</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;text-align:center;">JUMLAH</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;text-align:right;">%</th>
    </tr>
    @foreach($demografi['usia'] as $idx => $u)
    <tr>
        <td style="text-align:center;border:1px solid #000000;">{{ $idx + 1 }}</td>
        <td style="border:1px solid #000000;">{{ $u['label'] }}</td>
        <td style="text-align:center;font-weight:bold;border:1px solid #000000;">{{ $u['count'] }}</td>
        <td style="text-align:right;border:1px solid #000000;">{{ $u['pct'] }}%</td>
    </tr>
    @endforeach
    <tr style="font-weight:bold;background-color:#e5e7eb;">
        <td colspan="2" style="border:1px solid #000000;font-weight:bold;">Total</td>
        <td style="text-align:center;border:1px solid #000000;font-weight:bold;">{{ $jumlahResponden }}</td>
        <td style="text-align:right;border:1px solid #000000;font-weight:bold;">100%</td>
    </tr>

    <tr><th colspan="4"></th></tr>

    {{-- 3. PENDIDIKAN --}}
    <tr style="background-color:#fed7aa;">
        <th colspan="4" style="font-weight:bold;font-size:11pt;border:1px solid #000000;background-color:#fed7aa;">
            3. PENDIDIKAN
        </th>
    </tr>
    <tr>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;text-align:center;">NO</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;">PENDIDIKAN</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;text-align:center;">JUMLAH</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;text-align:right;">%</th>
    </tr>
    @foreach($demografi['pendidikan'] as $idx => $p)
    <tr>
        <td style="text-align:center;border:1px solid #000000;">{{ $idx + 1 }}</td>
        <td style="border:1px solid #000000;">{{ $p['label'] }}</td>
        <td style="text-align:center;font-weight:bold;border:1px solid #000000;">{{ $p['count'] }}</td>
        <td style="text-align:right;border:1px solid #000000;">{{ $p['pct'] }}%</td>
    </tr>
    @endforeach
    <tr style="font-weight:bold;background-color:#e5e7eb;">
        <td colspan="2" style="border:1px solid #000000;font-weight:bold;">Total</td>
        <td style="text-align:center;border:1px solid #000000;font-weight:bold;">{{ $jumlahResponden }}</td>
        <td style="text-align:right;border:1px solid #000000;font-weight:bold;">100%</td>
    </tr>

    <tr><th colspan="4"></th></tr>

    {{-- 4. PEKERJAAN --}}
    <tr style="background-color:#fed7aa;">
        <th colspan="4" style="font-weight:bold;font-size:11pt;border:1px solid #000000;background-color:#fed7aa;">
            4. PEKERJAAN
        </th>
    </tr>
    <tr>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;text-align:center;">NO</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;">PEKERJAAN</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;text-align:center;">JUMLAH</th>
        <th style="font-weight:bold;border:1px solid #000000;background-color:#f3f4f6;text-align:right;">%</th>
    </tr>
    @foreach($demografi['pekerjaan'] as $idx => $k)
    <tr>
        <td style="text-align:center;border:1px solid #000000;">{{ $idx + 1 }}</td>
        <td style="border:1px solid #000000;">{{ $k['label'] }}</td>
        <td style="text-align:center;font-weight:bold;border:1px solid #000000;">{{ $k['count'] }}</td>
        <td style="text-align:right;border:1px solid #000000;">{{ $k['pct'] }}%</td>
    </tr>
    @endforeach
    <tr style="font-weight:bold;background-color:#e5e7eb;">
        <td colspan="2" style="border:1px solid #000000;font-weight:bold;">Total</td>
        <td style="text-align:center;border:1px solid #000000;font-weight:bold;">{{ $jumlahResponden }}</td>
        <td style="text-align:right;border:1px solid #000000;font-weight:bold;">100%</td>
    </tr>
</table>

