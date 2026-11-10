<table>
    <thead>
        <tr>
            <th colspan="13" style="font-size:14pt;font-weight:bold;text-align:center;">ANALISA DATA SURVEY PELANGGAN</th>
        </tr>
        <tr>
            <th colspan="13" style="font-size:12pt;font-weight:bold;text-align:center;">LABORATORIUM MEDIS DAN KLINIK SIMA CILIWUNG MALANG</th>
        </tr>
        <tr>
            <th colspan="13" style="font-size:11pt;font-weight:bold;text-align:center;">PERIODE BULAN {{ $periodeBulan }}</th>
        </tr>
        <tr>
            <th colspan="13"></th>
        </tr>
        <tr>
            <th colspan="13" style="font-weight:bold;">Data Survey pelanggan {{ $periodeNama }}</th>
        </tr>
        <tr>
            <th colspan="13" style="font-weight:bold;">Jumlah responden : {{ $jumlahResponden }} responden</th>
        </tr>
        <tr>
            <th colspan="13"></th>
        </tr>
        <tr>
            <th rowspan="2" style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#CBD5E1;">NO</th>
            <th rowspan="2" style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#CBD5E1;">HAL</th>
            <th colspan="5" style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#E2E8F0;">Kinerja</th>
            <th colspan="5" style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#E2E8F0;">Kepentingan</th>
            <th rowspan="2" style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#CBD5E1;">TP %</th>
        </tr>
        <tr>
            <th style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#F1F5F9;">SP</th>
            <th style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#F1F5F9;">P</th>
            <th style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#F1F5F9;">TP</th>
            <th style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#F1F5F9;">STP</th>
            <th style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#94A3B8;">Nilai</th>
            <th style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#F1F5F9;">SP'</th>
            <th style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#F1F5F9;">P'</th>
            <th style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#F1F5F9;">TP'</th>
            <th style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#F1F5F9;">STP'</th>
            <th style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#94A3B8;">Nilai</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="13" style="border:1px solid #000000;font-weight:bold;color:#DC2626;background-color:#FEF2F2;">KUALITAS LAYANAN</td>
        </tr>
        @foreach($analysisData as $row)
        <tr>
            <td style="border:1px solid #000000;text-align:center;">{{ $row['no'] }}</td>
            <td style="border:1px solid #000000;">{{ $row['label'] }}</td>
            <td style="border:1px solid #000000;text-align:center;">{{ $row['k_sp'] }}</td>
            <td style="border:1px solid #000000;text-align:center;">{{ $row['k_p'] }}</td>
            <td style="border:1px solid #000000;text-align:center;">{{ $row['k_tp'] }}</td>
            <td style="border:1px solid #000000;text-align:center;">{{ $row['k_stp'] }}</td>
            <td style="border:1px solid #000000;text-align:center;font-weight:bold;background-color:#94A3B8;">{{ number_format($row['k_nilai'], 2, ',', '.') }}</td>
            <td style="border:1px solid #000000;text-align:center;">{{ $row['i_sp'] }}</td>
            <td style="border:1px solid #000000;text-align:center;">{{ $row['i_p'] }}</td>
            <td style="border:1px solid #000000;text-align:center;">{{ $row['i_tp'] }}</td>
            <td style="border:1px solid #000000;text-align:center;">{{ $row['i_stp'] }}</td>
            <td style="border:1px solid #000000;text-align:center;font-weight:bold;background-color:#94A3B8;">{{ number_format($row['i_nilai'], 2, ',', '.') }}</td>
            <td style="border:1px solid #000000;text-align:center;font-weight:bold;background-color:#22C55E;color:#FFFFFF;">{{ number_format($row['tp_persen'], 2, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="2" style="border:1px solid #000000;font-weight:bold;text-align:center;background-color:#CBD5E1;">Total / Rata-rata</td>
            <td colspan="4" style="border:1px solid #000000;background-color:#F8FAFC;"></td>
            <td style="border:1px solid #000000;text-align:center;font-weight:bold;background-color:#64748B;color:#FFFFFF;">{{ number_format($totalNilaiKinerja, 2, ',', '.') }}</td>
            <td colspan="4" style="border:1px solid #000000;background-color:#F8FAFC;"></td>
            <td style="border:1px solid #000000;text-align:center;font-weight:bold;background-color:#64748B;color:#FFFFFF;">{{ number_format($totalNilaiKepentingan, 2, ',', '.') }}</td>
            <td style="border:1px solid #000000;text-align:center;font-weight:bold;background-color:#16A34A;color:#FFFFFF;">{{ number_format($avgTpPersen, 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

