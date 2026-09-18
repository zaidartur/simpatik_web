<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Perintah Perjalanan Dinas (SPPD) - {{ $sppd->no_spd }}</title>
    <style>
        @page {
            size: legal portrait;
            margin: 1.2cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-sppd {
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #000;
        }
        .table-sppd td, .table-sppd th {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
        }
        .kop-table td {
            border: none;
            padding: 0;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .text-right { text-align: right; }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <!-- LEMBAR DEPAN (HALAMAN 1) -->
    <div>
        <table class="kop-table" style="width: 100%; margin-bottom: 5px;">
            <tr>
                <td style="width: 15%; text-align: center; vertical-align: middle;">
                    <img src="{{ public_path('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" width="65" alt="Logo">
                </td>
                <td style="width: 85%; text-align: center; vertical-align: middle;">
                    <div style="font-size: 13px; font-weight: bold; letter-spacing: 0.5px;">PEMERINTAH KABUPATEN KARANGANYAR</div>
                    <div style="font-size: 16px; font-weight: bold; letter-spacing: 0.5px; margin: 2px 0;">SEKRETARIAT DAERAH</div>
                    <div style="font-size: 9.5px; font-weight: normal;">Jl. Lawu No. 385 Telp. (0271) 495039, 495146 Karanganyar 57712</div>
                    <div style="font-size: 9px; font-weight: normal;">Website: www.karanganyarkab.go.id | Email: setda@karanganyarkab.go.id</div>
                </td>
            </tr>
        </table>
        <div style="border-bottom: 2px solid #000; margin-top: 4px; margin-bottom: 2px;"></div>
        <div style="border-bottom: 0.8px solid #000; margin-bottom: 12px;"></div>

        <table style="width: 100%; font-size: 10px; margin-bottom: 8px;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 15%;">Lembar Ke</td>
                <td style="width: 25%;">: 1 (Satu)</td>
            </tr>
            <tr>
                <td></td>
                <td>Kode No</td>
                <td>: 090 / SPPD</td>
            </tr>
            <tr>
                <td></td>
                <td>Nomor</td>
                <td>: {{ $sppd->no_spd }}</td>
            </tr>
        </table>

        <div class="center" style="margin-bottom: 10px;">
            <div style="font-size: 13px; font-weight: bold; text-decoration: underline; letter-spacing: 1px;">SURAT PERINTAH PERJALANAN DINAS</div>
            <div style="font-size: 11px; font-weight: bold; margin-top: 2px;">( S P P D )</div>
        </div>

        <table class="table-sppd">
            <tr>
                <td class="center bold" style="width: 5%;">1.</td>
                <td style="width: 45%;">Pejabat Berwenang yang memberi perintah</td>
                <td class="bold" style="width: 50%;">{{ $sign->level ?? 'Sekretaris Daerah Kabupaten Karanganyar' }}</td>
            </tr>
            <tr>
                <td class="center bold">2.</td>
                <td>Nama Pegawai yang diperintahkan</td>
                <td class="bold">{{ $sppd->nama }}</td>
            </tr>
            <tr>
                <td class="center bold">3.</td>
                <td>
                    a. Pangkat dan Golongan ruang gaji<br>
                    b. Jabatan / Instansi<br>
                    c. Tingkat Biaya Perjalanan Dinas
                </td>
                <td>
                    a. -<br>
                    b. {{ $sppd->jabatan ?? '-' }}<br>
                    c. Tingkat Standar
                </td>
            </tr>
            <tr>
                <td class="center bold">4.</td>
                <td>Maksud Perjalanan Dinas</td>
                <td>{{ $sppd->tujuan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="center bold">5.</td>
                <td>Alat angkut yang dipergunakan</td>
                <td>{{ $sppd->kendaraan ?? 'Kendaraan Dinas' }}</td>
            </tr>
            <tr>
                <td class="center bold">6.</td>
                <td>
                    a. Tempat berangkat<br>
                    b. Tempat tujuan
                </td>
                <td>
                    a. Karanganyar<br>
                    b. {{ $sppd->tujuan ?? '-' }}
                </td>
            </tr>
            <tr>
                <td class="center bold">7.</td>
                <td>
                    a. Lamanya Perjalanan Dinas<br>
                    b. Tanggal berangkat<br>
                    c. Tanggal harus kembali
                </td>
                <td>
                    a. 1 (satu) hari<br>
                    b. {{ \Carbon\Carbon::parse($sppd->tgl_berangkat)->isoFormat('D MMMM Y') }}<br>
                    c. {{ \Carbon\Carbon::parse($sppd->tgl_berangkat)->isoFormat('D MMMM Y') }}
                </td>
            </tr>
            <tr>
                <td class="center bold">8.</td>
                <td>Pengikut : Nama / NIP</td>
                <td>-</td>
            </tr>
            <tr>
                <td class="center bold">9.</td>
                <td>
                    Pembebanan Anggaran<br>
                    a. Instansi<br>
                    b. Mata Anggaran
                </td>
                <td>
                    <br>
                    a. Sekretariat Daerah Kabupaten Karanganyar<br>
                    b. DPA Sekretariat Daerah
                </td>
            </tr>
            <tr>
                <td class="center bold">10.</td>
                <td>Keterangan lain-lain</td>
                <td>-</td>
            </tr>
        </table>

        <!-- Tanda Tangan -->
        <table style="width: 100%; margin-top: 15px;">
            <tr>
                <td style="width: 55%;"></td>
                <td style="width: 45%; font-size: 11px;">
                    Dikeluarkan di : Karanganyar<br>
                    Pada tanggal : {{ \Carbon\Carbon::parse($sppd->tgl_surat)->isoFormat('D MMMM Y') }}<br>
                    <div style="margin-top: 5px; font-weight: bold;">
                        An. BUPATI KARANGANYAR<br>
                        {{ $sign->jabatan ?? 'Sekretaris Daerah' }}
                    </div>
                    <div style="height: 60px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">
                        {{ $sign->nama ?? 'Drs. ZULFIKAR HADIDH, M.Si' }}
                    </div>
                    <div>{{ $sign->pangkat ?? 'Pembina Utama Madya' }}</div>
                    <div>NIP. {{ $sign->nip ?? '19680512 199303 1 005' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- LEMBAR BELAKANG (VISUM SPPD) -->
    <div class="page-break">
        <table style="width: 100%; font-size: 10px; margin-bottom: 5px;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%; text-align: right;">SPPD No : <strong>{{ $sppd->no_spd }}</strong></td>
            </tr>
        </table>

        <table class="table-sppd" style="font-size: 9.5px;">
            <tr>
                <td style="width: 50%; border-right: 1px solid #000;">
                    &nbsp;
                </td>
                <td style="width: 50%;">
                    I. Berangkat dari : Karanganyar<br>
                    &nbsp;&nbsp;&nbsp;Ke : {{ $sppd->tujuan }}<br>
                    &nbsp;&nbsp;&nbsp;Pada tanggal : {{ \Carbon\Carbon::parse($sppd->tgl_berangkat)->isoFormat('D MMMM Y') }}<br>
                    <div style="text-align: center; margin-top: 8px;">
                        An. BUPATI KARANGANYAR<br>
                        {{ $sign->jabatan ?? 'Sekretaris Daerah' }}
                        <div style="height: 45px;"></div>
                        <strong><u>{{ $sign->nama ?? 'Drs. ZULFIKAR HADIDH, M.Si' }}</u></strong><br>
                        NIP. {{ $sign->nip ?? '19680512 199303 1 005' }}
                    </div>
                </td>
            </tr>
            <tr>
                <td style="height: 95px;">
                    II. Tiba di : {{ $sppd->tujuan }}<br>
                    &nbsp;&nbsp;&nbsp;Pada tanggal : {{ \Carbon\Carbon::parse($sppd->tgl_berangkat)->isoFormat('D MMMM Y') }}<br>
                    &nbsp;&nbsp;&nbsp;Kepala : <br>
                    <div style="height: 35px;"></div>
                    &nbsp;&nbsp;&nbsp;(............................................................)<br>
                    &nbsp;&nbsp;&nbsp;NIP.
                </td>
                <td>
                    Berangkat dari : {{ $sppd->tujuan }}<br>
                    Ke : Karanganyar<br>
                    Pada tanggal : {{ \Carbon\Carbon::parse($sppd->tgl_berangkat)->isoFormat('D MMMM Y') }}<br>
                    Kepala : <br>
                    <div style="height: 35px;"></div>
                    (............................................................)<br>
                    NIP.
                </td>
            </tr>
            <tr>
                <td style="height: 95px;">
                    III. Tiba di : <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;Pada tanggal : <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;Kepala : <br>
                    <div style="height: 35px;"></div>
                    &nbsp;&nbsp;&nbsp;&nbsp;(............................................................)<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;NIP.
                </td>
                <td>
                    Berangkat dari : <br>
                    Ke : <br>
                    Pada tanggal : <br>
                    Kepala : <br>
                    <div style="height: 35px;"></div>
                    (............................................................)<br>
                    NIP.
                </td>
            </tr>
            <tr>
                <td style="height: 95px;">
                    IV. Tiba di : Karanganyar<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;Pada tanggal : {{ \Carbon\Carbon::parse($sppd->tgl_berangkat)->isoFormat('D MMMM Y') }}<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;An. BUPATI KARANGANYAR<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;{{ $sign->jabatan ?? 'Sekretaris Daerah' }}
                    <div style="height: 35px;"></div>
                    &nbsp;&nbsp;&nbsp;&nbsp;<strong><u>{{ $sign->nama ?? 'Drs. ZULFIKAR HADIDH, M.Si' }}</u></strong><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;NIP. {{ $sign->nip ?? '19680512 199303 1 005' }}
                </td>
                <td>
                    Telah diperiksa dengan keterangan bahwa perjalanan tersebut di atas benar dilakukan atas perintahnya dan semata-mata untuk kepentingan jabatan dalam waktu yang sesingkat-singkatnya.<br>
                    <div style="text-align: center; margin-top: 5px;">
                        Pejabat yang berwenang / Pengguna Anggaran
                        <div style="height: 30px;"></div>
                        <strong><u>{{ $sign->nama ?? 'Drs. ZULFIKAR HADIDH, M.Si' }}</u></strong><br>
                        NIP. {{ $sign->nip ?? '19680512 199303 1 005' }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    V. CATATAN LAIN-LAIN :
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 8.5px; line-height: 1.2;">
                    VI. PERHATIAN :<br>
                    Pejabat yang berwenang menerbitkan SPPD, pegawai yang melakukan perjalanan dinas, para pejabat yang mengesahkan tanggal berangkat / tiba serta bendaharawan bertanggung jawab berdasarkan peraturan-peraturan Keuangan Negara apabila Negara mendapat rugi akibat kesalahan, kelalaian dan kealpaannya.
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
