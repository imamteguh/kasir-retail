<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Sales\POSController;
use App\Http\Controllers\Sales\ReportController as SalesReportController;
use App\Http\Controllers\Inventory\ReportController as InventoryReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('forgot-password');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('reset-password', [ResetPasswordController::class, 'reset']);
    
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);
});

Route::get('email/verify/{id}/{hash}', [RegisterController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');

Route::middleware(['auth', 'store.context', 'subscription.active'])->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    Route::group(['prefix' => 'masters'], function () {
        Route::get('categories', [\App\Http\Controllers\Masters\CategoryController::class, 'index']);
        Route::get('units', [\App\Http\Controllers\Masters\UnitController::class, 'index']);
        Route::get('products', [\App\Http\Controllers\Masters\ProductController::class, 'index']);
    });

    Route::group(['prefix' => 'inventory'], function () {
        Route::get('suppliers', [\App\Http\Controllers\Inventory\SupplierController::class, 'index']);
        Route::get('purchases', [\App\Http\Controllers\Inventory\PurchaseController::class, 'index']);
    });

    Route::get('pos', [POSController::class, 'index'])->name('pos.index');
    Route::get('pos/receipt/{sale}', [POSController::class, 'receipt'])->name('pos.receipt');

    Route::get('reports/sales', [SalesReportController::class, 'index'])->name('reports.sales');
    Route::get('reports/sales/pdf', [SalesReportController::class, 'pdf'])->name('reports.sales.pdf');

    Route::get('reports/purchases', [InventoryReportController::class, 'index'])->name('reports.purchases');
    Route::get('reports/purchases/pdf', [InventoryReportController::class, 'pdf'])->name('reports.purchases.pdf');

    // Profit & Loss report page and PDF
    Route::get('reports/profit-loss', [SalesReportController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('reports/profit-loss/pdf', [SalesReportController::class, 'profitLossPdf'])->name('reports.profit-loss.pdf');
});

Route::middleware(['auth', 'store.context', 'subscription.active'])->prefix('api')->group(function () {
    Route::apiResource('categories', \App\Http\Controllers\API\Masters\CategoryController::class)->except(['show']);
    Route::apiResource('units', \App\Http\Controllers\API\Masters\UnitController::class)->except(['show']);
    Route::apiResource('products', \App\Http\Controllers\API\Masters\ProductController::class)->except(['show']);
    Route::apiResource('suppliers', \App\Http\Controllers\API\Inventory\SupplierController::class)->except(['show']);
    Route::apiResource('purchases', \App\Http\Controllers\API\Inventory\PurchaseController::class)->except(['show']);

    Route::post('pos', [\App\Http\Controllers\API\Sales\POSController::class, 'store'])->name('pos.store');
    Route::get('pos/products', [\App\Http\Controllers\API\Sales\POSController::class, 'products'])->name('pos.products');

    Route::get('pos/reports/daily', [\App\Http\Controllers\API\Sales\ReportController::class, 'daily'])->name('pos.reports.daily');
    Route::get('pos/reports/monthly', [\App\Http\Controllers\API\Sales\ReportController::class, 'monthly'])->name('pos.reports.monthly');
    Route::get('pos/reports/profit-loss', [\App\Http\Controllers\API\Sales\ReportController::class, 'profitLoss'])->name('pos.reports.profit-loss');
});
