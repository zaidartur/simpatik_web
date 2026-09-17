@extends('layouts.layout')

@section('title', 'Dashboard Persuratan')

@section('css')
    <link href="{{ asset('templates/plugins/src/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/light/fullcalendar/custom-fullcalendar.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/plugins/css/dark/fullcalendar/custom-fullcalendar.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />

    <!-- ApexCharts CSS -->
    <link href="{{ asset('templates/plugins/src/apex/apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('templates/plugins/css/light/apex/custom-apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('templates/plugins/css/dark/apex/custom-apexcharts.css') }}" rel="stylesheet" type="text/css">

    <style>
        .stat-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
        .action-item {
            transition: background 0.15s ease;
            border-radius: 8px;
        }
        .action-item:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
    </style>
@endsection

@section('content')
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">

        <!-- Welcome Banner -->
        <div class="row layout-top-spacing mb-3">
            <div class="col-12">
                <div class="widget-content widget-content-area br-8 p-4 d-flex flex-wrap justify-content-between align-items-center bg-light">
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">
                            Selamat Datang, {{ Auth::user()->nama_lengkap }}! 👋
                        </h4>
                        <p class="mb-0 text-muted">
                            {{ Auth::user()->leveluser->nama ?? 'Pengguna Sistem' }} &bull; Sistem Informasi Pengelolaan Manajemen Administrasi Persuratan (SIPERMAS)
                        </p>
                    </div>
                    <div class="mt-2 mt-md-0 text-md-end">
                        <span class="badge badge-light-primary p-2 fs-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar me-1"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Stat Metric Cards -->
        <div class="row mb-4">
            <!-- Card 1: Surat Masuk -->
            @canany(['surat masuk', 'lihat surat masuk'])
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                <div class="widget-content widget-content-area br-8 p-3 stat-card h-100 border-start border-primary border-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted text-uppercase fw-bold small">Surat Masuk Hari Ini</span>
                            <h3 class="fw-bold mt-2 mb-1 text-primary">{{ number_format($masukHariIni) }}</h3>
                            <span class="badge badge-light-secondary small">
                                Bulan ini: <strong>{{ number_format($masukBulanIni) }}</strong> berkas
                            </span>
                        </div>
                        <div class="stat-icon bg-light-primary text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
            @endcanany

            <!-- Card 2: Surat Keluar -->
            @canany(['surat keluar', 'lihat surat keluar'])
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                <div class="widget-content widget-content-area br-8 p-3 stat-card h-100 border-start border-success border-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted text-uppercase fw-bold small">Surat Keluar Hari Ini</span>
                            <h3 class="fw-bold mt-2 mb-1 text-success">{{ number_format($keluarHariIni) }}</h3>
                            <span class="badge badge-light-secondary small">
                                Bulan ini: <strong>{{ number_format($keluarBulanIni) }}</strong> berkas
                            </span>
                        </div>
                        <div class="stat-icon bg-light-success text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        </div>
                    </div>
                </div>
            </div>
            @endcanany

            <!-- Card 3: Disposisi Menunggu Tindakan -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                <div class="widget-content widget-content-area br-8 p-3 stat-card h-100 border-start border-warning border-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted text-uppercase fw-bold small">Menunggu Disposisi Anda</span>
                            <h3 class="fw-bold mt-2 mb-1 text-warning">{{ number_format($disposisiMenungguUser) }}</h3>
                            <span class="badge badge-light-warning small">
                                @if($isAdmin)
                                    Total sistem: <strong>{{ number_format($totalDisposisiGlobal) }}</strong>
                                @else
                                    Perlu tindak lanjut
                                @endif
                            </span>
                        </div>
                        <div class="stat-icon bg-light-warning text-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Surat Selesai -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                <div class="widget-content widget-content-area br-8 p-3 stat-card h-100 border-start border-info border-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted text-uppercase fw-bold small">Surat Selesai Diarsip</span>
                            <h3 class="fw-bold mt-2 mb-1 text-info">{{ number_format($suratSelesai) }}</h3>
                            <span class="badge badge-light-info small">
                                Arsip tuntas
                            </span>
                        </div>
                        <div class="stat-icon bg-light-info text-info">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row Charts & Disposisi Tasks -->
        <div class="row mb-4">
            <!-- ApexCharts Monthly Trend -->
            <div class="col-xl-8 col-lg-7 col-md-12 mb-4">
                <div class="widget-content widget-content-area br-8 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2 me-1 text-primary"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            Tren Persuratan Tahun {{ $currentYear }}
                        </h5>
                        <span class="badge badge-outline-primary">Bulanan</span>
                    </div>
                    <div id="chart-persuratan" style="min-height: 320px;"></div>
                </div>
            </div>

            <!-- Disposisi Action List / Recent Pending -->
            <div class="col-xl-4 col-lg-5 col-md-12 mb-4">
                <div class="widget-content widget-content-area br-8 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h6 class="fw-bold mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle me-1 text-warning"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            Tindak Lanjut Anda
                        </h6>
                        <a href="{{ route('inbox') }}" class="btn btn-sm btn-link text-primary text-decoration-none p-0">Semua</a>
                    </div>

                    <div class="action-list" style="max-height: 330px; overflow-y: auto;">
                        @if($pendingDisposisis->isNotEmpty())
                            @foreach($pendingDisposisis as $dsp)
                                <div class="p-2 mb-2 border rounded action-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong class="text-truncate small text-dark" style="max-width: 170px;">
                                            {{ $dsp->inbox->nomor_surat ?? '-' }}
                                        </strong>
                                        <small class="text-muted" style="font-size: 0.72rem;">{{ $dsp->created_at ? $dsp->created_at->diffForHumans() : '-' }}</small>
                                    </div>
                                    <p class="text-muted small mb-1 text-truncate" title="{{ $dsp->inbox->perihal ?? '-' }}">
                                        {{ $dsp->inbox->perihal ?? '-' }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-secondary" style="font-size: 0.72rem;">Dari: {{ $dsp->pengirim->nama_lengkap ?? 'Pimpinan' }}</small>
                                        <a href="{{ route('inbox') }}" class="badge badge-light-primary text-decoration-none">Buka</a>
                                    </div>
                                </div>
                            @endforeach
                        @elseif($isAdmin && $recentInboxes->isNotEmpty())
                            @foreach($recentInboxes as $rin)
                                <div class="p-2 mb-2 border rounded action-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong class="text-truncate small text-dark" style="max-width: 170px;">
                                            {{ $rin->nomor_surat }}
                                        </strong>
                                        <small class="text-muted" style="font-size: 0.72rem;">{{ $rin->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="text-muted small mb-1 text-truncate">
                                        {{ $rin->perihal }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <span class="badge badge-light-warning" style="font-size: 0.7rem;">Diproses</span>
                                        <a href="{{ route('inbox') }}" class="badge badge-light-primary text-decoration-none">Buka</a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-square mb-2 text-success"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                <p class="mb-0 small">Semua surat dan disposisi telah selesai ditindaklanjuti.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- FullCalendar Persuratan -->
        <div class="row layout-top-spacing layout-spacing" id="cancel-row">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="widget-content widget-content-area br-8 p-3">
                    <h5 class="fw-bold mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar me-1 text-primary"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Kalender Agenda Persuratan
                    </h5>
                    <div class="calendar-container">
                        <div class="calendar"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail Kalender -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Surat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nomor Surat</label>
                                <input id="event-number" type="text" class="form-control" readonly>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Dari/Kepada</label>
                                <textarea name="event-title" id="event-title" class="form-control" cols="30" rows="3" readonly></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Isi Surat</label>
                                <textarea name="event-content" id="event-content" class="form-control" cols="30" rows="5" readonly></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Tanggal Surat</label>
                                <input id="event-date" type="date" class="form-control" readonly>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex mt-4">
                                    <div class="n-chk me-3">
                                        <div class="form-check form-check-primary form-check-inline">
                                            <input class="form-check-input" type="radio" name="event-level" value="Masuk" id="rMasuk" disabled>
                                            <label class="form-check-label" for="rMasuk">Masuk</label>
                                        </div>
                                    </div>
                                    <div class="n-chk">
                                        <div class="form-check form-check-danger form-check-inline">
                                            <input class="form-check-input" type="radio" name="event-level" value="Keluar" id="rKeluar" disabled>
                                            <label class="form-check-label" for="rKeluar">Keluar</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('templates/plugins/src/apex/apexcharts.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/fullcalendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/uuid/uuid4.min.js') }}"></script>
    <script src="{{ asset('templates/assets/js/custom-fullcalendar.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ApexCharts Monthly Trend Configuration
            const chartOptions = {
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                series: [
                    {
                        name: 'Surat Masuk',
                        data: @json($trendMasuk)
                    },
                    {
                        name: 'Surat Keluar',
                        data: @json($trendKeluar)
                    }
                ],
                colors: ['#4361ee', '#1abc9c'],
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 95, 100]
                    }
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return Math.round(val);
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + ' Berkas Surat';
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                grid: {
                    borderColor: '#f1f2f3',
                    strokeDashArray: 4
                }
            };

            const chartEl = document.querySelector('#chart-persuratan');
            if (chartEl) {
                const chart = new ApexCharts(chartEl, chartOptions);
                chart.render();
            }
        });
    </script>
@endsection
