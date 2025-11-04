<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemoKebijakanController;
use App\Http\Controllers\MemoAdministrasiController;
use App\Http\Controllers\MemoPermintaanDataController;
use App\Http\Controllers\MemoAuditController;
use App\Http\Controllers\MemoPenemuanController;
use App\Http\Controllers\TemplateDokumenController;
use App\Http\Controllers\AuditDatabaseController;
use App\Http\Controllers\WorkingPaperController;
use App\Http\Controllers\LaporanHasilAuditController;
use App\Http\Controllers\TindakAuditController;


// ====================
// Public Route (Login, Register, Logout)
// ====================
Route::get('/', fn () => redirect('/login'));
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Template Dokumen (admin full CRUD)
    Route::get('/template-dokumen', [TemplateDokumenController::class, 'index'])->name('template.dokumen');
    Route::post('/template-dokumen', [TemplateDokumenController::class, 'store'])->name('template.dokumen.store');
    Route::delete('/template-dokumen/{id}', [TemplateDokumenController::class, 'destroy'])->name('template.dokumen.destroy');
    Route::get('/template-dokumen/{id}/edit', [TemplateDokumenController::class, 'edit'])->name('template.dokumen.edit');
    Route::put('/template-dokumen/{id}', [TemplateDokumenController::class, 'update'])->name('template.dokumen.update');

        // Laporan Hasil Audit
    Route::get('/audit-laporan-hasil-akhir', [LaporanHasilAuditController::class, 'index'])->name('audit.lha');
    Route::post('/audit-laporan-hasil-akhir', [LaporanHasilAuditController::class, 'store'])->name('audit.lha.store');
    Route::delete('/audit-laporan-hasil-akhir/{id}', [LaporanHasilAuditController::class, 'destroy'])->name('audit.lha.destroy');
    Route::get('/audit-laporan-hasil-akhir/{id}/edit', [LaporanHasilAuditController::class, 'edit'])->name('audit.lha.edit');
    Route::put('/audit-laporan-hasil-akhir/{id}', [LaporanHasilAuditController::class, 'update'])->name('audit.lha.update');

// ====================
// USER ROUTES
// prefix: /user
// role: user
// ====================
// ====================
// USER ROUTES
// prefix: /user
// role: user
// ====================

Route::get('/register', [AuthController::class, 'showRegisterForm']);
Route::post('/register', [AuthController::class, 'register']);

Route::prefix('user')->middleware(['auth', 'level:2'])->group(function () {
    // redirect /user -> main_menu
    Route::get('/', fn () => redirect()->route('user.main_menu'))->name('user.home');

    // Main Menu user
    Route::get('/main_menu', [AuthController::class, 'mainMenu'])->name('user.main_menu');

    // Template Dokumen (VIEW ONLY)
    Route::get('/template-dokumen', [TemplateDokumenController::class, 'index'])
        ->name('user.template.dokumen');
    // NOTE: TIDAK ADA store/destroy/edit/update untuk user
});

// ====================
// ADMIN ROUTES
// prefix: /admin
// role: admin
// ====================
Route::prefix('admin')->middleware(['auth', 'level:1'])->group(function () {
    // Main Menu (versi admin)
    Route::get('/main_menu', [AuthController::class, 'mainMenu'])->name('admin.main_menu');

    // Memo Kebijakan
    Route::get('/memo-kebijakan', [MemoKebijakanController::class, 'index'])->name('memo.index');
    Route::post('/memo-kebijakan', [MemoKebijakanController::class, 'store'])->name('memo.store');
    Route::delete('/memo-kebijakan/{id}', [MemoKebijakanController::class, 'destroy'])->name('memo.destroy');
    Route::get('/memo-kebijakan/{id}/edit', [MemoKebijakanController::class, 'edit'])->name('memo.edit');
    Route::put('/memo-kebijakan/{id}', [MemoKebijakanController::class, 'update'])->name('memo.update');

    // Memo Administrasi
    Route::get('/memo-administrasi', [MemoAdministrasiController::class, 'index'])->name('memo.administrasi');
    Route::post('/memo-administrasi', [MemoAdministrasiController::class, 'store'])->name('memo.administrasi.store');
    Route::delete('/memo-administrasi/{id}', [MemoAdministrasiController::class, 'destroy'])->name('memo.administrasi.destroy');
    Route::get('/memo-administrasi/{id}/edit', [MemoAdministrasiController::class, 'edit'])->name('memo.administrasi.edit');
    Route::put('/memo-administrasi/{id}', [MemoAdministrasiController::class, 'update'])->name('memo.administrasi.update');

    // Memo Permintaan Data
    Route::get('/memo-permintaan-data', [MemoPermintaanDataController::class, 'index'])->name('memo.permintaan.data');
    Route::post('/memo-permintaan-data', [MemoPermintaanDataController::class, 'store'])->name('memo.permintaan.data.store');
    Route::delete('/memo-permintaan-data/{id}', [MemoPermintaanDataController::class, 'destroy'])->name('memo.permintaan.data.destroy');
    Route::get('/memo-permintaan-data/{id}/edit', [MemoPermintaanDataController::class, 'edit'])->name('memo.permintaan.data.edit');
    Route::put('/memo-permintaan-data/{id}', [MemoPermintaanDataController::class, 'update'])->name('memo.permintaan.data.update');

    // Memo Audit
    Route::get('/memo-audit', [MemoAuditController::class, 'index'])->name('memo.audit');
    Route::post('/memo-audit', [MemoAuditController::class, 'store'])->name('memo.audit.store');
    Route::delete('/memo-audit/{id}', [MemoAuditController::class, 'destroy'])->name('memo.audit.destroy');
    Route::get('/memo-audit/{id}/edit', [MemoAuditController::class, 'edit'])->name('memo.audit.edit');
    Route::put('/memo-audit/{id}', [MemoAuditController::class, 'update'])->name('memo.audit.update');

    // Memo Penemuan
    Route::get('/memo-penemuan', [MemoPenemuanController::class, 'index'])->name('memo.penemuan');
    Route::post('/memo-penemuan', [MemoPenemuanController::class, 'store'])->name('memo.penemuan.store');
    Route::delete('/memo-penemuan/{id}', [MemoPenemuanController::class, 'destroy'])->name('memo.penemuan.destroy');
    Route::get('/memo-penemuan/{id}/edit', [MemoPenemuanController::class, 'edit'])->name('memo.penemuan.edit');
    Route::put('/memo-penemuan/{id}', [MemoPenemuanController::class, 'update'])->name('memo.penemuan.update');

    // Audit Backup & Restore Database
    Route::get('/audit-brdb', [AuditDatabaseController::class, 'index'])->name('audit.brdb');
    Route::post('/audit-brdb', [AuditDatabaseController::class, 'store'])->name('audit.brdb.store');
    Route::delete('/audit-brdb/{id}', [AuditDatabaseController::class, 'destroy'])->name('audit.brdb.destroy');
    Route::get('/audit-brdb/{id}/edit', [AuditDatabaseController::class, 'edit'])->name('audit.brdb.edit');
    Route::put('/audit-brdb/{id}', [AuditDatabaseController::class, 'update'])->name('audit.brdb.update');

    // // Working Paper Audit
    Route::get('/audit-working-paper', [WorkingPaperController::class, 'index'])->name('audit.wp');
    Route::post('/audit-working-paper', [WorkingPaperController::class, 'store'])->name('audit.wp.store');
    Route::delete('/audit-working-paper/{id}', [WorkingPaperController::class, 'destroy'])->name('audit.wp.destroy');
    Route::get('/audit-working-paper/{id}/edit', [WorkingPaperController::class, 'edit'])->name('audit.wp.edit');
    Route::put('/audit-working-paper/{id}', [WorkingPaperController::class, 'update'])->name('audit.wp.update');

    // // Jadwal Audit DB
    Route::get('/tlha_audit', [TindakAuditController::class, 'index'])->name('audit.tlha');
    Route::post('/tlha_audit', [TindakAuditController::class, 'store'])->name('audit.tlha.store');
    Route::delete('/tlha_audit/{id}', [TindakAuditController::class, 'destroy'])->name('audit.tlha.destroy');
    Route::get('/tlha_audit/{id}/edit', [TindakAuditController::class, 'edit'])->name('audit.tlha.edit');
    Route::put('/tlha_audit/{id}', [TindakAuditController::class, 'update'])->name('audit.tlha.update');

});
