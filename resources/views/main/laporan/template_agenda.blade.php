<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Agenda {{ empty($jenis) ? 'Surat Masuk dan Keluar' : ('Surat '. $jenis) }}</title>
    <style>
        @page {
            size: legal landscape;
            margin: 1cm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            color: #333;
        }
        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 10.5px;
        }
        td, th {
            border: 1px solid #000;
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
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="">
    <div style="margin-bottom: 15px;">
        <table style="width: 100%; border: none;">
            <tr style="border: none;">
                <th style="text-align: center; width: 10%; border: none;">
                    <img src="{{ public_path('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" alt="Logo" width="80">
                </th>
                <th style="text-align: left; vertical-align: top; width: 90%; border: none;">
                    @role('umum')
                    <label style="font-size: 20; font-weight: bold;">BAGIAN UMUM DAN KEUANGAN SETDA</label>
                    @endrole
                    @role('setda')
                    <label style="font-size: 20; font-weight: bold;">SEKRETARIAT DAERAH</label>
                    @endrole
                    <br>
                    <label style="font-size: 14; font-weight: bold;">PEMERINTAH KABUPATEN KARANGANYAR</label>
                </th>
            </tr>
        </table>
        <div style="width: 100%; text-align: center">
            <h2>
                Daftar Agenda {{ empty($jenis) ? 'Surat Masuk dan Keluar' : ('Surat '. $jenis) }}
                {!! !empty($range) ? '<br>'.$range : '' !!}
            </h2>
        </div>
    </div>
    <table style="width:100%">
        <thead>
            <tr>
                <th style="width:5%; vertical-align: middle; text-align: left;">NO. AGENDA</th>
                <th style="width:10%; vertical-align: middle; text-align: left;">KEPADA</th>
                <th style="width:10%; vertical-align: middle; text-align: left;">TGL. KIRIM /<br>TGL. SURAT /<br>NO. SURAT</th>
                <th style="width:24%; vertical-align: middle; text-align: left;">KLASIFIKASI /<br>KET. JRA /<br>ISI INFORMASI</th>
                <th style="width:11%; vertical-align: middle; text-align: left;">DARI UNIT KERJA</th>

                @role(['administrator'])
                <th style="width: 13%; vertical-align: middle; text-align: left;">DISPOSISI SEKDA</th>
                <th style="width: 13%; vertical-align: middle; text-align: left;">DISPOSISI WAKIL BUPATI</th>
                <th style="width: 14%; vertical-align: middle; text-align: left;">DISPOSISI BUPATI</th>
                @endrole

                @role(['umum', 'setda'])
                <th style="width: 20%; vertical-align: middle; text-align: left;">DISPOSISI SEKDA</th>
                <th style="width: 20%; vertical-align: middle; text-align: left;">DISPOSISI BUPATI</th>
                @endrole

                @role(['wabup'])
                <th style="width: 40%; vertical-align: middle; text-align: left;">DISPOSISI WAKIL BUPATI</th>
                @endrole

                @role(['bupati'])
                <th style="width: 40%; vertical-align: middle; text-align: left;">DISPOSISI BUPATI</th>
                @endrole
            </tr>
        </thead>
        <tbody>
            <tr style="text-align: center; font-weight: bold; background-color: #f2f2f2;">
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
                <td>5</td>
                @role(['administrator'])
                <td>6</td>
                <td>7</td>
                <td>8</td>
                @endrole
                @role(['umum', 'setda'])
                <td>6</td>
                <td>7</td>
                @endrole
                @role(['wabup', 'bupati'])
                <td>6</td>
                @endrole
            </tr>

            @foreach ($data as $item)
                @php
                    $isObj = is_object($item);
                    $noAgenda = $isObj ? ($item->no_agenda ?? $item->NOAGENDA ?? '-') : ($item['no_agenda'] ?? '-');
                    $kepada = $isObj ? ($item->kepada ?? ($item->JENISSURAT == 'Masuk' ? $item->Posisi : $item->drkpd)) : ($item['kepada'] ?? '-');
                    $row3 = $isObj ? ($item->row3 ?? (!empty($item->TGLENTRY) ? \Carbon\Carbon::parse($item->TGLENTRY)->isoFormat('DD-MM-YYYY') . '<br>' . \Carbon\Carbon::parse($item->TGLSURAT)->isoFormat('DD-MM-YYYY') . '<br>' . ($item->NOSURAT ?? '-') : '-')) : ($item['row3'] ?? '-');
                    $row4 = $isObj ? ($item->row4 ?? (($item->KLAS3 ?? '-') . '<br><b>' . ($item->KETJRA ?? '') . '</b><br>' . ($item->ISI ?? '-'))) : ($item['row4'] ?? '-');
                    $dari = $isObj ? ($item->dari ?? ($item->JENISSURAT == 'Keluar' ? $item->NAMAUP : $item->drkpd)) : ($item['dari'] ?? '-');
                    $sekda = $isObj ? ($item->sekda ?? ($item->DisposisiSekda ?? '-')) : ($item['sekda'] ?? '-');
                    $wakil = $isObj ? ($item->wakil ?? ($item->DisposisiWakil ?? '-')) : ($item['wakil'] ?? '-');
                    $bupati = $isObj ? ($item->bupati ?? ($item->DisposisiBupati ?? '-')) : ($item['bupati'] ?? '-');
                @endphp
                <tr>
                    <td>{!! $noAgenda !!}</td>
                    <td>{{ $kepada }}</td>
                    <td>{!! $row3 !!}</td>
                    <td>{!! $row4 !!}</td>
                    <td>{{ $dari }}</td>

                    @role(['administrator'])
                    <td>{!! $sekda !!}</td>
                    <td>{!! $wakil !!}</td>
                    <td>{!! $bupati !!}</td>
                    @endrole

                    @role(['umum', 'setda'])
                    <td>{!! $sekda !!}</td>
                    <td>{!! $bupati !!}</td>
                    @endrole

                    @role(['wabup'])
                    <td>{!! $wakil !!}</td>
                    @endrole

                    @role(['bupati'])
                    <td>{!! $bupati !!}</td>
                    @endrole
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>