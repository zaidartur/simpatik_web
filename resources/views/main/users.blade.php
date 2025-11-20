@extends('layouts.layout')

@section('title', 'Daftar User')


@section('css')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">

    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->

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
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                Daftar User
                            </h4>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-success mb-2 me-4 float-end" id="bt_new" onclick="_new()">
                                {{-- <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> --}}
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                <span class="btn-text-inner">Buat User</span>
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
                                <th>#</th>
                                <th style="width: 10%">Nama User</th>
                                <th style="width: 10%">Username</th>
                                <th style="width: 10%">Email</th>
                                <th style="width: 10%">Level</th>
                                <th style="width: 20%">Instansi</th>
                                <th style="width: 20%">Status</th>
                                <th style="width: 10%">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lists as $i => $item)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $item->nama_lengkap }}</td>
                                    <td>{{ $item->username }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td><span class="badge {{ $badge[$item->level] }} mb-2 me-4">{{ $item->level }}</span></td>
                                    <td>{{ $item->jurusan }}</td>
                                    <td>{!! $item->blokir == 'N' ? '<span class="badge badge-success mb-2 me-4">Aktif</span>' : '<span class="badge badge-danger mb-2 me-4">Nonaktif</span>' !!}</td>
                                    <td>
                                        <div class="btn-group-vertical" role="group" aria-label="Second group">
                                            {{-- <button type="button" class="btn btn-outline-info bs-tooltip" onclick="_detail('{{ base64_encode(json_encode($item)) }}')" title="Detail User">
                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                            </button> --}}
                                            <a href="javascript:void(0)" onclick="_edit('{{ base64_encode(json_encode($item)) }}')" type="button" class="btn btn-outline-warning bs-tooltip" title="Edit User">
                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            </a>
                                            <button type="button" class="btn btn-outline-warning bs-tooltip" onclick="_password('{{ $item->nama_lengkap }}', '{{ Crypt::encryptString($item->id) }}')" title="Ubah Password">
                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>
                                            </button>
                                            @if($item->id != Auth::user()->id)
                                            <button type="button" class="btn btn-danger bs-tooltip" onclick="_delete('{{ $item->nama_lengkap }}', '{{ Crypt::encryptString($item->uuid) }}')" title="Hapus User">
                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </button>
                                            @endif
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


<div class="modal fade" id="userNew" tabindex="-1" role="dialog" aria-labelledby="userLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userLabel">Buat User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form action="" method="POST" class="needs-validation form-user" novalidate>
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
                                                <h5 class="card-title mb-3">Data Diri</h5>

                                                <div class="row mb-3">
                                                    <label for="nama" class="col-sm-3 col-form-label">Nama Lengkap *</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="nama" name="nama" value="" maxlength="100" required autofocus>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="nama" class="col-sm-3 col-form-label">Alamat Email</label>
                                                    <div class="col-sm-9">
                                                        <input type="email" class="form-control" id="email" name="email" placeholder="Opsional" value="">
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-5">
                                                    <label for="instansi" class="col-sm-3 col-form-label">Instansi*</label>
                                                    <div class="col-sm-9">
                                                        {{-- <input type="text" class="form-control" id="instansi" name="instansi" value="" maxlength="255" required> --}}
                                                        <select name="instansi" id="instansi" class="form-control" required>
                                                            <option value="">Pilih Instansi</option>
                                                            @foreach ($instansi as $ins)
                                                                <option value="{{ $ins->kode }}">{{ $ins->instansi }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>

                                                <h5 class="card-title mb-3">Data Akun</h5>
                                                <div class="row mb-3">
                                                    <label for="level" class="col-sm-3 col-form-label">Level*</label>
                                                    <div class="col-sm-5">
                                                        {{-- <input type="text" class="form-control" id="level" name="level" value="" maxlength="255" required> --}}
                                                        <select name="level" id="level" class="form-control" required>
                                                            <option value="">Pilih Level</option>
                                                            <option value="Administrator">Administrator</option>
                                                            <option value="operator">Operator</option>
                                                        </select>
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
            });
        });
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <script>
        function initForm() {
            var forms = document.getElementsByClassName('form-user');
            var invalid = $('.form-user .invalid-feedback');
            var fcontrol = $('.form-user .form-control');

            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        form.classList.add('was-validated');
                        // event.preventDefault();
                    }

                    fcontrol.each(function() {
                        var $input = $(this);

                        if ($(this).val().trim() === '' && $input[0].name !== 'email') {
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
        }

        function _new() {
            // remove field
            $('.is-user').remove()
            $('.is-pwd').remove()
            $('.is-pwdconf').remove()

            const add = `
                <div class="row mb-3 is-user">
                    <label for="username" class="col-sm-3 col-form-label">Username*</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="username" name="username" value="" maxlength="25" required>
                        <div class="invalid-feedback">
                            Field ini wajib di isi.
                        </div>
                    </div>
                </div>
                <div class="row mb-3 is-pwd">
                    <label for="password" class="col-sm-3 col-form-label">Password*</label>
                    <div class="col-sm-5">
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="" aria-describedby="button_pwd" maxlength="100" required>
                            <button class="btn btn-primary" type="button" id="button_pwd" value="on" onclick="_pwd()">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                        <div class="invalid-feedback">
                            Field ini wajib di isi.
                        </div>
                    </div>
                </div>
                <div class="row mb-3 is-pwdconf">
                    <label for="password-confirm" class="col-sm-3 col-form-label">Konfirmasi Password*</label>
                    <div class="col-sm-5">
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" id="password_confirm" name="password_confirmation" placeholder="" aria-describedby="button_pwdcon" maxlength="100" required>
                            <button class="btn btn-primary" type="button" id="button_pwdcon" value="on" onclick="_pwd_confirm()">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                        <div class="invalid-feedback">
                            Field ini wajib di isi.
                        </div>
                    </div>
                </div>
            `
            $('.new-user').append(add)
            $('.form-user')[0].reset()
            $('.form-user .invalid-feedback').css('display', 'none')
            $('.form-user .form-control').removeClass('is-invalid')
            $('.form-user .form-control').removeClass('is-valid')

            $('#userLabel').html('Buat User')
            $('.form-user').attr("action", "{{ route('user.store') }}")

            $('#userNew').modal('show')
        }

        function _edit(datas) {
            if (datas) {
                const data = JSON.parse(atob(datas))
                // console.log('data', data)
                $('.form-user .invalid-feedback').css('display', 'none')
                $('.form-user .form-control').removeClass('is-invalid')
                $('.form-user .form-control').removeClass('is-valid')

                $('#userLabel').html('Edit User')
                $('.form-user').attr("action", "{{ route('user.update') }}")
                // remove field
                $('.is-user').remove()
                $('.is-pwd').remove()
                $('.is-pwdconf').remove()

                $('#uid').val(data.id)
                $('#nama').val(data.nama_lengkap)
                $('#email').val(data.email)
                $('#instansi').val(data.jurusan)
                $('#username').val(data.username)
                $('#level').val(data.level)

                $('#userNew').modal('show')
            } else {
                Toast.fire({ icon: "error", title: "Data tidak ditemukan." })
            }
        }

        $('#userNew').on('shown.bs.modal', function() {
            initForm()
            $('#nama').focus();
        })

        function _password(name, datas) {
            if (datas) {
                Swal.fire({
                    title: 'Ubah Password',
                    text: `Anda ingin merubah password ${name}?`,
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Batalkan',
                    confirmButtonText: 'Ya, Lanjutkan',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                }).then((res) => {
                    if (res.isConfirmed) {
                        Swal.fire({
                            title: "Masukkan password Anda",
                            input: "password",
                            inputLabel: "Password",
                            inputPlaceholder: "Masukkan password Anda",
                            inputAttributes: {
                                maxlength: "10",
                                autocapitalize: "off",
                                autocorrect: "off"
                            },
                            showCancelButton: true,
                            cancelButtonText: 'Batalkan',
                            confirmButtonText: 'Konfirmasi',
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            showLoaderOnConfirm: true,
                            preConfirm: async(password) => {
                                if (password.length < 4) {
                                    Swal.showValidationMessage('Password tidak boleh kosong.');
                                    return false;
                                }
                                // return password;
                                try {
                                    await $.ajax({
                                        url: "{{ route('user.check') }}",
                                        type: "POST",
                                        data: { _token: $('meta[name="csrf-token"]').attr('content'), pass: btoa(password), uid: datas },
                                        success: function(res) {
                                            if (res.status === 'success') {
                                                // Toast.fire({ icon: "success", title: res.message })
                                                return datas
                                            } else {
                                                Swal.showValidationMessage(res.message);
                                                return false;
                                            }
                                        },
                                        error: function() {
                                            // Toast.fire({ icon: "error", title: "Terjadi kesalahan pada sistem." })
                                            Swal.showValidationMessage("Terjadi kesalahan pada sistem.");
                                            return false;
                                        }
                                    })
                                } catch (error) {
                                    Swal.showValidationMessage(error.message);
                                    return false;
                                }
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const uid = result.value;
                                Swal.fire({
                                    title: `Masukkan password baru dari user ${name}`,
                                    html: `
                                        <input type="password" id="password" class="swal2-input" placeholder="Password" />
                                        <input type="password" id="confirmPassword" class="swal2-input" placeholder="Konfirmasi Password" />
                                    `,
                                    focusConfirm: false,
                                    showCancelButton: true,
                                    cancelButtonText: 'Batalkan',
                                    confirmButtonText: 'Simpan',
                                    allowEscapeKey: false,
                                    allowOutsideClick: false,
                                    showLoaderOnConfirm: true,
                                    preConfirm: async() => {
                                        const password = document.getElementById('password').value;
                                        const confirmPassword = document.getElementById('confirmPassword').value;

                                        if (!password || !confirmPassword) {
                                            Swal.showValidationMessage('Mohon untuk mengisi semua field.');
                                            return false;
                                        }
                                        if (password !== confirmPassword) {
                                            Swal.showValidationMessage('Password tidak sama.');
                                            return false;
                                        }

                                        try {
                                            return await $.ajax({
                                                url: "{{ route('user.change_pwd') }}",
                                                type: "POST",
                                                data: { _token: $('meta[name="csrf-token"]').attr('content'), pass: btoa(password), uid: datas },
                                                success: function(usr) {
                                                    if (usr.status === 'success') {
                                                        return usr
                                                    } else {
                                                        Swal.showValidationMessage(usr.message);
                                                        return false;
                                                    }
                                                },
                                                error: function() {
                                                    Swal.showValidationMessage("Terjadi kesalahan pada sistem.");
                                                    return false;
                                                }
                                            })
                                        } catch(err) {
                                            Swal.showValidationMessage(error.message);
                                            return false;
                                        }
                                    }
                                }).then((rsp) => {
                                    if (rsp.isConfirmed) {
                                        console.log('res', rsp)
                                        Swal.fire({
                                            title: 'Sukses',
                                            text: `Password user ${name} berhasil diganti. ${rsp.value.data === 'relog' ? 'Memulai login ulang.' : ''}`,
                                            icon: 'success',
                                        }).then(async(final) => {
                                            if (rsp.value.data === 'relog') {
                                                $('#flogout').submit()
                                            }
                                        })
                                    }
                                })
                            }
                        })
                    }
                })
            }
        }

        function _delete(name, datas) {
            if (uid && datas) {
                Swal.fire({
                    title: 'Hapus Data',
                    html: `Anda yakin ingin menghapus data user <b>${name}</b>?`,
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Batalkan',
                    confirmButtonText: 'Konfirmasi',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('user.destroy') }}",
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

        function _pwd() {
            const type = $('#button_pwd').val()

            if (type === 'on') {
                $('#password').attr('type', 'text')
                $('#button_pwd').val('off')
                $('#button_pwd').html('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>')
            } else {
                $('#password').attr('type', 'password')
                $('#button_pwd').val('on')
                $('#button_pwd').html('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>')
            }
        }

        function _pwd_confirm() {
            const type = $('#button_pwdcon').val()

            if (type === 'on') {
                $('#password_confirm').attr('type', 'text')
                $('#button_pwdcon').val('off')
                $('#button_pwdcon').html('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>')
            } else {
                $('#password_confirm').attr('type', 'password')
                $('#button_pwdcon').val('on')
                $('#button_pwdcon').html('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>')
            }
        }
    </script>
@endsection
