@extends('layouts.layout')

@section('title', 'Agenda')


@section('css')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">

    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('templates/plugins/src/flatpickr/flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('templates/plugins/css/light/flatpickr/custom-flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('templates/plugins/css/dark/flatpickr/custom-flatpickr.css') }}" rel="stylesheet" type="text/css">
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
                                Daftar Agenda
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="widget-content widget-content-area br-8 p-3">
                    <div class="row col-12 justify-content-evenly">
                        <div class="form-group col-md-3 col-sm-6 mb-2 row">
                            <label for="" class="col-5">Jenis Surat</label>
                            <div class="col-7">
                                <select class="form-control form-control-sm bs-tooltip col-12" id="jenis" name="jenis" placeholder="Jenis Surat" title="Jenis Surat">
                                    <option value="" selected>Semua</option>
                                    <option value="Masuk">Masuk</option>
                                    <option value="Keluar">Keluar</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-sm-12 mb-2 row">
                            <label for="rangeCalendar" class="col-3">Rentang Waktu</label>
                            <div class="col-9">
                                <input id="rangeCalendar" class="form-control form-control-sm flatpickr flatpickr-input active" type="text" placeholder="Pilih Rentang Waktu" readonly="readonly">
                            </div>
                        </div>
                        {{-- <div class="form-group col-md-3 col-sm-6 mb-2 row">
                            <label for="" class="col-3">Bulan</label>
                            <div class="col-9">
                                <select class="form-control form-control-sm bs-tooltip col-12" id="bulan" name="bulan" placeholder="Bulan" title="Bulan">
                                    <option value="" selected>Semua</option>
                                    @for ($i = 1; $i < 13; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-3 col-sm-6 mb-2 row">
                            <label for="" class="col-3">Tahun</label>
                            <div class="col-9">
                                <select class="form-control form-control-sm bs-tooltip col-12" id="tahun" name="tahun" placeholder="Tahun" title="Tahun">
                                    @foreach ($years as $y => $year)
                                        <option value="{{ $year->TAHUN }}" {{ $y == 0 ? 'selected' : '' }}>{{ $year->TAHUN }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <div class="col-md-3 col-sm-6 mb-2 row">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-info" onclick="_filter()" id="btfilter">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                                    <span class="btn-text-inner">Filter</span>
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="_print()" id="btprint">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                    <span class="btn-text-inner">Cetak</span>
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
    <script src="https://unpkg.com/flatpickr@4.6.13/dist/l10n/id.js"></script>
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
                    url: "{{ route('report.agenda.ssr') }}",
                    type: 'GET',
                },
                "columns": [
                    // { data: null, orderable: false, searchable: false},
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
            // tb_inbox.on('draw.dt', function () {
            //     // Reinitialize tooltips after each draw
            //     $('.bs-tooltip').tooltip();
            //     // auto numbering
            //     var PageInfo = $('#zero-config').DataTable().page.info();
            //     tb_inbox.column(0, { page: 'current' }).nodes().each(function (cell, i) {
            //         cell.innerHTML = i + 1 + PageInfo.start;
            //     });
            // });

            var f3 = flatpickr(document.getElementById('rangeCalendar'), {
                mode: "range",
                locale: "id",
                dateFormat: "Y-m-d",
            });
        });

        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('new-sppd');
            var invalid = $('.new-sppd .invalid-feedback');
            var fcontrol = $('.new-sppd .form-control');

            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                    // invalid.css('display', 'block');
                } else {
                    // invalid.css('display', 'none');
                    form.classList.add('was-validated');
                }

                fcontrol.each(function() {
                    var $input = $(this);

                    if ($(this).val().trim() === '') {
                        // You can add a class, display an error message, etc.
                        $input.next('.invalid-feedback').css('display', 'block');
                        $(this).removeClass('is-valid');
                        $(this).addClass('is-invalid');
                    } else {
                        // This input field is not empty
                        $input.next('.invalid-feedback').css('display', 'none');
                        $(this).addClass('is-valid');
                        $(this).removeClass('is-invalid');
                    }
                });
            }, false);
            });

        }, false);
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <script>
        async function _filter() {
            const year = $('#tahun').val()
            const month = $('#bulan').val()
            const jenis = $('#jenis').val()
            let tb_inbox = $('#zero-config').DataTable()
            let date_range = $('#rangeCalendar').val()

            $('#btfilter').attr('disabled', '')
            $('#btprint').attr('disabled', '')
            await new Promise(resolve => {
                let startDate = ''
                let endDate = ''
                if (date_range && date_range.includes(' - ')) {
                    const dates = date_range.split(' - ')
                    startDate = dates[0]
                    endDate = dates[1]
                }
                // tb_inbox.ajax.url(`{{ route('report.agenda.ssr') }}?jenis=${jenis}&bulan=${month}&year=${year}`).load(() => {
                tb_inbox.ajax.url(`{{ route('report.agenda.ssr') }}?jenis=${jenis}&start_date=${startDate}&end_date=${endDate}`).load(() => {
                    $('#btfilter').removeAttr('disabled')
                    $('#btprint').removeAttr('disabled')
                    resolve()
                });
            });
        }

        function _print() {
            const year = $('#tahun').val()
            const month = $('#bulan').val()
            const jenis = $('#jenis').val()
            let date_range = $('#rangeCalendar').val()
            let startDate = ''
            let endDate = ''
            if (date_range && date_range.includes(' - ')) {
                const dates = date_range.split(' - ')
                startDate = dates[0]
                endDate = dates[1]
            }

            // window.open(`/laporan/print-agenda?tahun=${year}&bulan=${month}&jenis=${jenis}`, '_blank')
            window.open(`/laporan/print-agenda?start_date=${startDate}&end_date=${endDate}&jenis=${jenis}`, '_blank')
        }
    </script>
@endsection