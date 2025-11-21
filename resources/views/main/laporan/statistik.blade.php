@extends('layouts.layout')

@section('title', 'Statistik')


@section('css')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/dark/table/datatable/dt-global_style.css') }}">

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
                        <div class="col-12">
                            <h4 class="">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"></path></svg>
                                Tabel Statistik
                            </h4>
                        </div>
                        {{-- <div class="col-6">
                            <button class="btn btn-success mb-2 me-4 float-end" id="bt_new" onclick="_new()">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                <span class="btn-text-inner">Tambah Instansi</span>
                            </button>
                        </div> --}}
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="widget-content widget-content-area br-8" style="position: relative;">
                    <div class="table-overlay" id="table-overlay">
                        <div class="spinner"></div>
                    </div>

                    <div class="col-12 table-responsive">
                    <table id="zero-config" class="table dt-table-hover table-striped table-bordered" style="width:100%" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 40%">Bulan</th>
                                <th style="width: 20%">Surat Masuk</th>
                                <th style="width: 20%">Surat Keluar</th>
                                <th style="width: 20%">Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
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
            let year = '{{ $last->tahun }}'
            let tb_stat = $('#zero-config').DataTable({
                "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'f><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'>>>" +
                        "<'table-responsive mb-5'tr>" ,
                        // "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
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
                // "lengthMenu": [12, 24],
                "pageLength": 20,
                "processing": true,
                "serverSide": true,
                "ordering": false,
                "ajax": {
                    url: `{{ route('report.statistik.ssr') }}?tahun=${year}`,
                    type: 'GET',
                },
                "columns": [
                    { data: 'bulan', name: 'bulan' },
                    { data: 'surat_masuk', name: 'surat_masuk', className: 'text-center' },
                    { data: 'surat_keluar', name: 'surat_keluar', className: 'text-center' },
                    { data: 'total', name: 'total', className: 'text-center' },
                ],
            });

            tb_stat.on('processing.dt', function (e, settings, processing) {
                if (processing) {
                    $('#table-overlay').fadeIn();
                } else {
                    $('#table-overlay').fadeOut();
                }
            });
        });

        setTimeout(function () {
            let filter = $('#zero-config_filter');

            // Remove the default search input
            filter.find('input[type=search]').remove();
            filter.find('svg').remove();

            // Insert your custom select
            filter.append(`
                <div class="row mb-0">
                    <label for="customSearch" class="col-sm-4 col-form-label">Tahun</label>
                    <div class="col-sm-8">
                        <select id="customSearch" class="form-control form-select">
                            @foreach ($tahun as $t => $th)
                                <option value="{{ $th->tahun }}" {{ $t == 0 ? 'selected' : '' }}>{{ $th->tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            `);

            // Apply search on change
            $('#customSearch').on('change', function (e) {
                console.log(this.value, e);
                // tb_stat.search(this.value).draw();
                _year(this.value);
            });

        }, 500);
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <script>
        function _year(elem) {
            // let year = $(elem).val();
            let tb_stat = $('#zero-config').DataTable();
            tb_stat.ajax.url(`{{ route('report.statistik.ssr') }}?tahun=${elem}`).load();
        }
    </script>
@endsection
