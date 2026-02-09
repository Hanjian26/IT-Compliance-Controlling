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

Route::post('/store-pin', [AuthController::class, 'storePin'])
    ->middleware('auth')
    ->name('pin.store');

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

            Route::get('/template-dokumen', [TemplateDokumenController::class, 'index'])
            ->name('template.dokumen');

        Route::get('/memo-kebijakan', [MemoKebijakanController::class, 'index'])
            ->name('memo.kebijakan');

        // ================= AUDIT =================
        Route::prefix('audit')->name('audit.')->group(function () {

            Route::get('/laporan-hasil-audit', [LaporanHasilAuditController::class, 'index'])
                ->name('lha.index');

            Route::get('/tindak-lanjut-hasil-audit', [TindakAuditController::class, 'index'])
                ->name('tlha.index');
        });
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

        // Route::get('/template-dokumen', [TemplateDokumenController::class, 'index'])
        //     ->name('template.dokumen');

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

    Route::prefix('memo-administrasi')->name('memo.administrasi.')->group(function () {
        Route::get('/', [MemoAdministrasiController::class, 'index'])->name('index');
        Route::post('/', [MemoAdministrasiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [MemoAdministrasiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MemoAdministrasiController::class, 'update'])->name('update');

        // ❗ DELETE HANYA LEWAT CONTROLLER INI (ADA APPROVAL LOGIC)
        Route::delete('/{id}', [MemoAdministrasiController::class, 'destroy'])
            ->name('destroy');

        Route::get('/nomor/generate', [MemoAdministrasiController::class, 'generateNomor'])
            ->name('generate_nomor');
    });

        Route::prefix('memo-permintaan-data')->name('memo.permintaan-data.')->group(function () {
            Route::get('/', [MemoPermintaanDataController::class, 'index'])->name('index');
            Route::post('/', [MemoPermintaanDataController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [MemoPermintaanDataController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MemoPermintaanDataController::class, 'update'])->name('update');

            // ❗ DELETE HANYA LEWAT CONTROLLER INI (ADA APPROVAL LOGIC)
            Route::delete('/{id}', [MemoPermintaanDataController::class, 'destroy'])
                ->name('destroy');

            Route::get('/nomor/generate', [MemoPermintaanDataController::class, 'generateNomor'])
                ->name('generate_nomor');
    });

        Route::prefix('memo-penemuan')->name('memo.penemuan.')->group(function () {
            Route::get('/', [MemoPenemuanController::class, 'index'])->name('index');
            Route::post('/', [MemoPenemuanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [MemoPenemuanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MemoPenemuanController::class, 'update'])->name('update');         
            // ❗ DELETE HANYA LEWAT CONTROLLER INI (ADA APPROVAL LOGIC)
            Route::delete('/{id}', [MemoPenemuanController::class, 'destroy'])
                ->name('destroy');

            Route::get('/nomor/generate', [MemoPenemuanController::class, 'generateNomor'])
                ->name('generate_nomor');
        });

        // Menu Audit
        Route::prefix('audit-brdb')->name('audit.brdb.')->group(function () {
            Route::get('/', [AuditDatabaseController::class, 'index'])->name('index');
            Route::post('/', [AuditDatabaseController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AuditDatabaseController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AuditDatabaseController::class, 'update'])->name('update');
            Route::delete('/{id}', [AuditDatabaseController::class, 'destroy'])->name('destroy');
    });

        // Menu Working Paper   
        Route::prefix('audit-working-paper')->name('audit.working-paper.')->group(function () {
            Route::get('/', [WorkingPaperController::class, 'index'])->name('index');
            Route::post('/', [WorkingPaperController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [WorkingPaperController::class, 'edit'])->name('edit');
            Route::put('/{id}', [WorkingPaperController::class, 'update'])->name('update');
            Route::delete('/{id}', [WorkingPaperController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('audit-laporan-hasil-akhir')->name('audit.laporan-hasil-akhir.')->group(function () {
            Route::get('/', [LaporanHasilAuditController::class, 'index'])->name('index');
            Route::post('/', [LaporanHasilAuditController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [LaporanHasilAuditController::class, 'edit'])->name('edit');
            Route::put('/{id}', [LaporanHasilAuditController::class, 'update'])->name('update');
            Route::delete('/{id}', [LaporanHasilAuditController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('audit-tlha')->name('audit.tlha.')->group(function () {
            Route::get('/', [TindakAuditController::class, 'index'])->name('index');
            Route::post('/', [TindakAuditController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [TindakAuditController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TindakAuditController::class, 'update'])->name('update');
            Route::delete('/{id}', [TindakAuditController::class, 'destroy'])->name('destroy');
        });

        // Route::resource('memo-audit', MemoAuditController::class)
        //     ->except(['show', 'destroy']);
        // Route::resource('memo-all', MemoAllController::class)
        //     ->except(['show', 'destroy']);

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
      

 

        /*
        |--------------------------------------------------------------------------
        | TEMPLATE & HISTORY
        |--------------------------------------------------------------------------
        */
        
        Route::resource('template-dokumen', TemplateDokumenController::class)->except(['show']);
        Route::resource('track-history', HistoryController::class)->except(['show']);
    });