<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    // return view('welcome');
	//
    return redirect()->route('home');
});

Auth::routes(['register' => false, 'verify' => false]);


Route::prefix('/')->middleware(['auth', 'throttle:web-global'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('daftar-surat', [App\Http\Controllers\HomeController::class, 'list_surat'])->name('home.list');
    Route::get('settings', [App\Http\Controllers\HomeController::class, 'settings'])->name('admin.settings');
    Route::post('update-profile', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('admin.updateProfile');
    Route::post('change-password', [App\Http\Controllers\HomeController::class, 'changePassword'])->middleware('throttle:sensitive-auth')->name('admin.changePassword');
});

Route::post('detail-jra', [App\Http\Controllers\InboxController::class, 'get_jra'])->middleware(['auth'])->name('jra');

Route::prefix('/surat-masuk')->middleware(['auth'])->group(function () {
    Route::get('/view', [App\Http\Controllers\InboxController::class, 'index'])->name('inbox');
    Route::get('/daftar-surat', [App\Http\Controllers\InboxController::class, 'serverside'])->name('inbox.ssr');
    Route::get('/buat-surat', [App\Http\Controllers\InboxController::class, 'create'])->name('inbox.create');
    Route::post('/simpan-surat', [App\Http\Controllers\InboxController::class, 'store'])->name('inbox.store');
    Route::get('/edit-surat/{id}', [App\Http\Controllers\InboxController::class, 'edit'])->name('inbox.edit');
    Route::post('/update-surat', [App\Http\Controllers\InboxController::class, 'update'])->name('inbox.update');
    Route::post('/nomor-urut', [App\Http\Controllers\InboxController::class, 'nomor_urut'])->name('inbox.urut');
    Route::post('/hapus-surat', [App\Http\Controllers\InboxController::class, 'destroy'])->name('inbox.destroy');
    Route::get('/lihat-surat/{id}', [App\Http\Controllers\InboxController::class, 'show'])->name('inbox.show');
    Route::post('/diteruskan', [App\Http\Controllers\InboxController::class, 'forward'])->name('inbox.forward');
    Route::post('/tanggapi', [App\Http\Controllers\InboxController::class, 'reply'])->name('inbox.reply');

    Route::get('/print-pdf/{id}', [App\Http\Controllers\InboxController::class, 'view_pdf'])->name('inbox.pdf');
    Route::get('/lihat-file/{id}', [App\Http\Controllers\InboxController::class, 'view_file'])->name('inbox.view');
});

Route::prefix('/surat-keluar')->middleware(['auth'])->group(function () {
    Route::get('/view', [App\Http\Controllers\OutboxController::class, 'index'])->name('outbox');
    Route::get('/duplikasi-surat', [App\Http\Controllers\OutboxController::class, 'duplicate'])->name('outbox.duplicate');
    Route::get('/daftar-surat', [App\Http\Controllers\OutboxController::class, 'serverside'])->name('outbox.ssr');
    Route::get('/buat-surat', [App\Http\Controllers\OutboxController::class, 'create'])->name('outbox.create');
    Route::post('/simpan-surat', [App\Http\Controllers\OutboxController::class, 'store'])->name('outbox.store');
    Route::get('/edit-surat/{id}', [App\Http\Controllers\OutboxController::class, 'edit'])->name('outbox.edit');
    Route::get('/lihat-surat/{id}', [App\Http\Controllers\OutboxController::class, 'show'])->name('outbox.show');
    Route::post('/nomor-urut', [App\Http\Controllers\OutboxController::class, 'nomor_urut'])->name('outbox.urut');
    Route::get('/nomor-sppd', [App\Http\Controllers\OutboxController::class, 'last_sppd'])->name('outbox.sppd.last');
    Route::get('/template/{uid}', [App\Http\Controllers\OutboxController::class, 'template_test'])->name('outbox.template');
    Route::get('/lihat-surat-duplikat/{name}', [App\Http\Controllers\HomeController::class, 'view_duplikat'])->name('outbox.dup.view');
    Route::get('/unduh-surat-duplikat/{name}', [App\Http\Controllers\HomeController::class, 'download_duplikat'])->name('outbox.dup.download');
    Route::get('/print-pdf/{id}', [App\Http\Controllers\OutboxController::class, 'view_pdf'])->name('outbox.pdf');

    Route::post('/update-surat', [App\Http\Controllers\OutboxController::class, 'update'])->name('outbox.update');
    // Route::post('nomor-urut', [App\Http\Controllers\OutboxController::class, 'nomor_urut'])->name('outbox.urut');
    Route::post('/hapus-surat', [App\Http\Controllers\OutboxController::class, 'destroy'])->name('outbox.destroy');
    Route::post('/cek-nomor-surat', [App\Http\Controllers\OutboxController::class, 'check_surat'])->name('outbox.check');
    Route::post('/duplikat-surat', [App\Http\Controllers\OutboxController::class, 'duplikat'])->name('outbox.duplikat');

    Route::get('/lihat-file/{id}', [App\Http\Controllers\OutboxController::class, 'view_file'])->name('outbox.view');
});

Route::prefix('/sppd')->middleware(['auth'])->group(function () {
    Route::get('/view', [App\Http\Controllers\SppdController::class, 'index'])->name('sppd');
    Route::get('/json-list', [App\Http\Controllers\SppdController::class, 'list'])->name('sppd.list');
    Route::get('/daftar-sppd', [App\Http\Controllers\SppdController::class, 'serverside'])->name('sppd.ssr');
    Route::get('/buat-sppd', [App\Http\Controllers\SppdController::class, 'create'])->name('sppd.create');
    Route::post('/simpan-sppd', [App\Http\Controllers\SppdController::class, 'store'])->name('sppd.store');
    Route::post('/simpan-sppd-query', [App\Http\Controllers\SppdController::class, 'save'])->name('sppd.save');
    Route::post('/update-sppd', [App\Http\Controllers\SppdController::class, 'update'])->name('sppd.update');
    Route::post('/hapus-sppd', [App\Http\Controllers\SppdController::class, 'destroy'])->name('sppd.destroy');
    Route::get('/print-pdf/{id}', [App\Http\Controllers\SppdController::class, 'print_pdf'])->name('sppd.pdf');
});


Route::prefix('/user')->middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/view', [App\Http\Controllers\UserController::class, 'index'])->name('user');
    Route::post('/simpan-user', [App\Http\Controllers\UserController::class, 'store'])->name('user.store');
    Route::post('/update-user', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');
    Route::post('/hapus-user', [App\Http\Controllers\UserController::class, 'destroy'])->name('user.destroy');

    Route::post('/check-user', [App\Http\Controllers\UserController::class, 'check_user'])->middleware('throttle:sensitive-auth')->name('user.check');
    Route::post('/ubah-password-user', [App\Http\Controllers\UserController::class, 'change_pwd'])->middleware('throttle:sensitive-auth')->name('user.change_pwd');
    Route::post('/toggle-status', [App\Http\Controllers\UserController::class, 'toggle_status'])->name('user.toggle');
});

Route::prefix('/laporan')->middleware(['auth'])->group(function () {
    Route::get('/statistik', [App\Http\Controllers\LaporanController::class, 'statistik'])->name('report.statistik');
    Route::get('/tindak-lanjut', [App\Http\Controllers\LaporanController::class, 'tindak_lanjut'])->name('report.next');
    Route::get('/agenda', [App\Http\Controllers\LaporanController::class, 'agenda'])->name('report.agenda');
    Route::get('/tabel-statistik', [App\Http\Controllers\LaporanController::class, 'statistik_ssr'])->name('report.statistik.ssr');
    Route::get('/tabel-tindak-lanjut', [App\Http\Controllers\LaporanController::class, 'tindak_lanjut_ssr'])->name('report.next.ssr');
    Route::get('/tabel-agenda', [App\Http\Controllers\LaporanController::class, 'agenda_ssr'])->name('report.agenda.ssr');
    
    Route::get('/print-agenda', [App\Http\Controllers\LaporanController::class, 'agenda_print'])->name('report.agenda.print');
    Route::get('/print-agenda-fpdf', [App\Http\Controllers\LaporanController::class, 'agenda_print_fpdf'])->name('report.agenda.print_fpdf');
    Route::get('/export-agenda', [App\Http\Controllers\LaporanController::class, 'export_agenda'])->middleware('throttle:exports')->name('report.agenda.export');
    Route::get('/export-statistik', [App\Http\Controllers\LaporanController::class, 'export_statistik'])->middleware('throttle:exports')->name('report.statistik.export');
    Route::get('/cetak-tindak-lanjut', [App\Http\Controllers\LaporanController::class, 'tindak_lanjut_print'])->name('report.next.print');
    Route::get('/export-tindak-lanjut', [App\Http\Controllers\LaporanController::class, 'tindak_lanjut_excel'])->middleware('throttle:exports')->name('report.next.excel');
});

Route::get('/search', [App\Http\Controllers\SearchController::class, 'search'])->middleware(['auth'])->name('search');

Route::prefix('/notifikasi')->middleware(['auth'])->group(function () {
    Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'unread_count'])->name('notifikasi.count');
    Route::get('/recent', [App\Http\Controllers\NotificationController::class, 'recent'])->name('notifikasi.recent');
    Route::post('/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'mark_read'])->name('notifikasi.read');
    Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'mark_all_read'])->name('notifikasi.mark_all_read');
});

Route::prefix('/audit-log')->middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/view', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('audit');
    Route::get('/daftar-log', [App\Http\Controllers\ActivityLogController::class, 'serverside'])->name('audit.ssr');
});

Route::prefix('/referensi')->middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/{type?}', [App\Http\Controllers\ReferensiController::class, 'index'])->name('referensi.index');
    Route::post('/{type}/simpan', [App\Http\Controllers\ReferensiController::class, 'store'])->name('referensi.store');
    Route::post('/{type}/update', [App\Http\Controllers\ReferensiController::class, 'update'])->name('referensi.update');
    Route::post('/{type}/hapus', [App\Http\Controllers\ReferensiController::class, 'destroy'])->name('referensi.destroy');
});

Route::prefix('/instansi')->middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/view', [App\Http\Controllers\InstansiController::class, 'index'])->name('instansi');
    Route::post('/simpan-instansi', [App\Http\Controllers\InstansiController::class, 'save_instansi'])->name('instansi.save');
    Route::post('/update-instansi', [App\Http\Controllers\InstansiController::class, 'update_instansi'])->name('instansi.update');
    Route::post('/hapus-instansi', [App\Http\Controllers\InstansiController::class, 'delete_instansi'])->name('instansi.delete');
});

Route::prefix('/pimpinan')->middleware(['auth'])->group(function () {
    Route::get('/view', [App\Http\Controllers\HomeController::class, 'list_pejabat'])->name('pimpinan');
    Route::post('/simpan-pimpinan', [App\Http\Controllers\HomeController::class, 'save_pimpinan'])->name('pimpinan.save');
    Route::post('/update-pimpinan', [App\Http\Controllers\HomeController::class, 'update_pimpinan'])->name('pimpinan.update');
    Route::post('/set-default-pimpinan', [App\Http\Controllers\HomeController::class, 'set_default'])->name('pimpinan.default');
    Route::post('/hapus-pimpinan', [App\Http\Controllers\HomeController::class, 'delete_pimpinan'])->name('pimpinan.delete');
});

Route::prefix('/aplikasi')->middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/view', [App\Http\Controllers\ApplicationController::class, 'index'])->name('apps');
    Route::post('/update-permission', [App\Http\Controllers\ApplicationController::class, 'update_permission'])->name('apps.permission.update');
});

// Fitur Migrasi Data Legacy hanya aktif dan terdaftar pada mode local & testing (otomatis 404 pada production)
if (app()->environment(['local', 'testing'])) {
    Route::prefix('/legacy-migration')->middleware(['auth', 'role:administrator'])->group(function () {
        Route::get('/', [App\Http\Controllers\LegacyMigrationController::class, 'index'])->name('migration.index');
        Route::post('/process', [App\Http\Controllers\LegacyMigrationController::class, 'process'])->name('migration.process');
    });
}