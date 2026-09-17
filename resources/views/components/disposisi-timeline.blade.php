@props(['inbox'])

<div class="disposisi-timeline-container my-3">
    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-git-commit text-primary"><circle cx="12" cy="12" r="4"></circle><line x1="1.05" y1="12" x2="7" y2="12"></line><line x1="17.01" y1="12" x2="22.96" y2="12"></line></svg>
        Alur Jejak Disposisi Persuratan
    </h6>

    <div class="timeline-simple position-relative ps-4" style="border-left: 2px solid #e0e6ed; margin-left: 12px;">
        <!-- Tahap 1: Registrasi Surat -->
        <div class="timeline-item position-relative mb-4">
            <div class="timeline-badge position-absolute rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="left: -29px; top: 0; width: 24px; height: 24px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <div class="card shadow-none border bg-light p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <strong class="text-success small">1. Registrasi & Input Surat</strong>
                    <span class="badge badge-light-success text-success">{{ \Carbon\Carbon::parse($inbox->tgl_diterima)->format('d M Y') }}</span>
                </div>
                <p class="mb-1 small mt-1">
                    Surat dari <strong>{{ $inbox->dari }}</strong> diterima dan diinput oleh 
                    <strong>{{ $inbox->creator->nama_lengkap ?? 'Operator Persuratan' }}</strong>.
                </p>
                <small class="text-muted">No. Agenda: {{ $inbox->no_agenda }}/{{ $inbox->year }} | No. Surat: {{ $inbox->no_surat }}</small>
            </div>
        </div>

        <!-- Tahap Disposisi Berantai -->
        @forelse($inbox->disposisi->sortBy('created_at') as $idx => $dispo)
            @php
                $isDone = $dispo->is_completed;
                $stepNumber = $idx + 2;
                $pengirimName = $dispo->pengirim->nama_lengkap ?? ($dispo->pengirim->username ?? 'Pengirim');
                $pengirimLevel = $dispo->pengirim->leveluser->nama ?? 'Jabatan';
                $penerimaName = $dispo->penerima->nama_lengkap ?? ($dispo->penerima->username ?? 'Penerima');
                $penerimaLevel = $dispo->penerima->leveluser->nama ?? 'Jabatan';
            @endphp
            <div class="timeline-item position-relative mb-4">
                <div class="timeline-badge position-absolute rounded-circle {{ $isDone ? 'bg-primary' : 'bg-warning' }} text-white d-flex align-items-center justify-content-center" style="left: -29px; top: 0; width: 24px; height: 24px;">
                    @if($isDone)
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    @endif
                </div>
                <div class="card shadow-none border p-3 {{ $isDone ? 'bg-white' : 'border-warning bg-light-warning' }}">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
                        <strong class="{{ $isDone ? 'text-primary' : 'text-warning' }} small">
                            {{ $stepNumber }}. Tindak Lanjut: {{ $pengirimLevel }} &rarr; {{ $penerimaLevel }}
                        </strong>
                        <span class="badge {{ $isDone ? 'badge-light-primary text-primary' : 'badge-light-warning text-warning' }}">
                            {{ $isDone ? 'SELESAI DITANGGAPI' : 'MENUNGGU TANGGAPAN' }}
                        </span>
                    </div>

                    <div class="small my-1">
                        <div class="text-secondary">
                            <span class="fw-semibold">Diteruskan oleh:</span> {{ $pengirimName }} ({{ $pengirimLevel }})
                        </div>
                        <div class="text-secondary">
                            <span class="fw-semibold">Kepada:</span> {{ $penerimaName }} ({{ $penerimaLevel }})
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            Waktu Disposisi: {{ $dispo->created_at ? $dispo->created_at->translatedFormat('d M Y H:i') : '-' }}
                        </div>
                    </div>

                    @if(!empty($dispo->catatan_disposisi))
                        <div class="mt-2 p-2 bg-light rounded border-start border-3 border-info small">
                            <strong>Instruksi / Catatan Disposisi:</strong>
                            <div class="text-dark mt-1">{{ $dispo->catatan_disposisi }}</div>
                            @if($dispo->pimpinan)
                                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                    Pejabat Penandatangan: <em>{{ $dispo->pimpinan->nama }}</em> ({{ $dispo->pimpinan->jabatan }})
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="timeline-item position-relative mb-4">
                <div class="timeline-badge position-absolute rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="left: -29px; top: 0; width: 24px; height: 24px;">
                    &bull;
                </div>
                <div class="card shadow-none border bg-light p-3">
                    <p class="mb-0 text-muted small">Surat ini belum memiliki riwayat disposisi (surat selesai di tingkat operator / non-tindak lanjut).</p>
                </div>
            </div>
        @endforelse

        <!-- Tahap Akhir: Status Terkini -->
        <div class="timeline-item position-relative">
            <div class="timeline-badge position-absolute rounded-circle {{ $inbox->status_surat === 'selesai' ? 'bg-success' : 'bg-info' }} text-white d-flex align-items-center justify-content-center" style="left: -29px; top: 0; width: 24px; height: 24px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            </div>
            <div class="card shadow-none border p-3 {{ $inbox->status_surat === 'selesai' ? 'border-success bg-light-success' : 'border-info bg-light-info' }}">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <strong class="{{ $inbox->status_surat === 'selesai' ? 'text-success' : 'text-info' }} small">
                        Status Akhir Surat
                    </strong>
                    <span class="badge {{ $inbox->status_surat === 'selesai' ? 'badge-success' : 'badge-info' }}">
                        {{ strtoupper($inbox->status_surat) }}
                    </span>
                </div>
                <p class="mb-0 small mt-1 text-muted">
                    Posisi terakhir surat berada pada: 
                    <strong>{{ $inbox->posisi->nama_lengkap ?? ($inbox->posisi->username ?? 'Unit Penerima') }}</strong> 
                    ({{ $inbox->posisi->leveluser->nama ?? '-' }}).
                </p>
            </div>
        </div>
    </div>
</div>
