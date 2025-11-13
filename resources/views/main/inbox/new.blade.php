@extends('layouts.layout')

@section('title', 'Buat Surat Masuk')


@section('css')
    <link rel="stylesheet" href="{{ asset('templates/plugins/src/filepond/filepond.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/src/filepond/FilePondPluginImagePreview.min.css') }}">
    <link href="{{ asset('templates/plugins/src/autocomplete/css/autoComplete.02.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('templates/assets/css/light/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/light/filepond/custom-filepond.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/light/autocomplete/css/custom-autoComplete.css') }}" rel="stylesheet" type="text/css" />
    
    <link href="{{ asset('templates/assets/css/dark/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/dark/filepond/custom-filepond.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/dark/autocomplete/css/custom-autoComplete.css') }}" rel="stylesheet" type="text/css" />

    <style>
    input[readonly] {
        background-color: #ffffff !important;
        color: #2a2a2a !important;
    }
    </style>
@endsection


@section('content')
<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="page-meta">
            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inbox') }}">Surat Masuk</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Buat Surat Masuk</li>
                </ol>
            </nav>
        </div>

        <form method="POST" action="{{ route('inbox.store') }}" class="needs-validation" novalidate>
            <div class="row layout-top-spacing">
                @csrf
                <input type="hidden" name="uid" value="">

                <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                    <div class="widget-content widget-content-area br-8">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">Tanggal</h5>
                                        
                                        <div class="row mb-3">
                                            <label for="tgl_surat" class="col-sm-3 col-form-label">Tanggal Surat</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" id="tgl_surat" name="tgl_surat" value="" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="tgl_terima" class="col-sm-3 col-form-label">Tanggal Terima</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" id="tgl_terima" name="tgl_terima" value="" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="berkas" class="col-sm-3 col-form-label">Nama Berkas</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="berkas" name="berkas" value="" required>
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
                                        <h5 class="card-title mb-5">Asal Surat & Isi Surat</h5>

                                        <div class="row mb-3">
                                            <label for="darikepada" class="col-sm-2 col-form-label">Dari Kepada</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="darikepada" name="darikepada" value="" maxlength="255" required autofocus>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="wilayah" class="col-sm-2 col-form-label">Wilayah</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="wilayah" name="wilayah" value="" maxlength="100" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="perihal" class="col-sm-2 col-form-label">Perihal</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="perihal" name="perihal" maxlength="255" value="" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="isi" class="col-sm-2 col-form-label">Isi Surat</label>
                                            <div class="col-sm-10">
                                                {{-- <input type="text" class="form-control" id="isi" value="" required> --}}
                                                <textarea name="isi" id="isi" class="form-control" cols="30" rows="5" maxlength="255" required></textarea>
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
                                                <input type="text" class="form-control" id="klasifikasi_kode" name="klasifikasi_kode" value="" required>
                                                {{-- <input id="autoComplete" class="form-control"> --}}
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
                                                <input type="text" class="form-control" id="no_surat" name="no_surat" value="" maxlength="15" required>
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
                                                <input type="text" class="form-control" id="aktif" name="aktif" value="" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inaktif" class="col-sm-2 col-form-label">Retensi Inaktif</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inaktif" name="inaktif" value="" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="thn_aktif" class="col-sm-2 col-form-label">Tahun Aktif</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="thn_aktif" name="thn_aktif" value="" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="thn_inaktif" class="col-sm-2 col-form-label">Tahun Inaktif</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="thn_inaktif" name="thn_inaktif" value="" required readonly>
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
                                                    @foreach ($berkas as $br)
                                                        <option value="{{ $br->Nama }}" {{ $br->Nama == 'Map' ? 'selected' : '' }}>{{ $br->Nama }}</option>
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
                                                <select class="form-select" id="perkembangan" name="perkembangan" required>
                                                    <option value="Asli">Asli</option>
                                                    <option value="Foto Copy">Foto Copy</option>
                                                    <option value="Tembusan">Tembusan</option>
                                                    <option value="Salinan">Salinan</option>
                                                    <option value="Faksimile">Faksimile</option>
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
                                        <h5 class="card-title mb-5">Keterangan</h5>

                                        <div class="row mb-3">
                                            <label for="tgl_diteruskan" class="col-sm-2 col-form-label">Tanggal Diteruskan</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" id="tgl_diteruskan" name="tgl_diteruskan" value="{{ date('Y-m-d') }}">
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="diteruskan_kpd" class="col-sm-2 col-form-label">Diteruskan Kepada</label>
                                            <div class="col-sm-10">
                                                {{-- <input type="text" class="form-control" id="diteruskan_kpd" name="diteruskan_kpd" value=""> --}}
                                                <select class="form-select" id="diteruskan_kpd" name="diteruskan_kpd" required>
                                                    <option value="Sekretaris Daerah">Sekretaris Daerah</option>
                                                    <option value="Bupati">Bupati</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="row mb-3">
                                            <label for="disposisi" class="col-sm-2 col-form-label">Catatan Disposisi</label>
                                            <div class="col-sm-10">
                                                <textarea name="disposisi" id="disposisi" class="form-control" cols="30" rows="5"></textarea>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="row mb-3">
                                            <label for="sifat_surat" class="col-sm-2 col-form-label">Sifat Surat</label>
                                            <div class="col-sm-10">
                                                {{-- <input type="text" class="form-control" id="sifat_surat" name="sifat_surat" value=""> --}}
                                                <select class="form-select" id="sifat_surat" name="sifat_surat" required>
                                                    <option value="Biasa">Biasa</option>
                                                    <option value="Segera">Segera</option>
                                                    <option value="Penting">Penting</option>
                                                    <option value="Rahasia">Rahasia</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="tindakan" class="col-sm-2 col-form-label">Tindakan</label>
                                            <div class="col-sm-10">
                                                {{-- <input type="text" class="form-control" id="tindakan" name="tindakan" value=""> --}}
                                                <select class="form-select" id="tindakan" name="tindakan" required>
                                                    <option value="Non Balas">Non Balas</option>
                                                    <option value="Balas">Balas</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="tgl_balas" class="col-sm-2 col-form-label">Tanggal Balas</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" id="tgl_balas" name="tgl_balas" value="{{ date('Y-m-d') }}">
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

            const autoCompleteJS = new autoComplete({
                selector: "#klasifikasi_kode",
                placeHolder: "Input kode klasifikasi...",
                data: {
                    src: [
                        @foreach ($jra as $jr)
                            "{{ $jr->KLAS3 }} - {{ $jr->MASALAH3 }}",
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

                            autoCompleteJS.input.value = splitSelection[0];
                            document.getElementById('berkas').value = splitSelection[1]
                            document.getElementById('no_surat').value = splitSelection[0] + '/   /' + new Date().getFullYear();
                            focusNextInput();
                            get_nomor_urut();
                            set_jra(selection);

                            const inputElement = document.getElementById('no_surat');
                            inputElement.focus(); // Set focus on the input field

                            const textLength = inputElement.value.length;
                            const middleIndex = Math.floor(textLength / 2);

                            inputElement.setSelectionRange(middleIndex, middleIndex);
                        },
                    },
                },
            });
        });

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

        async function get_nomor_urut() {
            try {
                const response = await fetch("{{ route('inbox.urut') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ type: 'Masuk' })
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
                document.getElementById('aktif').value = data.jra.RAKTIF;
                document.getElementById('inaktif').value = data.jra.RINAKTIF;
                document.getElementById('thn_aktif').value = data.jra.thn_aktif;
                document.getElementById('thn_inaktif').value = data.jra.thn_inaktif;
                document.getElementById('jra').value = data.jra.KETJRA;
                document.getElementById('nilai_guna').value = data.jra.NILAIGUNA;
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script>
@endsection