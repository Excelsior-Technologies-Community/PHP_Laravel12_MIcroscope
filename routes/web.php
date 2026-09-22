<?php

use App\Http\Controllers\DemoController;
use App\Http\Controllers\MicroscopeDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DemoController::class, 'index'])
    ->name('home');

Route::prefix('microscope')->name('microscope.')->group(function () {

    Route::get('/dashboard', [
        MicroscopeDashboardController::class,
        'index',
    ])->name('dashboard');

    Route::post('/scan', [
        MicroscopeDashboardController::class,
        'scan',
    ])->name('scan');

    Route::get('/history', [
        MicroscopeDashboardController::class,
        'history',
    ])->name('history');
});