<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penilaian — {{ $periode->nama ?? 'Semua' }} — SIMA-REVIEW</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Arial', sans-serif; font-size: 12px; color: #1e293b; background: white; }
        .header { text-align: center; padding: 20px 0 16px; border-bottom: 2px solid #1e40af; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: 700; color: #1e40af; }
        .header h2 { font-size: 14px; font-weight: 600; color: #374151; margin-top: 4px; }
        .header p { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .meta { display: flex; gap: 24px; margin-bottom: 16px; padding: 12px 16px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; }
        .meta-item { flex: 1; }
        .meta-item .label { font-size: 10px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-item .value { font-size: 12px; font-weight: 700; color: #1e293b; margin-top: 2px; }
        .stat-row { display: flex; gap: 12px; margin-bottom: 16px; }
        .stat-box { flex: 1; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; }
        .stat-box .val { font-size: 20px; font-weight: 800; color: #1e40af; }
        .stat-box .lbl { font-size: 10px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #1e40af; color: white; }
        thead th { padding: 8px 10px; text-align: left; font-size: 11px; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .nilai { font-weight: 700; font-size: 13px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-primary { background: #dbeafe; color: #1e40af; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; font-size: 10px; color: #6b7280; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding:12px;background:#f1f5f9;text-align:center;border-bottom:1px solid #e2e8f0;">
        <button onclick="window.print()" style="padding:8px 20px;background:#1e40af;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;">
            🖨️ Print / Save PDF
        </button>
        <button onclick="window.close()" style="padding:8px 20px;background:#64748b;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;margin-left:8px;">
            ✕ Tutup
        </button>
    </div>

    <div style="padding:24px 32px;max-width:900px;margin:0 auto;">
        <div class="header">
            <h1>SIMA-REVIEW</h1>
            <h2>LAPORAN EVALUASI SERVICE EXCELLENT</h2>
            <p>{{ $periode->nama ?? 'Semua Periode' }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d M Y, H:i') }} WIB &nbsp;|&nbsp; Oleh: {{ auth()->user()->name }}</p>
        </div>

        <div class="meta">
            <div class="meta-item">
                <div class="label">Periode</div>
                <div class="value">{{ $periode->nama ?? 'Semua Periode' }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Divisi</div>
                <div class="value">{{ $divisi->nama ?? 'Semua Divisi' }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Tanggal Cetak</div>
                <div class="value">{{ now()->format('d M Y') }}</div>
            </div>
        </div>

        <div class="stat-row">
            <div class="stat-box">
                <div class="val">{{ $penilaians->count() }}</div>
                <div class="lbl">Karyawan Dinilai</div>
            </div>
            <div class="stat-box">
                <div class="val" style="color:#059669;">{{ number_format($penilaians->avg('nilai_akhir') ?? 0, 2) }}</div>
                <div class="lbl">Rata-rata</div>
            </div>
            <div class="stat-box">
                <div class="val">{{ number_format($penilaians->max('nilai_akhir') ?? 0, 1) }}</div>
                <div class="lbl">Tertinggi</div>
            </div>
            <div class="stat-box">
                <div class="val" style="color:#dc2626;">{{ number_format($penilaians->min('nilai_akhir') ?? 0, 1) }}</div>
                <div class="lbl">Terendah</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="30">#</th>
                    <th width="80">NIP</th>
                    <th>Nama Karyawan</th>
                    <th width="110">Jabatan</th>
                    <th width="100">Divisi</th>
                    <th width="80">Nilai</th>
                    <th width="90">Kategori</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penilaians->sortByDesc('nilai_akhir')->values() as $i => $p)
                @php $kat = $p->kategori; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->karyawan->nip ?? '-' }}</td>
                    <td style="font-weight:600;">{{ $p->karyawan->nama ?? '-' }}</td>
                    <td>{{ $p->karyawan->jabatan->nama ?? '-' }}</td>
                    <td>{{ $p->karyawan->divisi->nama ?? '-' }}</td>
                    <td>
                        <span class="nilai" style="color:{{ $kat?->warna=='success'?'#059669':($kat?->warna=='primary'?'#1e40af':($kat?->warna=='warning'?'#854d0e':'#991b1b')) }}">
                            {{ number_format($p->nilai_akhir ?? 0, 2) }}
                        </span>
                    </td>
                    <td>
                        @if($kat)
                        <span class="badge badge-{{ $kat->warna }}">{{ $kat->nama }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <div>SIMA-REVIEW — Sistem Evaluasi Service Excellent &nbsp;|&nbsp; {{ config('app.url') }}</div>
            <div>Halaman 1 / 1</div>
        </div>

        <div style="margin-top:48px;display:flex;justify-content:flex-end;">
            <div style="text-align:center;width:200px;">
                <p style="font-size:11px;color:#6b7280;">Mengetahui,</p>
                <div style="margin-top:56px;border-top:1px solid #1e293b;padding-top:4px;">
                    <p style="font-size:11px;font-weight:600;">{{ auth()->user()->name }}</p>
                    <p style="font-size:10px;color:#6b7280;">{{ auth()->user()->role_label }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
