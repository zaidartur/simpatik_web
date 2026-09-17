@extends('layouts.layout')

@section('title', 'Agenda')


@section('css')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">

    <link rel="stylesheet" type="text/css"
        href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">

    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('templates/plugins/src/flatpickr/flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('templates/plugins/css/light/flatpickr/custom-flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('templates/plugins/css/dark/flatpickr/custom-flatpickr.css') }}" rel="stylesheet" type="text/css">
    <!-- END PAGE LEVEL STYLES -->

    <style>
        table.dataTable td,
        table.dataTable th {
            white-space: normal !important;
            /* Allows text to wrap */
            word-wrap: break-word !important;
            /* Breaks long words if necessary */
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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                                        <line x1="22" y1="2" x2="11" y2="13"></line>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                    </svg>
                                    Daftar Agenda
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                    <div class="widget-content widget-content-area br-8 p-3">
                        <div class="row col-12 justify-content-between">
                            <div class="form-group col-md-3 col-sm-6 mb-2 row">
                                <label for="" class="col-5">Jenis Surat</label>
                                <div class="col-7">
                                    <select class="form-control form-control-sm bs-tooltip col-12" id="jenis" name="jenis"
                                        placeholder="Jenis Surat" title="Jenis Surat">
                                        <option value="" selected>Semua</option>
                                        <option value="Masuk">Masuk</option>
                                        <option value="Keluar">Keluar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-5 col-sm-6 mb-2 row">
                                <label for="rangeCalendar" class="col-4">Rentang Waktu</label>
                                <div class="col-8">
                                    <input id="rangeCalendar"
                                        class="form-control form-control-sm flatpickr flatpickr-input active" type="text"
                                        placeholder="Pilih Rentang Waktu" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 mb-2">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm" onclick="_filter()" id="btfilter">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor"
                                            stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                            class="css-i6dzq1">
                                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                        </svg>
                                        <span class="btn-text-inner">Filter</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="_resetFilter()"
                                        id="btreset" title="Reset Filter">
                                        <span class="btn-text-inner">Reset</span>
                                    </button>
                                    {{-- <button type="button" class="btn btn-secondary btn-sm" onclick="_print('dompdf')"
                                        id="btprint" title="Cetak PDF menggunakan Engine DomPDF">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor"
                                            stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                            class="css-i6dzq1">
                                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                            <path
                                                d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2">
                                            </path>
                                            <rect x="6" y="14" width="12" height="8"></rect>
                                        </svg>
                                        <span class="btn-text-inner">DomPDF</span>
                                    </button> --}}
                                    <button type="button" class="btn btn-primary btn-sm" onclick="_print('fpdf')"
                                        id="btprintfpdf" title="Cetak PDF Cepat & Hemat RAM menggunakan Engine Native FPDF">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor"
                                            stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                        </svg>
                                        <span class="btn-text-inner">FPDF</span>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm" onclick="_exportExcel()"
                                        id="btexcel" title="Export ke Excel">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="feather feather-file-text">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <polyline points="10 9 9 9 8 9"></polyline>
                                        </svg>
                                        <span class="btn-text-inner">Excel</span>
                                    </button>
                                </div>
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
                                    <th style="width: 6%; text-align: center;">No. Agenda</th>
                                    <th style="width: 10%; text-align: center;">Kepada</th>
                                    <th style="width: 10%; text-align: center;">
                                        Tgl. Kirim/ <br>
                                        Tgl. Surat/ <br>
                                        No. Surat
                                    </th>
                                    <th style="width: 15%; text-align: center;">
                                        Klasifikasi/ <br>
                                        Ket. JRA/ <br>
                                        Isi Informasi
                                    </th>
                                    <th style="width: 15%; text-align: center;">Dari / Kepada</th>

                                    @role(['administrator'])
                                    <th style="width: 12%; text-align: center;">Disposisi Sekda</th>
                                    <th style="width: 10%; text-align: center;">Disposisi Wakil Bupati</th>
                                    <th style="width: 12%; text-align: center;">Disposisi Bupati</th>
                                    @endrole

                                    @role(['umum', 'setda'])
                                    <th style="width: 22%; text-align: center;">Disposisi Sekda</th>
                                    <th style="width: 22%; text-align: center;">Disposisi Bupati</th>
                                    @endrole

                                    @role(['wabup'])
                                    <th style="width: 34%; text-align: center;">Disposisi Wakil Bupati</th>
                                    @endrole

                                    @role(['bupati'])
                                    <th style="width: 34%; text-align: center;">Disposisi Bupati</th>
                                    @endrole
                                </tr>
                            </thead>
                            <tbody></tbody>
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
    <script src="{{ asset('templates/plugins/src/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/flatpickr/l10n/id.js') }}"></script>
    <script>
        let tb_inbox;
        let startDate = null;
        let endDate = null;
        let f3;

        $(document).ready(function () {
            f3 = flatpickr(document.getElementById('rangeCalendar'), {
                mode: "range",
                locale: "id",
                dateFormat: "Y-m-d",
                onClose: function (selectedDates) {
                    if (selectedDates.length === 2) {
                        startDate = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                        endDate = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
                    } else if (selectedDates.length === 1) {
                        startDate = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                        endDate = startDate;
                    } else {
                        startDate = null;
                        endDate = null;
                    }
                }
            });

            tb_inbox = $('#zero-config').DataTable({
                "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                    "<'table-responsive'tr>" +
                    "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
                "language": {
                    "paginate": {
                        "previous": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                        "next": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                    },
                    "info": "Menampilkan data _START_ sampai _END_ dari _TOTAL_ entri total",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri total",
                    "search": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    "searchPlaceholder": "Pencarian...",
                    "lengthMenu": "Results :  _MENU_",
                    "emptyTable": "Tidak ada data yang tersedia di tabel",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "processing": " ",
                },
                "stripeClasses": [],
                "lengthMenu": [7, 10, 20, 50],
                "pageLength": 10,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('report.agenda.ssr') }}",
                    type: 'GET',
                    data: function (d) {
                        d.jenis = $('#jenis').val();
                        if (f3 && f3.selectedDates && f3.selectedDates.length === 2) {
                            d.start_date = flatpickr.formatDate(f3.selectedDates[0], 'Y-m-d');
                            d.end_date = flatpickr.formatDate(f3.selectedDates[1], 'Y-m-d');
                        } else if (f3 && f3.selectedDates && f3.selectedDates.length === 1) {
                            d.start_date = flatpickr.formatDate(f3.selectedDates[0], 'Y-m-d');
                            d.end_date = d.start_date;
                        } else {
                            d.start_date = startDate || '';
                            d.end_date = endDate || '';
                        }
                    }
                },
                "columns": [
                    { data: 'no_agenda', orderable: false },
                    { data: 'kepada', orderable: false },
                    { data: 'row3', orderable: false },
                    { data: 'row4', orderable: false },
                    { data: 'dari', orderable: false },
                    @role(['administrator'])
                                                                                                    { data: 'sekda', orderable: false },
                    { data: 'wakil', orderable: false },
                    { data: 'bupati', orderable: false },
                    @endrole
                    @role(['setda', 'umum'])
                                                                                                    { data: 'sekda', orderable: false },
                    { data: 'bupati', orderable: false },
                    @endrole
                    @role(['wabup'])
                                                                                                    { data: 'wakil', orderable: false },
                    @endrole
                    @role(['bupati'])
                                                                                                    { data: 'bupati', orderable: false },
                    @endrole
                ],
            });

            tb_inbox.on('processing.dt', function (e, settings, processing) {
                if (processing) {
                    $('#table-overlay').fadeIn();
                } else {
                    $('#table-overlay').fadeOut();
                }
            });
        });
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <script>
        function _filter() {
            let rangeVal = $('#rangeCalendar').val();
            if (!rangeVal) {
                startDate = null;
                endDate = null;
                if (f3) f3.clear();
            } else if (f3 && f3.selectedDates && f3.selectedDates.length > 0) {
                if (f3.selectedDates.length === 2) {
                    startDate = flatpickr.formatDate(f3.selectedDates[0], 'Y-m-d');
                    endDate = flatpickr.formatDate(f3.selectedDates[1], 'Y-m-d');
                } else {
                    startDate = flatpickr.formatDate(f3.selectedDates[0], 'Y-m-d');
                    endDate = startDate;
                }
            } else {
                let dates = rangeVal.split(/ s\/d | sampai | to | - /);
                if (dates.length === 2) {
                    startDate = dates[0].trim();
                    endDate = dates[1].trim();
                } else {
                    startDate = rangeVal.trim();
                    endDate = rangeVal.trim();
                }
            }

            tb_inbox.ajax.reload();
        }

        function _resetFilter() {
            $('#jenis').val('');
            $('#rangeCalendar').val('');
            if (f3) f3.clear();
            startDate = null;
            endDate = null;
            tb_inbox.ajax.reload();
        }

        function _print(engine = 'dompdf') {
            const jenis = $('#jenis').val();
            let sDate = startDate || '';
            let eDate = endDate || '';
            if (f3 && f3.selectedDates && f3.selectedDates.length > 0) {
                sDate = flatpickr.formatDate(f3.selectedDates[0], 'Y-m-d');
                eDate = f3.selectedDates.length === 2 ? flatpickr.formatDate(f3.selectedDates[1], 'Y-m-d') : sDate;
            } else if ($('#rangeCalendar').val()) {
                let rangeVal = $('#rangeCalendar').val();
                let dates = rangeVal.split(/ s\/d | sampai | to | - /);
                if (dates.length === 2) {
                    sDate = dates[0].trim();
                    eDate = dates[1].trim();
                } else {
                    sDate = rangeVal.trim();
                    eDate = rangeVal.trim();
                }
            }

            if (!sDate || !eDate) {
                alert("Rentang waktu harus dipilih terlebih dahulu untuk mencetak laporan agenda.");
                return;
            }

            // Validasi batas maksimal 31 hari
            const d1 = new Date(sDate);
            const d2 = new Date(eDate);
            const diffTime = Math.abs(d2 - d1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            if (diffDays > 31) {
                alert("Rentang waktu cetak PDF maksimal 31 hari (1 bulan). Silakan gunakan tombol Excel untuk rentang waktu yang lebih panjang.");
                return;
            }

            const endpoint = (engine === 'fpdf') ? '{{ route('report.agenda.print_fpdf') }}' : '{{ route('report.agenda.print') }}';
            window.open(`${endpoint}?start_date=${sDate}&end_date=${eDate}&jenis=${jenis}`, '_blank');
        }

        function _exportExcel() {
            const jenis = $('#jenis').val();
            let sDate = startDate || '';
            let eDate = endDate || '';
            if (f3 && f3.selectedDates && f3.selectedDates.length > 0) {
                sDate = flatpickr.formatDate(f3.selectedDates[0], 'Y-m-d');
                eDate = f3.selectedDates.length === 2 ? flatpickr.formatDate(f3.selectedDates[1], 'Y-m-d') : sDate;
            } else if ($('#rangeCalendar').val()) {
                let rangeVal = $('#rangeCalendar').val();
                let dates = rangeVal.split(/ s\/d | sampai | to | - /);
                if (dates.length === 2) {
                    sDate = dates[0].trim();
                    eDate = dates[1].trim();
                } else {
                    sDate = rangeVal.trim();
                    eDate = rangeVal.trim();
                }
            }

            window.open(`{{ route('report.agenda.export') }}?start_date=${sDate}&end_date=${eDate}&jenis=${jenis}`, '_blank');
        }
    </script>
@endsection