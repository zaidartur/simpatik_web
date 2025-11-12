<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('home');
});

Route::get('/test', function () {
    return view('test');
});
Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::prefix('/')->middleware(['auth'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('daftar-surat', [App\Http\Controllers\HomeController::class, 'list_surat'])->name('home.list');
    Route::get('settings', [App\Http\Controllers\HomeController::class, 'settings'])->name('admin.settings');
    Route::post('update-profile', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('admin.updateProfile');
    Route::post('change-password', [App\Http\Controllers\HomeController::class, 'changePassword'])->name('admin.changePassword');
});

Route::post('detail-jra', [App\Http\Controllers\InboxController::class, 'get_jra'])->middleware(['auth'])->name('jra');

Route::prefix('/surat-masuk')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\InboxController::class, 'index'])->name('inbox');
    Route::get('/daftar-surat', [App\Http\Controllers\InboxController::class, 'serverside'])->name('inbox.ssr');
    Route::get('buat-surat', [App\Http\Controllers\InboxController::class, 'create'])->name('inbox.create');
    Route::post('simpan-surat', [App\Http\Controllers\InboxController::class, 'store'])->name('inbox.store');
    Route::get('edit-surat/{id}', [App\Http\Controllers\InboxController::class, 'edit'])->name('inbox.edit');
    Route::post('update-surat/{id}', [App\Http\Controllers\InboxController::class, 'update'])->name('inbox.update');
    Route::post('nomor-urut', [App\Http\Controllers\InboxController::class, 'nomor_urut'])->name('inbox.urut');
    Route::post('hapus-surat', [App\Http\Controllers\InboxController::class, 'destroy'])->name('inbox.destroy');
    Route::get('lihat-surat/{id}', [App\Http\Controllers\InboxController::class, 'show'])->name('inbox.show');

    Route::get('print-pdf/{id}', [App\Http\Controllers\InboxController::class, 'view_pdf'])->name('inbox.pdf');
});

Route::prefix('/surat-keluar')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\OutboxController::class, 'index'])->name('outbox');
    Route::get('/daftar-surat', [App\Http\Controllers\OutboxController::class, 'serverside'])->name('outbox.ssr');
    Route::get('buat-surat', [App\Http\Controllers\OutboxController::class, 'create'])->name('outbox.create');
    Route::post('simpan-surat', [App\Http\Controllers\OutboxController::class, 'store'])->name('outbox.store');
    Route::get('edit-surat/{id}', [App\Http\Controllers\OutboxController::class, 'edit'])->name('outbox.edit');
    Route::post('update-surat/{id}', [App\Http\Controllers\OutboxController::class, 'update'])->name('outbox.update');
    Route::post('hapus-surat', [App\Http\Controllers\OutboxController::class, 'destroy'])->name('outbox.destroy');
    Route::get('lihat-surat/{id}', [App\Http\Controllers\OutboxController::class, 'show'])->name('outbox.show');
});

Route::prefix('/sppd')->middleware(['auth'])->group(function () {
    Route::get('/view', [App\Http\Controllers\SppdController::class, 'index'])->name('sppd');
    Route::get('/json-list', [App\Http\Controllers\SppdController::class, 'list'])->name('sppd.list');
    Route::get('/daftar-sppd', [App\Http\Controllers\SppdController::class, 'serverside'])->name('sppd.ssr');
    Route::get('buat-sppd', [App\Http\Controllers\SppdController::class, 'create'])->name('sppd.create');
    Route::post('simpan-sppd', [App\Http\Controllers\SppdController::class, 'store'])->name('sppd.store');
    Route::post('simpan-sppd-query', [App\Http\Controllers\SppdController::class, 'save'])->name('sppd.save');
    Route::get('edit-sppd/{id}', [App\Http\Controllers\SppdController::class, 'edit'])->name('sppd.edit');
    Route::post('update-sppd', [App\Http\Controllers\SppdController::class, 'update'])->name('sppd.update');
    Route::post('hapus-sppd', [App\Http\Controllers\SppdController::class, 'destroy'])->name('sppd.destroy');
    Route::get('lihat-sppd/{id}', [App\Http\Controllers\SppdController::class, 'show'])->name('sppd.show');
});


Route::prefix('/user')->middleware(['auth'])->group(function () {
    Route::get('/view', [App\Http\Controllers\UserController::class, 'index'])->name('user');
    Route::get('buat-user', [App\Http\Controllers\UserController::class, 'create'])->name('user.create');
    Route::post('simpan-user', [App\Http\Controllers\UserController::class, 'store'])->name('user.store');
    Route::post('simpan-user-query', [App\Http\Controllers\UserController::class, 'save'])->name('user.save');
    Route::post('update-user', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');
    Route::post('hapus-user', [App\Http\Controllers\UserController::class, 'destroy'])->name('user.destroy');

    Route::post('chcek-user', [App\Http\Controllers\UserController::class, 'check_user'])->name('user.check');
    Route::post('ubah-password-user', [App\Http\Controllers\UserController::class, 'change_pwd'])->name('user.change_pwd');
});

