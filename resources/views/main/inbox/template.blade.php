<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lembar Disposisi</title>
    <style>
        @page {
            size: A4 portrait;
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
<div class="half-page">
    <div style="margin-bottom: 35px;">
        <table class="no-border" style="margin-bottom: 10px;">
            <tr>
                <td style="text-align: center;">
                    <img src="{{ public_path('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" alt="Logo" width="60">
                </td>
                <td style="vertical-align: middle;">
                    <label style="font-weight: bold; font-size: 14px;"><b>PEMERINTAH KABUPATEN KARANGANYAR</b></label>
                    <br>
                    <label>Bagian Umum</label>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td colspan="3">
                    <div class="title">LEMBAR DISPOSISI</div>
                </td>
                <td class="center" style="border: 1px solid #000;" rowspan="3">
                    <div><b>Surat Masuk</b></div>
                    <div><b>{{ $data->NOURUT ?? '' }}</b></div>
                    <div>{{ $data->TGLSURAT ? date_format(date_create($data->TGLSURAT), 'd-m-Y') : '' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3">BERKAS : </td>
            </tr>
            <tr>
                <td colspan="2">INDEX : {{ $data->drkpd ?? '' }}</td>
                <td>KODE : {{ $data->KLAS3 ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="4" style="height: 70px;">ISI : {{ $data->ISI ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="3">DARI : {{ $data->drkpd ?? '' }}</td>
                <td rowspan="2" style="text-align: center;">Lampiran</td>
            </tr>
            <tr>
                <td colspan="3">ALAMAT : {{ $data->NAMAKOTA ?? '' }}</td>
            </tr>
            <tr>
                <td style="width: 25%;">TGL. SURAT : {{ $data->TGLSURAT ? date_format(date_create($data->TGLSURAT), 'd-m-Y') : '' }}</td>
                <td style="width: 25%;">TERIMA : {{ $data->TGLTERIMA ? date_format(date_create($data->TGLTERIMA), 'd-m-Y') : '' }}</td>
                <td style="width: 35%;">NO. SURAT : {{ $data->NOSURAT ?? '' }}</td>
                <td style="width: 15%;"></td>
            </tr>
        </table>

        <table>
            <tr>
                <td colspan="3" style="text-align: center; border-top: 0px;"><b>DISPOSISI</b></td>
            </tr>
            <tr>
                <td style="border-right: 0px; height: 80px; width: {{ 100/3 }}%;">
                    <div class="disposisi-list">( &nbsp; ) Kasubbag Tata Usaha<br><span style="margin-left: 21px;">Pimpinan, Staf Ahli dan</span><br><span style="margin-left: 21px;">Kepegawaian</span></div>
                    {{-- <div class="disposisi-list">( &nbsp; ) Pimpinan Staf Ahli dan<br><span style="margin-left: 21px;">Kepegawaian</span></div> --}}
                    <div class="disposisi-list">( &nbsp; ) Kasubbag Keuangan</div>
                </td>
                <td style="border-left: 0px; border-right: 0px; width: {{ 100/3 }}%;">
                    <div class="disposisi-list">( &nbsp; ) Pengurus Barang</div>
                    <div class="disposisi-list">( &nbsp; ) Kasubbag Rumah Tangga dan<br><span style="margin-left: 21px;">Perlengkapan</span></div>
                    <div class="disposisi-list">( &nbsp; ) </div>
                </td>
                <td style="border-left: 0px; width: {{ 100/3 }}%;">
                    <div class="disposisi-list">( &nbsp; ) </div>
                    <div class="disposisi-list">( &nbsp; ) </div>
                    <div class="disposisi-list">( &nbsp; ) </div>
                    <div class="disposisi-list">( &nbsp; ) </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center"><b>DISPOSISI</b></td>
            </tr>
            <tr>
                <td colspan="3" style="height: 80px;">{{ $data->CATATAN }}</td>
            </tr>
        </table>
    </div>

    {{-- <div class="disposisi-box"></div> --}}

    <div>
        <table class="no-border" style="margin-bottom: 10px;">
            <tr>
                <td style="text-align: center;">
                    <img src="{{ public_path('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" alt="Logo" width="60">
                </td>
                <td style="vertical-align: middle;">
                    <label style="font-weight: bold; font-size: 14px;"><b>PEMERINTAH KABUPATEN KARANGANYAR</b></label>
                    <br>
                    <label>Bagian Umum</label>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td colspan="3">
                    <div class="title">LEMBAR DISPOSISI</div>
                </td>
                <td class="center" style="border: 1px solid #000;" rowspan="3">
                    <div><b>Surat Masuk</b></div>
                    <div><b>{{ $data->NOURUT ?? '' }}</b></div>
                    <div>{{ $data->TGLSURAT ? date_format(date_create($data->TGLSURAT), 'd-m-Y') : '' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3">BERKAS : </td>
            </tr>
            <tr>
                {{-- <td colspan="2">INDEX : {{ $data->poenx ? explode(' ', $data->poenx)[0] : '' }}</td> --}}
                <td colspan="2">INDEX : {{ $data->drkpd ?? '' }}</td>
                <td>KODE : {{ $data->KLAS3 ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="4" style="height: 70px;">ISI : {{ $data->ISI ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="4">DARI : {{ $data->drkpd ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="4">ALAMAT : {{ $data->NAMAKOTA ?? '' }}</td>
            </tr>
            <tr>
                <td style="width: 30%;">TGL. SURAT : {{ $data->TGLSURAT ? date_format(date_create($data->TGLSURAT), 'd-m-Y') : '' }}</td>
                <td style="width: 55%;" colspan="2">NO. SURAT : {{ $data->NOSURAT ?? '' }}</td>
                <td style="width: 15%;">Lampiran</td>
            </tr>

            <tr>
                <td colspan="4">UNIT PENGELOLA : {{ $data->NAMAUP ?? '' }}</td>
            </tr>

            <tr>
                <td colspan="3" style="height: 80px;">CATATAN : <br>{{ $data->CATATAN ?? '' }}</td>
                <td>Tanda Terima<br></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>