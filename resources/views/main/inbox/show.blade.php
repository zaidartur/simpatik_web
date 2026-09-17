@extends('layouts.layout')

@section('title', 'Detail Surat Masuk')

@section('content')
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">

            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="widget-content widget-content-area br-8 p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('inbox') }}" class="btn btn-outline-secondary btn-sm">
                                &larr; Kembali
                            </a>
                            <h4 class="mb-0">Detail Surat Masuk</h4>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('inbox.pdf', \Illuminate\Support\Facades\Crypt::encryptString($inbox->uuid)) }}?type=disposisi" target="_blank" class="btn btn-outline-info btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                Cetak Disposisi
                            </a>
                            @if($inbox->softcopy)
                                <a href="{{ route('inbox.view', \Illuminate\Support\Facades\Crypt::encryptString($inbox->softcopy)) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                    Lihat File Scan
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- Kolom Informasi Surat -->
                        <div class="col-lg-6">
                            <div class="card shadow-sm border h-100">
                                <div class="card-header bg-light fw-bold">
                                    Informasi Surat Masuk
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered mb-0">
                                        <tr>
                                            <th style="width: 35%">No. Agenda</th>
                                            <td><span class="badge badge-primary">{{ $inbox->no_agenda }}/{{ $inbox->year }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>No. Surat</th>
                                            <td><strong>{{ $inbox->no_surat ?: '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Surat</th>
                                            <td>{{ \Carbon\Carbon::parse($inbox->tgl_surat)->translatedFormat('d F Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Diterima</th>
                                            <td>{{ \Carbon\Carbon::parse($inbox->tgl_diterima)->translatedFormat('d F Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pengirim / Dari</th>
                                            <td>{{ $inbox->dari }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat / Wilayah</th>
                                            <td>{{ $inbox->wilayah ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Perihal</th>
                                            <td><strong class="text-dark">{{ $inbox->perihal }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Isi Ringkas</th>
                                            <td>{{ $inbox->isi_surat ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sifat Surat</th>
                                            <td>{{ $inbox->sifat->nama_sifat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tempat Berkas</th>
                                            <td>{{ $inbox->berkas->nama ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status Surat</th>
                                            <td>
                                                <span class="badge {{ $inbox->status_surat === 'selesai' ? 'badge-success' : 'badge-warning' }}">
                                                    {{ strtoupper($inbox->status_surat) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Posisi Terakhir</th>
                                            <td>
                                                {{ $inbox->posisi->nama_lengkap ?? '-' }} 
                                                <span class="text-muted">({{ $inbox->posisi->leveluser->nama ?? '-' }})</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Visual Timeline Disposisi -->
                        <div class="col-lg-6">
                            <div class="card shadow-sm border h-100">
                                <div class="card-header bg-light fw-bold">
                                    Jejak Rantai Disposisi
                                </div>
                                <div class="card-body">
                                    <x-disposisi-timeline :inbox="$inbox" />
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
