@extends('layouts.layout')

@section('title', 'Buat Surat Keluar')


@section('css')
    <link rel="stylesheet" href="{{ asset('templates/plugins/src/filepond/filepond.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/src/filepond/FilePondPluginImagePreview.min.css') }}">
    <link href="{{ asset('templates/plugins/src/autocomplete/css/autoComplete.02.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('templates/assets/css/light/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/light/filepond/custom-filepond.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    
    <link href="{{ asset('templates/assets/css/dark/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/dark/filepond/custom-filepond.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('templates/assets/css/light/components/accordions.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/accordions.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .autoComplete_wrapper {
            width: 100%;
        }
        .autoComplete_wrapper .form-control {
            width: 100%;
        }
    </style>
@endsection


@section('content')
<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="page-meta">
            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('outbox') }}">Surat Keluar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Buat Surat Keluar</li>
                </ol>
            </nav>
        </div>

        <form action="{{ route('outbox.store') }}" method="POST" enctype="multipart/form-data" class="is-outbox" novalidate>
            <div class="row layout-top-spacing">
                @csrf

                <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                    <div class="widget-content widget-content-area br-8">
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">Asal Surat</h5>

                                        <div class="row mb-3">
                                            <label for="darikepada" class="col-sm-3 col-form-label">Kepada</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="darikepada" name="darikepada" value="" maxlength="255" required autocomplete="off" autofocus>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="wilayah" class="col-sm-3 col-form-label">Wilayah</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="wilayah" name="wilayah" value="" maxlength="150" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="perihal" class="col-sm-3 col-form-label">Perihal Surat</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="perihal" name="perihal" value="" maxlength="100" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                    <div class="widget-content widget-content-area br-8">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">Tanggal</h5>
                                        
                                        <div class="row mb-3">
                                            <label for="berkas" class="col-sm-3 col-form-label">Nama Berkas</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="berkas" name="berkas" value="" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="tgl_surat" class="col-sm-3 col-form-label">Tanggal Surat</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" id="tgl_surat" name="tgl_surat" value="" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="row mb-3">
                                            <label for="nama" class="col-sm-3 col-form-label">&nbsp;</label>
                                            <div class="col-sm-9">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="Berkas Di Tinggal" id="ditinggal" name="ditinggal" checked>
                                                    <label class="form-check-label" for="ditinggal">Berkas Di Tinggal?</label>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">Keterangan</h5>

                                        <div class="row mb-3">
                                            <label for="naik" class="col-sm-2 col-form-label">Naik</label>
                                            <div class="col-sm-4">
                                                <input type="date" class="form-control" id="tgl_naik" name="tgl_naik" value="{{ date('Y-m-d') }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                            <label for="tgl_diteruskan" class="col-sm-2 col-form-label">Diteruskan</label>
                                            <div class="col-sm-4">
                                                <input type="date" class="form-control" id="tgl_diteruskan" name="tgl_diteruskan" value="{{ date('Y-m-d') }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="nama_up" class="col-sm-2 col-form-label">Unit Pengolah</label>
                                            <div class="col-sm-6">
                                                {{-- select nama unit pengolah beserta kodenya --}}
                                                {{-- <input type="hidden" name="kode_up" id="kode_up" value=""> --}}
                                                <input type="text" class="form-control" id="nama_up" name="nama_up" value="" autocomplete="off" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control form-control-sm" name="kode_up" id="kode_up" placeholder="Kode Unit" value="" readonly>
                                            </div>
                                        </div>

                                        {{-- <div class="row mb-3">
                                            <label for="nama" class="col-sm-2 col-form-label">No. SPPD</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="text" class="form-control bs-tooltip" id="sppd" name="sppd" value="" onclick="_sppd()" readonly style="cursor: pointer;" title="Klik untuk melihat data SPPD" aria-describedby="button-clear">
                                                    <button class="btn btn-warning" type="button" id="button-clear" onclick="_clear()">
                                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                                        Bersihkan
                                                    </button>
                                                    <div class="invalid-feedback">
                                                        Field ini wajib di isi.
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                    <div class="widget-content widget-content-area br-8">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">Klasifikasi</h5>
                                        
                                        <div class="row mb-3">
                                            <label for="klasifikasi_kode" class="col-sm-3 col-form-label">Kode Klasifikasi</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="klasifikasi_kode" name="klasifikasi_kode" value="" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="urut" class="col-sm-3 col-form-label">Nomor Urut</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="urut" name="urut" value="" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="no_surat" class="col-sm-3 col-form-label">Nomor Surat</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="no_surat" name="no_surat" value="" maxlength="20" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="sifat_surat" class="col-sm-3 col-form-label">Sifat Surat</label>
                                            <div class="col-sm-9">
                                                {{-- <input type="text" class="form-control" id="sifat_surat" name="sifat_surat" value="" required> --}}
                                                <select class="form-select" id="sifat_surat" name="sifat_surat" required>
                                                    @foreach ($sifat as $sf)
                                                        <option value="{{ $sf->id }}">{{ $sf->nama_sifat }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">Isi Surat</h5>

                                        <div class="row mb-3">
                                            <label for="isi" class="col-sm-3 col-form-label">Isi</label>
                                            <div class="col-sm-9">
                                                {{-- <input type="text" class="form-control" id="isi" name="isi" value="" maxlength="255" required> --}}
                                                <textarea name="isi" id="isi" class="form-control" cols="30" rows="5" required></textarea>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="keterangan" class="col-sm-3 col-form-label">Catatan</label>
                                            <div class="col-sm-9">
                                                <textarea name="keterangan" id="keterangan" cols="30" rows="5" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        {{-- <div class="row mb-3">
                                            <label for="ttd" class="col-sm-3 col-form-label">Ditandatangani Oleh</label>
                                            <div class="col-sm-9">
                                                <select class="form-select" id="ttd" name="ttd" required>
                                                    <option value="Sekretaris Daerah">Sekretaris Daerah</option>
                                                    <option value="Bupati">Bupati</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>

                            @can('spd')
                            <div class="col-md-12">
                                <div id="iconsAccordion" class="accordion-icons accordion">
                                    <div class="card">
                                        <div class="card-header bs-tooltip" id="tab_sppd" title="Klik untuk mengisi data SPPD">
                                            <section class="mb-0 mt-0">
                                                <div role="menu" class="collapsed" data-bs-toggle="collapse" data-bs-target="#iconAccordionOne" aria-expanded="false" aria-controls="iconAccordionOne">
                                                    <div class="accordion-icon">
                                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                                    </div>
                                                    SPPD
                                                    <div class="icons">
                                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>

                                        <div id="iconAccordionOne" class="collapse" aria-labelledby="tab_sppd" data-bs-parent="#iconsAccordion">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="row mb-3">
                                                            <label for="sppd" class="col-sm-2 col-form-label">No. SPD</label>
                                                            <div class="col-sm-10">
                                                                <div class="input-group is-sppd">
                                                                    <input type="text" class="form-control bs-tooltip" id="sppd" name="sppd" value="">
                                                                    <button class="btn btn-info" type="button" id="button_generate" onclick="generate_sppd()">
                                                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                                                        Generate Nomor
                                                                    </button>
                                                                    <button class="btn btn-warning" type="button" id="button_clear" onclick="_clear()">
                                                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                                                        Bersihkan
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" id="nama" name="nama" value="">
                                                                <div class="invalid-feedback">
                                                                    Field ini wajib di isi.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="jabatan" class="col-sm-2 col-form-label">Jabatan</label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" id="jabatan" name="jabatan" value="">
                                                                <div class="invalid-feedback">
                                                                    Field ini wajib di isi.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <div class="row mb-3">
                                                            <label for="tujuan" class="col-sm-2 col-form-label">Tujuan</label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" id="tujuan" name="tujuan" value="">
                                                                <div class="invalid-feedback">
                                                                    Field ini wajib di isi.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="kendaraan" class="col-sm-2 col-form-label">Kendaraan Dinas</label>
                                                            <div class="col-sm-10">
                                                                {{-- <input type="text" class="form-control" id="kendaraan" name="kendaraan" value=""> --}}
                                                                <select class="form-select" id="kendaraan" name="kendaraan" required>
                                                                    <option value="Dinas">Dinas</option>
                                                                    <option value="Pribadi">Pribadi</option>
                                                                    <option value="Kendaraan Umum">Kendaraan Umum</option>
                                                                </select>
                                                                <div class="invalid-feedback">
                                                                    Field ini wajib di isi.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="berangkat" class="col-sm-2 col-form-label">Tanggal Berangkat</label>
                                                            <div class="col-sm-10">
                                                                <input type="date" class="form-control" id="berangkat" name="berangkat" value="">
                                                                <div class="invalid-feedback">
                                                                    Field ini wajib di isi.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="card mb-3 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">SPPD</h5>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="row mb-3">
                                                    <label for="sppd" class="col-sm-2 col-form-label">No. SPPD</label>
                                                    <div class="col-sm-10">
                                                        <div class="input-group is-sppd">
                                                            <input type="text" class="form-control bs-tooltip" id="sppd" name="sppd" value="">
                                                            <button class="btn btn-info" type="button" id="button_generate" onclick="generate_sppd()">
                                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                                                Generate Nomor
                                                            </button>
                                                            <button class="btn btn-warning" type="button" id="button_clear" onclick="_clear()">
                                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                                                Bersihkan
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="nama" name="nama" value="">
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="jabatan" class="col-sm-2 col-form-label">Jabatan</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="jabatan" name="jabatan" value="">
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="row mb-3">
                                                    <label for="tujuan" class="col-sm-2 col-form-label">Tujuan</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="tujuan" name="tujuan" value="">
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="kendaraan" class="col-sm-2 col-form-label">Kendaraan Dinas</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="kendaraan" name="kendaraan" value="">
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="berangkat" class="col-sm-2 col-form-label">Tanggal Berangkat</label>
                                                    <div class="col-sm-10">
                                                        <input type="date" class="form-control" id="berangkat" name="berangkat" value="">
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div> --}}
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                    <div class="widget-content widget-content-area br-8">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">Tempat Penyimpanan</h5>

                                        <div class="row mb-3">
                                            <label for="tempat_berkas" class="col-sm-3 col-form-label">Tempat Berkas</label>
                                            <div class="col-sm-9">
                                                {{-- <input type="text" class="form-control" id="tempat_berkas" name="tempat_berkas" value="" required> --}}
                                                <select class="form-select" id="tempat_berkas" name="tempat_berkas" required>
                                                    <option value="">Pilih Tempat Berkas</option>
                                                    @foreach ($berkas as $br)
                                                        <option value="{{ $br->id }}">{{ $br->nama }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="perkembangan" class="col-sm-3 col-form-label">Tk Perkembangan</label>
                                            <div class="col-sm-9">
                                                {{-- <input type="text" class="form-control" id="perkembangan" name="perkembangan" value="" required> --}}
                                                <select name="perkembangan" id="perkembangan" class="form-control" required>
                                                    @foreach ($perkembangan as $pk)
                                                        <option value="{{ $pk->id }}">{{ $pk->nama }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">Keterangan JRA</h5>

                                        <div class="row mb-3">
                                            <label for="aktif" class="col-sm-2 col-form-label">Retensi Aktif</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="aktif" name="aktif" value="" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inaktif" class="col-sm-2 col-form-label">Retensi Inaktif</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="inaktif" name="inaktif" value="" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="thn_aktif" class="col-sm-2 col-form-label">Tahun Aktif</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="thn_aktif" name="thn_aktif" value="" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="thn_inaktif" class="col-sm-2 col-form-label">Tahun Inaktif</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="thn_inaktif" name="thn_inaktif" value="" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="jra" class="col-sm-2 col-form-label">JRA</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="jra" name="jra" value="" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="nilai_guna" class="col-sm-2 col-form-label">Nilai Guna</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="nilai_guna" name="nilai_guna" value="" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                    <div class="widget-content widget-content-area br-8">
                        <div class="row justify-content-center">
                            {{-- <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">
                                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                            Gambar
                                        </h5>
                                        
                                        <div class="row mb-3">
                                            <div class="col-sm-12">
                                                <label for="berkas" class="col-form-label text-center col-12">Upload Gambar (maksimum 5 file @3Mb)</label>
                                                <div class="multiple-file-upload">
                                                    <input type="file" 
                                                        class="filepond file-upload-multiple"
                                                        name="gambar[]" 
                                                        multiple 
                                                        data-allow-reorder="true"
                                                        data-max-file-size="3MB"
                                                        data-max-files="5"
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">
                                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                            Naskah Surat
                                        </h5>
                                        
                                        <div class="row mb-3">
                                            <div class="col-sm-12">
                                                <label for="berkas" class="col-form-label text-center col-12">Upload File Naskah Surat Keluar (maksimum 10Mb, format PDF/Gambar)</label>
                                                <div class="alert alert-light-danger fade show border-0 mb-4 is-alert" role="alert">
                                                    <strong>Perhatian!</strong> <span id="content_alert">Ukuran file melebihi batas dan file tidak akan tersimpan.</span></button>
                                                </div>
                                                {{-- <div class="multiple-file-upload">
                                                    <input type="file" 
                                                        class="filepond file-upload-scan"
                                                        name="scan" 
                                                        multiple 
                                                        data-allow-reorder="true"
                                                        data-max-file-size="5MB"
                                                    >
                                                </div> --}}
                                                <input class="form-control file-upload-input" type="file" id="is_scan" name="is_scan" accept=".pdf,.PDF,.png,.jpg,.jpeg,.PNG,.JPG">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row col-12 justify-content-center">
                                <button type="submit" class="btn btn-success mb-2 me-4 col-4">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                                    <span class="btn-text-inner">Simpan</span>
                                </button>
                                <button type="button" class="btn btn-info mb-2 me-4 col-4">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 8 12 12 16"></polyline><line x1="16" y1="12" x2="8" y2="12"></line></svg>
                                    <span class="btn-text-inner">Kembali</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>

@can('spd')
<div class="modal fade" id="sppdModal" tabindex="-1" role="dialog" aria-labelledby="sppdLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data SPD</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">
                <div id="sppd_content" class="row col-12 g-3 gx-3"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark" data-bs-dismiss="modal">Batalkan</button>
                {{-- <button type="button" class="btn btn-primary">Konfimasi</button> --}}
            </div>
        </div>
    </div>
</div>
@endcan

<div class="modal fade" id="sppdNew" tabindex="-1" role="dialog" aria-labelledby="sppdLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sppdLabel">Buat SPD</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form action="" method="POST" class="needs-validation new-sppd" novalidate>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="uid" name="uid" value="">
                    <div class="row layout-top-spacing">
                        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                            <div class="widget-content widget-content-area br-8">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                {{-- <h5 class="card-title mb-5">Asal Surat</h5> --}}

                                                <div class="row mb-3">
                                                    <label for="nosppd" class="col-sm-3 col-form-label">Nomor SPPD</label>
                                                    <div class="col-sm-5">
                                                        <input type="text" class="form-control" id="nosppd" name="nosppd" value="" maxlength="100" required autofocus>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="nama_sppd" class="col-sm-3 col-form-label">Nama</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" style="text-transform: uppercase;" id="nama_sppd" name="nama" value="" maxlength="255" required>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="jabatan_sppd" class="col-sm-3 col-form-label">Jabatan</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" style="text-transform: uppercase;" id="jabatan_sppd" name="jabatan" value="" maxlength="255" required>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="tujuan_sppd" class="col-sm-3 col-form-label">Tujuan</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" style="text-transform: uppercase;" id="tujuan_sppd" name="tujuan" value="" maxlength="255" required>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="kendaraan_sppd" class="col-sm-3 col-form-label">Kendaraan</label>
                                                    <div class="col-sm-5">
                                                        <input type="text" class="form-control" style="text-transform: uppercase;" id="kendaraan_sppd" name="kendaraan" value="" maxlength="255" required>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="tgl_surat_sppd" class="col-sm-3 col-form-label">Tanggal Surat</label>
                                                    <div class="col-sm-5">
                                                        <input type="date" class="form-control" id="tgl_surat_sppd" name="tgl_surat" value="" required>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="tgl_berangkat_sppd" class="col-sm-3 col-form-label">Tanggal Berangkat</label>
                                                    <div class="col-sm-5">
                                                        <input type="date" class="form-control" id="tgl_berangkat_sppd" name="tgl_berangkat" value="" required>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('js')
    <script src="{{ asset('templates/plugins/src/filepond/filepond.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/filepond/FilePondPluginFileValidateType.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/filepond/FilePondPluginImageExifOrientation.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/filepond/FilePondPluginImagePreview.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/filepond/FilePondPluginImageCrop.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/filepond/FilePondPluginImageResize.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/filepond/FilePondPluginImageTransform.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/filepond/filepondPluginFileValidateSize.min.js') }}"></script>

    <script src="{{ asset('templates/plugins/src/autocomplete/autoComplete.min.js') }}"></script>
    
    <script>
        const btg = `<button class="btn btn-info" type="button" id="button_generate" onclick="generate_sppd()">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                        Generate Nomor
                    </button>`
        const btload = `<button class="btn btn-info" type="button" id="bt_loading" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Generating...
                    </button>`
        const btc = `<button class="btn btn-warning" type="button" id="button_clear" onclick="_clear()">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        Bersihkan
                    </button>`

        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('is-outbox');
            var invalid = $('.is-outbox .invalid-feedback');

            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    let hasError = false;
                    $(this).find('[required]').each(function() {
                        console.log($(this).val())
                        if ($(this).val().trim() === '') {
                            hasError = true;
                            // console.log($(this).attr('name') + ' is required.');
                            $(this).removeClass('is-valid');
                            $(this).addClass('is-invalid');
                        } else {
                            $(this).removeClass('is-invalid');
                            $(this).addClass('is-valid');
                        }
                    });

                    if (hasError) {
                        event.preventDefault();
                        Toast.fire({
                            icon: 'error',
                            title: 'Mohon untuk mengisi field'
                        })
                    }
                }, false);
            });
        }, false);

        $(document).ready(function () {
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginImageExifOrientation,
                FilePondPluginFileValidateSize,
                FilePondPluginFileValidateType,
                // FilePondPluginImageEdit
            );
            
            // Select the file input and use 
            // create() to turn it into a pond
            var multifiles = FilePond.create(document.querySelector('.file-upload-multiple'), {
                acceptedFileTypes: ['image/jpg', 'image/png', 'image/jpeg'],
                fileValidateTypeLabelExpectedTypesMap: { 'image/*': '.jpg, .png, .jpeg' },
                fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
                    resolve(type);
                }),
            });

            var lampiran = FilePond.create(document.querySelector('.file-upload-scan'), {
                acceptedFileTypes: ['application/pdf', 'image/jpg', 'image/png', 'image/jpeg'],
                fileValidateTypeLabelExpectedTypesMap: { 'application/pdf': '.pdf', 'image/*': '.jpg, .png, .jpeg' },
            });

            const autoCompleteJS = new autoComplete({
                selector: "#klasifikasi_kode",
                placeHolder: "Input kode klasifikasi...",
                data: {
                    src: [
                        @foreach ($jra as $jr)
                            "{{ $jr->klas3 }} - {{ $jr->masalah3 }}",
                        @endforeach
                    ],
                    cache: true,
                },
                resultsList: {
                    element: (list, data) => {
                        if (!data.results.length) {
                            // Create "No Results" message element
                            const message = document.createElement("div");
                            // Add class to the created element
                            message.setAttribute("class", "no_result");
                            // Add message text content
                            message.innerHTML = `<span>Found No Results for "${data.query}"</span>`;
                            // Append message element to the results list
                            list.prepend(message);
                        }
                    },
                    noResults: true,
                },
                resultItem: {
                    highlight: {
                        render: true
                    }
                },
                events: {
                    input: {
                        focus() {
                            if (autoCompleteJS.input.value.length) autoCompleteJS.start();
                        },
                        selection(event) {
                            const feedback = event.detail;
                            // Prepare User's Selected Value
                            const selection = feedback.selection.value;
                            // Replace Input value with the selected value
                            const splitSelection = selection.split(' - ');

                            focusNextInput();
                            get_nomor_urut();
                            set_jra(selection);

                            document.getElementById('no_surat').value = 'Generating...';
                            document.getElementById('no_surat').readOnly = true;
                            setTimeout(() => {
                                const nomor = document.getElementById('urut').value;
                                autoCompleteJS.input.value = splitSelection[0];
                                document.getElementById('berkas').value = splitSelection[1]
                                document.getElementById('no_surat').value = splitSelection[0] + '/' + nomor + '/' + new Date().getFullYear();
                                document.getElementById('no_surat').readOnly = false;
                            }, 2000);
                            // focusCursor();
                        },
                    },
                },
            });

            const autoCompleteTo = new autoComplete({
                selector: "#darikepada",
                placeHolder: "Nama instansi / perusahaan",
                data: {
                    src: [
                        @foreach ($instansi as $ins)
                            "{{ $ins->nama_unit }}",
                        @endforeach
                    ],
                    cache: true,
                },
                resultsList: {
                    element: (list, data) => {
                        if (!data.results.length) {
                            // Create "No Results" message element
                            const message = document.createElement("div");
                            // Add class to the created element
                            message.setAttribute("class", "no_result");
                            // Add message text content
                            message.innerHTML = `<span>Found No Results for "${data.query}"</span>`;
                            // Append message element to the results list
                            list.prepend(message);
                        }
                    },
                    noResults: true,
                },
                resultItem: {
                    highlight: {
                        render: true
                    }
                },
                events: {
                    input: {
                        focus() {
                            if (autoCompleteTo.input.value.length) autoCompleteTo.start();
                        },
                        selection(event) {
                            const feedback = event.detail;
                            // Prepare User's Selected Value
                            const selection = feedback.selection.value;
                            autoCompleteTo.input.value = selection;

                            const inputElement = document.getElementById('wilayah');
                            inputElement.focus();
                        },
                    },
                },
            });

            const autoCompleteUp = new autoComplete({
                selector: "#nama_up",
                placeHolder: "Nama Unit Pengolah",
                data: {
                    src: [
                        @foreach ($instansi as $ins)
                            "{{ $ins->kode }} - {{ $ins->nama_unit }}",
                        @endforeach
                    ],
                    cache: true,
                },
                resultsList: {
                    element: (list, data) => {
                        if (!data.results.length) {
                            // Create "No Results" message element
                            const message = document.createElement("div");
                            // Add class to the created element
                            message.setAttribute("class", "no_result");
                            // Add message text content
                            message.innerHTML = `<span>Found No Results for "${data.query}"</span>`;
                            // Append message element to the results list
                            list.prepend(message);
                        }
                    },
                    noResults: true,
                },
                resultItem: {
                    highlight: {
                        render: true
                    }
                },
                events: {
                    input: {
                        focus() {
                            if (autoCompleteUp.input.value.length) autoCompleteUp.start();
                        },
                        selection(event) {
                            const feedback = event.detail;
                            // Prepare User's Selected Value
                            const selection = feedback.selection.value;
                            const splitSelection = selection.split(' - ');
                            autoCompleteUp.input.value = splitSelection[1];
                            $('#kode_up').val(splitSelection[0])

                            const inputElement = document.getElementById('isi');
                            inputElement.focus();
                        },
                    },
                },
            });

            $('.new-sppd').submit(function(e) {
                e.preventDefault()
                var invalid = $('.new-sppd .invalid-feedback');
                var fcontrol = $('.new-sppd .form-control');
                var total = 0;

                fcontrol.each(function() {
                    var $input = $(this)

                    if ($input.val().trim() === '') {
                        $input.next('.invalid-feedback').css('display', 'block');
                        $input.removeClass('is-valid');
                        $input.addClass('is-invalid');
                        total++
                    } else {
                        $input.next('.invalid-feedback').css('display', 'none');
                        $input.addClass('is-valid');
                        $input.removeClass('is-invalid');
                    }
                })

                if (total > 0) {
                    Toast.fire({ icon: 'error', title: 'Field belum terisi semua.' })
                } else {
                    $.ajax({
                        url: "{{ route('sppd.save') }}",
                        type: "POST",
                        data: $('.new-sppd').serialize(),
                        success: function(res) {
                            if (res.status === 'success') {
                                Toast.fire({ icon: 'success', title: res.message })
                            } else {
                                Toast.fire({ icon: 'error', title: res.message })
                            }
                            $('#sppdNew').modal('hide')
                            _sppd()
                        },
                        error: function() {
                            Toast.fire({ icon: 'error', title: 'Terjadi kesalahan pada sistem.' })
                        }
                    })
                }
            })

            $('#button_generate').remove()
            $('#button_clear').remove()
            if ($('#sppd').val().trim() !== '') {
                $('#button_generate').remove()
                $('.is-sppd').append(btc)
            } else {
                $('#button_clear').remove()
                $('.is-sppd').append(btg)
            }

            $('.is-alert').hide()
            var uploadField = document.getElementById("is_scan");
            uploadField.onchange = function() {
                const list = ['pdf', 'png', 'jpg', 'jpeg']
                const myfile = this.files[0]
                const lastDotIndex = myfile.name.split('.').pop()
                if(myfile.size > 10485760) { // 10MB limit
                    $('#content_alert').html('Ukuran file melebihi batas dan file tidak akan tersimpan.')
                    this.value = "";
                    $('.is-alert').show()
                } else if (!list.includes(lastDotIndex.toLowerCase())) { // check extention
                    $('#content_alert').html('Jenis file tidak sesuai ketentuan dan file tidak akan tersimpan.')
                    this.value = "";
                    $('.is-alert').show()
                } else {
                    $('.is-alert').hide()
                }
            };
        });
    </script>

    <script>
        function _sppd() {
            $.ajax({
                url: "{{ route('sppd.list') }}",
                type: "GET",
                dataType: "JSON",
                success: function(res) {
                    if (res.status === 'success') {
                        const data = res.data
                        let text = ''
                        data.forEach((dt) => {
                            text += `
                                <div class="card col-4 px-3 style-4">
                                    <div class="card-body pt-3">
                                        
                                        <div class="media mt-0 mb-3">
                                            <div class="media-body">
                                                <h4 class="media-heading mb-0">${dt.nosppd}</h4>
                                                <p class="media-text">${dt.tujuan}</p>
                                            </div>
                                        </div>
                                        <p class="card-text mt-4 mb-0"><b>${dt.nama}</b><br>${dt.jabatan}</p>
                                        <br>
                                        <p>Surat: ${dt.tglsurat} <br>Berangkat: ${dt.tglberangkat}</p>
                                    </div>
                                    <div class="card-footer pt-0 border-0 text-center">
                                        <div class="row">
                                            <a href="javascript:void(0);" class="btn btn-secondary w-50" onclick="_use_sppd('${dt.nosppd}')">
                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                                <span class="btn-text-inner ms-3">Gunakan</span>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-warning w-50" onclick="_copy_sppd('${btoa(JSON.stringify(dt))}')">
                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                                <span class="btn-text-inner ms-3">Salin</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `
                        })
                        $('#sppd_content').html(text)
                        $('#spppdModal').modal('show')
                    } else {
                        Toast.fire({ icon: 'error', title: res.message })
                    }
                },
                error: function() {
                    Toast.fire({ icon: 'error', title: 'Terjadi kesalahan pada sistem.' })
                }
            })
            $('#sppdModal').modal('show')
        }

        function _use_sppd(no) {
            $('#sppd').val(no)
            $('#sppdModal').modal('hide')
        }

        function _copy_sppd(datas) {
            if (datas) {
                const data = JSON.parse(atob(datas))
                $('.new-sppd .invalid-feedback').css('display', 'none')

                $('#sppdLabel').html('Salin SPPD')
                $('.new-sppd').attr("action", "{{ route('sppd.store') }}")

                $('#nosppd').val(data.nosppd)
                $('#nama_sppd').val(data.nama)
                $('#jabatan_sppd').val(data.jabatan)
                $('#tujuan_sppd').val(data.tujuan)
                $('#kendaraan_sppd').val(data.kendaraan)
                $('#tgl_surat_sppd').val(data.tglsurat)
                $('#tgl_berangkat_sppd').val(data.tglberangkat)

                $('#sppdModal').modal('hide')
                $('#sppdNew').modal('show')
            }
        }

        function _clear() {
            $('#sppd').val('')
            $('#button_clear').remove()
            $('.is-sppd').append(btg)

            // $('#button_generate').show()
            // $('#button_clear').hide()
        }

        function focusNextInput() {
            const formInputs = document.querySelectorAll('form input[type="text"]');
            let currentIndex = -1;

            // Find the currently focused input
            for (let i = 0; i < formInputs.length; i++) {
                if (document.activeElement === formInputs[i]) {
                currentIndex = i;
                break;
                }
            }

            // Calculate the next index
            let nextIndex = currentIndex + 2;

            // If at the end, loop back to the first input
            if (nextIndex >= formInputs.length) {
                nextIndex = 0;
            }

            // Focus the next input field
            formInputs[nextIndex].focus();
        }

        function focusCursor() {
            const formInputs = $('#no_surat');
            const firstSlash = formInputs.val().indexOf("/");
            let position = firstSlash + 1;
            // console.log(firstSlash, position);

            formInputs.focus()[0].setSelectionRange(position, position);
        }

        async function get_nomor_urut() {
            try {
                const response = await fetch("{{ route('outbox.urut') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ type: 'Keluar' })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                document.getElementById('urut').value = data.urut;
            } catch (error) {
                console.error('Error fetching nomor urut:', error);
                return null;
            }
        }

        function set_jra(datas) {
            const split = datas.split(' - ')

            fetch("{{ route('jra') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ kode: split[0], name: split[1] })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    console.error('Error:', data.message);
                    return;
                }
                document.getElementById('aktif').value = data.jra.r_aktif;
                document.getElementById('inaktif').value = data.jra.r_inaktif;
                document.getElementById('thn_aktif').value = data.jra.thn_aktif;
                document.getElementById('thn_inaktif').value = data.jra.thn_inaktif;
                document.getElementById('jra').value = data.jra.ket_jra;
                document.getElementById('nilai_guna').value = data.jra.nilai_guna;
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }

        async function generate_sppd() {
            const bulan = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"]
            const id = $('#sppd');
            if (id.val().trim() === '') {
                const kode = $('#klasifikasi_kode').val();
                const up = $('#kode_up').val();
                const month = parseInt(new Date().getMonth()) + 1;
                const year = new Date().getFullYear();

                $('#button_generate').remove()
                $('.is-sppd').append(btload)

                let nomor = await $.ajax({
                    url: "{{ route('outbox.sppd.last') }}",
                    type: "GET",
                    dataType: "JSON",
                    success: function(r) {
                        if (!r) return null;
                        return r.nomor;
                    }, 
                    error: function() {
                        Toast.fire({ icon: 'error', title: 'Terjadi kesalahan pada sistem.' })
                        return null;
                    }
                })

                console.log(nomor);
                id.val(`${kode}/${nomor.nomor ?? ''}.${up}/${bulan[month]}/${year}`);

                const slashIndex = id.val().indexOf("/");
                const dotIndex = id.val().indexOf(".");
                let position = slashIndex + 1;

                if (slashIndex !== -1 && dotIndex !== -1 && slashIndex < dotIndex) {
                    id.focus()[0].setSelectionRange(position, position);
                }
                $('#bt_loading').remove()
                $('.is-sppd').append(btc)
            }
        }
    </script>
@endsection