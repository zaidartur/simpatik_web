@extends('layouts.layout')

@section('title', 'Tindak Lanjut')


@section('css')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">

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
                                Laporan Tindak Lanjut
                            </h4>
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
                                <th style="width: 5%; text-align: center;" rowspan="2">#</th>
                                <th style="width: 10%; text-align: center;" rowspan="2">No. Surat</th>
                                <th style="width: 10%; text-align: center;" rowspan="2">Nama Berkas</th>
                                <th style="width: 15%; text-align: center;" rowspan="2">Instansi</th>
                                <th style="width: 20%; text-align: center;" rowspan="2">Perihal</th>
                                <th style="width: 20%; text-align: center;" colspan="3">Tindak Lanjut</th>
                                <th style="width: 10%; text-align: center;" rowspan="2">Posisi</th>
                                <th style="width: 10%; text-align: center;" rowspan="2">Status</th>
                            </tr>
                            <tr>
                                <th>Sekda</th>
                                <th>Wabup</th>
                                <th>Bupati</th>
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
                    url: "{{ route('report.next.ssr') }}",
                    type: 'GET',
                },
                "columns": [
                    { data: null, orderable: false, searchable: false},
                    { data: 'nomor', orderable: false },
                    { data: 'berkas', orderable: false },
                    { data: 'kepada', orderable: false },
                    { data: 'perihal', orderable: false },
                    { data: 'sekda', orderable: false },
                    { data: 'wakil', orderable: false },
                    { data: 'bupati', orderable: false },
                    { data: 'posisi', orderable: false, searchable: false},
                    { data: 'status', orderable: false, searchable: false},
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
        function _new() {
            $('.new-sppd')[0].reset()
            $('.new-sppd .invalid-feedback').css('display', 'none')

            $('#sppdLabel').html('Buat SPPD')
            $('.new-sppd').attr("action", "{{ route('sppd.store') }}")

            $('#sppdNew').modal('show')
            // $('#nosppd').focus()
        }

        function _edit(datas) {
            if (datas) {
                const data = JSON.parse(atob(datas))
                // console.log('data', data)
                $('.new-sppd .invalid-feedback').css('display', 'none')

                $('#sppdLabel').html('Edit SPPD')
                $('.new-sppd').attr("action", "{{ route('sppd.update') }}")

                $('#uid').val(data.id)
                $('#nosppd').val(data.nosppd)
                $('#nama').val(data.nama)
                $('#jabatan').val(data.jabatan)
                $('#tujuan').val(data.tujuan)
                $('#kendaraan').val(data.kendaraan)
                $('#tgl_surat').val(data.tglsurat)
                $('#tgl_berangkat').val(data.tglberangkat)

                $('#sppdNew').modal('show')
            } else {
                Toast.fire({ icon: "error", title: "Data tidak ditemukan." })
            }
        }

        $('#sppdNew').on('shown.bs.modal', function() {
            $('#nosppd').focus();
        })

        function _delete(uid, datas, no) {
            if (uid && datas) {
                const data = JSON.parse(atob(datas))
                Swal.fire({
                    title: 'Hapus Data',
                    html: `
                        Anda yakin ingin menghapus data SPPD? <br>
                        <div class="col-12 justify-content-between bg-light-danger" style="border-radius: 15px; padding: 15px; text-align: left; font-size: 14px;">
                            <table style="width: 100%">
                                <tr><td style="width: 40%;">Nomor Surat </td><td style="width: 5%;"> : </td><td style="width: 55%;">${data.nosppd}</td>
                                <tr><td>Nama </td><td> : </td><td>${data.nama}</td>
                                <tr><td>Tanggal Surat </td><td> : </td><td>${data.tglsurat}</td>
                                <tr><td>Tanggal Berangkat </td><td> : </td><td>${data.tglberangkat}</td>
                            </table>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Batalkan',
                    confirmButtonText: 'Konfirmasi',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('sppd.destroy') }}",
                            type: "POST",
                            dataType: 'JSON',
                            data: { _token: $('meta[name="csrf-token"]').attr('content'), uid: no },
                            success: function(res) {
                                if (res.status === 'success') {
                                    Swal.fire({
                                        title: 'Sukses',
                                        text: res.message,
                                        icon: 'success',
                                    }).then(() => { location.reload() })
                                    // Toast.fire({ icon: 'success', title: res.message }).then(() => { location.reload() })
                                } else {
                                    Toast.fire({ icon: 'error', title: res.message }).then(() => { location.reload() })
                                }
                            },
                            error: function() {
                                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan pada sistem.' })
                            }
                        })
                    }
                })
            } else {
                Toast.fire({ icon: "error", title: "Data tidak ditemukan." })
            }
        }
    </script>
@endsection
