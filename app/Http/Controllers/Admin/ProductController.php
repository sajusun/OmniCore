<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Verdict;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.products.index');
    }

    /**
     * Return datatables ajax data.
     */
    public function data(Request $request)
    {
        $query = Product::query();
        return DataTables::of($query)
            ->addColumn('image', function (Product $product) {
                if ($product->image_url) {
                    return '<img src="' . e($product->image_url) . '" alt="Image" style="height:40px; width:auto;" />';
                }
                return '';
            })
            ->addColumn('action', function (Product $product) {
            $showUrl = route('admin.products.show', $product);
                $editUrl = route('admin.products.edit', $product);
                $deleteUrl = route('admin.products.destroy', $product);
                return '<a href="' . $showUrl . '" class="btn btn-sm btn-info me-1">View</a>' .
                    '<a href="' . $editUrl . '" class="btn btn-sm btn-primary me-1">Edit</a>' .
                    '<form method="POST" action="' . $deleteUrl . '" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\');">' .
                    csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-sm btn-danger">Delete</button>' .
                    '</form>';
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $verdicts = Verdict::cases();
        return view('backend.products.create', compact('verdicts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = Storage::url($path);
        }
        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {

        $verdicts = Verdict::cases();
        return view('backend.products.edit', compact('product', 'verdicts'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('backend.products.show', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                $oldPath = str_replace('/storage/', '', $product->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = Storage::url($path);
        }
        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image_url) {
            $oldPath = str_replace('/storage/', '', $product->image_url);
            Storage::disk('public')->delete($oldPath);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
