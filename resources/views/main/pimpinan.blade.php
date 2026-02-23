@extends('layouts.layout')

@section('title', 'Daftar Pimpinan')


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
                                Daftar Pimpinan
                            </h4>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-success mb-2 me-4 float-end" id="bt_new" onclick="_new()">
                                {{-- <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> --}}
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                <span class="btn-text-inner">Tambah Pimpinan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="widget-content widget-content-area br-8" style="position: relative;">
                    <div class="m-3">
                        <div class="alert alert-light-primary alert-dismissible fade show border-0 mb-4" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><svg> ... </svg></button>
                            <strong>Informasi</strong> Nama pimpinan digunakan pada surat disposisi.</button>
                        </div> 
                    </div>

                    <div class="col-12 row justify-content-evenly m-3">
                        @foreach ($lists as $item)
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                            <div class="card" style="min-height: 300px; max-height: 450px;">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        {!! $item->is_default == true ? '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>' : '' !!}
                                        <b>{{ $item->leveluser->nama }}</b> :
                                    </h5>
                                    <p class="mb-0">{!! nl2br($item->jabatan) !!}</p>
                                    <p style="height: 35px;">&nbsp;</p>
                                    <p class="mb-0"><b><u>{{ $item->nama }}</u></b></p>
                                    {!! !empty($item->nip) ? '<p class="mb-0">' . $item->nip . '</p>' : '' !!}
                                    {!! !empty($item->pangkat_golongan) ? '<p class="mb-0">' . $item->pangkat_golongan . '</p>' : '' !!}
                                </div>
                                <div class="card-footer pt-0 border-0 text-center">
                                    {{-- <a href="javascript:void(0);" class="btn btn-secondary w-100"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-slack"><path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z"></path><path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"></path><path d="M9.5 14c.83 0 1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5S8 21.33 8 20.5v-5c0-.83.67-1.5 1.5-1.5z"></path><path d="M3.5 14H5v1.5c0 .83-.67 1.5-1.5 1.5S2 16.33 2 15.5 2.67 14 3.5 14z"></path><path d="M14 14.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5z"></path><path d="M15.5 19H14v1.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"></path><path d="M10 9.5C10 8.67 9.33 8 8.5 8h-5C2.67 8 2 8.67 2 9.5S2.67 11 3.5 11h5c.83 0 1.5-.67 1.5-1.5z"></path><path d="M8.5 5H10V3.5C10 2.67 9.33 2 8.5 2S7 2.67 7 3.5 7.67 5 8.5 5z"></path></svg> <span class="btn-text-inner ms-3">Join her on Slack</span></a> --}}
                                    <div class="btn-group d-flex col-auto mx-auto" role="group">
                                        @if ($item->is_default == 0)
                                            <button type="button" class="btn btn-outline-info bs-tooltip" title="Jadikan default" onclick="_default('{{ base64_encode(json_encode([$item->id, $item->level])) }}')">
                                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                <span class="btn-text-inner">Default</span>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-outline-warning bs-tooltip" title="Edit Data" onclick="_edit('{{ base64_encode(json_encode($item)) }}')">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            <span class="btn-text-inner">Edit</span>
                                        </button>
                                        <button type="button" class="btn btn-danger bs-tooltip" title="Hapus Data" onclick="_delete('{{ base64_encode($item->id) }}')">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            <span class="btn-text-inner">Hapus</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
    
</div>


<div class="modal fade" id="userNew" tabindex="-1" role="dialog" aria-labelledby="userLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userLabel">Tambah Pimpinan</h5>
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
                                                {{-- <h5 class="card-title mb-3">Data Diri</h5> --}}

                                                <div class="row mb-3">
                                                    <label for="nama" class="col-sm-3 col-form-label">Nama Lengkap dan Gelar *</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="nama" name="nama" value="" maxlength="100" required autofocus>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="jabatan" class="col-sm-3 col-form-label">Jabatan *</label>
                                                    <div class="col-sm-9">
                                                        {{-- <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Opsional" value=""> --}}
                                                        <textarea name="jabatan" id="jabatan" cols="30" rows="5" class="form-control" required></textarea>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-5">
                                                    <label for="pangkat" class="col-sm-3 col-form-label">Pangkat dan Golongan</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="pangkat" name="pangkat" value="" maxlength="255">
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-5">
                                                    <label for="nip" class="col-sm-3 col-form-label">NIP</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="nip" name="nip" value="" maxlength="255">
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-5">
                                                    <label for="pangkat" class="col-sm-3 col-form-label">Role *</label>
                                                    <div class="col-sm-9">
                                                        <select name="role" id="role" class="form-control" required>
                                                            <option value="">-- Pilih Role --</option>
                                                            @foreach ($instansi as $role)
                                                                <option value="{{ $role->id }}">{{ $role->nama }}</option>    
                                                            @endforeach
                                                            {{-- <option value="Bupati">Bupati</option>
                                                            <option value="Wakil Bupati">Wakil Bupati</option>
                                                            <option value="Sekretaris Daerah">Sekretaris Daerah</option>
                                                            <option value="Bagian Umum">Bagian Umum</option> --}}
                                                        </select>
                                                        <div class="invalid-feedback">
                                                            Field ini wajib di isi.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-5">
                                                    <label for="nip" class="col-sm-3 col-form-label">Opsi</label>
                                                    <div class="col-sm-9">
                                                        <div class="form-check form-check-primary form-check-inline">
                                                            <input type="hidden" name="is_default" value="no">
                                                            <input class="form-check-input" type="checkbox" id="is_default">
                                                            <label class="form-check-label" for="is_default">
                                                                Jadikan Default?
                                                            </label>
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
            $('.needs-validation').submit(function(event) {
                event.preventDefault();
                var form = this;
                if (form.checkValidity() === false) {
                    event.stopPropagation();
                }
                form.classList.add('was-validated');

                if (form.checkValidity() === true) {
                    $.ajax({
                        type: "POST",
                        url: $(form).attr('action'),
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#userNew').modal('hide')
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data berhasil disimpan.'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                $('#userNew').modal('hide')
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Gagal menyimpan data. ' + (response.message ? response.message : '')
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            $('#userNew').modal('hide')
                            Toast.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan saat menyimpan data.'
                            });
                        }
                    });
                }
            });

            $('#is_default').change(function() {
                if ($(this).is(':checked')) {
                    $(this).val('yes');
                    $('input[name="is_default"]').val('yes');
                } else {
                    $(this).val('no');
                    $('input[name="is_default"]').val('no');
                }
            });
        });
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <script>
        function _new() {
            $('#userLabel').html('Tambah Pimpinan')
            $('.form-user')[0].reset()
            $('.form-user').attr("action", "{{ route('pimpinan.save') }}")

            $('#userNew').modal('show')
        }

        function _edit(obj) {
            $('#userLabel').html('Edit Pimpinan')
            $('.form-user')[0].reset()
            $('.form-user').attr("action", "{{ route('pimpinan.update') }}")
            const datas = JSON.parse(atob(obj))
            if (!datas) {
                Toast.fire({
                    icon: 'error',
                    title: 'Data tidak valid.'
                });
                return
            }

            $('#uid').val(datas.id)
            $('#nama').val(datas.nama)
            $('#jabatan').val(datas.jabatan)
            $('#pangkat').val(datas.pangkat_golongan)
            $('#nip').val(datas.nip)
            $('#role').val(datas.level)
            if (datas.is_default) {
                $('#is_default').prop('checked', true);
                $('#is_default').val('yes');
                $('input[name="is_default"]').val('yes');
            } else {
                $('#is_default').prop('checked', false);
                $('#is_default').val('no');
                $('input[name="is_default"]').val('no');
            }

            $('#userNew').modal('show')
        }

        function _delete(uid) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('pimpinan.delete') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            uid: uid
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data berhasil dihapus.'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Gagal menghapus data. ' + (response.message ? response.message : '')
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Toast.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan saat menghapus data.'
                            });
                        }
                    });
                }
            })
        }

        function _default(uid) {
            const data = JSON.parse(atob(uid));
            if (!data) {
                Toast.fire({
                    icon: 'error',
                    title: 'Data tidak valid.'
                });
                return
            }
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data ini akan dijadikan sebagai pimpinan default!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, jadikan default!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('pimpinan.default') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            uid: btoa(data[0]),
                            role: data[1]
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pimpinan berhasil dijadikan default.'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Gagal mengubah data. ' + (response.message ? response.message : '')
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Toast.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan saat mengubah data.'
                            });
                        }
                    });
                }
            })
        }
    </script>
@endsection
