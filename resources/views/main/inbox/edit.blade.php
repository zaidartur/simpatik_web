@extends('layouts.layout')

@section('title', 'Edit Surat Masuk')


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
                    <li class="breadcrumb-item"><a href="{{ route('inbox') }}">Surat Masuk</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Surat Masuk</li>
                </ol>
            </nav>
        </div>

        <form method="POST" action="{{ route('inbox.update') }}" class="needs-validation" enctype="multipart/form-data" novalidate>
            <div class="row layout-top-spacing">
                @csrf

                <input type="hidden" name="uid" value="{{ $inbox->uuid }}">
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
                                                <input type="text" class="form-control" id="berkas" value="{{ $inbox->nama_berkas }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="tgl_terima" class="col-sm-3 col-form-label">Tanggal Terima</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" id="tgl_terima" value="{{ date_format(date_create($inbox->tgl_diterima), 'Y-m-d') }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="tgl_surat" class="col-sm-3 col-form-label">Tanggal Surat</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" id="tgl_surat" value="{{ date_format(date_create($inbox->tgl_surat), 'Y-m-d') }}" required>
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
                                                <input type="text" class="form-control" id="darikepada" value="{{ $inbox->dari }}" required autofocus>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="wilayah" class="col-sm-2 col-form-label">Wilayah</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="wilayah" value="{{ $inbox->wilayah }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="perihal" class="col-sm-2 col-form-label">Perihal</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="perihal" value="{{ $inbox->perihal }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="isi" class="col-sm-2 col-form-label">Isi Surat</label>
                                            <div class="col-sm-10">
                                                {{-- <input type="text" class="form-control" id="isi" value="" required> --}}
                                                <textarea name="isi" id="isi" class="form-control" cols="30" rows="5" required>{{ $inbox->isi_surat }}</textarea>
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
                                                <input type="text" class="form-control" id="klasifikasi_kode" value="{{ $inbox->klasifikasi->klas3 }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="no_surat" class="col-sm-3 col-form-label">Nomor Surat</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="no_surat" value="{{ $inbox->no_surat }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="urut" class="col-sm-3 col-form-label">Nomor Urut</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="urut" value="{{ $inbox->no_agenda }}" required readonly>
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

                                        {{-- <div class="row mb-3">
                                            <label for="klasifikasi" class="col-sm-2 col-form-label">Klasifikasi</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="klasifikasi" value="{{ $inbox->SIFAT_SURAT }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="row mb-3">
                                            <label for="aktif" class="col-sm-2 col-form-label">Retensi Aktif</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="aktif" value="{{ $inbox->klasifikasi->r_aktif }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="inaktif" class="col-sm-2 col-form-label">Retensi Inaktif</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="inaktif" value="{{ $inbox->klasifikasi->r_inaktif }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="thn_aktif" class="col-sm-2 col-form-label">Tahun Aktif</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="thn_aktif" value="{{ intval(\Carbon\Carbon::parse($inbox->tgl_surat)->format('Y')) + intval($inbox->klasifikasi->r_aktif) }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="thn_inaktif" class="col-sm-2 col-form-label">Tahun Inaktif</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="thn_inaktif" value="{{ intval(\Carbon\Carbon::parse($inbox->tgl_surat)->format('Y')) + intval($inbox->klasifikasi->r_aktif) + intval($inbox->klasifikasi->r_inaktif) }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="jra" class="col-sm-2 col-form-label">JRA</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="jra" value="{{ $inbox->klasifikasi->ket_jra }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="nilai_guna" class="col-sm-2 col-form-label">Nilai Guna</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="nilai_guna" value="{{ $inbox->klasifikasi->nilai_guna }}" required readonly>
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
                                            <label for="darikepada" class="col-sm-3 col-form-label">Tempat Berkas</label>
                                            <div class="col-sm-9">
                                                {{-- <input type="text" class="form-control" id="darikepada" value="" required> --}}
                                                <select class="form-select" id="tempat_berkas" name="tempat_berkas" required>
                                                    <option value="">Pilih Tempat Berkas</option>
                                                    @foreach ($berkas as $br)
                                                        <option value="{{ $br->id }}" {{ $br->id == $inbox->tempat_berkas ? 'selected' : '' }}>{{ $br->nama }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="darikepada" class="col-sm-3 col-form-label">Tk Penyimpanan</label>
                                            <div class="col-sm-9">
                                                <select class="form-select" id="perkembangan" name="perkembangan" required>
                                                    @foreach ($perkembangan as $pk)
                                                        <option value="{{ $pk->id }}" {{ $pk->id == $inbox->id_perkembangan ? 'selected' : '' }}>{{ $pk->nama }}</option>
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
                                        <h5 class="card-title mb-5">Keterangan</h5>

                                        <div class="row mb-3">
                                            <label for="sifat_surat" class="col-sm-2 col-form-label">Sifat Surat</label>
                                            <div class="col-sm-10">
                                                <select class="form-select" id="sifat_surat" name="sifat_surat" required>
                                                    @foreach ($sifat as $sf)
                                                        <option value="{{ $sf->id }}" {{ $sf->id == $inbox->sifat_surat ? 'selected' : '' }}>{{ $sf->nama_sifat }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="tindakan" class="col-sm-2 col-form-label">Tindakan</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="tindakan" value="{{ $inbox->tindakan }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="tgl_balas" class="col-sm-2 col-form-label">Tanggal Balas</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" id="tgl_balas" value="{{ date_format(date_create($inbox->tgl_balas), 'Y-m-d') }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-1">
                                            <label for="is_diteruskan" class="col-sm-2 col-form-label">&nbsp;</label>
                                            <div class="col-sm-10">
                                                <div class="form-check form-check-info form-check-inline">
                                                    <input class="form-check-input" type="checkbox" value="Berkas Di Tinggal" id="is_diteruskan" name="is_diteruskan" value="yes" onchange="_forward()" {{ Auth::user()->leveluser->tindak_lanjut ? 'checked' : '' }}>
                                                    <label class="form-check-label bs-tooltip" for="is_diteruskan" title="Hapus centang apabila surat tidak akan diteruskan"><span class="badge badge-info mb-2 me-4">Surat Diteruskan?</span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="tgl_diteruskan" class="col-sm-2 col-form-label">Tanggal Diteruskan</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control lbl-diteruskan" id="tgl_diteruskan" value="{{ date_format(date_create($inbox->tgl_diteruskan), 'Y-m-d') }}" required>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="diteruskan_kpd" class="col-sm-2 col-form-label">Diteruskan Kepada</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control lbl-diteruskan" id="diteruskan_kpd" value="{{ $inbox->disposisi[0]->penerima?->leveluser?->nama ?? '' }}" required readonly>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="row mb-3">
                                            <label for="disposisi" class="col-sm-2 col-form-label">Catatan Disposisi</label>
                                            <div class="col-sm-10">
                                                <textarea name="disposisi" id="disposisi" class="form-control" cols="30" rows="5">{{ $inbox->CATATAN }}</textarea>
                                                <div class="invalid-feedback">
                                                    Field ini wajib di isi.
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
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
                </div> --}}

                <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                    <div class="widget-content widget-content-area br-8">
                        <div class="row justify-content-center">
                            @if (!empty($inbox->softcopy))
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        @if (in_array(strtolower(pathinfo($inbox->softcopy, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                            <h5 class="card-title mb-5">File Gambar Tersimpan</h5>
                                            <img src="{{ route('inbox.view', $inbox->cryptfile) }}" width="100%" height="auto" alt="image-scan" onclick="view_file(`{{ $inbox->cryptfile }}`)" class="round img-thumbnail bs-tooltip" title="Klik untuk melihat secara penuh">

                                        @elseif (strtolower(pathinfo($inbox->softcopy, PATHINFO_EXTENSION)) == 'pdf')
                                            <h5 class="card-title mb-5">File Dokumen Tersimpan</h5>
                                            <div class="row justify-content-center" style="vertical-align: middle;">
                                                <button type="button" class="btn btn-info col-md-6 mb-5" onclick="view_file(`{{ $inbox->cryptfile }}`)">Lihat File Dokumen</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="{{ !empty($inbox->softcopy) ? 'col-md-6' : 'col-md-8' }}">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5 {{ !empty($inbox->softcopy) ? 'text-warning' : '' }}">
                                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                            @if (!empty($inbox->softcopy))
                                            <b>Ubah</b> Scan Surat Masuk
                                            @else
                                            Scan Surat Masuk
                                            @endif
                                        </h5>
                                        
                                        <div class="row mb-3">
                                            <div class="col-sm-12">
                                                <label for="berkas" class="col-form-label text-center col-12">Upload File Hasil Scan Surat Masuk (maksimum 10Mb, format PDF/Gambar)</label>
                                                <div class="multiple-file-upload">
                                                    <input type="file" 
                                                        class="filepond file-upload-scan"
                                                        name="is_scan" 
                                                        multiple 
                                                        data-allow-reorder="true"
                                                        data-max-file-size="10MB"
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

            var lampiran = FilePond.create(document.querySelector('.file-upload-scan'), {
                acceptedFileTypes: ['application/pdf'],
                fileValidateTypeLabelExpectedTypesMap: { 'application/pdf': '.pdf' },
            });
        });

        function _forward() {
            const ask = document.getElementById('is_diteruskan');
            const ele = ask;
            const isChecked = ele.checked;
            const forwardFields = document.querySelectorAll('.lbl-diteruskan');

            forwardFields.forEach(field => {
                field.disabled = !isChecked;
            });
        }

        function view_file(file) {
            window.open(`/surat-masuk/lihat-file/${file}`, '_blank')
        }
    </script>
@endsection