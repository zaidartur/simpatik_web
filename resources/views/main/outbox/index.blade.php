@extends('layouts.layout')

@section('title', 'Surat Keluar')


@section('css')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/dark/table/datatable/dt-global_style.css') }}">
    
    <link href="{{ asset('templates/plugins/css/light/loaders/custom-loader.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/dark/loaders/custom-loader.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->

    <style>
        table.dataTable td,
        table.dataTable th {
            white-space: normal !important; /* Allows text to wrap */
            word-wrap: break-word !important; /* Breaks long words if necessary */
        }

        .table-overlay {
            position: absolute;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.7);
            width: 100%;
            height: 100%;
            z-index: 100;
            display: none;

            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: none; 
        }

        /* Spinner */
        .table-overlay .spinner {
            border: 5px solid #ccc;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        /* Spinner Animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection


@section('content')
<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">

            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="widget-content widget-content-area br-8 p-3">
                    <div class="row justify-content-space-between">
                        <div class="col-6">
                            <h4 class="">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                Daftar Surat Keluar
                            </h4>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-info mb-2 me-4 float-end" onclick="location.href='{{ route('outbox.create') }}'">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                <span class="btn-text-inner">Buat Data</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="widget-content widget-content-area br-8" style="position: relative;">
                    <div class="table-overlay" id="table-overlay">
                        <div class="spinner"></div>
                    </div>

                    <table id="zero-config" class="table dt-table-hover" style="width:100%" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 10%">No. Agenda</th>
                                <th style="width: 15%">Tanggal Entry</th>
                                <th style="width: 10%">Klasifikasi</th>
                                <th style="width: 10%">Nama Berkas</th>
                                <th style="width: 15%">Tujuan</th>
                                <th style="width: 25%">Perihal</th>
                                <th style="width: 10%">Opsi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="detailSurat" tabindex="-1" role="dialog" aria-labelledby="detailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailLabel">Detail Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">
                <div id="detail" class="col-12"></div>
                {{-- <div id="wizard_Icons" class="col-lg-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <div class="widget-content widget-content-area" id="detail"></div>
                    </div>
                </div> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js')
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('templates/plugins/src/table/datatable/datatables.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/stepper/bsStepper.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            let tb_inbox = $('#zero-config').DataTable({
                "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                        "<'table-responsive'tr>" +
                        "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
                "language": {
                    "paginate": { 
                        "previous": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', 
                        "next": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' 
                    },
                    // "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "info": "Menampilkan data _START_ sampai _END_ dari _TOTAL_ entri total",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri total",
                    "search": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    "searchPlaceholder": "Pencarian...",
                    "lengthMenu": "Results :  _MENU_",
                    "emptyTable": "Tidak ada data yang tersedia di tabel",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    // "processing": "Memproses data...",
                    "processing": " ",
                },
                "stripeClasses": [],
                "lengthMenu": [7, 10, 20, 50],
                "pageLength": 10,
                "processing": true,
                "serverSide": true,
                // "ordering": false,
                "ajax": {
                    url: "{{ route('outbox.ssr') }}",
                    type: 'GET',
                },
                "columns": [
                    { data: null, orderable: false, searchable: false},
                    { data: 'no_agenda', orderable: false },
                    { data: 'tgl_buat', orderable: false },
                    { data: 'klasifikasi', orderable: false },
                    { data: 'berkas', orderable: false },
                    { data: 'kepada', orderable: false },
                    { data: 'perihal', orderable: false },
                    { data: 'option', orderable: false, searchable: false},
                ],
            });

            tb_inbox.on('processing.dt', function (e, settings, processing) {
                if (processing) {
                    $('#table-overlay').fadeIn();
                } else {
                    $('#table-overlay').fadeOut();
                }
            });
            tb_inbox.on('draw.dt', function () {
                // Reinitialize tooltips after each draw
                $('.bs-tooltip').tooltip();
                // auto numbering
                var PageInfo = $('#zero-config').DataTable().page.info();
                tb_inbox.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                });
            });
        });
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <script>
        @role(['administrator', 'umum'])
        function _delete(uid) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('outbox.destroy') }}",
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            uid: uid
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#zero-config').DataTable().ajax.reload(null, false);
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Surat keluar berhasil dihapus.'
                                });
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Gagal menghapus surat keluar.'
                                });
                            }
                        },
                        error: function() {
                            Toast.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan pada server.'
                            });
                        }
                    });
                }
            });
        }

        function printPdf(id) {
            window.open(`/surat-keluar/print-pdf/${id}?type=kartu`, '_blank')
        }

        function _detail(uid) {
            const data = JSON.parse(atob(uid));
            console.log(data)
            let header = `
                <div class="bs-stepper stepper-icons">
                    <div class="bs-stepper-header" role="tablist">
                        <div class="step" data-target="#withIconsStep-one">
                            <button type="button" class="step-trigger" role="tab" >
                                <span class="bs-stepper-circle"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></span>
                                <span class="bs-stepper-label">Step One</span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#withIconsStep-two">
                            <button type="button" class="step-trigger" role="tab"  >
                                <span class="bs-stepper-circle"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg></span>
                                <span class="bs-stepper-label">Step Two</span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step active" data-target="#withIconsStep-three">
                            <button type="button" class="step-trigger" role="tab"  >
                                <span class="bs-stepper-circle"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Step Three</span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="bs-stepper-content">
                        <div id="withIconsStep-one" class="content" role="tabpanel"></div>
                        <div id="withIconsStep-two" class="content" role="tabpanel"></div>
                        <div id="withIconsStep-three" class="content" role="tabpanel"></div>
                    </div>
                </div>
            `
            let detail = `
                <div class="col-12 mt-4">
                    <table class="table table-bordered table-hover" style="table-layout: fixed; border-collapse: collapse; width: 100%;">
                        <tr>
                            <td colspan="4">
                                <h3 class="text-center">DETAIL SURAT KELUAR</h3>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="20%"><label><b>No. Urut / Tahun</b></label></td>
                            <td class="text-wrap" style="width: 30%; white-space: normal !important;"><label>${data.no_agenda} / ${data.year}</label></td>
                            <td width="20%"><label><b>Kode</b></label></td>
                            <td style="white-space: normal; overflow-wrap: break-word; word-wrap: break-word; width: 30%;"><label>${data.id_klasifikasi}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Berkas</b></label></td>
                            <td><label>${data.nama_berkas}</label></td>
                            <td><label><b>Lampiran</b></label></td>
                            <td><label>${data.lampiran ?? '-'}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Perihal</b></label></td>
                            <td colspan="3"><label>${data.perihal}</label></td>
                        </tr>
                        <tr>
                            <td style="height: 100px; vertical-align: top; text-align: left;"><label><b>Isi</b></label></td>
                            <td colspan="3" style="height: 100px; vertical-align: top; text-align: left;"><label>${data.isi_surat}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Dari</b></label></td>
                            <td colspan="3"><label>${data.kepada}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Alamat</b></label></td>
                            <td colspan="3"><label>${data.wilayah}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Tgl. Surat</b></label></td>
                            <td><label>${data.tgl_surat}</label></td>
                            <td><label><b>No. Surat</b></label></td>
                            <td><label>${data.no_surat}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Tgl. Terima</b></label></td>
                            <td><label>${data.tgl_diterima}</label></td>
                            <td><label><b>Diteruskan?</b></label></td>
                            <td><label>${null}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Sifat</b></label></td>
                            <td><label>${data.sifat.nama_sifat}</label></td>
                            <td><label><b>Tindakan</b></label></td>
                            <td><label>${data.tindakan ?? '-'}</label></td>
                        </tr>
                        `    
                        
            detail += `<tr>
                            <td><label><b>File Arsip</b></label></td>
                            <td colspan="3"><label>${data.softcopy ? '<button class="btn btn-info bs-tooltip" title="Lihat dokumen" onclick="view_file(`'+data.cryptfile+'`)">Lihat</button>' : '-'}</label></td>
                        </tr>
                    </table>
                </div>
            `
            $('#detail').html(detail);
            $('#detailSurat').modal('show');
        }

        function view_file(file) {
            window.open(`/surat-keluar/lihat-file/${file}`, '_blank')
        }

        @endrole
    </script>
@endsection
