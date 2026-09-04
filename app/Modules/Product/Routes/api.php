<?php

use App\Modules\Product\Http\Controllers\Api\BrandCatalogController;
use App\Modules\Product\Http\Controllers\Api\CatalogController;
use App\Modules\Product\Http\Controllers\Api\CategoryCatalogController;
use App\Modules\Product\Http\Controllers\Api\ProductDetailController;
use App\Modules\Product\Http\Controllers\Api\ReviewController;
use App\Modules\Product\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront / Customer E-Commerce API Routes
|--------------------------------------------------------------------------
| Prefix: /api/v1/store/products or /api/v1/store
*/

Route::prefix('v1/store')->group(function () {
    // 1. Products Catalog
    Route::get('/products', [CatalogController::class, 'index']);
    Route::get('/products/featured', [CatalogController::class, 'featured']);
    Route::get('/products/{slug}', [ProductDetailController::class, 'show']);
    Route::get('/products/{slug}/related', [ProductDetailController::class, 'related']);

    // 2. Categories
    Route::get('/categories', [CategoryCatalogController::class, 'tree']);
    Route::get('/categories/{slug}', [CategoryCatalogController::class, 'show']);

    // 3. Brands
    Route::get('/brands', [BrandCatalogController::class, 'index']);
    Route::get('/brands/{slug}', [BrandCatalogController::class, 'show']);

    // 4. Product Reviews (Public view)
    Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);

    // Protected Customer Routes
    Route::middleware(['auth:api'])->group(function () {
        // Submit Review
        Route::post('/products/{productId}/reviews', [ReviewController::class, 'store']);

        // Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist/toggle/{productId}', [WishlistController::class, 'toggle']);
    });
});
