@extends('layouts.layout')

@section('title', 'Edit Surat Keluar')


@section('css')
    <link rel="stylesheet" href="{{ asset('templates/plugins/src/filepond/filepond.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/src/filepond/FilePondPluginImagePreview.min.css') }}">
    <link href="{{ asset('templates/plugins/src/autocomplete/css/autoComplete.02.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('templates/assets/css/light/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/light/filepond/custom-filepond.css') }}" rel="stylesheet" type="text/css" />
    
    <link href="{{ asset('templates/assets/css/dark/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/dark/filepond/custom-filepond.css') }}" rel="stylesheet" type="text/css" />
@endsection


@section('content')
<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="page-meta">
            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('outbox') }}">Surat Keluar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Surat Keluar</li>
                </ol>
            </nav>
        </div>

        <form class="needs-validation" novalidate>
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
                                            <label for="kepada" class="col-sm-3 col-form-label">Kepada</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="kepada" name="kepada" value="{{ $outbox->drkpd }}" maxlength="255" required autofocus>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="wilayah" class="col-sm-3 col-form-label">Wilayah</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="wilayah" name="wilayah" value="{{ $outbox->NAMAKOTA }}" maxlength="255" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="perihal" class="col-sm-3 col-form-label">Perihal Surat</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="perihal" name="perihal" value="{{ $outbox->PERIHAL }}" maxlength="255" required>
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
                                                <input type="text" class="form-control" id="berkas" name="berkas" value="{{ $outbox->NAMABERKAS }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="tgl_surat" class="col-sm-3 col-form-label">Tanggal Surat</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" id="tgl_surat" name="tgl_surat" value="{{ $outbox->TGLSURAT }}" required>
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
                                        <h5 class="card-title mb-5">Keterangan</h5>

                                        <div class="row mb-3">
                                            <label for="naik" class="col-sm-2 col-form-label">Naik</label>
                                            <div class="col-sm-4">
                                                <input type="date" class="form-control" id="naik" name="naik" value="" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                            <label for="diteruskan" class="col-sm-2 col-form-label">Diteruskan</label>
                                            <div class="col-sm-4">
                                                <input type="date" class="form-control" id="diteruskan" name="diteruskan" value="{{ date_format(date_create($outbox->TGLTERUS), 'Y-m-d') }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="nama_up" class="col-sm-2 col-form-label">Unit Pengolah</label>
                                            <div class="col-sm-10">
                                                {{-- select nama unit pengolah beserta kodenya --}}
                                                <input type="text" class="form-control" id="nama_up" name="nama_up" value="{{ $outbox->KODEUP }} - {{ $outbox->NAMAUP }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="nama" name="nama" value="" required>
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
                                        <h5 class="card-title mb-5">Klasifikasi</h5>
                                        
                                        <div class="row mb-3">
                                            <label for="klasifikasi_kode" class="col-sm-3 col-form-label">Kode Klasifikasi</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="klasifikasi_kode" name="klasifikasi_kode" value="{{ $outbox->KLAS3 }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="urut" class="col-sm-3 col-form-label">Nomor Urut</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="urut" name="urut" value="{{ $outbox->NOURUT }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="no_surat" class="col-sm-3 col-form-label">Nomor Surat</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="no_surat" name="no_surat" value="{{ $outbox->NOSURAT }}" maxlength="20" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="sifat_surat" class="col-sm-3 col-form-label">Sifat Surat</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="sifat_surat" name="sifat_surat" value="{{ $outbox->SIFAT_SURAT }}" required>
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
                                            <label for="isi" class="col-sm-2 col-form-label">Isi</label>
                                            <div class="col-sm-10">
                                                {{-- <input type="text" class="form-control" id="isi" name="isi" value="" maxlength="255" required> --}}
                                                <textarea name="isi" id="isi" class="form-control" cols="30" rows="5" required>{{ $outbox->ISI }}</textarea>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="ttd" class="col-sm-2 col-form-label">Ditandatangani Oleh</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="ttd" name="ttd" value="{{ $outbox->TTD }}" maxlength="100" required>
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
                                        <h5 class="card-title mb-5">Tempat Penyimpanan</h5>

                                        <div class="row mb-3">
                                            <label for="tempat_berkas" class="col-sm-3 col-form-label">Tempat Berkas</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="tempat_berkas" name="tempat_berkas" value="{{ $outbox->TMPTBERKAS }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="perkembangan" class="col-sm-3 col-form-label">Tk Perkembangan</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="perkembangan" name="perkembangan" value="{{ $outbox->TK_PERKEMBANGAN }}" required>
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
                                                <input type="text" class="form-control" id="aktif" value="{{ $outbox->AKTIF }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inaktif" class="col-sm-2 col-form-label">Retensi Inaktif</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inaktif" value="{{ $outbox->INAKTIF }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="thn_aktif" class="col-sm-2 col-form-label">Tahun Aktif</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="thn_aktif" value="{{ $outbox->THAKTIF }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="thn_inaktif" class="col-sm-2 col-form-label">Tahun Inaktif</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="thn_inaktif" value="{{ $outbox->THINAKTIF }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="jra" class="col-sm-2 col-form-label">JRA</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="jra" value="{{ $outbox->KETJRA }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="nilai_guna" class="col-sm-2 col-form-label">Nilai Guna</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="nilai_guna" value="{{ $outbox->NILAIGUNA }}" required readonly>
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
                            <div class="col-md-6">
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
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">
                                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                            File Lampiran
                                        </h5>
                                        
                                        <div class="row mb-3">
                                            <div class="col-sm-12">
                                                <label for="berkas" class="col-form-label text-center col-12">Upload File Lampiran (maksimum 5Mb, format PDF)</label>
                                                <div class="multiple-file-upload">
                                                    <input type="file" 
                                                        class="filepond file-upload-lampiran"
                                                        name="lampiran" 
                                                        multiple 
                                                        data-allow-reorder="true"
                                                        data-max-file-size="5MB"
                                                    >
                                                </div>
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
                                    <span class="btn-text-inner">Update</span>
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

            var lampiran = FilePond.create(document.querySelector('.file-upload-lampiran'), {
                acceptedFileTypes: ['application/pdf'],
                fileValidateTypeLabelExpectedTypesMap: { 'application/pdf': '.pdf' },
            });
        });
    </script>
@endsection