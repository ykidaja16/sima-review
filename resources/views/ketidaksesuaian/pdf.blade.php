<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>FTKP {{ $ketidaksesuaian->nomor_ftkp }} — SIMA-MONEV</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11.5px; color: #1e293b; line-height: 1.5; padding: 24px 30px; }
        .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { font-size: 16px; color: #1e40af; font-weight: 700; }
        .header h2 { font-size: 13px; color: #334155; margin-top: 3px; }
        .header p { font-size: 10.5px; color: #64748b; margin-top: 4px; }
        .section { border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 14px; overflow: hidden; }
        .section-title { background: #f1f5f9; padding: 7px 12px; font-weight: 700; font-size: 11.5px; color: #1e40af; border-bottom: 1px solid #cbd5e1; }
        .section-body { padding: 10px 12px; }
        table.info-table { width: 100%; border-collapse: collapse; }
        table.info-table td { padding: 5px 6px; vertical-align: top; border-bottom: 1px solid #f1f5f9; }
        table.info-table tr:last-child td { border-bottom: none; }
        .lbl { width: 32%; color: #64748b; font-weight: 600; }
        .val { width: 68%; color: #0f172a; }
        .box-text { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 10px; margin-top: 4px; white-space: pre-wrap; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; background: #e2e8f0; color: #1e293b; }
        .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 9.5px; color: #64748b; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FORMULIR TINDAKAN KOREKTIF DAN PERBAIKAN (FTKP)</h1>
        <h2>No. FTKP: {{ $ketidaksesuaian->nomor_ftkp }}</h2>
        <p>Tanggal Laporan: {{ $ketidaksesuaian->tanggal_laporan?->format('d F Y') }} &nbsp;|&nbsp; Status: <strong>{{ $ketidaksesuaian->status_label }}</strong></p>
    </div>

    {{-- BAGIAN 1 --}}
    <div class="section">
        <div class="section-title">BAGIAN 1 – PENGGAGAS</div>
        <div class="section-body">
            <table class="info-table">
                <tr>
                    <td class="lbl">Penggagas / Pelapor</td>
                    <td class="val"><strong>{{ $ketidaksesuaian->pelapor?->nama ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td class="lbl">Departemen / Bagian Penggagas</td>
                    <td class="val">{{ $ketidaksesuaian->divisiPelapor?->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Ditujukan ke Divisi</td>
                    <td class="val"><strong>{{ $ketidaksesuaian->divisiTujuan?->nama ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td class="lbl">Jenis Ketidaksesuaian</td>
                    <td class="val">
                        @if($ketidaksesuaian->jenis)
                            {{ $ketidaksesuaian->jenis->nama }}
                        @elseif($ketidaksesuaian->jenis_lainnya)
                            Lain-lain: {{ $ketidaksesuaian->jenis_lainnya }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="lbl">Kategori Temuan</td>
                    <td class="val"><span class="badge">{{ $ketidaksesuaian->kategori_label }}</span></td>
                </tr>
                <tr>
                    <td class="lbl">Penjelasan Temuan</td>
                    <td class="val">
                        <div class="box-text">{{ $ketidaksesuaian->penjelasan_temuan }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- BAGIAN 2 --}}
    <div class="section">
        <div class="section-title">BAGIAN 2 – TINDAKAN</div>
        <div class="section-body">
            @if($ketidaksesuaian->tindaklanjut)
                @php $tl = $ketidaksesuaian->tindaklanjut; @endphp
                <table class="info-table">
                    <tr>
                        <td class="lbl">Diisi Oleh</td>
                        <td class="val"><strong>{{ $tl->user?->name ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="lbl">Perkiraan Tanggal Selesai</td>
                        <td class="val">{{ $tl->perkiraan_tanggal_selesai?->format('d F Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Akar Masalah / Analisa</td>
                        <td class="val"><div class="box-text">{{ $tl->akar_masalah }}</div></td>
                    </tr>
                    <tr>
                        <td class="lbl">Tindakan Korektif/Perbaikan</td>
                        <td class="val"><div class="box-text">{{ $tl->tindakan_korektif }}</div></td>
                    </tr>
                    <tr>
                        <td class="lbl">Tindakan Koreksi</td>
                        <td class="val"><div class="box-text">{{ $tl->tindakan_pencegahan }}</div></td>
                    </tr>
                </table>
            @else
                <div style="color:#64748b;font-style:italic;padding:6px 0;">Tindak lanjut belum diisi oleh divisi terkait.</div>
            @endif
        </div>
    </div>

    {{-- BAGIAN 3 --}}
    <div class="section">
        <div class="section-title">BAGIAN 3 – VERIFIKASI</div>
        <div class="section-body">
            <table class="info-table">
                <tr>
                    <td class="lbl">Verifikasi 1 (Manager Penggagas / Kacab)</td>
                    <td class="val">
                        @if($ketidaksesuaian->verifikasiManager)
                            @php $vm = $ketidaksesuaian->verifikasiManager; @endphp
                            <strong>{{ $vm->tindakan_efektif ? 'Tindakan Efektif' : 'Tidak Efektif' }}</strong>
                            — Oleh: {{ $vm->verifikator?->name ?? '-' }}
                            @if($vm->alasan)
                                <div class="box-text">{{ $vm->alasan }}</div>
                            @endif
                        @else
                            <span style="color:#64748b;font-style:italic;">Menunggu verifikasi 1...</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="lbl">Verifikasi 2 (Kepala Cabang)</td>
                    <td class="val">
                        @if($ketidaksesuaian->verifikasiKacab)
                            @php $vk = $ketidaksesuaian->verifikasiKacab; @endphp
                            <strong>{{ $vk->tindakan_efektif ? 'Tindakan Efektif' : 'Tidak Efektif' }}</strong>
                            — Oleh: {{ $vk->verifikator?->name ?? '-' }}
                            @if($vk->alasan)
                                <div class="box-text">{{ $vk->alasan }}</div>
                            @endif
                            @if($vk->ftkp_baru_no)
                                <div style="margin-top:4px;font-size:10.5px;">No. FTKP Baru: <strong>{{ $vk->ftkp_baru_no }}</strong></div>
                            @endif
                        @else
                            <span style="color:#64748b;font-style:italic;">Menunggu verifikasi kepala cabang...</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Dicetak dari SIMA-MONEV pada {{ now()->format('d/m/Y H:i') }} WIB
    </div>
</body>
</html>

