<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Surat Keluar</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" />
    
    <style>
        @page {
            /* size: A4 portrait; */
            /* margin: 1cm; */
            size: 243mm 114.5mm;
            margin: 0mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            /* font-size: 12px; */
            margin: 0mm;
        }
        .kop {
            font-family: 'Times New Roman';
        }
        .main-layout {
            width: 188mm;
            height: 107mm;
            display: grid;
            grid-template-columns: 25mm 43.5mm 56mm 63.5mm;
            grid-template-rows: 20mm 22mm 18mm 21mm 25mm;
            margin-left: 28mm;
            margin-right: 26mm;
            margin-top: 2.5mm;
            /* border: 1px solid #000; */
            box-sizing: border-box;
        }
        .main-table {
            width: 188mm;
            height: 107mm;
            margin-left: 28mm;
            margin-right: 26mm;
            margin-top: 2.5mm;
        }
        .sidebar {
            width: 25mm;
            min-width: 25mm;
            max-width: 25mm;
            overflow: hidden; 
            /* line-height: 1.2;  */
            vertical-align: middle; 
            text-align: left;
        }
        .sidebar-logo {
            width: 11mm;
            transform: rotate(-90deg);
            position: relative;
            bottom: -42mm;
            left: 7mm;
        }
        .sidebar-title {
            transform: rotate(-90deg); 
            white-space: nowrap; 
            text-transform: uppercase; 
            font-size: 13; 
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;

            position: relative;
            left: 0.5mm;
            bottom: -9mm;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            /* font-size: 12px;a */
        }
        td, th {
            border: 2px solid #000;
            /* padding: 4px 6px; */
            vertical-align: top;
        }
        .margin {
            margin-top: 1.5mm;
            margin-left: 1.5mm;
            font-size: 10;
            font-weight: bold;
        }
        .content {
            margin-top: 1.5mm;
            margin-left: 1.5mm;
            font-size: 10;
            font-weight: normal;
        }
        .center { text-align: center; }
        .v-center { vertical-align: middle; }
        .v-top { vertical-align: top; }
        .no-border td, .no-border th { border: none; }
    </style>
</head>
<body>
<div class="">
    <div style="">
        <table class="main-table">
            <colgroup>
                <col style="width: 25mm;">
                <col style="width: 43.5mm;">
                <col style="width: 56mm;">
                <col style="width: 63.5mm;">
            </colgroup>
            <tr>
                <td rowspan="5" class="sidebar">
                    <div>
                        <img src="{{ public_path('templates/images/kop/kra-bw.jpg') }}" alt="Logo" class="sidebar-logo">
                    </div>
                    <div class="sidebar-title">
                        kabupaten karanganyar
                        <br>
                        kartu surat masuk
                    </div>
                </td>
                <td style="height: 19.5mm; width: 43.5mm;">
                    <div class="margin">
                        Indeks : <br>
                    </div>
                    <div class="content">{{ $data->KODEOPR }} <br>{{ $data->TGLTERUS }}</div>
                </td>
                <td style="height: 19.5mm; width: 56mm;">
                    <div class="margin">
                        Kode : <br>
                    </div>
                    <div class="content">{{ $data->KLAS3 }}</div>
                </td>
                <td style="height: 19.5mm; width: 63.5mm;">
                    <div class="margin">
                        Nomor Urut : <br>
                    </div>
                    <div class="content">{{ $data->NOAGENDA }}.{{ $data->KODEUP }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="height: 21.5mm; width: 163mm;">
                    <div class="margin">
                        Isi Ringkas : {!! !empty($data->nosppd) ? '<span style="margin-left: 50mm;">No. SPD : <span style="font-weight: normal;">'.$data->nosppd.'</span></span>' : '' !!}
                    </div>
                    <div class="content">{{ $data->ISI }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="height: 17.5mm; width: 163mm;">
                    <div class="margin">
                        Kepada : <br>
                    </div>
                    <div class="content">{{ $data->drkpd }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <table style="width: 162mm; height: 21mm;">
                        <colgroup>
                            <col style="width: 44mm;">
                            <col style="width: 50mm;">
                            <col style="width: 68mm;">
                        </colgroup>
                        <tr>
                            <td style="height: 20.5mm; width: 69mm;">
                                <div class="margin">
                                    Pengolah : <br>
                                </div>
                                <div class="content">{{ $data->NAMAUP }}</div>
                            </td>
                            <td style="height: 20.5mm; width: 41mm;">
                                <div class="margin">
                                    Tgl Surat : <br>
                                </div>
                                <div class="content">{{ $data->TGLSURAT }}</div>
                            </td>
                            <td style="height: 20.5mm; width: 51.5mm;">
                                <div class="margin">
                                    Lampiran : <br>
                                </div>
                                <div class="content">{{ $data->lampiran }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="height: 24.5mm; width: 163mm;">
                    <div class="margin">
                        Catatan : <br>
                    </div>
                    <div class="content">{{ $data->CATATAN }}</div>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>