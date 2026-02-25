<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Surat Keluar</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" />
    
    <style>
        @page {
            size: legal portrait;
            margin: 1cm;
            margin-bottom: 0;
            /* size: 243mm 114.5mm;
            margin: 0mm; */
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
            width: 100%;
            height: auto;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .sidebar {
            width: 15%;
            overflow: hidden; 
            vertical-align: middle; 
            text-align: left;
        }
        .sidebar-logo {
            width: 12mm;
            transform: rotate(-90deg);
            position: relative;
            bottom: -47mm;
            left: 7mm;
        }
        .sidebar-title {
            transform: rotate(-90deg); 
            transform-origin: center;
            white-space: nowrap; 
            text-transform: uppercase; 
            font-size: 13; 
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            margin-left: -2mm;
            margin-top: 8mm;
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
            font-size: 11pt;
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
    @for ($i=0; $i<3; $i++)
    <div style="margin-bottom: 3mm;">
        <table class="main-table">
            <tr>
                <td rowspan="5" class="sidebar">
                    <div>
                        <img src="{{ public_path('templates/images/kop/kra-bw.jpg') }}" alt="Logo" class="sidebar-logo">
                    </div>
                    <div class="sidebar-title">
                        kabupaten karanganyar
                        <br>
                        kartu surat keluar
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="margin">
                        Indeks : <br>
                    </div>
                    <div class="content">{{ $data->creator->nama_lengkap }} <br>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</div>
                </td>
                <td style="width: 35%;">
                    <div class="margin">
                        Kode : <br>
                    </div>
                    <div class="content">{{ $data->klasifikasi->klas3 }}</div>
                </td>
                <td style="width: 25%;">
                    <div class="margin">
                        Nomor Urut : <br>
                    </div>
                    <div class="content" style="font-size: 20pt;">{{ $data->no_agenda }}.{{ $data->pengolah->kode }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="height: 21.5mm; width: 163mm;">
                    <div class="margin">
                        Isi Ringkas : {!! !empty($data->id_spd) ? '<span style="margin-left: 50mm;">No. SPD : <span style="font-weight: normal;">'.$data->spd->no_spd.'</span></span>' : '' !!}
                    </div>
                    <div class="content">{{ $data->isi_surat }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="height: 17.5mm; width: 163mm;">
                    <div class="margin">
                        Kepada : <br>
                    </div>
                    <div class="content">{{ $data->kepada }}</div>
                </td>
            </tr>
            <tr>
                <td style="height: 20.5mm; width: 69mm;">
                    <div class="margin">
                        Pengolah : <br>
                    </div>
                    <div class="content">{{ empty($data->id_unit) ? $data->unit : $data->pengolah->nama_unit }}</div>
                </td>
                <td style="height: 20.5mm; width: 41mm;">
                    <div class="margin">
                        Tgl Surat : <br>
                    </div>
                    <div class="content">{{ \Carbon\Carbon::parse($data->tgl_surat)->format('d/m/Y') }}</div>
                </td>
                <td style="height: 20.5mm; width: 51.5mm;">
                    <div class="margin">
                        Lampiran : <br>
                    </div>
                    <div class="content">{{ $data->lampiran }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="height: 24.5mm; width: 163mm;">
                    <div class="margin">
                        Catatan : <br>
                    </div>
                    <div class="content">{{ $data->keterangan }}</div>
                </td>
            </tr>
        </table>

        {{-- <div style="height: 16mm;">&nbsp;</div> --}}
        {{-- <table class="no-border" style="width: 167.5mm; margin-left: 20mm; margin-right: 28mm; margin-top: 16mm;">
            <tr>
                <td style="text-align: center; height: 26.5mm; width: 20mm; vertical-align: top;">
                    <img src="{{ public_path('templates/images/kop/karanganyar.png') }}" alt="Logo" style="height: 23mm;">
                </td>
                <td class="center v-top" style="height: 26.5mm;">
                    <div style="font-size: 12pt; text-transform: uppercase;">kabupaten karanganyar</div>
                    <div style="font-size: 16pt; text-transform: uppercase; font-weight: bold;">kartu surat keluar</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="height: 2mm; width: 167.5mm; vertical-align: bottom;">
                    <hr style="width: 171mm; height: 1px; border: none; background-color: #000; margin: 0px; padding: 0px;">
                </td>
            </tr>
        </table>

        <div style="height: 4.8mm;">&nbsp;</div>

        <table style="width: 167.5mm; margin-left: 20mm; margin-right: 28mm;">
            <tr>
                <td colspan="2" style="height: 11.7mm;" class="center v-center">
                    LEMBAR DISPOSISI
                </td>
            </tr>
            <tr>
                <td style="height: 29.5mm; width: 72.5mm;" class="v-top">
                    <div style="height: 14.5mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 20mm; vertical-align: top; margin-top: 0.75mm;">Surat dari</label>
                        <label style="display: inline-block; vertical-align: top; width: 3mm; margin-top: 0.75mm;">:</label>
                        <label style="display: inline-block; vertical-align: top; width: 46mm; margin-top: 0.75mm;">{{ $data->drkpd }}</label>
                    </div>
                    <div style="height: 7mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 20mm; vertical-align: bottom;">No. Surat</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 3mm;">:</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 46mm;">{{ $data->NOSURAT }}</label>
                    </div>
                    <div style="height: 8mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 20mm; vertical-align: bottom;">Tgl. Surat</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 3mm;">:</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 46mm;">{{ date_format(date_create($data->TGLSURAT), 'd-m-Y') }}</label>
                    </div>
                </td>
                <td style="width: 95mm;" class="v-top">
                    <div style="height: 7.5mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 25mm; vertical-align: bottom;">Diterima Tgl.</label>
                        <label>:</label>
                        <label>{{ date_format(date_create($data->TGLTERIMA), 'd-m-Y') }}</label>
                    </div>
                    <div style="height: 7.5mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 25mm; vertical-align: bottom;">No. Agenda</label>
                        <label>:</label>
                        <label>{{ $data->NOAGENDA }}</label>
                    </div>
                    <div style="height: 7.5mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 25mm; vertical-align: bottom;">Sifat</label>
                        <label>:</label>
                        <label></label>
                    </div>
                    <div style="height: 7.5mm; margin-left: 3mm;">
                        <input type="checkbox" name="sangat" id="sangat" style="display: inline-block; vertical-align: bottom;"> 
                        <label style="display: inline-block; vertical-align: bottom;">Sangat Segera &nbsp;&nbsp;</label>

                        <input type="checkbox" name="segera" id="segera" style="display: inline-block; vertical-align: bottom;">
                        <label style="display: inline-block; vertical-align: bottom;">Segera &nbsp;&nbsp;</label>

                        <input type="checkbox" name="rahasia" id="rahasia" style="display: inline-block; vertical-align: bottom;">
                        <label style="display: inline-block; vertical-align: bottom;">Rahasia &nbsp;&nbsp;</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="height: 34mm;" colspan="2">
                    <label style="display: inline-block; width: 20mm; vertical-align: bottom; margin-left: 3mm;">Hal</label>
                    <label>: </label>
                    <label style="display: inline-block; width: auto; vertical-align: bottom; margin-left: 3mm;">{{ $data->ISI }}</label>
                </td>
            </tr>
            <tr>
                <td style="height: 44mm; width: 77.5mm;" class="v-top">
                    <div style="height: 9mm; margin-left: 3mm;">
                        <label>Diteruskan Kepada Sdr.:</label>
                    </div>
                    <div style="height: 8mm; margin-left: 3mm;">
                        <input type="checkbox" style="display: inline-block; vertical-align: bottom;">
                        <label style="display: inline-block; vertical-align: bottom;">
                            ..................................................
                        </label>
                    </div>
                    <div style="height: 8mm; margin-left: 3mm;">
                        <input type="checkbox" style="display: inline-block; vertical-align: bottom;">
                        <label style="display: inline-block; vertical-align: bottom;">
                            ..................................................
                        </label>
                    </div>
                    <div style="height: 8mm; margin-left: 3mm;">
                        <input type="checkbox" style="display: inline-block; vertical-align: bottom;">
                        <label style="display: inline-block; vertical-align: bottom;">
                            ..................................................
                        </label>
                    </div>
                    <div style="height: 11mm; margin-left: 3mm;">
                        <label style="display: inline-block; vertical-align: bottom; max-width: 75mm; width: 75mm;">
                            dan seterusnya .............................
                        </label>
                    </div>
                </td>
                <td style="width: 90mm;" class="v-top">
                    <div style="height: 7mm; margin-left: 3mm;">
                        <label>Dengan hormat, harap:</label>
                    </div>
                    <div style="height: 7mm; margin-left: 3mm;">
                        <input type="checkbox" style="display: inline-block; vertical-align: bottom;"> Tanggapan dan Saran
                    </div>
                    <div style="height: 7mm; margin-left: 3mm;">
                        <input type="checkbox" style="display: inline-block; vertical-align: bottom;"> Proses Lebih Lanjut
                    </div>
                    <div style="height: 7mm; margin-left: 3mm;">
                        <input type="checkbox" style="display: inline-block; vertical-align: bottom;"> Kooridinasikan/Konfirmasikan
                    </div>
                    <div style="height: 7mm; margin-left: 3mm;">
                        <input type="checkbox" style="display: inline-block; vertical-align: bottom;">
                        <label style="display: inline-block; vertical-align: bottom; max-width: 80mm; width: 80mm;">
                            .................................................
                        </label>
                    </div>
                </td>
            </tr>
        </table> --}}
    </div>

    @if($i < 2)
    <hr style="border-top: 1px dashed black; color: transparent; margin-bottom: 3mm; margin-left: -10mm; margin-right: -10mm;">
    @endif

    @endfor
</div>
</body>
</html>