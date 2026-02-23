@extends('layouts.layout')

@section('title', 'Daftar Instansi')


@section('css')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/dark/table/datatable/dt-global_style.css') }}">

    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/tagify/tagify.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/tagify/custom-tagify.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/dark/tagify/custom-tagify.css') }}">
    <!-- END PAGE LEVEL STYLES -->

@endsection


@section('content')
<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">

            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="widget-content widget-content-area br-8 p-3">
                    <div class="row justify-content-space-between">
                        <div class="col-8">
                            <h4 class="">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"></path></svg>
                                Tabel Daftar Instansi
                            </h4>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-success mb-2 me-4 float-end" id="bt_new" onclick="_new()">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                <span class="btn-text-inner">Tambah Instansi</span>
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

                    <div class="col-12 table-responsive">
                    <table id="zero-config" class="table dt-table-hover table-striped table-bordered" style="width:100%" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 50%">Nama Instansi</th>
                                <th style="width: 10%">Kode Instansi</th>
                                <th style="width: 20%">Akronim</th>
                                <th style="width: 15%">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($instansi as $i => $ins)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td><b>{{ $ins->nama }}</b></td>
                                    <td>{{ $ins->kode }}</td>
                                    <td>{{ $ins->akronim }}</td>
                                    <td>
                                        <div class="btn-group-vertical" role="group" aria-label="Second group">
                                            {{-- <button type="button" class="btn btn-outline-info bs-tooltip" onclick="_detail('{{ base64_encode(json_encode($ins)) }}')" title="Detail Instansi">
                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                            </button> --}}
                                            <a href="javascript:void(0)" onclick="_edit('{{ base64_encode(json_encode($ins)) }}')" type="button" class="btn btn-outline-warning bs-tooltip" title="Edit Instansi">
                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            </a>
                                            <button type="button" class="btn btn-danger bs-tooltip" onclick="_delete('{{ $ins->INSTANSI }}', '{{ Crypt::encryptString($ins->id) }}')" title="Hapus Instansi">
                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
</div>


<div class="modal fade" id="modalInstansi" tabindex="-1" role="dialog" aria-labelledby="userLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userLabel">Buat User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form action="" method="POST" class="needs-validation form-ins" novalidate>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="uid" name="uid" value="">
                    <div class="row layout-top-spacing">
                        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                            <div class="widget-content widget-content-area br-8">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body new-user">

                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group mb-4">
                                                            <label for="nama">Nama Instansi</label>
                                                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Instansi" maxlength="100" required autofocus>
                                                            <div class="invalid-feedback">
                                                                Field ini wajib di isi.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-9">
                                                        <div class="form-group mb-4">
                                                            <label for="akronim">Nama Akronim (Singkatan) *</label>
                                                            <input type="text" class="form-control" id="akronim" name="akronim" placeholder="Nama Singkatan" maxlength="20" required>
                                                            <div class="invalid-feedback">
                                                                Field ini wajib di isi.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-group mb-4">
                                                            <label for="kode">Kode Instansi *</label>
                                                            <input type="text" class="form-control" id="kode" name="kode" placeholder="Kode Instansi" maxlength="10" required>
                                                            <div class="invalid-feedback">
                                                                Field ini wajib di isi.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group mb-4">
                                                            <label for="alamat">Alamat Instansi</label>
                                                            {{-- <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat Instansi"> --}}
                                                            <textarea name="alamat" id="alamat" cols="30" rows="5" class="form-control"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group mb-4">
                                                            <label for="telepon">No. Telepon</label>
                                                            <input type="number" class="form-control" id="telepon" name="telepon" placeholder="Nomor Telepon">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group mb-4">
                                                            <label for="email">Alamat Email</label>
                                                            <input type="email" class="form-control" id="email" name="email" placeholder="Alamat Email">
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
    <script src="{{ asset('templates/plugins/src/tagify/tagify.min.js') }}"></script>
    {{-- <script src="../src/plugins/src/tagify/custom-tagify.js"></script> --}}
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
            });
        });
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <script>
        function initForm() {
            var forms = document.getElementsByClassName('form-ins');
            var invalid = $('.form-ins .invalid-feedback');
            var fcontrol = $('.form-ins .form-control');

            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    const requiredFields = ['nama', 'akronim', 'kode'];
                    fcontrol.each(function() {
                        var $input = $(this);

                        if ($(this).val().trim() === '' && requiredFields.includes($input[0].name)) {
                            // You can add a class, display an error message, etc.
                            $input.next('.invalid-feedback').css('display', 'block');
                            $(this).removeClass('is-valid');
                            $(this).addClass('is-invalid');
                            event.preventDefault();
                            event.stopPropagation();
                        } else {
                            // This input field is not empty
                            $input.next('.invalid-feedback').css('display', 'none');
                            $(this).addClass('is-valid');
                            $(this).removeClass('is-invalid');
                        }
                    });
                }, false);
            });
        }

        function _new() {
            $('#userLabel').html('Tambah Instansi')
            $('.form-ins')[0].reset();
            $('.form-ins').attr("action", "{{ route('instansi.save') }}")

            $('#modalInstansi').modal('show')
        }

        function _edit(datas) {
            if (datas) {
                const data = JSON.parse(atob(datas))
                $('#userLabel').html('Edit Instansi')
                $('.form-ins')[0].reset();
                $('.form-ins').attr("action", "{{ route('instansi.update') }}")
                
                $('#uid').val(data.id)
                $('#nama').val(data.INSTANSI)
                $('#akronim').val(data.Akronim)
                $('#kode').val(data.KODE)
                $('#alamat').val(data.ALAMAT)
                $('#telepon').val(data.TELEPON)

                $('#modalInstansi').modal('show')
            } else {
                Toast.fire({ icon: "error", title: "Data tidak ditemukan." })
            }
        }

        $('#modalInstansi').on('shown.bs.modal', function() {
            initForm()
            $('#nama').focus();
        })

        function _delete(name, datas) {
            if (uid && datas) {
                Swal.fire({
                    title: 'Hapus Data',
                    html: `Anda yakin ingin menghapus data instansi <b>${name}</b>?`,
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Batalkan',
                    confirmButtonText: 'Konfirmasi',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('instansi.delete') }}",
                            type: "POST",
                            dataType: 'JSON',
                            data: { _token: $('meta[name="csrf-token"]').attr('content'), uid: datas },
                            success: function(res) {
                                if (res.status === 'success') {
                                    Swal.fire({
                                        title: 'Sukses',
                                        text: res.message,
                                        icon: 'success',
                                    }).then(() => { location.reload() })
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
                Toast.fire({ icon: "error", title: "Data user tidak diketahui." })
            }
        }
    </script>
@endsection
