@extends('layouts.layout')

@section('title', 'Migrasi Data Legacy')

@section('css')
    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/light/loaders/custom-loader.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/dark/loaders/custom-loader.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .card-stat {
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        .step-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .table-overlay {
            position: absolute;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.85);
            width: 100%;
            height: 100%;
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
        }
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .code-box {
            background-color: #1e1e2d;
            color: #50cd89;
            font-family: Consolas, monospace;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
        }
    </style>
@endsection

@section('content')
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        
        <!-- Header & Breadcrumb -->
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="widget-content widget-content-area br-8 p-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1 text-primary d-flex align-items-center gap-2">
                                <svg viewBox="0 0 24 24" width="26" height="26" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="feather"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                Migrasi Data Legacy (Arsip Aktif)
                            </h4>
                            <p class="text-muted mb-0">Impor data mentah aplikasi lama (tabel <code>aktif.sql</code>) langsung ke Surat Masuk & Surat Keluar.</p>
                        </div>
                        <div>
                            <span class="badge badge-light-warning py-2 px-3 fw-bold">
                                MODE: {{ strtoupper(app()->environment()) }} (INTERNAL TESTING ONLY)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Environment Alert -->
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="alert alert-light-primary border-0 mb-0 d-flex align-items-center" role="alert">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shield me-3 text-primary"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <div>
                        <strong>Keamanan Lingkungan:</strong> Rute dan antarmuka web ini hanya aktif pada environment <code>local</code> dan <code>testing</code>. Jika sistem diubah ke <code>APP_ENV=production</code>, rute ini otomatis tidak terdaftar (HTTP 404) dan tombol menu disembunyikan total.
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 layout-spacing">
                <div class="card card-stat border-0 shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="step-icon bg-light-primary text-primary">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </div>
                        <div>
                            <span class="text-muted small">Berkas Sumber Bawaan (Storage Private)</span>
                            <h5 class="mb-0 fw-bold">{{ $displayPath ?? 'storage/app/private/legacy/aktif.sql' }}</h5>
                            @if ($defaultFileExists)
                                <span class="badge badge-light-success mt-1">Tersedia ({{ $defaultFileSize }} MB)</span>
                            @else
                                <span class="badge badge-light-danger mt-1">Tidak Ditemukan</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 layout-spacing">
                <div class="card card-stat border-0 shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="step-icon bg-light-info text-info">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                        </div>
                        <div>
                            <span class="text-muted small">Surat Masuk Saat Ini</span>
                            <h4 class="mb-0 fw-bold text-info">{{ number_format($inboxCount) }}</h4>
                            <span class="text-muted small">Target Tabel: <code>inboxes</code></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 layout-spacing">
                <div class="card card-stat border-0 shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="step-icon bg-light-success text-success">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </div>
                        <div>
                            <span class="text-muted small">Surat Keluar Saat Ini</span>
                            <h4 class="mb-0 fw-bold text-success">{{ number_format($outboxCount) }}</h4>
                            <span class="text-muted small">Target Tabel: <code>outboxes</code></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Migration Form & Control Panel -->
            <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 layout-spacing">
                <div class="widget-content widget-content-area br-8 p-4 position-relative" id="migration-card">
                    <!-- Overlay Loader -->
                    <div class="table-overlay" id="migration-overlay">
                        <div class="text-center">
                            <div class="spinner mx-auto mb-3"></div>
                            <h5 class="fw-bold mb-1" id="overlay-title">Memproses Migrasi Data...</h5>
                            <p class="text-muted small mb-0" id="overlay-desc">Mohon tunggu hingga konversi dan batch insert selesai.</p>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3">Konfigurasi Migrasi</h5>

                    <form id="migration-form" enctype="multipart/form-data">
                        @csrf

                        <!-- Source Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">1. Pilih Sumber Berkas:</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="source_type" id="source_default" value="default" checked>
                                <label class="form-check-label" for="source_default">
                                    <strong>Gunakan berkas bawaan server (Storage Private):</strong> <code>{{ $displayPath ?? 'storage/app/private/legacy/aktif.sql' }}</code> 
                                    @if($defaultFileExists)
                                        <span class="badge badge-light-success ms-1">Siap (8.080 baris)</span>
                                    @else
                                        <span class="badge badge-light-danger ms-1">Berkas Tidak Ditemukan</span>
                                    @endif
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="source_type" id="source_upload" value="upload">
                                <label class="form-check-label" for="source_upload">
                                    <strong>Unggah berkas baru:</strong> (.sql dump MySQL atau .csv terpisah)
                                </label>
                            </div>

                            <div class="mt-2 d-none" id="upload-input-group">
                                <input type="file" class="form-control" name="uploaded_file" id="uploaded_file" accept=".sql,.csv,.txt">
                                <div class="form-text text-muted">Maksimum ukuran file: 50 MB. Mendukung file .sql (tabel aktif) atau .csv terpisah.</div>
                            </div>
                        </div>

                        <!-- Data Type Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">2. Tipe Data yang Diimpor:</label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="data_type" id="type_all" value="all" checked>
                                            <label class="form-check-label fw-bold" for="type_all">Semua Data</label>
                                        </div>
                                        <p class="text-muted small mt-1 mb-0">Impor Surat Masuk (~7.688) dan Surat Keluar (~392).</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="data_type" id="type_masuk" value="masuk">
                                            <label class="form-check-label fw-bold" for="type_masuk">Hanya Surat Masuk</label>
                                        </div>
                                        <p class="text-muted small mt-1 mb-0">Hanya impor data berjenis Masuk ke tabel <code>inboxes</code>.</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="data_type" id="type_keluar" value="keluar">
                                            <label class="form-check-label fw-bold" for="type_keluar">Hanya Surat Keluar</label>
                                        </div>
                                        <p class="text-muted small mt-1 mb-0">Hanya impor data berjenis Keluar ke tabel <code>outboxes</code>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dry Run Checkbox -->
                        <div class="mb-4 p-3 bg-light rounded border">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="dry_run" name="dry_run" value="1" checked>
                                <label class="form-check-label fw-bold" for="dry_run">
                                    Simulasi DRY RUN (Validasi tanpa menyimpan ke Database)
                                </label>
                            </div>
                            <div class="text-muted small mt-1" id="dry-run-note">
                                Rekomendasi: Biarkan opsi ini aktif untuk memverifikasi validitas data terlebih dahulu. Hilangkan centang jika Anda siap menyimpan ke database PostgreSQL.
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                Kembali
                            </button>
                            <button type="submit" class="btn btn-warning px-4 py-2 fw-bold" id="btn-submit">
                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                <span id="btn-submit-text">Jalankan Simulasi (Dry Run)</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Terminal / Info Panel -->
            <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 layout-spacing">
                <div class="widget-content widget-content-area br-8 p-4 h-100">
                    <h5 class="fw-bold mb-3">Alternatif via Terminal</h5>
                    <p class="text-muted small">
                        Untuk proses migrasi skala besar atau otomatisasi di background, Anda juga dapat menggunakan Artisan CLI:
                    </p>

                    <div class="mb-3">
                        <span class="text-muted small fw-bold">1. Simulasi Cepat (Dry Run):</span>
                        <div class="code-box mt-1">php artisan app:migrate-legacy --dry-run</div>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted small fw-bold">2. Eksekusi Live (Simpan ke DB):</span>
                        <div class="code-box mt-1">php artisan app:migrate-legacy</div>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted small fw-bold">3. Impor Spesifik Surat Masuk:</span>
                        <div class="code-box mt-1">php artisan app:migrate-legacy --type=masuk</div>
                    </div>

                    <div class="alert alert-light-warning border-0 p-2 mt-4 small">
                        <strong>Catatan Normalisasi:</strong> Sistem secara otomatis memetakan relasi kode klasifikasi arsip, sifat surat, tempat berkas, media (Teks), dan UUID user pencatat arsip.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Toggle input file
        $('input[name="source_type"]').on('change', function() {
            if ($(this).val() === 'upload') {
                $('#upload-input-group').removeClass('d-none');
            } else {
                $('#upload-input-group').addClass('d-none');
            }
        });

        // Toggle button style based on Dry Run
        $('#dry_run').on('change', function() {
            if ($(this).is(':checked')) {
                $('#btn-submit').removeClass('btn-danger btn-primary').addClass('btn-warning');
                $('#btn-submit-text').text('Jalankan Simulasi (Dry Run)');
                $('#dry-run-note').text('Mode Simulasi aktif: Sistem akan membaca dan memvalidasi seluruh data tanpa mengubah isi database.');
            } else {
                $('#btn-submit').removeClass('btn-warning').addClass('btn-danger');
                $('#btn-submit-text').text('Jalankan Migrasi LIVE (Simpan ke DB)');
                $('#dry-run-note').text('PERINGATAN: Mode LIVE aktif! Baris data yang valid akan langsung disimpan ke tabel inboxes dan outboxes.');
            }
        });

        // Form Submit
        $('#migration-form').on('submit', function(e) {
            e.preventDefault();

            const isDryRun = $('#dry_run').is(':checked');
            const sourceType = $('input[name="source_type"]:checked').val();
            const dataType = $('input[name="data_type"]:checked').val();

            if (sourceType === 'upload' && !$('#uploaded_file').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Berkas Belum Dipilih',
                    text: 'Silakan pilih berkas .sql atau .csv yang ingin diunggah terlebih dahulu.',
                });
                return;
            }

            const title = isDryRun 
                ? 'Mulai Simulasi Validasi?' 
                : 'Mulai Migrasi LIVE ke Database?';
            const text = isDryRun 
                ? 'Sistem akan memvalidasi data tanpa menyimpan ke tabel database.' 
                : 'Data arsip legacy akan dimasukkan ke tabel inboxes dan outboxes. Pastikan data sudah diverifikasi.';

            Swal.fire({
                title: title,
                text: text,
                icon: isDryRun ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: isDryRun ? '#e2a03f' : '#e7515a',
                cancelButtonColor: '#888ea8',
                confirmButtonText: isDryRun ? 'Ya, Jalankan Simulasi' : 'Ya, Migrasikan Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeMigration();
                }
            });
        });

        function executeMigration() {
            const formData = new FormData($('#migration-form')[0]);
            formData.set('dry_run', $('#dry_run').is(':checked') ? '1' : '0');

            $('#migration-overlay').css('display', 'flex');
            $('#btn-submit').prop('disabled', true);

            $.ajax({
                url: "{{ route('migration.process') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#migration-overlay').hide();
                    $('#btn-submit').prop('disabled', false);

                    if (response.success) {
                        const d = response.data;
                        const isDry = d.dry_run;

                        Swal.fire({
                            icon: 'success',
                            title: isDry ? 'Simulasi Sukses!' : 'Migrasi Live Berhasil!',
                            html: `
                                <div class="text-start mt-2">
                                    <table class="table table-sm table-bordered">
                                        <tr><th>Total Diparsing</th><td class="text-end fw-bold">${d.total_parsed.toLocaleString()}</td></tr>
                                        <tr><th>Surat Masuk</th><td class="text-end text-primary fw-bold">${d.masuk_success.toLocaleString()}</td></tr>
                                        <tr><th>Surat Keluar</th><td class="text-end text-success fw-bold">${d.keluar_success.toLocaleString()}</td></tr>
                                        <tr><th>Dilewati/Skip</th><td class="text-end text-muted">${d.skipped.toLocaleString()}</td></tr>
                                        <tr><th>Durasi Waktu</th><td class="text-end fw-bold">${d.duration} detik</td></tr>
                                        <tr><th>Mode</th><td class="text-end">${isDry ? '<span class="badge badge-warning">DRY RUN</span>' : '<span class="badge badge-success">LIVE DB</span>'}</td></tr>
                                    </table>
                                </div>
                            `,
                            confirmButtonText: 'Tutup'
                        }).then(() => {
                            if (!isDry) {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Migrasi Gagal',
                            text: response.message || 'Terjadi kesalahan saat memproses migrasi data.',
                        });
                    }
                },
                error: function(xhr) {
                    $('#migration-overlay').hide();
                    $('#btn-submit').prop('disabled', false);

                    let errMsg = 'Terjadi kesalahan pada server saat memproses data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errMsg,
                    });
                }
            });
        }
    });
</script>
@endsection
