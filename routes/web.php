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
    Route::get('settings', [App\Http\Controllers\HomeController::class, 'settings'])->name('admin.settings');
    Route::post('update-profile', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('admin.updateProfile');
    Route::post('change-password', [App\Http\Controllers\HomeController::class, 'changePassword'])->name('admin.changePassword');
});

Route::prefix('/surat-masuk')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\InboxController::class, 'index'])->name('inbox');
    Route::get('/daftar-surat', [App\Http\Controllers\InboxController::class, 'serverside'])->name('inbox.ssr');
    Route::get('buat-surat', [App\Http\Controllers\InboxController::class, 'create'])->name('inbox.create');
    Route::post('simpan-surat', [App\Http\Controllers\InboxController::class, 'store'])->name('inbox.store');
    Route::get('edit-surat/{id}', [App\Http\Controllers\InboxController::class, 'edit'])->name('inbox.edit');
    Route::post('update-surat/{id}', [App\Http\Controllers\InboxController::class, 'update'])->name('inbox.update');
    Route::get('hapus-surat/{id}', [App\Http\Controllers\InboxController::class, 'destroy'])->name('inbox.destroy');
    Route::get('lihat-surat/{id}', [App\Http\Controllers\InboxController::class, 'show'])->name('inbox.show');
});

Route::prefix('/surat-keluar')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\OutboxController::class, 'index'])->name('outbox');
    Route::get('/daftar-surat', [App\Http\Controllers\OutboxController::class, 'serverside'])->name('outbox.ssr');
    Route::get('buat-surat', [App\Http\Controllers\OutboxController::class, 'create'])->name('outbox.create');
    Route::post('simpan-surat', [App\Http\Controllers\OutboxController::class, 'store'])->name('outbox.store');
    Route::get('edit-surat/{id}', [App\Http\Controllers\OutboxController::class, 'edit'])->name('outbox.edit');
    Route::post('update-surat/{id}', [App\Http\Controllers\OutboxController::class, 'update'])->name('outbox.update');
    Route::get('hapus-surat/{id}', [App\Http\Controllers\OutboxController::class, 'destroy'])->name('outbox.destroy');
    Route::get('lihat-surat/{id}', [App\Http\Controllers\OutboxController::class, 'show'])->name('outbox.show');
});

