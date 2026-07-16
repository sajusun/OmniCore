<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{


    public function search(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $products = Product::select([
            'id',
            'upc',
            'name',
            'image_url',
            'verdict',
            'score',
            'serving_size_text',
            'ingredients_text',
            'calories',
            'protein_g',
            'carbs_g',
            'fiber_g',
            'sugar_g',
            'fat_g'
        ])->where('name', 'like', "%{$search}%")->get();
            // ->transform(function ($product) {
            //     $product->image = $product->image_url ? asset($product->image_url) : null;
            //     return $product;
            // });
        return $this->success($products, "search result fetch success");
    }


    public function index(): JsonResponse
    {
        $products = Product::latest()->paginate(20);

        return response()->json($products);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        return response()->json([
            'message' => 'Product created successfully.',
            'data' => $product,
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json([
            'message' => 'Product updated successfully.',
            'data' => $product,
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ]);
    }
}
