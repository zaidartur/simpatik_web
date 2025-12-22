@extends('layouts.layout')

@section('title', 'SPD')


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
                                Daftar SPPD
                            </h4>
                        </div>
                        @role(['administrator'])
                        <div class="col-6">
                            <button class="btn btn-info mb-2 me-4 float-end" id="bt_new" onclick="_new()">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                <span class="btn-text-inner">Buat SPPD</span>
                            </button>
                        </div>
                        @endrole
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
                                <th>#</th>
                                <th style="width: 10%">No. SPPD</th>
                                <th style="width: 10%">Nama</th>
                                <th style="width: 10%">Jabatan</th>
                                <th style="width: 10%">Tujuan</th>
                                <th style="width: 10%">Kendaraan</th>
                                <th style="width: 20%">Tgl. Surat</th>
                                <th style="width: 20%">Tgl. Berangkat</th>
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


<div class="modal fade" id="sppdNew" tabindex="-1" role="dialog" aria-labelledby="sppdLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sppdLabel">Buat SPPD</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form action="" method="POST" class="needs-validation new-sppd" novalidate>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="uid" name="uid" value="">
                    <div class="row layout-top-spacing">
                        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                            <div class="widget-content widget-content-area br-8">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                {{-- <h5 class="card-title mb-5">Asal Surat</h5> --}}

                                                <div class="row mb-3">
                                                    <label for="nosppd" class="col-sm-3 col-form-label">Nomor SPPD</label>
                                                    <div class="col-sm-5">
                                                        <input type="text" class="form-control" id="nosppd" name="nosppd" value="" maxlength="100" required autofocus>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="nama" class="col-sm-3 col-form-label">Nama</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" style="text-transform: uppercase;" id="nama" name="nama" value="" maxlength="255" required>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="jabatan" class="col-sm-3 col-form-label">Jabatan</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" style="text-transform: uppercase;" id="jabatan" name="jabatan" value="" maxlength="255" required>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="tujuan" class="col-sm-3 col-form-label">Tujuan</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" style="text-transform: uppercase;" id="tujuan" name="tujuan" value="" maxlength="255" required>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="kendaraan" class="col-sm-3 col-form-label">Kendaraan</label>
                                                    <div class="col-sm-5">
                                                        <input type="text" class="form-control" style="text-transform: uppercase;" id="kendaraan" name="kendaraan" value="" maxlength="255" required>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="tgl_surat" class="col-sm-3 col-form-label">Tanggal Surat</label>
                                                    <div class="col-sm-5">
                                                        <input type="date" class="form-control" id="tgl_surat" name="tgl_surat" value="" required>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="tgl_berangkat" class="col-sm-3 col-form-label">Tanggal Berangkat</label>
                                                    <div class="col-sm-5">
                                                        <input type="date" class="form-control" id="tgl_berangkat" name="tgl_berangkat" value="" required>
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
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
                    url: "{{ route('sppd.ssr') }}",
                    type: 'GET',
                },
                "columns": [
                    { data: null, orderable: false, searchable: false},
                    { data: 'nomor', orderable: false },
                    { data: 'nama', orderable: false },
                    { data: 'jabatan', orderable: false },
                    { data: 'tujuan', orderable: false },
                    { data: 'kendaraan', orderable: false },
                    { data: 'tgl_surat', orderable: false },
                    { data: 'tgl_berangkat', orderable: false },
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
