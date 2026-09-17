@extends('layouts.layout')

@section('title', 'Pencarian Global Surat')

@section('css')
    <style>
        .search-highlight {
            background-color: #fff3cd;
            font-weight: bold;
            padding: 0 2px;
            border-radius: 2px;
        }
        .search-result-card {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            border-left: 4px solid transparent;
        }
        .search-result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .card-masuk {
            border-left-color: #4361ee !important;
        }
        .card-keluar {
            border-left-color: #00ab55 !important;
        }
    </style>
@endsection

@section('content')
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">

            <!-- Search Header Box -->
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="widget-content widget-content-area br-8 p-4">
                    <form action="{{ route('search') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                </span>
                                <input type="text" name="q" class="form-control form-control-lg border-start-0" placeholder="Ketik kata kunci perihal, no. surat, no. agenda, atau instansi..." value="{{ $term }}" autofocus>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                Cari Surat
                            </button>
                        </div>
                    </form>

                    <!-- Filter Tabs -->
                    <div class="mt-4 pt-3 border-top d-flex gap-2 flex-wrap align-items-center">
                        <span class="text-muted small me-2">Kategori:</span>
                        <a href="{{ route('search', ['q' => $term, 'type' => 'all']) }}" class="btn btn-sm {{ $filterType === 'all' ? 'btn-dark' : 'btn-outline-dark' }}">
                            Semua ({{ $totalCount }})
                        </a>
                        <a href="{{ route('search', ['q' => $term, 'type' => 'masuk']) }}" class="btn btn-sm {{ $filterType === 'masuk' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Surat Masuk ({{ $countMasuk }})
                        </a>
                        <a href="{{ route('search', ['q' => $term, 'type' => 'keluar']) }}" class="btn btn-sm {{ $filterType === 'keluar' ? 'btn-success' : 'btn-outline-success' }}">
                            Surat Keluar ({{ $countKeluar }})
                        </a>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                @if(!empty($term))
                    <p class="text-muted mb-3">
                        Menemukan <strong>{{ $totalCount }}</strong> hasil untuk kata kunci <em>"{{ $term }}"</em>:
                    </p>
                @endif

                <div class="row g-3">
                    @forelse($results as $item)
                        @php
                            $isMasuk = $item->search_type === 'masuk';
                            $badgeClass = $isMasuk ? 'badge-light-primary text-primary' : 'badge-light-success text-success';
                            $cardBorder = $isMasuk ? 'card-masuk' : 'card-keluar';
                            $typeLabel = $isMasuk ? 'SURAT MASUK' : 'SURAT KELUAR';
                            $highlight = function($text) use ($term) {
                                if (empty($term)) return e($text);
                                return preg_replace('/(' . preg_quote($term, '/') . ')/i', '<mark class="search-highlight">$1</mark>', e($text));
                            };
                        @endphp

                        <div class="col-12">
                            <div class="card search-result-card {{ $cardBorder }} shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $badgeClass }} fw-bold px-2 py-1">{{ $typeLabel }}</span>
                                            <span class="badge badge-light-secondary">Agenda: {{ $item->no_agenda }}/{{ $item->year }}</span>
                                            @if($item->sifat)
                                                <span class="badge badge-light-info">{{ $item->sifat->nama_sifat }}</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            Tanggal: {{ \Carbon\Carbon::parse($item->tgl_surat)->format('d M Y') }}
                                        </small>
                                    </div>

                                    <h5 class="card-title mb-1 text-dark">
                                        {!! $highlight($item->perihal ?: '(Tanpa Perihal)') !!}
                                    </h5>

                                    <div class="row g-1 text-muted small my-2">
                                        <div class="col-md-4">
                                            <strong>No. Surat:</strong> {!! $highlight($item->no_surat ?: '-') !!}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>{{ $isMasuk ? 'Dari:' : 'Kepada:' }}</strong> {!! $highlight($isMasuk ? $item->dari : $item->kepada) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Wilayah:</strong> {!! $highlight($item->wilayah ?: '-') !!}
                                        </div>
                                    </div>

                                    @if(!empty($item->isi_surat))
                                        <p class="card-text text-secondary small bg-light p-2 rounded mb-2">
                                            {!! $highlight(\Illuminate\Support\Str::limit($item->isi_surat, 200)) !!}
                                        </p>
                                    @endif

                                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                                        @if($item->softcopy)
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="viewFile('{{ $isMasuk ? 'surat-masuk' : 'surat-keluar' }}', '{{ \Illuminate\Support\Facades\Crypt::encryptString($item->softcopy) }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                                Berkas Scan
                                            </button>
                                        @endif
                                        @if($isMasuk)
                                            <a href="{{ route('inbox') }}" class="btn btn-sm btn-primary">
                                                Buka di Surat Masuk
                                            </a>
                                        @else
                                            <a href="{{ route('outbox') }}" class="btn btn-sm btn-success">
                                                Buka di Surat Keluar
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="mb-3 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                            </div>
                            <h5>Tidak ada surat yang sesuai kriteria pencarian</h5>
                            <p class="text-muted">Coba gunakan kata kunci lain seperti nomor surat, nama pengirim, perihal, atau bersihkan filter.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        function viewFile(type, encUid) {
            window.open(`/${type}/lihat-file/${encUid}`, '_blank');
        }
    </script>
@endsection
