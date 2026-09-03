<?php

use App\Modules\CMS\Http\Controllers\Backend\PageContentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CMS Admin Routes (Dynamic Page & Section Management)
|--------------------------------------------------------------------------
*/

Route::middleware('permission:cms.manage')->group(function () {
    Route::get('page/{page}/{section}', [PageContentController::class, 'edit'])->name('cms.page.edit');
    Route::post('page/{page}/{section}', [PageContentController::class, 'update'])->name('cms.page.update');
});
