<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>FTKP {{ $ketidaksesuaian->nomor_ftkp }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #000000;
            line-height: 1.35;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000000;
        }
        .main-table th, .main-table td {
            border: 1px solid #000000;
            padding: 5px 7px;
            vertical-align: top;
        }
        .section-header {
            background-color: #cccccc;
            font-weight: bold;
            font-size: 9.5pt;
            text-align: center;
            padding: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cb {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000000;
            text-align: center;
            line-height: 9px;
            font-size: 8.5pt;
            font-weight: bold;
            vertical-align: middle;
            margin-right: 4px;
        }
        .cb-checked {
            font-family: DejaVu Sans, Arial, sans-serif;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .underline { text-decoration: underline; }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logosima.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

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

    <table class="main-table">
        <!-- HEADER FORMULIR -->
        <tr>
            <td style="width: 22%; text-align: center; vertical-align: middle; padding: 6px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="max-height: 48px; max-width: 125px; object-fit: contain;">
                @else
                    <strong style="font-size: 14pt; color: #0284c7;">SIMA<span style="color: #16a34a;">lab</span></strong>
                @endif
            </td>
            <td style="width: 50%; text-align: center; vertical-align: middle; font-weight: bold; font-size: 10pt; line-height: 1.3; padding: 6px;">
                FORMULIR TINDAKAN KOREKTIF DAN PERBAIKAN
            </td>
            <td style="width: 28%; padding: 0; vertical-align: top;">
                <table style="width: 100%; border-collapse: collapse; border: none;">
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; padding: 4px 6px; font-size: 8.5pt;">
                            <span class="text-bold">1. FTKP No. :</span>
                            <span class="underline" style="margin-left: 2px;">{{ $ketidaksesuaian->nomor_ftkp }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 4px 6px; font-size: 8.5pt;">
                            <span class="text-bold">2. <span class="underline">TANGGAL</span> :</span>
                            <span>{{ $ketidaksesuaian->tanggal_laporan ? $ketidaksesuaian->tanggal_laporan->translatedFormat('d F Y') : '-' }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- BAGIAN 1 - PENGGAGAS -->
        <tr>
            <td colspan="3" class="section-header">BAGIAN 1 – PENGGAGAS</td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 4px 7px;">
                <div class="text-bold">3. PENGGAGAS</div>
                <div class="text-center" style="padding-top: 4px;">{{ $ketidaksesuaian->pelapor?->nama ?? '-' }}</div>
            </td>
            <td style="width: 50%; padding: 4px 7px;">
                <div class="text-bold">4. DEPARTEMEN/BAGIAN</div>
                <div class="text-center" style="padding-top: 4px;">{{ $ketidaksesuaian->pelapor?->jabatan?->nama ?? ($ketidaksesuaian->divisiPelapor?->nama ?? '-') }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding: 5px 7px;">
                <div class="text-bold" style="margin-bottom: 3px;">5. JENIS KETIDAKSESUAIAN/POTENSIAL KETIDAKSESUAIAN</div>
                <div style="margin-left: 10px; line-height: 1.45;">
                    <div><span class="cb {{ $isKeluhan ? 'cb-checked' : '' }}">{!! $isKeluhan ? '&#10003;' : '&nbsp;' !!}</span> Keluhan Pelanggan</div>
                    <div><span class="cb {{ $isAudit ? 'cb-checked' : '' }}">{!! $isAudit ? '&#10003;' : '&nbsp;' !!}</span> Laporan Audit</div>
                    <div>
                        <span class="cb {{ $isLain ? 'cb-checked' : '' }}">{!! $isLain ? '&#10003;' : '&nbsp;' !!}</span>
                        Lain-lain, sebutkan: <span class="underline">{{ $isLain ? $namaLain : '' }}</span>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 5px 7px; vertical-align: top;">
                <div class="text-bold">6. PENJELASAN TEMUAN (yang timbul atau potensial)</div>
                <div style="padding-top: 6px; padding-left: 6px; min-height: 60px; line-height: 1.4;">
                    {!! nl2br(e($ketidaksesuaian->penjelasan_temuan)) !!}
                </div>
            </td>
            <td style="padding: 5px 7px; vertical-align: middle;">
                <div style="padding-left: 8px; line-height: 1.8;">
                    <div><span class="cb {{ $isOk ? 'cb-checked' : '' }}">{!! $isOk ? '&#10003;' : '&nbsp;' !!}</span> OK</div>
                    <div><span class="cb {{ $isObservasi ? 'cb-checked' : '' }}">{!! $isObservasi ? '&#10003;' : '&nbsp;' !!}</span> OBSERVASI</div>
                    <div><span class="cb {{ $isNc ? 'cb-checked' : '' }}">{!! $isNc ? '&#10003;' : '&nbsp;' !!}</span> NC</div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 4px 7px;">
                <div class="text-bold"><span class="underline">7. Penggagas</span> :</div>
                <div class="text-center" style="padding-top: 14px;">{{ $ketidaksesuaian->pelapor?->nama ?? '-' }}</div>
            </td>
            <td style="padding: 4px 7px;">
                <div class="text-bold">Manajer Bagian</div>
                <div class="text-center" style="padding-top: 14px;">{{ $ketidaksesuaian->divisiPelapor?->nama ?? '-' }}</div>
            </td>
        </tr>

        <!-- BAGIAN 2 - TINDAKAN -->
        <tr>
            <td colspan="3" class="section-header">BAGIAN 2 – TINDAKAN</td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 4px 7px;">
                <div><span class="text-bold">8A. DEPARTEMEN :</span> &nbsp; {{ $ketidaksesuaian->divisiTujuan?->nama ?? '-' }}</div>
                <div style="margin-top: 4px;"><span class="text-bold">8B. MANAJER BAGIAN :</span> &nbsp; {{ $tl?->user?->name ?? '-' }}</div>
            </td>
            <td style="padding: 4px 7px;">
                <div class="text-bold">9. PERKIRAAN TGL PENYELESAIAN</div>
                <div class="text-center" style="padding-top: 6px;">
                    {{ $tl?->perkiraan_tanggal_selesai ? $tl->perkiraan_tanggal_selesai->translatedFormat('d F Y') : '-' }}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding: 5px 7px;">
                <div class="text-bold">10. AKAR MASALAH (atau Analisa untuk Tindakan Pencegahan)</div>
                <div style="padding-top: 5px; padding-left: 6px; min-height: 48px; line-height: 1.4;">
                    {!! nl2br(e($tl?->akar_masalah ?? '-')) !!}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 5px 7px;">
                <div class="text-bold">11. TINDAKAN KOREKTIF</div>
                <div style="padding-top: 5px; padding-left: 6px; min-height: 52px; line-height: 1.4;">
                    {!! nl2br(e($tl?->tindakan_korektif ?? '-')) !!}
                </div>
            </td>
            <td style="width: 50%; padding: 5px 7px;">
                <div class="text-bold">12. TINDAKAN PENCEGAHAN</div>
                <div style="padding-top: 5px; padding-left: 6px; min-height: 52px; line-height: 1.4;">
                    {!! nl2br(e($tl?->tindakan_pencegahan ?? '-')) !!}
                </div>
            </td>
        </tr>

        <!-- BAGIAN 3 - VERIFIKASI -->
        <tr>
            <td colspan="3" class="section-header">BAGIAN 3 – VERIFIKASI</td>
        </tr>
        <tr>
            <td style="width: 28%; padding: 5px 7px;">
                <div class="text-bold" style="margin-bottom: 4px;">13. TINDAKAN EFEKTIF?</div>
                <div style="padding-top: 2px;">
                    <span class="cb {{ $isEfektifYa ? 'cb-checked' : '' }}">{!! $isEfektifYa ? '&#10003;' : '&nbsp;' !!}</span> Ya
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="cb {{ $isEfektifTidak ? 'cb-checked' : '' }}">{!! $isEfektifTidak ? '&#10003;' : '&nbsp;' !!}</span> Tidak
                </div>
            </td>
            <td style="width: 42%; padding: 5px 7px;">
                <div class="text-bold">14. ALASAN</div>
                <div style="padding-top: 3px; font-size: 8.5pt;">
                    {{ $verifikasi?->alasan ?? '-' }}
                </div>
            </td>
            <td style="width: 30%; padding: 5px 7px;">
                <div class="text-bold">15. FTKP BARU No. (jika perlu)</div>
                <div style="padding-top: 3px; font-size: 8.5pt;">
                    {{ $verifikasi?->ftkp_baru_no ?? '-' }}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 5px 7px;">
                <div class="text-bold">16. TANDA TANGAN YANG MEMVERIFIKASI</div>
                <div class="text-center" style="padding-top: 18px;">
                    {{ $verifikasi?->verifikator?->name ?? '-' }}
                </div>
            </td>
            <td style="padding: 5px 7px;">
                <div class="text-bold">17. TANGGAL</div>
                <div class="text-center" style="padding-top: 18px;">
                    {{ $verifikasi?->created_at ? $verifikasi->created_at->translatedFormat('d F Y') : '-' }}
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
