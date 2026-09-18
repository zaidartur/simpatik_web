@php
    $encryptedUuid = \Illuminate\Support\Facades\Crypt::encryptString($ibx->uuid);
    $encodedIbx = base64_encode(json_encode($ibx));
@endphp
<div class="btn-group-vertical" role="group" aria-label="Outbox Actions">
    {{-- Detail Surat Keluar --}}
    <button type="button" class="btn btn-outline-info bs-tooltip" title="Detail" onclick="_detail('{{ $encodedIbx }}')">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
    </button>

    {{-- Edit Surat Keluar --}}
    @can('edit surat keluar')
    <a href="{{ route('outbox.edit', $encryptedUuid) }}" type="button" class="btn btn-outline-warning bs-tooltip" title="Edit Surat">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
    </a>
    @endcan

    {{-- Cetak Kartu --}}
    @can('cetak surat keluar')
    <button type="button" class="btn btn-outline-info bs-tooltip" title="Cetak Kartu" onclick="printPdf('{{ $encryptedUuid }}')">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
    </button>
    @endcan

    {{-- Hapus Surat Keluar --}}
    @can('hapus surat keluar')
    <button type="button" class="btn btn-danger bs-tooltip" title="Hapus Data" onclick="_delete('{{ $encryptedUuid }}')">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
    </button>
    @endcan
</div>
