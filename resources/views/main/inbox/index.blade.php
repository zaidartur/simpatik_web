@extends('layouts.layout')

@section('title', 'Surat Masuk')


@section('css')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/dark/table/datatable/dt-global_style.css') }}">

    <link href="{{ asset('templates/plugins/css/light/loaders/custom-loader.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/dark/loaders/custom-loader.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/stepper/bsStepper.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/stepper/custom-bsStepper.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/dark/stepper/custom-bsStepper.css') }}">
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
                    <div class="row justify-content-space-between" style="align-items: center;">
                        <div class="col-3">
                            <h4 class="">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                                Daftar Surat Masuk
                            </h4>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row justify-content-center">
                                        <div class="col-auto mb-2">
                                            <select class="form-control form-control-sm bs-tooltip" id="klasifikasi" name="klasifikasi" placeholder="Sifat Surat" title="Sifat Surat">
                                                <option value="" selected>Semua</option>
                                                <option value="Biasa">Biasa</option>
                                                <option value="Segera">Segera</option>
                                                <option value="Penting">Penting</option>
                                                <option value="Rahasia">Rahasia</option>
                                            </select>
                                        </div>
                                        <div class="col-auto me-2 mb-2">
                                            <select name="tahun" id="tahun" class="form-control form-control-sm bs-tooltip" placeholder="Tahun Arsip" title="Tahun Arsip">
                                                @foreach ($years as $y => $year)
                                                    <option value="{{ $year->TAHUN }}" {{ $y == 0 ? 'selected' : '' }}>{{ $year->TAHUN }}</option>
                                                @endforeach
                                                <option value="">Semua</option>
                                            </select>
                                        </div>
                                        <div class="col-auto mb-2">
                                            <select class="form-control form-control-sm bs-tooltip" id="posisi" name="posisi" placeholder="Posisi Surat" title="Posisi Surat">
                                                <option value="" selected>Semua</option>
                                                @foreach ($posisi as $pos)
                                                    <option value="{{ $pos }}">{{ $pos }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-auto mb-2">
                                            <button class="btn btn-warning is-active" onclick="_filter()">
                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                                                <span class="btn-text-inner">Filter</span>
                                            </button>
                                            <button class="btn btn-warning is-loading d-none" type="button" disabled>
                                                <div class="spinner-border text-white me-2 align-self-center loader-sm "></div> Memproses
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            @can ('input surat masuk')
                            <button class="btn btn-info mb-2 me-4 float-end" onclick="location.href='{{ route('inbox.create') }}'">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                <span class="btn-text-inner">Buat Surat Masuk</span>
                            </button>
                            @endcan
                            <button class="btn btn-secondary is-status mb-2 me-4 float-end" value="unfinish" onclick="_disposisi()">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                <span class="btn-text-inner">Belum Ditanggapi</span>
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
                                <th style="width: 5%">#</th>
                                <th style="width: 10%">No. Surat</th>
                                <th style="width: 10%">No. Agenda</th>
                                <th style="width: 10%">Klasifikasi</th>
                                <th style="width: 10%">Nama Berkas</th>
                                <th style="width: 15%">Instansi</th>
                                {{-- <th style="width: 20%">Isi</th> --}}
                                <th style="width: 20%">Perihal</th>
                                <th style="width: 10%">Posisi</th>
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
    {{-- <script src="{{ asset('templates/plugins/src/stepper/custom-bsStepper.min.js') }}"></script> --}}

    <script>
        $(document).ready(function() {
            let klasifikasi = $('#klasifikasi').val();
            let tahun = $('#tahun').val();
            let posisi = $('#posisi').val();
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
                "createdRow": function(row, data, dataIndex) {
                    // $(row).css('vertical-align', 'top');
                    // $(row).addClass('bg-info')
                },
                "stripeClasses": [],
                "lengthMenu": [7, 10, 20, 50],
                "pageLength": 10,
                "processing": true,
                "serverSide": true,
                // "ordering": false,
                "ajax": {
                    url: `{{ route('inbox.ssr') }}?klasifikasi=${klasifikasi}&tahun=${tahun}&posisi=${posisi}`,
                    type: 'GET',
                },
                "columns": [
                    { data: null, orderable: false, searchable: false},
                    { data: 'nomor', orderable: false },
                    { data: 'no_agenda', orderable: false },
                    { data: 'klasifikasi', orderable: false },
                    { data: 'berkas', orderable: false },
                    { data: 'wilayah', orderable: false },
                    // { data: 'isi_surat', orderable: false },
                    { data: 'perihal', orderable: false },
                    { data: 'posisi', orderable: false, render: function(data, type, row) {
                        const clr = data === 'Sekretaris Daerah' ? 'info' : (data === 'Wakil Bupati' ? 'secondary' : (data === 'Bupati' ? 'primary' : ''))
                        return `<b class="text-${clr}">${data}</b>`;
                    }},
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

        function printPdf(id) {
            window.open(`/surat-masuk/print-pdf/${id}`, '_blank')
        }
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
                        url: `{{ route('inbox.destroy') }}`,
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
        @endrole

        async function _filter() {
            let klasifikasi = $('#klasifikasi').val();
            let tahun = $('#tahun').val();
            let posisi = $('#posisi').val();
            let tb_inbox = $('#zero-config').DataTable();

            $('.is-active').addClass('d-none');
            $('.is-loading').removeClass('d-none');
            $('.is-status').attr('disabled', '')
            await new Promise(resolve => {
                tb_inbox.ajax.url(`{{ route('inbox.ssr') }}?klasifikasi=${klasifikasi}&tahun=${tahun}&posisi=${posisi}`).load(() => {
                    $('.is-loading').addClass('d-none');
                    $('.is-active').removeClass('d-none');
                    $('.is-status').removeAttr('disabled');

                    $('.is-status').html(`<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg><span class="btn-text-inner">Belum Ditanggapi</span>`);
                    $('.is-status').val('unfinish')
                    $('.is-status').attr('onclick', '_disposisi()')
                    resolve()
                });
            });
        }

        async function _disposisi() {
            let klasifikasi = $('#klasifikasi').val();
            let tahun = $('#tahun').val();
            let posisi = $('#posisi').val();
            let tb_inbox = $('#zero-config').DataTable();

            $('.is-status').html(`<div class="spinner-border text-white me-2 align-self-center loader-sm "></div> Memproses`);
            $('.is-status').attr('disabled', '')
            $('.is-active').attr('disabled', '')
            await new Promise(resolve => {
                tb_inbox.ajax.url(`{{ route('inbox.ssr') }}?klasifikasi=${klasifikasi}&tahun=${tahun}&posisi=${posisi}&status=${$('.is-status').val() === 'unfinish' ? 'disposisi' : ''}`).load(() => {
                    if ($('.is-status').val() === 'unfinish') {
                        $('.is-status').html(`<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg><span class="btn-text-inner">Semua Data</span>`);
                        $('.is-status').val('all')
                        // $('.is-status').attr('onclick', '_reset()')
                    } else {
                        $('.is-status').html(`<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg><span class="btn-text-inner">Belum Ditanggapi</span>`);
                        $('.is-status').val('unfinish')
                        // $('.is-status').attr('onclick', '_disposisi()')
                    }
                    $('.is-status').removeAttr('disabled')
                    $('.is-active').removeAttr('disabled')
                    resolve()
                });
            });
        }

        @role(['administrator', 'setda'])
        function followUp(uid) {
            Swal.fire({
                title: 'Tindak Lanjut Surat',
                // text: 'Surat akan ditandai sebagai telah ditanggapi (selesai) setelah Anda mengonfirmasi tindakan ini.',
                html: `
                    <select id="tujuan" class="swal2-select" style="width: 80%; margin: auto;">
                        <option value="" disabled selected>Pilih Tindakan</option>
                        <option value="Wakil Bupati">Wakil Bupati</option>
                        <option value="Bupati">Bupati</option>
                    </select>
                `,
                icon: 'question',
                focusConfirm: false,
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonText: 'Konfirmasi',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showLoaderOnConfirm: true,
                preConfirm: async() => {
                    const forward = document.getElementById('tujuan').value;
                    if (!forward) {
                        Swal.showValidationMessage('Mohon untuk memilih tujuan');
                        return false;
                    }

                    try {
                        return $.ajax({
                            url: `{{ route('inbox.forward') }}`,
                            type: "POST",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                uid: uid,
                                tujuan: forward,
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    return response;
                                } else {
                                    Swal.showValidationMessage(response.message || 'Gagal menandai surat sebagai telah ditanggapi.');
                                    return false;
                                }
                            },
                            error: function() {
                                Swal.showValidationMessage('Terjadi kesalahan pada sistem.');
                                return false;
                            }
                        });
                    } catch(error) {
                        Swal.showValidationMessage('Terjadi kesalahan pada sistem.');
                        return false;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value && result.value.status === 'success') {
                        $('#zero-config').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: result.value.message || 'Surat berhasil ditandai sebagai telah ditanggapi.',
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: result.value.message || 'Gagal menandai surat sebagai telah ditanggapi.'
                        });
                    }
                }
            });
        }
        @endrole

        @role(['administrator', 'wabup', 'bupati', 'setda'])
        function _reply(uid) {
            Swal.fire({
                title: 'Tanggapi Surat',
                html: `
                    <textarea id="notes" class="swal2-textarea" placeholder="Catatan" style="width: 80%;"></textarea>
                `,
                focusConfirm: false,
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonText: 'Konfirmasi',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showLoaderOnConfirm: true,
                preConfirm: async() => {
                    const forward = document.getElementById('notes').value;
                    if (!forward) {
                        Swal.showValidationMessage('Mohon untuk mengisi catatan!');
                        return false;
                    }

                    try {
                        return await $.ajax({
                            url: `{{ route('inbox.reply') }}`,
                            type: "POST",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                uid: uid,
                                tujuan: $('#tujuan').val(),
                                notes: $('#notes').val(),
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    return response;
                                    // $('#zero-config').DataTable().ajax.reload(null, false);
                                    // Swal.fire({
                                    //     icon: 'success',
                                    //     title: 'Surat berhasil ditindaklanjuti.',
                                    // });
                                } else {
                                    Swal.showValidationMessage(response.message || 'Gagal menindaklanjuti surat.');
                                    return false;
                                }
                            },
                            error: function() {
                                Swal.showValidationMessage('Terjadi kesalahan pada sistem.');
                                return false;
                            }
                        })
                    } catch(error) {
                        Swal.showValidationMessage('Terjadi kesalahan pada sistem.');
                        return false;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#zero-config').DataTable().ajax.reload(null, false);
                    if (result.value && result.value.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: result.value.message || 'Surat berhasil ditindaklanjuti.',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: result.value.message || 'Gagal menindaklanjuti surat.',
                        });
                    }
                }
            });
        }
        @endrole

        @role(['setda', 'wabup', 'bupati'])
        // Initialize the stepper
        // var stepperWizardIcon = document.querySelector('.stepper-icons');
        // var stepperIcon = new Stepper(stepperWizardIcon, {
        //     animation: true
        // })
        // var lastLoc = 0;

        $('#detailSurat').on('shown.bs.modal', function (event) {
            // stepperIcon.goTo(lastLoc);
        });

        function viewSurat(uid) {
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
                    <table class="table table-bordered table-hover" width="100%">
                        <tr>
                            <td colspan="4">
                                <h3 class="text-center">DETAIL SURAT MASUK</h3>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td><label><b>No. Urut</b></label></td>
                            <td><label>${data.NOAGENDA}</label></td>
                            <td><label><b>Kode</b></label></td>
                            <td><label>${data.KLAS3}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Berkas</b></label></td>
                            <td colspan="3"><label>${data.NAMABERKAS}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Perihal</b></label></td>
                            <td><label>${data.PERIHAL}</label></td>
                            <td><label><b>Lampiran</b></label></td>
                            <td><label>${data.lampiran ?? '-'}</label></td>
                        </tr>
                        <tr>
                            <td style="height: 100px; vertical-align: top; text-align: left;"><label><b>Isi</b></label></td>
                            <td colspan="3" style="height: 100px; vertical-align: top; text-align: left;"><label>${data.ISI}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Dari</b></label></td>
                            <td colspan="3"><label>${data.drkpd}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Alamat</b></label></td>
                            <td colspan="3"><label>${data.NAMAKOTA}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Tgl. Surat</b></label></td>
                            <td><label>${data.TGLSURAT}</label></td>
                            <td><label><b>No. Surat</b></label></td>
                            <td><label>${data.NOSURAT}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Tgl. Terima</b></label></td>
                            <td><label>${data.TGLTERIMA}</label></td>
                            <td><label><b>Diteruskan?</b></label></td>
                            <td><label>${data.NAMAUP ? 'Ya' : 'Tidak'}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>Sifat</b></label></td>
                            <td><label>${data.SIFAT_SURAT}</label></td>
                            <td><label><b>Tindakan</b></label></td>
                            <td><label>${data.BALAS ?? '-'}</label></td>
                        </tr>`
            if (data.NAMAUP && data.NAMAUP.length > 0) {
                detail += `
                        <tr>
                            <td><label><b>Diterukan Kpd.</b></label></td>
                            <td><label>${data.NAMAUP}</label></td>
                            <td><label><b>Tanggal Diteruskan</b></label></td>
                            <td><label>${data.TGLTERUS}</label></td>
                        </tr>
                `
            }    
                        
            detail += `<tr>
                            <td><label><b>Status Surat</b></label></td>
                            <td><label>${data.statussurat}</label></td>
                            <td><label><b>Posisi Terakhir Surat</b></label></td>
                            <td><label>${data.Posisi}</label></td>
                        </tr>
                        <tr>
                            <td><label><b>File Arsip</b></label></td>
                            <td colspan="3"><label>${data.pdf ?? '-'}</label></td>
                        </tr>
                    </table>
                </div>
            `
            $('#detail').html(detail);
            // lastLoc = 3;
            $('#detailSurat').modal('show');
        }
        @endrole
    </script>
@endsection
