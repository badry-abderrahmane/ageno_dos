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
    Route::get('/api/clients', [ClientController::class, 'apiIndex'])->name('api.clients');
    Route::get('/api/clients/{client}', [ClientController::class, 'show'])->name('api.clients.show');

    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'downloadPdf'])
        ->name('invoice.download');
    Route::resource('invoice', InvoiceController::class);

    Route::resource('product', ProductController::class);
    Route::get('/api/products', [ProductController::class, 'apiIndex'])->name('api.products');
    Route::get('/api/products/{product}', [ProductController::class, 'show'])->name('api.products.show');

    Route::resource('supplier', SupplierController::class);
    Route::get('/api/suppliers', [SupplierController::class, 'apiIndex'])->name('api.suppliers');
    Route::get('/api/suppliers/{supplier}', [SupplierController::class, 'show'])->name('api.suppliers.show');

    Route::resource('productCategory', ProductCategoryController::class);
    Route::get('/api/productCategory', [ProductCategoryController::class, 'apiIndex'])->name('api.productCategories');
    Route::get('/api/productCategory/{category}', [ProductCategoryController::class, 'show'])->name('api.productCategories.show');
});



require __DIR__ . '/settings.php';
