<?php

namespace App\Http\Controllers\Api;

use App\Models\CMS;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Resources\CMSResource;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function index(string $name, $section)
    {
        $cms = CMS::where('page', $name)->where('section', $section)->where('status', 'active')->first();

        if (! $cms) {
            return Helper::jsonErrorResponse(
                'Page not found.',
                404,
                []
            );
        }

        return Helper::jsonResponse(
            true,
            ucfirst(str_replace('-', ' ', $name)) . ' page retrieved successfully.',
            200,
            new CMSResource($cms)
        );
    }
}
