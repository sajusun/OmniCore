<?php

namespace App\Http\Controllers\Web\Ajax;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\Subcategory;

class SubcategoryController extends Controller
{
    public function index($category_id)
    {
        $subcategories = Subcategory::where('status', 'active')->where('category_id', $category_id)->get();
        $data = [
            'subcategories' => $subcategories
        ];
        return Helper::jsonResponse(true, 'Category', 200, $data);

    }
}