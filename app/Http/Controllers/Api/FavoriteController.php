<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

class FavoriteController extends Controller
{

    public function index(): JsonResponse
    {
        $favorites = Favorite::with('favoritable')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return response()->json($favorites);
    }
}