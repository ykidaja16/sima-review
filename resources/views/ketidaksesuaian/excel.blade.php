<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
@php
    // Jenis ketidaksesuaian logic
    $jenisName = strtolower($ketidaksesuaian->jenis?->nama ?? '');
    $isKeluhan = str_contains($jenisName, 'keluhan') || str_contains($jenisName, 'pelanggan');
    $isAudit = str_contains($jenisName, 'audit');
    $isLain = !$isKeluhan && !$isAudit;
    $namaLain = $ketidaksesuaian->jenis_lainnya ?: ($ketidaksesuaian->jenis?->nama ?: '');

    // Kategori temuan logic
    $katLower = strtolower($ketidaksesuaian->kategori_temuan ?? '');
    $isOk = ($katLower === 'ok');
    $isObservasi = str_contains($katLower, 'observasi') || str_contains($katLower, 'ofi');
    $isNc = str_contains($katLower, 'nc') || (!$isOk && !$isObservasi && !empty($katLower));

    // Verifikasi logic
    $verifikasi = $ketidaksesuaian->verifikasiKacab ?? $ketidaksesuaian->verifikasiManager;
    $isEfektifYa = $verifikasi && $verifikasi->tindakan_efektif == 1;
    $isEfektifTidak = $verifikasi && $verifikasi->tindakan_efektif == 0 && !is_null($verifikasi->tindakan_efektif);

    // Tindak lanjut
    $tl = $ketidaksesuaian->tindaklanjut;
@endphp

<table>
    <!-- BARIS 1: TOP SPACER / MARGIN -->
    <tr>
        <td></td>
        <td colspan="8"></td>
        <td></td>
    </tr>

    <!-- BARIS 2: HEADER FORMULIR -->
    <tr>
        <td></td>
        <td colspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle;">
            &nbsp;
        </td>
        <td colspan="3" style="border: 1px solid #000000; font-size: 11pt; font-weight: bold; text-align: center; vertical-align: middle;">
            FORMULIR TINDAKAN KOREKTIF DAN PERBAIKAN
        </td>
        <td colspan="3" style="border: 1px solid #000000; font-size: 9pt; vertical-align: top;">
            <b>1. FTKP No. :</b> {{ $ketidaksesuaian->nomor_ftkp }}<br />
            <b>2. TANGGAL :</b> {{ $ketidaksesuaian->tanggal_laporan ? $ketidaksesuaian->tanggal_laporan->translatedFormat('d F Y') : '-' }}
        </td>
        <td></td>
    </tr>

    <!-- BARIS 3: BAGIAN 1 - PENGGAGAS -->
    <tr>
        <td></td>
        <td colspan="8" style="border: 1px solid #000000; background-color: #cccccc; font-weight: bold; text-align: center; font-size: 10pt;">
            BAGIAN 1 – PENGGAGAS
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="4" style="border: 1px solid #000000; vertical-align: top;">
            <b>3. PENGGAGAS</b><br />
            {{ $ketidaksesuaian->pelapor?->nama ?? '-' }}
        </td>
        <td colspan="4" style="border: 1px solid #000000; vertical-align: top;">
            <b>4. DEPARTEMEN/BAGIAN</b><br />
            {{ $ketidaksesuaian->pelapor?->jabatan?->nama ?? ($ketidaksesuaian->divisiPelapor?->nama ?? '-') }}
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="8" style="border: 1px solid #000000; vertical-align: top;">
            <b>5. JENIS KETIDAKSESUAIAN/POTENSIAL KETIDAKSESUAIAN</b><br />
            {{ $isKeluhan ? '[X]' : '[  ]' }} Keluhan Pelanggan<br />
            {{ $isAudit ? '[X]' : '[  ]' }} Laporan Audit<br />
            {{ $isLain ? '[X]' : '[  ]' }} Lain-lain, sebutkan: {{ $isLain ? $namaLain : '' }}
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="6" style="border: 1px solid #000000; vertical-align: top;">
            <b>6. PENJELASAN TEMUAN (yang timbul atau potensial)</b><br />
            {{ $ketidaksesuaian->penjelasan_temuan }}
        </td>
        <td colspan="2" style="border: 1px solid #000000; vertical-align: middle;">
            {{ $isOk ? '[X]' : '[  ]' }} OK<br />
            {{ $isObservasi ? '[X]' : '[  ]' }} OBSERVASI<br />
            {{ $isNc ? '[X]' : '[  ]' }} NC
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="4" style="border: 1px solid #000000; vertical-align: top;">
            <b>7. Penggagas :</b><br /><br />
            {{ $ketidaksesuaian->pelapor?->nama ?? '-' }}
        </td>
        <td colspan="4" style="border: 1px solid #000000; vertical-align: top;">
            <b>Manajer Bagian</b><br /><br />
            {{ $ketidaksesuaian->divisiPelapor?->nama ?? '-' }}
        </td>
        <td></td>
    </tr>

    <!-- BARIS 8: BAGIAN 2 - TINDAKAN -->
    <tr>
        <td></td>
        <td colspan="8" style="border: 1px solid #000000; background-color: #cccccc; font-weight: bold; text-align: center; font-size: 10pt;">
            BAGIAN 2 – TINDAKAN
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="4" style="border: 1px solid #000000; vertical-align: top;">
            <b>8A. DEPARTEMEN :</b> {{ $ketidaksesuaian->divisiTujuan?->nama ?? '-' }}<br />
            <b>8B. MANAJER BAGIAN :</b> {{ $tl?->user?->name ?? '-' }}
        </td>
        <td colspan="4" style="border: 1px solid #000000; vertical-align: top;">
            <b>9. PERKIRAAN TGL PENYELESAIAN</b><br />
            {{ $tl?->perkiraan_tanggal_selesai ? $tl->perkiraan_tanggal_selesai->translatedFormat('d F Y') : '-' }}
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="8" style="border: 1px solid #000000; vertical-align: top;">
            <b>10. AKAR MASALAH (atau Analisa untuk Tindakan Pencegahan)</b><br />
            {{ $tl?->akar_masalah ?? '-' }}
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="4" style="border: 1px solid #000000; vertical-align: top;">
            <b>11. TINDAKAN KOREKTIF</b><br />
            {{ $tl?->tindakan_korektif ?? '-' }}
        </td>
        <td colspan="4" style="border: 1px solid #000000; vertical-align: top;">
            <b>12. TINDAKAN PENCEGAHAN</b><br />
            {{ $tl?->tindakan_pencegahan ?? '-' }}
        </td>
        <td></td>
    </tr>

    <!-- BARIS 13: BAGIAN 3 - VERIFIKASI -->
    <tr>
        <td></td>
        <td colspan="8" style="border: 1px solid #000000; background-color: #cccccc; font-weight: bold; text-align: center; font-size: 10pt;">
            BAGIAN 3 – VERIFIKASI
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="border: 1px solid #000000; vertical-align: top;">
            <b>13. TINDAKAN EFEKTIF?</b><br />
            {{ $isEfektifYa ? '[X]' : '[  ]' }} Ya &nbsp;&nbsp; {{ $isEfektifTidak ? '[X]' : '[  ]' }} Tidak
        </td>
        <td colspan="3" style="border: 1px solid #000000; vertical-align: top;">
            <b>14. ALASAN</b><br />
            {{ $verifikasi?->alasan ?? '-' }}
        </td>
        <td colspan="3" style="border: 1px solid #000000; vertical-align: top;">
            <b>15. FTKP BARU No. (jika perlu)</b><br />
            {{ $verifikasi?->ftkp_baru_no ?? '-' }}
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5" style="border: 1px solid #000000; vertical-align: top;">
            <b>16. TANDA TANGAN YANG MEMVERIFIKASI</b><br /><br />
            {{ $verifikasi?->verifikator?->name ?? '-' }}
        </td>
        <td colspan="3" style="border: 1px solid #000000; vertical-align: top;">
            <b>17. TANGGAL</b><br /><br />
            {{ $verifikasi?->created_at ? $verifikasi->created_at->translatedFormat('d F Y') : '-' }}
        </td>
        <td></td>
    </tr>

    <!-- BOTTOM SPACER -->
    <tr>
        <td></td>
        <td colspan="8"></td>
        <td></td>
    </tr>
</table>
</body>
</html>
