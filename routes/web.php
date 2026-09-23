<?php

use App\Http\Controllers\DemoController;
use App\Http\Controllers\MicroscopeDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [
    DemoController::class,
    'index',
])->name('home');


Route::prefix('microscope')
    ->name('microscope.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [
            MicroscopeDashboardController::class,
            'index',
        ])->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Full Scan
        |--------------------------------------------------------------------------
        */

        Route::post('/scan', [
            MicroscopeDashboardController::class,
            'scan',
        ])->name('scan');


        /*
        |--------------------------------------------------------------------------
        | Individual Check
        |--------------------------------------------------------------------------
        */

        Route::post('/run-check', [
            MicroscopeDashboardController::class,
            'runCheck',
        ])->name('run-check');


        /*
        |--------------------------------------------------------------------------
        | History
        |--------------------------------------------------------------------------
        */

        Route::get('/history', [
            MicroscopeDashboardController::class,
            'history',
        ])->name('history');


        /*
        |--------------------------------------------------------------------------
        | Delete
        |--------------------------------------------------------------------------
        */

        Route::delete('/history/{scan}', [
            MicroscopeDashboardController::class,
            'destroy',
        ])->name('destroy');


        /*
        |--------------------------------------------------------------------------
        | Bulk Delete
        |--------------------------------------------------------------------------
        */

        Route::delete('/history/bulk-delete', [
            MicroscopeDashboardController::class,
            'bulkDelete',
        ])->name('bulk-delete');


        /*
        |--------------------------------------------------------------------------
        | Re-run
        |--------------------------------------------------------------------------
        */

        Route::post('/history/{scan}/rerun', [
            MicroscopeDashboardController::class,
            'rerun',
        ])->name('rerun');


        /*
        |--------------------------------------------------------------------------
        | CSV
        |--------------------------------------------------------------------------
        */

        Route::get('/history-export/csv', [
            MicroscopeDashboardController::class,
            'exportCsv',
        ])->name('export.csv');


        /*
        |--------------------------------------------------------------------------
        | JSON
        |--------------------------------------------------------------------------
        */

        Route::get('/history-export/json', [
            MicroscopeDashboardController::class,
            'exportJson',
        ])->name('export.json');
    });