<?php

use App\Http\Controllers\LeadCallController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ManagerLeadController;
use Illuminate\Support\Facades\Route;

Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
Route::post('/leads/{lead}/calls', [LeadCallController::class, 'store'])->name('leads.calls.store');
Route::get('/managers/{manager}/leads', [ManagerLeadController::class, 'index'])->name('managers.leads.index');
