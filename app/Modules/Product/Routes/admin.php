<?php

use App\Modules\Product\Http\Controllers\Backend\AttributeController;
use App\Modules\Product\Http\Controllers\Backend\BrandController;
use App\Modules\Product\Http\Controllers\Backend\CategoryController;
use App\Modules\Product\Http\Controllers\Backend\InventoryController;
use App\Modules\Product\Http\Controllers\Backend\ProductController;
use App\Modules\Product\Http\Controllers\Backend\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Product Web Admin Routes (Dashboard Web UI)
|--------------------------------------------------------------------------
| Prefix: /admin/products, /admin/categories, etc.
*/

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
});

Route::prefix('brands')->name('brands.')->group(function () {
    Route::get('/', [BrandController::class, 'index'])->name('index');
});

Route::prefix('attributes')->name('attributes.')->group(function () {
    Route::get('/', [AttributeController::class, 'index'])->name('index');
});

Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/low-stock', [InventoryController::class, 'index'])->name('index');
});

Route::prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/', [ReviewController::class, 'index'])->name('index');
});
