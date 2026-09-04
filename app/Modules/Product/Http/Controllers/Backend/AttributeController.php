<?php

namespace App\Modules\Product\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\ProductAttribute;
use App\Modules\Product\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AttributeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductAttribute::with('values')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name_display', function ($row) {
                    return '<div class="fw-bold">' . e($row->name) . '</div><small class="text-muted">Slug: ' . e($row->slug) . '</small>';
                })
                ->addColumn('type', fn ($row) => '<span class="badge bg-info text-capitalize">' . e($row->type) . '</span>')
                ->addColumn('values_display', function ($row) {
                    $badges = $row->values->map(function ($val) {
                        $colorIndicator = $val->code ? '<span class="d-inline-block me-1 border" style="width: 10px; height: 10px; background-color: ' . e($val->code) . ';"></span>' : '';
                        return '<span class="badge bg-light text-dark border me-1 mb-1 d-inline-flex align-items-center">' . $colorIndicator . e($val->value) . ' <button type="button" class="btn-close ms-1" style="font-size: 0.5rem;" onclick="deleteAttributeValue(' . $val->id . ')" title="Remove"></button></span>';
                    })->implode(' ');

                    return $badges ?: '<span class="text-muted small">No values yet</span>';
                })
                ->addColumn('action', function ($row) {
                    $json = htmlspecialchars(json_encode([
                        'id' => $row->id,
                        'name' => $row->name,
                        'type' => $row->type,
                    ]), ENT_QUOTES, 'UTF-8');

                    return '<div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-success" onclick="openAddValueModal(' . $row->id . ', \'' . e($row->name) . '\', \'' . e($row->type) . '\')" title="Add Value"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-sm btn-primary" onclick=\'openEditAttributeModal(' . $json . ')\' title="Edit"><i class="fa fa-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteAttribute(' . $row->id . ')" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['name_display', 'type', 'values_display', 'action'])
                ->make(true);
        }

        return view('product::backend.attributes.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:select,color,button,text',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $attribute = ProductAttribute::create($validated);

        if ($request->filled('initial_values')) {
            $vals = explode(',', $request->input('initial_values'));
            foreach ($vals as $idx => $v) {
                $v = trim($v);
                if (! empty($v)) {
                    $attribute->values()->create([
                        'value' => $v,
                        'code' => $attribute->type === 'color' && str_starts_with($v, '#') ? $v : null,
                        'order' => $idx,
                    ]);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Attribute created successfully!']);
        }

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute created successfully!');
    }

    public function update(Request $request, int $id)
    {
        $attribute = ProductAttribute::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:select,color,button,text',
        ]);

        $attribute->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Attribute updated successfully!']);
        }

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute updated successfully!');
    }

    public function destroy(int $id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        $attribute->values()->delete();
        $attribute->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attribute deleted successfully.',
        ]);
    }

    public function storeValue(Request $request, int $id)
    {
        $attribute = ProductAttribute::findOrFail($id);

        $validated = $request->validate([
            'value' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
        ]);

        $attribute->values()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Option value added successfully.',
        ]);
    }

    public function destroyValue(int $valueId)
    {
        $val = ProductAttributeValue::findOrFail($valueId);
        $val->delete();

        return response()->json([
            'success' => true,
            'message' => 'Option value removed successfully.',
        ]);
    }
}
