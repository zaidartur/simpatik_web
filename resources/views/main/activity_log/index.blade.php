@extends('layouts.layout')

@section('title', 'Audit Trail / Log Aktivitas')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/dark/table/datatable/dt-global_style.css') }}">
    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/src/flatpickr/flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('templates/plugins/css/light/flatpickr/custom-flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('templates/plugins/css/dark/flatpickr/custom-flatpickr.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">

            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="widget-content widget-content-area br-8 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                            Audit Trail / Log Aktivitas Sistem
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="widget-content widget-content-area br-8 p-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label mb-1">Rentang Tanggal</label>
                            <input id="rangeCalendar" class="form-control form-control-sm flatpickr" type="text" placeholder="Pilih Rentang Tanggal" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Modul</label>
                            <select id="filterModule" class="form-select form-select-sm">
                                <option value="">Semua Modul</option>
                                @foreach($modules as $mod)
                                    <option value="{{ $mod }}">{{ strtoupper(str_replace('_', ' ', $mod)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Aksi</label>
                            <select id="filterAction" class="form-select form-select-sm">
                                <option value="">Semua Aksi</option>
                                @foreach($actions as $act)
                                    <option value="{{ $act }}">{{ strtoupper($act) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1">Pengguna</label>
                            <select id="filterUser" class="form-select form-select-sm">
                                <option value="">Semua Pengguna</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->uuid }}">{{ $u->nama_lengkap }} ({{ $u->username }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="applyFilter()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                                Filter
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilter()">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="widget-content widget-content-area br-8 p-3">
                    <table id="log-table" class="table dt-table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 15%">Waktu</th>
                                <th style="width: 15%">Pengguna</th>
                                <th style="width: 10%">Modul</th>
                                <th style="width: 10%">Aksi</th>
                                <th style="width: 30%">Deskripsi Aktivitas</th>
                                <th style="width: 10%">IP Address</th>
                                <th style="width: 5%">Detail</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Detail Log -->
<div class="modal fade" id="modalLogDetail" tabindex="-1" aria-labelledby="modalLogDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLogDetailLabel">Detail Riwayat Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="fw-bold">Deskripsi:</h6>
                    <p id="logDesc" class="text-muted"></p>
                </div>
                <div class="mb-3">
                    <h6 class="fw-bold">User Agent:</h6>
                    <code id="logUserAgent" class="d-block p-2 bg-light text-dark rounded small text-break"></code>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold text-danger">Nilai Lama (Sebelum):</h6>
                        <pre id="logOldValues" class="p-2 bg-light text-dark rounded small" style="max-height: 250px; overflow-y: auto;"></pre>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold text-success">Nilai Baru (Sesudah):</h6>
                        <pre id="logNewValues" class="p-2 bg-light text-dark rounded small" style="max-height: 250px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('templates/plugins/src/table/datatable/datatables.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/flatpickr/flatpickr.js') }}"></script>

    <script>
        let logTable;
        let dateRangePicker;
        let startDate = null;
        let endDate = null;

        $(document).ready(function () {
            dateRangePicker = flatpickr('#rangeCalendar', {
                mode: 'range',
                dateFormat: 'Y-m-d',
                onClose: function(selectedDates) {
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

            logTable = $('#log-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("audit.ssr") }}',
                    data: function (d) {
                        d.module = $('#filterModule').val();
                        d.action = $('#filterAction').val();
                        d.user_uuid = $('#filterUser').val();
                        d.tgl_mulai = startDate;
                        d.tgl_selesai = endDate;
                    }
                },
                columns: [
                    { data: 'no', orderable: false, searchable: false },
                    { data: 'created_at', orderable: false },
                    { data: 'user', orderable: false },
                    { data: 'module', orderable: false },
                    { data: 'action', orderable: false },
                    { data: 'description', orderable: false },
                    { data: 'ip_address', orderable: false },
                    { data: 'details', orderable: false, searchable: false },
                ],
                dom: "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                     "<'table-responsive'tr>" +
                     "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
                language: {
                    paginate: { 
                        previous: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', 
                        next: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' 
                    },
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri total",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    search: "Cari:",
                    zeroRecords: "Tidak ada data aktivitas ditemukan."
                }
            });
        });

        function applyFilter() {
            logTable.ajax.reload();
        }

        function resetFilter() {
            $('#filterModule').val('');
            $('#filterAction').val('');
            $('#filterUser').val('');
            if (dateRangePicker) {
                dateRangePicker.clear();
            }
            startDate = null;
            endDate = null;
            logTable.ajax.reload();
        }

        function viewLogDetail(data) {
            $('#logDesc').text(data.description);
            $('#logUserAgent').text(data.user_agent || '-');
            $('#logOldValues').text(data.old_values ? JSON.stringify(data.old_values, null, 2) : 'Tidak ada perubahan nilai sebelumnya.');
            $('#logNewValues').text(data.new_values ? JSON.stringify(data.new_values, null, 2) : 'Tidak ada perubahan nilai baru.');
            $('#modalLogDetail').modal('show');
        }
    </script>
@endsection
