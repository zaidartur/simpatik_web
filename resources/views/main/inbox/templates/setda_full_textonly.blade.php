<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lembar Disposisi Setda</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" />
    
    <style>
        @page {
            /* size: A4 portrait; */
            /* margin: 1cm; */
            size: 215.5mm 290mm;
            margin: 0mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            /* font-size: 12px; */
            margin: 0mm;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            /* font-size: 12px;a */
        }
        td, th {
            border: 1px solid #000;
            /* padding: 4px 6px; */
            vertical-align: top;
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
        <table class="no-border" style="width: 170.5mm; margin-left: 22mm; margin-right: 23mm; margin-top: 15mm;">
            <tr>
                <td style="text-align: center; height: 31mm; width: 25mm; vertical-align: top;">
                    {{-- <img src="{{ public_path('templates/images/kop/karanganyar.png') }}" alt="Logo" style="height: 23mm;"> --}}
                    <div style="height: 23mm; width: auto;">&nbsp;</div>
                </td>
                <td class="center v-top" style="height: 31mm; color: transparent;">
                    <div style="font-size: 12pt; text-transform: uppercase;">pemerintah kabupaten karanganyar</div>
                    <div style="font-size: 16pt; text-transform: uppercase; font-weight: bold; margin-bottom: 0mm;">sekretariat daerah</div>
                    <div style="font-size: 10pt;">
                        Jalan Lawu Nomor 385-B, Karanganyar, Jawa Tengah, Kode Pos 57712<br>
                        Telepon (0271) 495039, Faksimile (0271) 495590, Laman: setda.karanganyarkab.go.id,<br>
                        Pos-el: setda@karanganyarkab.go.id
                    </div>
                </td>
            </tr>
        </table>

        {{-- <div style="height: 5mm;">&nbsp;</div> --}}

        <table class="no-border" style="width: 170.5mm; margin-left: 20mm; margin-right: 28mm;">
            <tr>
                <td colspan="2" style="height: 12mm; color: transparent;" class="center v-center">
                    LEMBAR DISPOSISI
                </td>
            </tr>
            <tr>
                <td style="height: 30mm; width: 80mm;" class="v-top">
                    <div style="height: 15mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 20mm; vertical-align: top; margin-top: 0.75mm; color: transparent;">Surat dari</label>
                        <label style="display: inline-block; vertical-align: top; width: 3mm; margin-top: 0.75mm; color: transparent;">:</label>
                        <label style="display: inline-block; vertical-align: top; width: 50mm; margin-top: 0.75mm;">{{ $data->drkpd }}</label>
                    </div>
                    <div style="height: 7mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 20mm; vertical-align: bottom; color: transparent;">No. Surat</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 3mm; color: transparent;">:</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 50mm;">{{ $data->NOSURAT }}</label>
                    </div>
                    <div style="height: 8mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 20mm; vertical-align: bottom; color: transparent;">Tgl. Surat</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 3mm; color: transparent;">:</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 50mm;">{{ date_format(date_create($data->TGLSURAT), 'd-m-Y') }}</label>
                    </div>
                </td>
                <td style="width: 91mm;" class="v-top">
                    <div style="height: 7.5mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 25mm; vertical-align: bottom; color: transparent;">Diterima Tgl.</label>
                        <label style="color: transparent;">:</label>
                        <label>{{ date_format(date_create($data->TGLTERIMA), 'd-m-Y') }}</label>
                    </div>
                    <div style="height: 7.5mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 25mm; vertical-align: bottom; color: transparent;">No. Agenda</label>
                        <label style="color: transparent;">:</label>
                        <label>{{ $data->NOAGENDA }}</label>
                    </div>
                    <div style="height: 7.5mm; margin-left: 3mm; color: transparent;">
                        <label style="display: inline-block; width: 25mm; vertical-align: bottom;">Sifat</label>
                        <label>:</label>
                        <label></label>
                    </div>
                    <div style="height: 7.5mm; margin-left: 3mm; color: transparent;">
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
                    <label style="display: inline-block; width: 20mm; vertical-align: bottom; margin-left: 3mm; color: transparent;">Hal</label>
                    <label style="color: transparent;">: </label>
                    <label style="display: inline-block; width: auto; vertical-align: bottom; margin-left: 3mm;">{{ $data->ISI }}</label>
                </td>
            </tr>
            <tr>
                <td style="height: 44mm; width: 80mm; color: transparent;" class="v-top">
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
                <td style="width: 91mm; color: transparent;" class="v-top">
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
            <tr>
                <td style="height: 80mm; border-right: none;" class="v-top">
                    <label style="display: inline-block; width: 20mm; vertical-align: middle; margin-left: 3mm; margin-top: 5mm; color: transparent;">Catatan</label>
                    <label style="color: transparent;">: </label>
                    <label style="display: inline-block; width: auto; vertical-align: middle; margin-left: 3mm; margin-top: 5mm;">{!! $data->DisposisiSekda ? '&quot;'. $data->DisposisiSekda .'&quot;' : '' !!}</label>
                </td>
                <td style="height: 80mm; border-left: none; color: transparent;" class="v-top">
                    <div style="height: 30mm;">&nbsp;</div>
                    <div style="height: 8mm; margin-left: 5mm;">
                        <label style="display: inline-block; vertical-align: bottom;">Pj. Sekretaris Daerah,</label>
                    </div>
                    <div style="height: 22mm; margin-left: 5mm;">&nbsp;</div>
                    <div style="height: 10mm; margin-left: 5mm;">
                        <label style="display: inline-block; vertical-align: bottom;">
                            {!! $sign ? ($sign->nama ? $sign->nama : '') : '' !!}
                            {!! $sign ? ($sign->pangkat_golongan ? ('<br>' . $sign->pangkat_golongan) : '') : '' !!}
                            {!! $sign ? ($sign->nip ? ('<br>NIP ' . $sign->nip) : '') : '' !!}
                        </label>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>