<?php

use App\Modules\CMS\Http\Controllers\Api\CmsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CMS API Routes (Dynamic Page & Section Content API)
|--------------------------------------------------------------------------
*/

Route::prefix('cms')->name('cms.')->group(function () {
    Route::get('/', [CmsController::class, 'index'])->name('index');             // All pages & their sections
    Route::get('{page}', [CmsController::class, 'page'])->name('page');          // All sections of a page
    Route::get('{page}/{section}', [CmsController::class, 'section'])->name('section'); // Single section
});
