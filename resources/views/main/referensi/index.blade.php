@extends('layouts.layout')

@section('title', 'Manajemen Referensi Data')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/dark/table/datatable/dt-global_style.css') }}">
    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/light/components/tabs.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/tabs.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">

            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="widget-content widget-content-area br-8 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                            Manajemen Referensi Data Persuratan
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="widget-content widget-content-area br-8 p-4">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs mb-3" id="refTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'klasifikasi' ? 'active' : '' }}" id="klasifikasi-tab" data-bs-toggle="tab" data-bs-target="#tab-klasifikasi" type="button" role="tab">
                                Klasifikasi JRA ({{ $klasifikasis->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'sifat-surat' ? 'active' : '' }}" id="sifat-tab" data-bs-toggle="tab" data-bs-target="#tab-sifat" type="button" role="tab">
                                Sifat Surat ({{ $sifats->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'tempat-berkas' ? 'active' : '' }}" id="tempat-tab" data-bs-toggle="tab" data-bs-target="#tab-tempat" type="button" role="tab">
                                Tempat Berkas ({{ $tempats->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'perkembangan' ? 'active' : '' }}" id="perkembangan-tab" data-bs-toggle="tab" data-bs-target="#tab-perkembangan" type="button" role="tab">
                                Tingkat Perkembangan ({{ $perkembangans->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'media-surat' ? 'active' : '' }}" id="media-tab" data-bs-toggle="tab" data-bs-target="#tab-media" type="button" role="tab">
                                Media Surat ({{ $medias->count() }})
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="refTabContent">

                        <!-- Tab 1: Klasifikasi JRA -->
                        <div class="tab-pane fade {{ $activeTab === 'klasifikasi' ? 'show active' : '' }}" id="tab-klasifikasi" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Daftar Klasifikasi JRA</h5>
                                <button class="btn btn-primary btn-sm" onclick="openKlasifikasiModal()">
                                    + Tambah Klasifikasi
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover datatable-ref" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Masalah / Uraian</th>
                                            <th>Series</th>
                                            <th>Retensi Aktif</th>
                                            <th>Retensi Inaktif</th>
                                            <th>Keterangan</th>
                                            <th style="width: 15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($klasifikasis as $k)
                                            <tr>
                                                <td><span class="badge badge-light-primary fw-bold">{{ $k->klas3 }}</span></td>
                                                <td>{{ $k->masalah3 }}</td>
                                                <td>{{ $k->series ?: '-' }}</td>
                                                <td>{{ $k->r_aktif }} Tahun</td>
                                                <td>{{ $k->r_inaktif }} Tahun</td>
                                                <td>{{ $k->ket_jra ?: '-' }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-warning" onclick='editKlasifikasi(@json($k))'>Edit</button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteRef('klasifikasi', {{ $k->id }}, '{{ $k->klas3 }}')">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 2: Sifat Surat -->
                        <div class="tab-pane fade {{ $activeTab === 'sifat-surat' ? 'show active' : '' }}" id="tab-sifat" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Daftar Sifat Surat</h5>
                                <button class="btn btn-primary btn-sm" onclick="openSimpleModal('sifat-surat', 'Sifat Surat', 'nama_sifat')">
                                    + Tambah Sifat Surat
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover datatable-ref" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%">#</th>
                                            <th>Nama Sifat Surat</th>
                                            <th style="width: 20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sifats as $idx => $s)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td><strong>{{ $s->nama_sifat }}</strong></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-warning" onclick="editSimple('sifat-surat', 'Sifat Surat', 'nama_sifat', {{ $s->id }}, '{{ $s->nama_sifat }}')">Edit</button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteRef('sifat-surat', {{ $s->id }}, '{{ $s->nama_sifat }}')">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: Tempat Berkas -->
                        <div class="tab-pane fade {{ $activeTab === 'tempat-berkas' ? 'show active' : '' }}" id="tab-tempat" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Daftar Tempat Berkas</h5>
                                <button class="btn btn-primary btn-sm" onclick="openSimpleModal('tempat-berkas', 'Tempat Berkas', 'nama')">
                                    + Tambah Tempat Berkas
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover datatable-ref" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%">#</th>
                                            <th>Nama Tempat Berkas</th>
                                            <th style="width: 20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tempats as $idx => $t)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td><strong>{{ $t->nama }}</strong></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-warning" onclick="editSimple('tempat-berkas', 'Tempat Berkas', 'nama', {{ $t->id }}, '{{ $t->nama }}')">Edit</button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteRef('tempat-berkas', {{ $t->id }}, '{{ $t->nama }}')">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 4: Tingkat Perkembangan -->
                        <div class="tab-pane fade {{ $activeTab === 'perkembangan' ? 'show active' : '' }}" id="tab-perkembangan" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Daftar Tingkat Perkembangan</h5>
                                <button class="btn btn-primary btn-sm" onclick="openSimpleModal('perkembangan', 'Tingkat Perkembangan', 'nama')">
                                    + Tambah Tingkat Perkembangan
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover datatable-ref" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%">#</th>
                                            <th>Nama Tingkat Perkembangan</th>
                                            <th style="width: 20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($perkembangans as $idx => $p)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td><strong>{{ $p->nama }}</strong></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-warning" onclick="editSimple('perkembangan', 'Tingkat Perkembangan', 'nama', {{ $p->id }}, '{{ $p->nama }}')">Edit</button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteRef('perkembangan', {{ $p->id }}, '{{ $p->nama }}')">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 5: Media Surat -->
                        <div class="tab-pane fade {{ $activeTab === 'media-surat' ? 'show active' : '' }}" id="tab-media" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Daftar Media Surat</h5>
                                <button class="btn btn-primary btn-sm" onclick="openSimpleModal('media-surat', 'Media Surat', 'nama')">
                                    + Tambah Media Surat
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover datatable-ref" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%">#</th>
                                            <th>Nama Media Surat</th>
                                            <th style="width: 20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($medias as $idx => $m)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td><strong>{{ $m->nama }}</strong></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-warning" onclick="editSimple('media-surat', 'Media Surat', 'nama', {{ $m->id }}, '{{ $m->nama }}')">Edit</button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteRef('media-surat', {{ $m->id }}, '{{ $m->nama }}')">Hapus</button>
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
    </div>
</div>

<!-- Modal Klasifikasi -->
<div class="modal fade" id="modalKlasifikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formKlasifikasi" onsubmit="submitKlasifikasi(event)">
            @csrf
            <input type="hidden" id="klasId" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="klasModalTitle">Tambah Klasifikasi JRA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kode Klasifikasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="klas3" name="klas3" required placeholder="Contoh: 005.1">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Uraian Masalah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="masalah3" name="masalah3" required placeholder="Contoh: Undangan Rapat Dinas">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Series</label>
                            <input type="text" class="form-control" id="series" name="series" placeholder="Series berkas">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Retensi Aktif (Thn) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="r_aktif" name="r_aktif" required min="0" value="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Retensi Inaktif (Thn) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="r_inaktif" name="r_inaktif" required min="0" value="2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Keterangan JRA</label>
                            <input type="text" class="form-control" id="ket_jra" name="ket_jra" placeholder="Musnah / Permanen">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nilai Guna</label>
                            <input type="text" class="form-control" id="nilai_guna" name="nilai_guna" placeholder="Administrasi / Keuangan">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveKlas">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Simple (Sifat, Tempat, Perkembangan, Media) -->
<div class="modal fade" id="modalSimple" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formSimple" onsubmit="submitSimple(event)">
            @csrf
            <input type="hidden" id="simpleId" name="id">
            <input type="hidden" id="simpleType">
            <input type="hidden" id="simpleField">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="simpleModalTitle">Tambah Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" id="simpleLabel">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="simpleValue" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('templates/plugins/src/table/datatable/datatables.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.datatable-ref').DataTable({
                pageLength: 10,
                language: {
                    paginate: {
                        previous: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                        next: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                    },
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    zeroRecords: "Tidak ada data."
                }
            });
        });

        // Klasifikasi Modal Handlers
        function openKlasifikasiModal() {
            $('#formKlasifikasi')[0].reset();
            $('#klasId').val('');
            $('#klasModalTitle').text('Tambah Klasifikasi JRA');
            $('#modalKlasifikasi').modal('show');
        }

        function editKlasifikasi(data) {
            $('#klasId').val(data.id);
            $('#klas3').val(data.klas3);
            $('#masalah3').val(data.masalah3);
            $('#series').val(data.series);
            $('#r_aktif').val(data.r_aktif);
            $('#r_inaktif').val(data.r_inaktif);
            $('#ket_jra').val(data.ket_jra);
            $('#nilai_guna').val(data.nilai_guna);
            $('#klasModalTitle').text('Edit Klasifikasi JRA: ' + data.klas3);
            $('#modalKlasifikasi').modal('show');
        }

        function submitKlasifikasi(e) {
            e.preventDefault();
            const id = $('#klasId').val();
            const url = id ? '{{ route("referensi.update", "klasifikasi") }}' : '{{ route("referensi.store", "klasifikasi") }}';
            const formData = {
                _token: '{{ csrf_token() }}',
                id: id,
                klas3: $('#klas3').val(),
                masalah3: $('#masalah3').val(),
                series: $('#series').val(),
                r_aktif: $('#r_aktif').val(),
                r_inaktif: $('#r_inaktif').val(),
                ket_jra: $('#ket_jra').val(),
                nilai_guna: $('#nilai_guna').val(),
            };

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (res) {
                    $('#modalKlasifikasi').modal('hide');
                    Swal.fire({ icon: 'success', title: res.message }).then(() => {
                        window.location.href = '{{ route("referensi.index") }}?tab=klasifikasi';
                    });
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan saat menyimpan.';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                }
            });
        }

        // Simple Modals Handlers
        function openSimpleModal(type, title, field) {
            $('#formSimple')[0].reset();
            $('#simpleId').val('');
            $('#simpleType').val(type);
            $('#simpleField').val(field);
            $('#simpleModalTitle').text('Tambah ' + title);
            $('#simpleLabel').html('Nama ' + title + ' <span class="text-danger">*</span>');
            $('#modalSimple').modal('show');
        }

        function editSimple(type, title, field, id, val) {
            $('#simpleId').val(id);
            $('#simpleType').val(type);
            $('#simpleField').val(field);
            $('#simpleValue').val(val);
            $('#simpleModalTitle').text('Edit ' + title);
            $('#simpleLabel').html('Nama ' + title + ' <span class="text-danger">*</span>');
            $('#modalSimple').modal('show');
        }

        function submitSimple(e) {
            e.preventDefault();
            const type = $('#simpleType').val();
            const field = $('#simpleField').val();
            const id = $('#simpleId').val();
            const val = $('#simpleValue').val();

            const url = id ? `/referensi/${type}/update` : `/referensi/${type}/simpan`;
            const data = {
                _token: '{{ csrf_token() }}',
                id: id,
            };
            data[field] = val;

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function (res) {
                    $('#modalSimple').modal('hide');
                    Swal.fire({ icon: 'success', title: res.message }).then(() => {
                        window.location.href = `{{ route("referensi.index") }}?tab=${type}`;
                    });
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan saat menyimpan.';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                }
            });
        }

        // Delete with Relational Protection
        function deleteRef(type, id, label) {
            Swal.fire({
                title: 'Hapus data referensi?',
                text: `Anda akan menghapus "${label}". Pastikan data tidak digunakan dalam arsip surat.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e7515a',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/referensi/${type}/hapus`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function (res) {
                            Swal.fire({ icon: 'success', title: res.message }).then(() => {
                                window.location.href = `{{ route("referensi.index") }}?tab=${type}`;
                            });
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Data tidak dapat dihapus.';
                            Swal.fire({ icon: 'error', title: 'Gagal Menghapus', text: msg });
                        }
                    });
                }
            });
        }
    </script>
@endsection
