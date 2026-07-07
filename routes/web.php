<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AuthenticateClient;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/select-contract', [AuthController::class, 'selectContract'])->name('select-contract');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware([AuthenticateClient::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/refresh', [DashboardController::class, 'refreshData'])->name('dashboard.refresh');
    Route::get('/faturas', [DashboardController::class, 'invoices'])->name('invoices');
    Route::get('/fatura/{id}', [DashboardController::class, 'invoiceDetail'])->name('invoice.detail');

    Route::get('/suporte', [SupportController::class, 'index'])->name('support');
    Route::post('/suporte/abrir', [SupportController::class, 'store'])->name('support.store');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');

    Route::middleware([AdminMiddleware::class])->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/config/api', [AdminController::class, 'saveApiConfig'])->name('config.api');
        Route::post('/config/visual', [AdminController::class, 'saveVisualConfig'])->name('config.visual');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    });
});
