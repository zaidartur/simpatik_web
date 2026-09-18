<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Monitoring Disposisi & Tindak Lanjut</title>
    <style>
        @page {
            size: legal landscape;
            margin: 1cm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            color: #222;
        }
        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 9.5px;
        }
        td, th {
            border: 1px solid #333;
            padding: 4px 5px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        tr {
            page-break-inside: avoid;
        }
        .center { text-align: center; }
        .no-border td, .no-border th { border: none; }
        .title {
            font-weight: bold;
            text-align: center;
            font-size: 13px;
            margin-bottom: 2px;
        }
        .subtitle {
            font-size: 11px;
            text-align: center;
            margin-bottom: 12px;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: bold;
        }
        .badge-success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .badge-warning { background-color: #fff8e1; color: #f57f17; border: 1px solid #ffe082; }
        .th-sub {
            background-color: #f5f5f5;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div>
    <div style="margin-bottom: 10px;">
        <table style="width: 100%; border: none;">
            <tr style="border: none;">
                <th style="text-align: center; width: 8%; border: none;">
                    <img src="{{ public_path('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" alt="Logo" width="65">
                </th>
                <th style="text-align: left; vertical-align: top; width: 92%; border: none; padding-left: 10px;">
                    <span style="font-size: 16px; font-weight: bold; text-transform: uppercase;">SEKRETARIAT DAERAH</span><br>
                    <span style="font-size: 13px; font-weight: bold; text-transform: uppercase;">PEMERINTAH KABUPATEN KARANGANYAR</span><br>
                    <span style="font-size: 10px; font-weight: normal; color: #555;">Jl. Lawu No. 385 Karanganyar, Jawa Tengah</span>
                </th>
            </tr>
        </table>
        <hr style="border: none; border-top: 2px solid #000; margin-top: 5px; margin-bottom: 10px;">
    </div>

    <div class="title">LAPORAN MONITORING DISPOSISI & TINDAK LANJUT SURAT</div>
    <div class="subtitle">
        @if(!empty($startDate) && !empty($endDate))
            Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}
        @else
            Seluruh Data Tercatat
        @endif
    </div>

    <table>
        <thead>
            <tr style="background-color: #e9ecef;">
                <th style="width: 3%; text-align: center;" rowspan="2">No</th>
                <th style="width: 11%; text-align: center;" rowspan="2">Nomor & Tgl Surat</th>
                <th style="width: 7%; text-align: center;" rowspan="2">No. Agenda</th>
                <th style="width: 14%; text-align: center;" rowspan="2">Instansi / Pengirim</th>
                <th style="width: 18%; text-align: center;" rowspan="2">Perihal</th>
                <th style="width: 33%; text-align: center;" colspan="3">Instruksi / Tindak Lanjut</th>
                <th style="width: 8%; text-align: center;" rowspan="2">Posisi</th>
                <th style="width: 6%; text-align: center;" rowspan="2">Status</th>
            </tr>
            <tr style="background-color: #e9ecef;">
                <th style="width: 11%; text-align: center;">Sekda</th>
                <th style="width: 11%; text-align: center;">Wabup</th>
                <th style="width: 11%; text-align: center;">Bupati</th>
            </tr>
            <tr class="th-sub">
                <th style="text-align: center;">1</th>
                <th style="text-align: center;">2</th>
                <th style="text-align: center;">3</th>
                <th style="text-align: center;">4</th>
                <th style="text-align: center;">5</th>
                <th style="text-align: center;">6</th>
                <th style="text-align: center;">7</th>
                <th style="text-align: center;">8</th>
                <th style="text-align: center;">9</th>
                <th style="text-align: center;">10</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $i => $row)
                @php
                    $dispoSekda = trim(($row->DisposisiSekda ?? '') . ' ' . ($row->DisposisiSekda2 ?? ''));
                @endphp
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $row->NOSURAT ?? '-' }}</strong><br>
                        <small style="color: #666;">Tgl: {{ $row->TGLSURAT ?? '-' }}</small>
                    </td>
                    <td class="center">{{ $row->NOAGENDA ?? '-' }}</td>
                    <td>{{ $row->drkpd ?? '-' }}</td>
                    <td>{{ $row->PERIHAL ?? '-' }}</td>
                    <td>{!! !empty($dispoSekda) ? nl2br(e($dispoSekda)) : '-' !!}</td>
                    <td>{!! !empty($row->DisposisiWakil) ? nl2br(e($row->DisposisiWakil)) : '-' !!}</td>
                    <td>{!! !empty($row->DisposisiBupati) ? nl2br(e($row->DisposisiBupati)) : '-' !!}</td>
                    <td>{{ $row->Posisi ?? '-' }}</td>
                    <td class="center">
                        @if($row->statussurat == 'selesai')
                            <span class="badge badge-success">Selesai</span>
                        @else
                            <span class="badge badge-warning">Menunggu</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center" style="padding: 20px;">Tidak ada data tindak lanjut yang sesuai dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
