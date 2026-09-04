<?php

use App\Modules\Product\Http\Controllers\Admin\AdminAttributeController;
use App\Modules\Product\Http\Controllers\Admin\AdminBrandController;
use App\Modules\Product\Http\Controllers\Admin\AdminCategoryController;
use App\Modules\Product\Http\Controllers\Admin\AdminInventoryController;
use App\Modules\Product\Http\Controllers\Admin\AdminProductController;
use App\Modules\Product\Http\Controllers\Admin\AdminReviewController;
use App\Modules\Product\Http\Controllers\Admin\AdminVariantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin E-Commerce Management API Routes
|--------------------------------------------------------------------------
| Prefix: /api/v1/admin/products ...
*/

Route::prefix('v1/admin')->name('admin.')->middleware(['auth:api'])->group(function () {
    // 1. Products Management
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}', [AdminProductController::class, 'show'])->name('products.show');
    Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/bulk-status', [AdminProductController::class, 'bulkStatus'])->name('products.bulk_status');
    Route::post('/products/bulk-prices', [AdminProductController::class, 'bulkPrices'])->name('products.bulk_prices');

    // 2. Product Variants & Cartesian Matrix Generator
    Route::post('/products/{productId}/variants/generate', [AdminVariantController::class, 'generate'])->name('variants.generate');
    Route::put('/products/{productId}/variants/{variantId}', [AdminVariantController::class, 'update'])->name('variants.update');
    Route::delete('/products/{productId}/variants/{variantId}', [AdminVariantController::class, 'destroy'])->name('variants.destroy');

    // 3. Categories Management
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}', [AdminCategoryController::class, 'show'])->name('categories.show');
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // 4. Brands Management
    Route::get('/brands', [AdminBrandController::class, 'index'])->name('brands.index');
    Route::post('/brands', [AdminBrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{id}', [AdminBrandController::class, 'show'])->name('brands.show');
    Route::put('/brands/{id}', [AdminBrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{id}', [AdminBrandController::class, 'destroy'])->name('brands.destroy');

    // 5. Attributes Management
    Route::get('/attributes', [AdminAttributeController::class, 'index'])->name('attributes.index');
    Route::post('/attributes', [AdminAttributeController::class, 'store'])->name('attributes.store');
    Route::post('/attributes/{id}/values', [AdminAttributeController::class, 'addValue'])->name('attributes.add_value');
    Route::delete('/attributes/{attributeId}/values/{valueId}', [AdminAttributeController::class, 'deleteValue'])->name('attributes.delete_value');
    Route::delete('/attributes/{id}', [AdminAttributeController::class, 'destroy'])->name('attributes.destroy');

    // 6. Inventory & Stock Alerts
    Route::get('/inventory/low-stock', [AdminInventoryController::class, 'lowStock'])->name('inventory.index');
    Route::post('/inventory/adjust', [AdminInventoryController::class, 'adjust'])->name('inventory.adjust');

    // 7. Review Moderation Queue
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::put('/reviews/{id}/status', [AdminReviewController::class, 'updateStatus'])->name('reviews.status');
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
});
