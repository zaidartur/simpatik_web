<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Surat Masuk</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" />
    
    <style>
        @page {
            size: legal portrait;
            margin: 1cm;
            margin-bottom: 0;
            /* size: 243mm 114.5mm; */
            /* margin: 0mm; */
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
            /* width: 188mm; */
            /* height: 107mm; */
            /* margin-left: 28mm; */
            /* margin-right: 26mm; */
            /* margin-top: 2.5mm; */
        }
        .sidebar {
            width: 15%;
            overflow: hidden; 
            /* line-height: 1.2;  */
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
            white-space: normal;
        }
        td {
            word-break: break-word;
            overflow-wrap: break-word;
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
                <td rowspan="6" class="sidebar">
                    <div>
                        <img src="{{ public_path('templates/images/kop/kra-bw.jpg') }}" alt="Logo" class="sidebar-logo">
                    </div>
                    <div class="sidebar-title">
                        kabupaten karanganyar
                        <br>
                        kartu surat masuk
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="margin">
                        Indeks : <br>
                    </div>
                    <div class="content">{{ $data->creator->nama_lengkap }} <br>{{ \Carbon\Carbon::parse($data->tgl_diteruskan)->format('d/m/Y') }}</div>
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
                    <div class="content" style="font-size: 20pt;">{{ $data->no_agenda }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="height: 17.5mm;">
                    <div class="margin">
                        Isi Ringkas :
                    </div>
                    <div class="content">{{ $data->isi_surat }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="">
                    <div class="margin">
                        Dari : <br>
                    </div>
                    <div class="content">{{ $data->dari }}</div>
                </td>
            </tr>
            <tr>
                <td style="height: 15mm; border-bottom: none;">
                    <div class="margin">
                        Tgl Surat : <br>
                    </div>
                    <div class="content">{{ \Carbon\Carbon::parse($data->tgl_surat)->format('d/m/Y') }}</div>
                </td>
                <td style="height: 15mm; border-bottom: none;">
                    <div class="margin">
                        Nomor Surat : <br>
                    </div>
                    <div class="content">{{ $data->no_surat }}</div>
                </td>
                <td style="height: 15mm;">
                    <div class="margin">
                        Lampiran : <br>
                    </div>
                    <div class="content">{{ $data->lampiran }}</div>
                </td>
            </tr>
            <tr>
                <td style="height: 15mm;">
                    <div class="margin">
                        Pengolah : <br>
                    </div>
                    <div class="content">{{ $data->level->role }}</div>
                </td>
                <td style="height: 15mm;">
                    <div class="margin">
                        Tgl Diteruskan : <br>
                    </div>
                    <div class="content">{{ \Carbon\Carbon::parse($data->tgl_diteruskan)->format('d/m/Y') }}</div>
                </td>
                <td rowspan="2">
                    <div class="margin">
                        Tanda Terima : <br>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="height: 15mm;">
                    <div class="margin">
                        Catatan : <br>
                    </div>
                    <div class="content">{{ $data->keterangan }}</div>
                </td>
            </tr>

            {{-- <tr>
                <td colspan="3">
                    <table style="width: 162mm; height: 45mm;">
                        <colgroup>
                            <col style="width: 44mm;">
                            <col style="width: 50mm;">
                            <col style="width: 68mm;">
                        </colgroup>
                        <tr>
                            <td style="height: 15mm; width: 69mm;">
                                <div class="margin">
                                    Tgl Surat : <br>
                                </div>
                                <div class="content">{{ $data->TGLSURAT }}</div>
                            </td>
                            <td style="height: 15mm; width: 41mm;">
                                <div class="margin">
                                    Nomor Surat : <br>
                                </div>
                                <div class="content">{{ $data->NOSURAT }}</div>
                            </td>
                            <td style="height: 15mm; width: auto;">
                                <div class="margin">
                                    Lampiran : <br>
                                </div>
                                <div class="content">{{ $data->lampiran }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 15mm; width: 69mm;">
                                <div class="margin">
                                    Pengolah : <br>
                                </div>
                                <div class="content">{{ $data->NAMAUP }}</div>
                            </td>
                            <td style="height: 15mm; width: 41mm;">
                                <div class="margin">
                                    Tgl Diteruskan : <br>
                                </div>
                                <div class="content">{{ $data->TGLTERUS }}</div>
                            </td>
                            <td rowspan="2" style="width: auto;">
                                <div class="margin">
                                    Tanda Terima : <br>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="height: 15mm; width: auto;">
                                <div class="margin">
                                    Catatan : <br>
                                </div>
                                <div class="content">{{ $data->CATATAN }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr> --}}
            {{-- <tr>
                <td colspan="3" style="height: 24.5mm; width: 163mm;">
                    <div class="margin">
                        Catatan : <br>
                    </div>
                    <div class="content">{{ $data->CATATAN }}</div>
                </td>
            </tr> --}}
        </table>
    </div>
    @if($i < 2)
    <hr style="border-top: 1px dashed black; color: transparent; margin-bottom: 3mm; margin-left: -10mm; margin-right: -10mm;">
    @endif

    @endfor
</div>
</body>
</html>