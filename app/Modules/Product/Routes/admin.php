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

Route::prefix('v1/admin')->middleware(['auth:api'])->group(function () {
    // 1. Products Management
    Route::get('/products', [AdminProductController::class, 'index']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::get('/products/{id}', [AdminProductController::class, 'show']);
    Route::put('/products/{id}', [AdminProductController::class, 'update']);
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);
    Route::post('/products/bulk-status', [AdminProductController::class, 'bulkStatus']);
    Route::post('/products/bulk-prices', [AdminProductController::class, 'bulkPrices']);

    // 2. Product Variants & Cartesian Matrix Generator
    Route::post('/products/{productId}/variants/generate', [AdminVariantController::class, 'generate']);
    Route::put('/products/{productId}/variants/{variantId}', [AdminVariantController::class, 'update']);
    Route::delete('/products/{productId}/variants/{variantId}', [AdminVariantController::class, 'destroy']);

    // 3. Categories Management
    Route::get('/categories', [AdminCategoryController::class, 'index']);
    Route::post('/categories', [AdminCategoryController::class, 'store']);
    Route::get('/categories/{id}', [AdminCategoryController::class, 'show']);
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy']);

    // 4. Brands Management
    Route::get('/brands', [AdminBrandController::class, 'index']);
    Route::post('/brands', [AdminBrandController::class, 'store']);
    Route::get('/brands/{id}', [AdminBrandController::class, 'show']);
    Route::put('/brands/{id}', [AdminBrandController::class, 'update']);
    Route::delete('/brands/{id}', [AdminBrandController::class, 'destroy']);

    // 5. Attributes Management
    Route::get('/attributes', [AdminAttributeController::class, 'index']);
    Route::post('/attributes', [AdminAttributeController::class, 'store']);
    Route::post('/attributes/{id}/values', [AdminAttributeController::class, 'addValue']);
    Route::delete('/attributes/{attributeId}/values/{valueId}', [AdminAttributeController::class, 'deleteValue']);
    Route::delete('/attributes/{id}', [AdminAttributeController::class, 'destroy']);

    // 6. Inventory & Stock Alerts
    Route::get('/inventory/low-stock', [AdminInventoryController::class, 'lowStock']);
    Route::post('/inventory/adjust', [AdminInventoryController::class, 'adjust']);

    // 7. Review Moderation Queue
    Route::get('/reviews', [AdminReviewController::class, 'index']);
    Route::put('/reviews/{id}/status', [AdminReviewController::class, 'updateStatus']);
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy']);
});
