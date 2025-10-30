@extends('layouts.layout')

@section('title', 'Surat Masuk')


@section('css')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
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
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                                Daftar Surat Masuk
                            </h4>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-info mb-2 me-4 float-end" onclick="location.href='{{ route('inbox.create') }}'">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                <span class="btn-text-inner">Buat Surat Masuk</span>
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

                    <table id="zero-config" class="table dt-table-bordered" style="width:100%" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th style="width: 10%">No. Surat</th>
                                <th style="width: 10%">No. Agenda</th>
                                <th style="width: 10%">Klasifikasi</th>
                                <th style="width: 10%">Nama Berkas</th>
                                <th style="width: 10%">Instansi</th>
                                <th style="width: 20%">Isi</th>
                                <th style="width: 20%">Perihal</th>
                                <th style="width: 10%">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($inbox as $ibx)
                            <tr>
                                <td style="width: 10%">{{ $ibx->NOSURAT }}</td>
                                <td style="width: 10%">{{ $ibx->NOAGENDA }}</td>
                                <td style="width: 10%">{{ $ibx->SIFAT_SURAT }}</td>
                                <td style="width: 10%">{{ $ibx->NAMABERKAS }}</td>
                                <td style="width: 10%">{{ $ibx->WILAYAH }}</td>
                                <td style="width: 20%">{{ $ibx->ISI }}</td>
                                <td style="width: 20%">{{ $ibx->PERIHAL }}</td>
                                <td style="width: 10%">
                                    <div class="btn-group-vertical" role="group" aria-label="Second group">
                                        <a href="{{ route('inbox.show', Crypt::encryptString($ibx->NO)) }}" type="button" class="btn btn-outline-warning bs-tooltip" title="Edit Surat">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                        </a>
                                        <button type="button" class="btn btn-outline-primary bs-tooltip" title="Disposisi Surat">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                        </button>
                                        <button type="button" class="btn btn-outline-success bs-tooltip" title="Cetak Surat">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                        </button>
                                        <button type="button" class="btn btn-outline-info bs-tooltip" title="Tindak Lanjut">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                        </button>
                                        <button type="button" class="btn btn-danger bs-tooltip" title="Hapus Surat">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    
</div>
@endsection


@section('js')
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('templates/plugins/src/table/datatable/datatables.js') }}"></script>
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
                    url: "{{ route('inbox.ssr') }}",
                    type: 'GET',
                },
                "columns": [
                    { data: null, orderable: false, searchable: false},
                    { data: 'nomor', orderable: false },
                    { data: 'no_agenda', orderable: false },
                    { data: 'klasifikasi', orderable: false },
                    { data: 'berkas', orderable: false },
                    { data: 'wilayah', orderable: false },
                    { data: 'isi_surat', orderable: false },
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
@endsection
