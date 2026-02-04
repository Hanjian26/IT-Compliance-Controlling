<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemoKebijakanController;
use App\Http\Controllers\MemoAdministrasiController;
use App\Http\Controllers\MemoPermintaanDataController;
use App\Http\Controllers\MemoAuditController;
use App\Http\Controllers\MemoPenemuanController;
use App\Http\Controllers\ManagerApprovalController;
use App\Http\Controllers\TemplateDokumenController;
use App\Http\Controllers\AuditDatabaseController;
use App\Http\Controllers\WorkingPaperController;
use App\Http\Controllers\LaporanHasilAuditController;
use App\Http\Controllers\TindakAuditController;
use App\Http\Controllers\MemoAllController;
use App\Http\Controllers\HistoryController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| USER ROUTES (LEVEL 2 – VIEW ONLY)
|--------------------------------------------------------------------------
*/
Route::prefix('user')
    ->middleware(['auth', 'level:2'])
    ->name('user.')
    ->group(function () {

        Route::redirect('/', '/user/main-menu');

        Route::get('/main-menu', [AuthController::class, 'mainMenu'])
            ->name('main_menu');

        Route::get('/memo-kebijakan', [MemoKebijakanController::class, 'index'])
            ->name('memo.kebijakan');

        Route::get('/template-dokumen', [TemplateDokumenController::class, 'index'])
            ->name('template.dokumen');

        Route::get('/audit-laporan-hasil-akhir', [LaporanHasilAuditController::class, 'index'])
            ->name('audit.lha');

        Route::get('/tlha-audit', [TindakAuditController::class, 'index'])
            ->name('audit.tlha');
    });

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (LEVEL 1)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'level:1'])
    ->name('admin.')
    ->group(function () {

        Route::get('/main-menu', [AuthController::class, 'mainMenu'])
            ->name('main_menu');

        /*
        |--------------------------------------------------------------------------
        | MEMO KEBIJAKAN (DELETE DIKONTROL KETAT)
        |--------------------------------------------------------------------------
        */
        Route::prefix('memo-kebijakan')->name('memo.kebijakan.')->group(function () {
            Route::get('/', [MemoKebijakanController::class, 'index'])->name('index');
            Route::post('/', [MemoKebijakanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [MemoKebijakanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MemoKebijakanController::class, 'update'])->name('update');

            // ❗ DELETE HANYA LEWAT CONTROLLER INI (ADA APPROVAL LOGIC)
            Route::delete('/{id}', [MemoKebijakanController::class, 'destroy'])
                ->name('destroy');

            Route::get('/nomor/generate', [MemoKebijakanController::class, 'generateNomor'])
                ->name('generate_nomor');
        });

        /*
        |--------------------------------------------------------------------------
        | MEMO LAIN (TANPA DELETE LANGSUNG – ANTI BYPASS)
        |--------------------------------------------------------------------------
        */
        Route::resource('memo-administrasi', MemoAdministrasiController::class)
            ->except(['show', 'destroy']);

        Route::resource('memo-permintaan-data', MemoPermintaanDataController::class)
            ->except(['show', 'destroy']);

        Route::resource('memo-audit', MemoAuditController::class)
            ->except(['show', 'destroy']);

        Route::resource('memo-penemuan', MemoPenemuanController::class)
            ->except(['show', 'destroy']);

        Route::resource('memo-all', MemoAllController::class)
            ->except(['show', 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | LIST MEMO PENDING
        |--------------------------------------------------------------------------
        */
        Route::get('/memo-pending', [MemoKebijakanController::class, 'pendingList'])
            ->name('memo.pending');

        /*
        |--------------------------------------------------------------------------
        | APPROVAL (MANAGER ONLY)
        |--------------------------------------------------------------------------
        */
        Route::prefix('approval')
            ->middleware('manager')
            ->name('approval.')
            ->group(function () {

                Route::post('/memo/{id}/approve', [ManagerApprovalController::class, 'approve'])
                    ->name('memo.approve');

                Route::post('/memo/{id}/reject', [ManagerApprovalController::class, 'reject'])
                    ->name('memo.reject');
            });

        /*
        |--------------------------------------------------------------------------
        | AUDIT MODULE
        |--------------------------------------------------------------------------
        */
        Route::resource('audit-brdb', AuditDatabaseController::class)->except(['show']);
        Route::resource('audit-working-paper', WorkingPaperController::class)->except(['show']);
        Route::resource('audit-laporan-hasil-akhir', LaporanHasilAuditController::class)->except(['show']);
        Route::resource('tlha-audit', TindakAuditController::class)->except(['show']);

        /*
        |--------------------------------------------------------------------------
        | TEMPLATE & HISTORY
        |--------------------------------------------------------------------------
        */
        Route::resource('template-dokumen', TemplateDokumenController::class)->except(['show']);
        Route::resource('track-history', HistoryController::class)->except(['show']);
    });