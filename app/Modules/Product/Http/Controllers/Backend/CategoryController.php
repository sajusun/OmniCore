<?php

namespace App\Modules\Product\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductCategory::with('parent')->withCount('products')->orderBy('order', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name_display', function ($row) {
                    $icon = $row->icon ? '<i class="' . e($row->icon) . ' me-2 text-primary"></i>' : '<i class="fa fa-folder me-2 text-secondary"></i>';
                    return '<div class="fw-bold d-flex align-items-center">' . $icon . e($row->name) . '</div><small class="text-muted">Slug: ' . e($row->slug) . '</small>';
                })
                ->addColumn('parent', fn ($row) => $row->parent ? '<span class="badge bg-light text-dark border">' . e($row->parent->name) . '</span>' : '<span class="badge bg-secondary-subtle text-secondary">Root Category</span>')
                ->addColumn('products_count', fn ($row) => '<span class="badge bg-primary rounded-pill">' . $row->products_count . ' Products</span>')
                ->addColumn('status', fn ($row) => $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', function ($row) {
                    $json = htmlspecialchars(json_encode([
                        'id' => $row->id,
                        'name' => $row->name,
                        'slug' => $row->slug,
                        'parent_id' => $row->parent_id,
                        'icon' => $row->icon,
                        'commission_rate' => $row->commission_rate,
                        'description' => $row->description,
                        'is_active' => (bool)$row->is_active,
                    ]), ENT_QUOTES, 'UTF-8');

                    return '<div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-primary" onclick=\'openEditCategoryModal(' . $json . ')\' title="Edit"><i class="fa fa-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteCategory(' . $row->id . ')" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['name_display', 'parent', 'products_count', 'status', 'action'])
                ->make(true);
        }

        $parentCategories = ProductCategory::whereNull('parent_id')->orderBy('name')->get();

        return view('product::backend.categories.index', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:product_categories,id',
            'icon' => 'nullable|string|max:100',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['is_active'] = $request->boolean('is_active', true);

        ProductCategory::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Category created successfully!']);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function update(Request $request, int $id)
    {
        $category = ProductCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:product_categories,id|not_in:' . $category->id,
            'icon' => 'nullable|string|max:100',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $category->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Category updated successfully!']);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(int $id)
    {
        $category = ProductCategory::findOrFail($id);
        ProductCategory::where('parent_id', $category->id)->update(['parent_id' => null]);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }
}
