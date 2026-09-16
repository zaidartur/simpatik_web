@php
    $user = Auth::user();
    $encryptedUuid = \Illuminate\Support\Facades\Crypt::encryptString($ibx->uuid);
    $encodedIbx = base64_encode(json_encode($ibx));
@endphp
<div class="btn-group-vertical" role="group" aria-label="Inbox Actions">
    {{-- Detail Surat --}}
    <button type="button" class="btn btn-info bs-tooltip" title="Detail Surat" onclick="viewSurat('{{ $encodedIbx }}')">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
    </button>

    {{-- Edit Surat --}}
    @can('edit surat masuk')
    <a href="{{ route('inbox.edit', $encryptedUuid) }}" type="button" class="btn btn-outline-warning bs-tooltip" title="Edit Surat">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
    </a>
    @endcan

    {{-- Tindak Lanjut Administrator --}}
    @if($user && $user->hasRole(['administrator']))
        @if($ibx->status_surat == 'diproses')
            <button type="button" class="btn btn-outline-primary bs-tooltip" title="Tindak Lanjut (Teruskan)" onclick="followUp('{{ $encryptedUuid }}')">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            </button>
        @else
            <button type="button" class="btn btn-outline-primary bs-tooltip" title="Batalkan Tindak Lanjut" onclick="cancelFollowUp('{{ $encryptedUuid }}')">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            </button>
        @endif
    @elseif($user && ($user->leveluser->tindak_lanjut ?? false) && ($ibx->status_surat == 'diproses') && ($ibx->posisi_level == $user->level))
        <button type="button" class="btn btn-outline-primary bs-tooltip" title="Tindak Lanjut (Teruskan)" onclick="followUp('{{ $encryptedUuid }}')">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg>
        </button>
    @endif

    {{-- Status Selesai --}}
    @if($ibx->status_surat == 'selesai')
        <button type="button" class="btn btn-outline-success bs-tooltip" title="Surat sudah ditindak lanjuti">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </button>
    @endif

    {{-- Menunggu Tindakan --}}
    @if($user && ($user->leveluser->tindak_lanjut ?? false) && $ibx->status_surat == 'diproses' && !empty($ibx->mydisposisi) && $ibx->mydisposisi->is_completed == false)
        <button type="button" class="btn btn-outline-secondary bs-tooltip" title="Menunggu Tindakan">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader spin"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
        </button>
    @endif

    {{-- Cetak Surat --}}
    @if($ibx->status_surat == 'selesai')
        @if($user && $user->hasRole(['administrator', 'admin']))
            <div class="btn-group" role="group">
                <button id="btndefault" type="button" class="btn btn-outline-info bs-tooltip dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Cetak">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                </button>
                <div class="dropdown-menu" aria-labelledby="btndefault">
                    <a href="javascript:void(0);" class="dropdown-item" onclick="printPdf('{{ $encryptedUuid }}')"><i class="flaticon-home-fill-1 mr-1"></i>Disposisi</a>
                    <a href="javascript:void(0);" class="dropdown-item" onclick="printKartu('{{ $encryptedUuid }}')"><i class="flaticon-gear-fill mr-1"></i>Kartu Surat Masuk</a>
                </div>
            </div>
        @else
            <button type="button" class="btn btn-outline-info bs-tooltip" title="Cetak Disposisi" onclick="printPdf('{{ $encryptedUuid }}')">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            </button>
        @endif
    @endif

    {{-- Hapus Surat --}}
    @can('hapus surat masuk')
    <button type="button" class="btn btn-danger bs-tooltip" title="Hapus Surat" onclick="_delete('{{ $encryptedUuid }}')">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
    </button>
    @endcan

    {{-- Disposisi / Tanggapi Surat --}}
    @if(($ibx->status_surat == 'diproses' && isset($ibx->mydisposisi->is_completed) && $ibx->mydisposisi->is_completed) || ($ibx->status_surat == 'diproses' && !isset($ibx->mydisposisi) && !empty($ibx->getdisposisi->id) && !$ibx->getdisposisi->is_completed))
    <button type="button" class="btn btn-secondary bs-tooltip" title="Disposisi Surat" onclick="_reply('{{ $encryptedUuid }}')">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
    </button>
    @endif
</div>
