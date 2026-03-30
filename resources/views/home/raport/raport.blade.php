<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAPORT PERKEMBANGAN SISWA - Bimbel MURIKA</title>
    <style>
        @page {
            size: A4;
            margin: 1cm 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt;
            color: #2d3748;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 18cm;
            margin: auto;
        }
        
        /* Premium Header */
        .header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 30px;
            position: relative;
        }
        .header h1 {
            font-size: 20pt;
            font-weight: 900;
            color: #1a202c;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-sub {
            color: #718096;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: block;
            margin-top: 5px;
        }

        /* Identity Section */
        .section-header {
            font-size: 11pt;
            font-weight: 800;
            color: #2d3748;
            background: #f7fafc;
            padding: 8px 12px;
            border-left: 4px solid #4a5568;
            margin: 25px 0 15px 0;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 120px; padding: 4px 0; color: #4a5568; font-weight: bold; }
        .info-val { display: table-cell; border-bottom: 1px solid #edf2f7; padding: 4px 0 4px 10px; color: #1a202c; font-weight: normal; }

        /* Tables - Modern Design */
        .modern-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .modern-table th {
            background-color: #2d3748;
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: 700;
            padding: 10px 8px;
            text-transform: uppercase;
            border: 1px solid #2d3748;
        }
        .modern-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #edf2f7;
            border-left: 1px solid #edf2f7;
            border-right: 1px solid #edf2f7;
            vertical-align: middle;
        }
        .modern-table tr:nth-child(even) { background-color: #fcfcfc; }
        
        .center { text-align: center !important; }
        .score-box { 
            font-weight: 800; 
            color: #2d3748;
            font-size: 11pt;
        }

        /* Rating Guide */
        .guide-box {
            display: table;
            width: 100%;
            background: #f8fafc;
            font-size: 8pt;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }
        .guide-item { color: #64748b; margin-right: 20px; display: inline-block; }
        .guide-item span { font-weight: bold; color: #334155; }

        /* Description lines */
        .desc-block { margin-top: 15px; }
        .desc-title { font-weight: 700; font-size: 9pt; color: #4a5568; margin-bottom: 5px; display: block; }
        .dotted-area { border-bottom: 1px dotted #cbd5e0; height: 22px; margin-bottom: 3px; }

        /* Signatures - Professional Alignment */
        .sig-section { margin-top: 40px; }
        .sig-table { width: 100%; border: none; }
        .sig-table td { text-align: center; width: 33.33%; padding-top: 20px; vertical-align: bottom; }
        .sig-space { height: 70px; }
        .sig-line { 
            width: 80%; 
            margin: auto; 
            border-top: 1px solid #2d3748; 
            padding-top: 5px; 
            font-weight: 700; 
            font-size: 9pt;
        }

        /* Summary Area */
        .summary-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .summary-item { margin-bottom: 5px; font-size: 10pt; }
        .summary-item b { width: 100px; display: inline-block; color: #4a5568; }

        .footer-note {
            text-align: center;
            font-size: 7.5pt;
            color: #94a3b8;
            margin-top: 50px;
            letter-spacing: 0.5px;
        }

        .page-break { page-break-after: always; }
        .page-no { text-align: right; font-size: 7pt; color: #cbd5e0; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h1>RAPORT PERKEMBANGAN</h1>
            <span class="header-sub">Bimbel MURIKA - Digital Reporting System</span>
        </div>

        <!-- IDENTITY -->
        <div class="section-header">DATA PESERTA DIDIK</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nama Siswa</div>
                <div class="info-val">{{ $murid->name ?? '.........................................' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Program</div>
                <div class="info-val">{{ $items->reg->programs->name ?? '.........................................' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Level / Periode</div>
                <div class="info-val">Level {{ $items->reg->latestLevel->level ?? '.....' }} / {{ $items->reg->created_at->format('Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Unit</div>
                <div class="info-val">{{ $items->reg->units->name ?? '.........................................' }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Pengajar</div>
                <div class="info-val">{{ $items->teacher ?? '.........................................' }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Leader</div>
                <div class="info-val">{{ $items->reg->units->zone->first()->pic ?? '.........................................' }}</div>
            </div>
        </div>

        <div class="section-header">CAPAIAN KOMPETENSI UTAMA</div>
        <div class="guide-box">
            <div class="guide-item"><span>4</span> : Sangat Baik</div>
            <div class="guide-item"><span>3</span> : Baik</div>
            <div class="guide-item"><span>2</span> : Cukup</div>
            <div class="guide-item"><span>1</span> : Perlu Bimbingan</div>
        </div>

        <table class="modern-table">
            <thead>
                <tr>
                    <th class="center" style="width: 30px;">#</th>
                    <th>Indikator Penilaian</th>
                    <th class="center" style="width: 80px;">Skor</th>
                    <th style="width: 150px;">Catatan Pengajar</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="center">1</td><td>Penguasaan konsep materi utama</td><td class="center score-box">{{ $items->score_concept ?? '' }}</td><td>{{ $items->note_concept ?? '' }}</td></tr>
                <tr><td class="center">2</td><td>Ketahanan konsentrasi saat belajar</td><td class="center score-box">{{ $items->score_concentration ?? '' }}</td><td>{{ $items->note_concentration ?? '' }}</td></tr>
                <tr><td class="center">3</td><td>Ketepatan dan kecepatan pengerjaan</td><td class="center score-box">{{ $items->score_accuracy ?? '' }}</td><td>{{ $items->note_accuracy ?? '' }}</td></tr>
                <tr><td class="center">4</td><td>Kemandirian dalam penyelesaian soal</td><td class="center score-box">{{ $items->score_independence ?? '' }}</td><td>{{ $items->note_independence ?? '' }}</td></tr>
            </tbody>
        </table>

        <!-- DESCRIPTIONS -->
        <div class="section-header">DESKRIPSI PERKEMBANGAN</div>
        <div class="desc-block">
            <span class="desc-title">KEKUATAN SISWA :</span>
            <div class="dotted-area" style="border:none; height:auto; min-height:22px;">{{ $items->strength ?? '' }}</div>
            <div class="dotted-area"></div>
        </div>

        <div class="desc-block">
            <span class="desc-title">PERKEMBANGAN PERIODE INI :</span>
            <div class="dotted-area" style="border:none; height:auto; min-height:22px;">{{ $items->progress_period ?? '' }}</div>
            <div class="dotted-area"></div>
        </div>

        <div class="desc-block">
            <span class="desc-title">HAL YANG PERLU DITINGKATKAN :</span>
            <div class="dotted-area" style="border:none; height:auto; min-height:22px;">{{ $items->improvement ?? '' }}</div>
            <div class="dotted-area"></div>
        </div>


        <div class="page-no">Halaman 1 dari 2</div>
        <div class="page-break"></div>

        <!-- PAGE 2 -->
        <div class="section-header">RINGKASAN & REKOMENDASI</div>
        
        <div class="summary-card">
            <div class="summary-item"><b>Total Skor</b> : {{ ($items->score_concept ?? 0) + ($items->score_concentration ?? 0) + ($items->score_accuracy ?? 0) + ($items->score_independence ?? 0) }}</div>
            <div class="summary-item"><b>Rata-rata</b> : {{ number_format((($items->score_concept ?? 0) + ($items->score_concentration ?? 0) + ($items->score_accuracy ?? 0) + ($items->score_independence ?? 0)) / 4, 1) }}</div>
            <div class="summary-item"><b>Kategori</b> : {{ $items->category ?? '...................' }}</div>
        </div>

        <div class="desc-block" style="margin-top:30px;">
            <span class="desc-title">STATUS REKOMENDASI :</span>
            <div style="margin-top: 10px;">
                @foreach(['Lanjut Level', 'Perlu Penguatan Materi', 'Siap Naik Level', 'Perlu Evaluasi Khusus'] as $rec)
                <div style="margin-bottom:8px;">
                    <span style="display:inline-block; width:15px; height:15px; border:1px solid #4a5568; vertical-align:middle; margin-right:10px; text-align:center; line-height:15px; font-family: DejaVu Sans, sans-serif; font-size: 10pt;">
                        {!! ($items->recommendation ?? '') == $rec ? '&#10004;' : '' !!}
                    </span> {{ $rec }}
                </div>
                @endforeach
            </div>
            <div class="desc-block">
                <span class="desc-title">Catatan Tambahan :</span>
                <div class="dotted-area" style="border:none; height:auto; min-height:22px;">{{ $items->recommendation_note ?? '' }}</div>
            </div>
        </div>

        <!-- SIGNATURES -->
        <div class="sig-section">
            <div style="text-align: right; margin-bottom: 20px; font-size: 9pt; color: #4a5568;">
                Tanggal Terbit: {{ isset($items->created_at) ? $items->created_at->format('d F Y') : date('d F Y') }}
            </div>
            
            <table class="sig-table">
                <tr>
                    <td>
                        <div style="font-size: 8pt; color: #718096; margin-bottom: 5px;">Mengetahui,</div>
                        <div>Orang Tua / Wali</div>
                        <div class="sig-space"></div>
                        <div class="sig-line">NAMA ORANG TUA</div>
                    </td>
                    <td>
                        <div style="font-size: 8pt; color: #718096; margin-bottom: 5px;">Diverifikasi oleh,</div>
                        <div>Leader Cabang</div>
                        <div class="sig-space"></div>
                        <div class="sig-line">{{ $items->reg->units->zone->first()->pic ?? 'MURIKA MANAGEMENT' }}</div>
                    </td>
                    <td>
                        <div style="font-size: 8pt; color: #718096; margin-bottom: 5px;">Disusun oleh,</div>
                        <div>Pengajar / Mentor</div>
                        <div class="sig-space"></div>
                        <div class="sig-line">{{ $items->teacher ?? '...................................' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer-note">
            Laporan ini diterbitkan secara otomatis oleh Sistem Manajemen Pendidikan ELMU - Bimbel MURIKA.<br>
            © {{ date('Y') }} MURIKA GROUP - Seluruh Hak Cipta Dilindungi.
        </div>
        
        <div class="page-no">Halaman 2 dari 2</div>
    </div>

</body>
</html>
