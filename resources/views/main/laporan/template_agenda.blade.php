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
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
        }
        .half-page {
            /* height: 14.85cm; Half of A4 (29.7cm / 2) */
            /* border: 1px solid #000; */
            padding: 10px;
            margin-left: 20px;
            margin-right: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        td, th {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        .center { text-align: center; }
        .no-border td, .no-border th { border: none; }
        .title {
            font-weight: bold;
            text-align: center;
            font-size: 12px;
            /* margin-bottom: 8px; */
        }
        .disposisi-list {
            margin-bottom: 2px;
        }
        .disposisi-box {
            height: 80px;
            border: 1px solid #000;
            margin-top: 5px;
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
            <h2>Daftar Agenda {{ empty($jenis) ? 'Surat Masuk dan Keluar' : ('Surat '. $jenis) }} Tahun {{ $tahun }} {{ !empty($bulan) ? ('Bulan ' . (intval($bulan) < 10 ? '0'.$bulan : $bulan)) : '' }}</h2>
        </div>
    </div>
    <table style="width:100%">
        <thead>
            <tr>
                <th style="width:5%; vertical-align: middle; text-align: left;">NO. AGENDA</th>
                <th style="width:10%; vertical-align: middle; text-align: left; word-wrap: break-word; white-space: normal;">KEPADA</th>
                <th style="width:5%; vertical-align: middle; text-align: left; word-wrap: break-word; white-space: normal;">TGL. KIRIM /<br>TGL. SURAT /<br>NO. SURAT</th>
                <th style="width:15%; vertical-align: middle; text-align: left; word-wrap: break-word; white-space: normal;">KLASIFIKASI /<br>KET. JRA /<br>ISI INFORMASI</th>
                <th style="width:10%; vertical-align: middle; text-align: left; word-wrap: break-word; white-space: normal;">DARI UNIT KERJA</th>

                @role(['administrator'])
                <th style="width: 18%; vertical-align: middle; text-align: left; word-wrap: break-word; white-space: normal;">DISPOSISI SEKDA</th>
                <th style="width: 18%; vertical-align: middle; text-align: left; word-wrap: break-word; white-space: normal;">DISPOSISI WAKIL BUPATI</th>
                <th style="width: 19%; vertical-align: middle; text-align: left; word-wrap: break-word; white-space: normal;">DISPOSISI BUPATI</th>
                @endrole

                @role(['umum', 'setda'])
                <th style="width: 27%; vertical-align: middle; text-align: left; word-wrap: break-word; white-space: normal;">DISPOSISI SEKDA</th>
                <th style="width: 28%; vertical-align: middle; text-align: left; word-wrap: break-word; white-space: normal;">DISPOSISI BUPATI</th>
                @endrole

                @role(['wabup'])
                <th style="width: 30%; vertical-align: middle; text-align: left; word-wrap: break-word; white-space: normal;">DISPOSISI WAKIL BUPATI</th>
                @endrole

                @role(['bupati'])
                <th style="width: 30%; vertical-align: middle; text-align: left; word-wrap: break-word; white-space: normal;">DISPOSISI BUPATI</th>
                @endrole
            </tr>
        </thead>
        <tbody>
            <tr>
                @for ($t = 1; $t < 8; $t++)
                <td>{{ $t }}</td>
                @endfor
            </tr>

            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->NOAGENDA }}</td>
                    <td>{{ $item->JENISSURAT == 'Masuk' ? $item->Posisi : $item->drkpd }}</td>
                    <td>{!! \Carbon\Carbon::parse($item->TGLENTRY)->isoFormat('DD-MM-YYYY'). '<br>' .\Carbon\Carbon::parse($item->TGLSURAT)->isoFormat('DD-MM-YYYY'). '<br>' .$item->NOSURAT !!}</td>
                    <td>{!! $item->KLAS3. '<br><b>' .$item->KETJRA. '</b><br>' .$item->ISI !!}</td>
                    <td>{{ $item->JENISSURAT == 'Keluar' ? $item->NAMAUP : $item->drkpd }}</td>

                    @role(['administrator'])
                    <td>{!! '<b><p style="width: 100%; text-align: right;">' .(empty($item->tglsekda1) ? null : \Carbon\Carbon::parse($item->tglsekda1)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$item->DisposisiSekda !!}</td>
                    <td>{!! '<b><p style="width: 100%; text-align: right;">'. (empty($item->tglwakil) ? null : \Carbon\Carbon::parse($item->tglwakil)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$item->DisposisiWakil !!}</td>
                    <td>{!! '<b><p style="width: 100%; text-align: right;">' .(empty($item->tglbupati1) ? null : \Carbon\Carbon::parse($item->tglbupati1)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$item->DisposisiBupati !!}</td>
                    @endrole

                    @role(['umum', 'setda'])
                    <td>{!! '<b><p style="width: 100%; text-align: right;">' .(empty($item->tglsekda1) ? null : \Carbon\Carbon::parse($item->tglsekda1)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$item->DisposisiSekda !!}</td>
                    <td>{!! '<b><p style="width: 100%; text-align: right;">' .(empty($item->tglbupati1) ? null : \Carbon\Carbon::parse($item->tglbupati1)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$item->DisposisiBupati !!}</td>
                    @endrole

                    @role(['wabup'])
                    <td>{!! '<b><p style="width: 100%; text-align: right;">'. (empty($item->tglwakil) ? null : \Carbon\Carbon::parse($item->tglwakil)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$item->DisposisiWakil !!}</td>
                    @endrole

                    @role(['bupati'])
                    <td>{!! '<b><p style="width: 100%; text-align: right;">' .(empty($item->tglbupati1) ? null : \Carbon\Carbon::parse($item->tglbupati1)->isoFormat('DD-MM-YYYY HH:mm')). '</p></b><br>' .$item->DisposisiBupati !!}</td>
                    @endrole
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>