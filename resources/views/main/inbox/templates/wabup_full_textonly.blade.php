<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lembar Disposisi Wakil Bupati</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" />
    
    <style>
        @page {
            /* size: A4 portrait; */
            /* margin: 1cm; */
            size: 216mm 330mm;
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
        <table class="no-border" style="">
            <tr>
                <td style="text-align: center; height: 52mm; vertical-align: middle; margin-top: 12mm;">
                    <div style="height: 35mm">
                        {{-- <img src="{{ public_path('templates/images/kop/garuda_big.png') }}" alt="Logo" style="height: 30mm;"> --}}
                        <div style="height: 30mm; width: auto;">&nbsp;</div>
                    </div>
                    <label style="font-weight: bold; font-size: 15pt; color: transparent;">WAKIL BUPATI KARANGANYAR</label>
                </td>
            </tr>
        </table>

        <table class="no-border" style="width: 167.5mm; margin-left: 21mm; margin-right: 27mm;">
            <tr>
                <td colspan="2" style="height: 12mm; color: transparent;" class="center v-center">
                    LEMBAR DISPOSISI
                </td>
            </tr>
            <tr>
                <td style="height: 30mm; width: 75mm;" class="v-top">
                    <div style="height: 15mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 20mm; vertical-align: top; margin-top: 0.75mm; color: transparent;">Surat dari</label>
                        <label style="display: inline-block; vertical-align: top; width: 3mm; margin-top: 0.75mm; color: transparent;">:</label>
                        <label style="display: inline-block; vertical-align: top; width: 48mm; margin-top: 0.75mm;">{{ $data->drkpd }}</label>
                    </div>
                    <div style="height: 7mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 20mm; vertical-align: bottom; color: transparent;">No. Surat</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 3mm; color: transparent;">:</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 48mm;">{{ $data->NOSURAT }}</label>
                    </div>
                    <div style="height: 8mm; margin-left: 3mm;">
                        <label style="display: inline-block; width: 20mm; vertical-align: bottom; color: transparent;">Tgl. Surat</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 3mm; color: transparent;">:</label>
                        <label style="display: inline-block; vertical-align: bottom; width: 48mm;">{{ date_format(date_create($data->TGLSURAT), 'd-m-Y') }}</label>
                    </div>
                </td>
                <td style="width: 92.5mm;" class="v-top">
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
                    <label style="display: inline-block; width: 144mm; vertical-align: bottom; margin-left: 3mm;">{{ $data->ISI }}</label>
                </td>
            </tr>
            <tr>
                <td style="height: 44mm; width: 75mm; color: transparent;" class="v-top">
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
                <td style="width: 92.5mm; color: transparent;" class="v-top">
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
                <td style="height: 51mm; border-right: none;" class="v-top">
                    <label style="display: inline-block; width: 20mm; vertical-align: middle; margin-left: 3mm; margin-top: 5mm; color: transparent;">Catatan</label>
                    <label style="color: transparent;">: </label>
                    <label style="display: inline-block; width: auto; vertical-align: middle; margin-left: 3mm; margin-top: 5mm;">{!! $data->DisposisiWakil ? '&quot;'. $data->DisposisiWakil .'&quot;' : '' !!}</label>
                </td>
                <td style="height: 51mm; border-left: none; color: transparent;" class="v-top">
                    <div style="height: 8mm; margin-left: 5mm; margin-top: 5mm;">
                        <label style="display: inline-block; width: 30mm; vertical-align: bottom;">{{ $sign ? $sign->jabatan : 'Wakil Bupati,' }}</label>
                    </div>
                    <div style="height: 33mm; margin-left: 5mm;">&nbsp;</div>
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
        <div style="height: 49mm;">&nbsp;</div>
        <div style="margin-left: 10mm; margin-right: 10mm;" class="center">
            <label style="font-size: 10pt; color: transparent;">
                Jalan Lawu Nomor 385-B, Karanganyar, Jawa Tengah 57712<br>
                Telepon (0271) 495039, Faksimile (0271) 495590, Laman www.karanganyarkab.go.id, Pos-el bupati@karanganyarkab.go.id
            </label>
        </div>
    </div>
</div>
</body>
</html>