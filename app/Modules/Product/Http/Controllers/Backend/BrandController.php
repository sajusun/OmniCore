<?php

namespace App\Modules\Product\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\ProductBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductBrand::withCount('products')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name_display', function ($row) {
                    $featuredBadge = $row->is_featured ? ' <span class="badge bg-warning text-dark"><i class="fa fa-star"></i></span>' : '';

                    return '<div class="fw-bold">'.e($row->name).$featuredBadge.'</div><small class="text-muted">Slug: '.e($row->slug).'</small>';
                })
                ->addColumn('products_count', fn ($row) => '<span class="badge bg-primary">'.$row->products_count.' Products</span>')
                ->addColumn('website_link', fn ($row) => $row->website ? '<a href="'.e($row->website).'" target="_blank" class="text-primary text-decoration-none"><i class="fa fa-external-link me-1"></i>'.e($row->website).'</a>' : '<span class="text-muted small">N/A</span>')
                ->addColumn('status', fn ($row) => $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', function ($row) {
                    $json = htmlspecialchars(json_encode([
                        'id' => $row->id,
                        'name' => $row->name,
                        'website' => $row->website,
                        'description' => $row->description,
                        'is_active' => (bool) $row->is_active,
                        'is_featured' => (bool) $row->is_featured,
                    ]), ENT_QUOTES, 'UTF-8');

                    return '<div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-primary" onclick=\'openEditBrandModal('.$json.')\' title="Edit"><i class="fa fa-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteBrand('.$row->id.')" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['name_display', 'products_count', 'website_link', 'status', 'action'])
                ->make(true);
        }

        return view('product::backend.brands.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']).'-'.Str::random(4);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        ProductBrand::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Brand created successfully!']);
        }

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully!');
    }

    public function update(Request $request, int $id)
    {
        $brand = ProductBrand::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        $brand->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Brand updated successfully!']);
        }

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully!');
    }

    public function destroy(int $id)
    {
        $brand = ProductBrand::findOrFail($id);
        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully.',
        ]);
    }
}
