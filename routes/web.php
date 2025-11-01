<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Settings\OrganizationController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('client', ClientController::class);
    Route::get('/invoice/bill', [InvoiceController::class, 'bills'])->name('invoice.bill');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'downloadPdf'])
        ->name('invoice.download');
    Route::resource('invoice', InvoiceController::class);
    Route::resource('product', ProductController::class);
    Route::resource('supplier', SupplierController::class);
    Route::resource('productCategory', ProductCategoryController::class);
});



require __DIR__ . '/settings.php';
