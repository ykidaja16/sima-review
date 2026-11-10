<table>
    <tr>
        <th colspan="9" style="font-size:14pt;font-weight:bold;text-align:center;">
            ANALISA SURVEY PELANGGAN INTEREN
        </th>
    </tr>
    <tr>
        <th colspan="9" style="font-size:11pt;font-weight:bold;text-align:center;">
            Penilaian Tingkat Kesesuaian dan Rata-Rata Variabel Persepsi dan Harapan pada Pelayanan
        </th>
    </tr>
    <tr>
        <th colspan="9" style="font-size:11pt;font-weight:bold;text-align:center;">
            Laboratorium Medis dan Klinik SIMA Malang - Jl. Ciliwung No.51 Malang
        </th>
    </tr>
    <tr>
        <th colspan="9" style="font-size:11pt;font-weight:bold;text-align:center;">
            {{ $periodeNama }} &bull; Total Responden: {{ $jumlahResponden }}
        </th>
    </tr>
    <tr>
        <th colspan="9"></th>
    </tr>
    <thead>
        <tr style="background-color:#ea580c;color:#ffffff;">
            <th style="font-weight:bold;text-align:center;border:1px solid #000000;background-color:#fed7aa;width:40px;">NO</th>
            <th style="font-weight:bold;text-align:center;border:1px solid #000000;background-color:#fed7aa;width:350px;">URAIAN</th>
            <th style="font-weight:bold;text-align:center;border:1px solid #000000;background-color:#fed7aa;width:100px;">NILAI KINERJA</th>
            <th style="font-weight:bold;text-align:center;border:1px solid #000000;background-color:#fed7aa;width:110px;">NILAI KEPENTINGAN</th>
            <th style="font-weight:bold;text-align:center;border:1px solid #000000;background-color:#fed7aa;width:110px;">TINGKAT KESESUAIAN (%)</th>
            <th style="font-weight:bold;text-align:center;border:1px solid #000000;background-color:#fed7aa;width:90px;">SUMB X</th>
            <th style="font-weight:bold;text-align:center;border:1px solid #000000;background-color:#fed7aa;width:90px;">SUMB Y</th>
            <th style="font-weight:bold;text-align:center;border:1px solid #000000;background-color:#fed7aa;width:90px;">KUADRAN</th>
            <th style="font-weight:bold;text-align:center;border:1px solid #000000;background-color:#fed7aa;width:180px;">KETERANGAN KUADRAN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td style="text-align:center;border:1px solid #000000;">{{ $item['no'] }}</td>
            <td style="border:1px solid #000000;">{{ $item['uraian'] }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ number_format($item['nilai_kinerja'], 2, ',', '.') }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ number_format($item['nilai_kepentingan'], 2, ',', '.') }}</td>
            <td style="text-align:center;font-weight:bold;border:1px solid #000000;">{{ number_format($item['kesesuaian'], 2, ',', '.') }}%</td>
            <td style="text-align:center;border:1px solid #000000;">{{ number_format($item['sumb_x'], 2, ',', '.') }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ number_format($item['sumb_y'], 2, ',', '.') }}</td>
            <td style="text-align:center;font-weight:bold;border:1px solid #000000;">{{ $item['kuadran'] }}</td>
            <td style="border:1px solid #000000;">
                @if($item['kuadran'] === 'A') Prioritas Utama (Top Priority)
                @elseif($item['kuadran'] === 'B') Pertahankan Prestasi (Maintain)
                @elseif($item['kuadran'] === 'C') Prioritas Rendah (Low Priority)
                @else Berlebihan (Possible Overkill)
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="font-weight:bold;background-color:#fef08a;">
            <td colspan="5" style="text-align:right;border:1px solid #000000;font-weight:bold;">Rata-rata Nilai X dan Y :</td>
            <td style="text-align:center;border:1px solid #000000;font-weight:bold;">{{ number_format($avgX, 2, ',', '.') }}</td>
            <td style="text-align:center;border:1px solid #000000;font-weight:bold;">{{ number_format($avgY, 2, ',', '.') }}</td>
            <td colspan="2" style="border:1px solid #000000;">Garis Batas Kuadran</td>
        </tr>
    </tfoot>
</table>

