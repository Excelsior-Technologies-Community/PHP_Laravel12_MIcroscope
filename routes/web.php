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

        /*
        |--------------------------------------------------------------------------
        | Static Analysis & Pint Auto-Fixer
        |--------------------------------------------------------------------------
        */

        Route::get('/static-analysis', [
            MicroscopeDashboardController::class,
            'staticAnalysis',
        ])->name('static-analysis');

        Route::post('/larastan/run', [
            MicroscopeDashboardController::class,
            'runLarastan',
        ])->name('larastan.run');

        Route::post('/pint/fix', [
            MicroscopeDashboardController::class,
            'runPint',
        ])->name('pint.fix');

        Route::post('/debug-clean', [
            MicroscopeDashboardController::class,
            'cleanDebug',
        ])->name('debug-clean');

        /*
        |--------------------------------------------------------------------------
        | Security Audit & CVE Scanner
        |--------------------------------------------------------------------------
        */

        Route::get('/security-audit', [
            MicroscopeDashboardController::class,
            'securityAudit',
        ])->name('security-audit');

        Route::post('/security-audit/run', [
            MicroscopeDashboardController::class,
            'runSecurityAudit',
        ])->name('security-audit.run');

        /*
        |--------------------------------------------------------------------------
        | Performance & Dead Asset Analyzer
        |--------------------------------------------------------------------------
        */

        Route::get('/performance-analyzer', [
            MicroscopeDashboardController::class,
            'performanceAnalyzer',
        ])->name('performance-analyzer');

        Route::post('/performance-analyzer/scan', [
            MicroscopeDashboardController::class,
            'scanPerformance',
        ])->name('performance-analyzer.scan');
    });