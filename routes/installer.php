<?php

use App\Http\Controllers\InstallerController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'install', 'as' => 'installer.'], function () {
    Route::get('/', [InstallerController::class, 'index'])->name('index');
    Route::get('/requirements', [InstallerController::class, 'requirements'])->name('requirements');
    Route::get('/permissions', [InstallerController::class, 'permissions'])->name('permissions');
    Route::get('/purchaseValidation', [InstallerController::class, 'purchaseValidation'])->name('purchaseValidation');
    Route::post('/purchaseValidation', [InstallerController::class, 'purchaseValidationStore'])->name('purchaseValidation.store');
    Route::get('/purchaseValidation-error', [InstallerController::class, 'purchaseValidationError'])->name('purchaseValidation-error');
    Route::get('/database', [InstallerController::class, 'databaseForm'])->name('database')->middleware('purchase.validated');
    Route::post('/database', [InstallerController::class, 'databaseStore'])->name('database.store');
    Route::get('/database-error', [InstallerController::class, 'databaseError'])->name('database-error');
    Route::get('/migrate', [InstallerController::class, 'migrate'])->name('migrate');
    Route::get('/DBmigrate', [InstallerController::class, 'databaseMigration'])->name('database.migrate');
    Route::get('/forcemigrate', [InstallerController::class, 'databaseForceMigration'])->name('database.forcemigrate');
    Route::get('/seed', [InstallerController::class, 'seed'])->name('seed');
    Route::get('/admin', [InstallerController::class, 'adminForm'])->name('admin');
    Route::post('/admin', [InstallerController::class, 'adminStore'])->name('admin.store');
    Route::get('/finish', [InstallerController::class, 'finish'])->name('finish');
});
