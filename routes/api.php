<?php

use App\Http\Controllers\LeadCallController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ManagerLeadController;
use Illuminate\Support\Facades\Route;

Route::prefix('leads')
    ->name('leads.')
    ->group(function (): void {
        Route::post('/', [LeadController::class, 'store'])->name('store');

        Route::prefix('{lead}/calls')
            ->controller(LeadCallController::class)
            ->name('calls.')
            ->group(function (): void {
                Route::post('/', 'store')->name('store');
            });
    });

Route::prefix('managers')
    ->name('managers.')
    ->group(function (): void {
        Route::prefix('{manager}/leads')
            ->controller(ManagerLeadController::class)
            ->name('leads.')
            ->group(function (): void {
                Route::get('/', 'index')->name('index');
            });
    });
