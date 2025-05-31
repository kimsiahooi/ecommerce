<?php

use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::prefix('admin')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        Route::prefix('shop')->name('shop.')->group(function () {
            Route::resource('products', ProductController::class);
            Route::resource('product-categories', ProductCategoryController::class);
        });

        require __DIR__ . '/settings.php';
    });

require __DIR__ . '/auth.php';
