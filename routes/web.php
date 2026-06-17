<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BaleWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/webhook/bale', [BaleWebhookController::class, 'handle'])->name('bale.webhook');
