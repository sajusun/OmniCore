<?php

namespace App\Http\Controllers\Web\Backend\Access;

use App\Enums\Permission as PermissionEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Show only web-guard permissions (avoid duplicates in UI)
            $data = Permission::where('guard_name', 'web')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('display_name', fn ($row) => $row->display_name
                    ? '<span class="text-body">' . e($row->display_name) . '</span>'
                    : '<span class="text-muted">—</span>')
                ->editColumn('name', function ($row) {
                    return '<code class="fw-semibold" style="font-size: 0.8125rem;">' . e($row->name) . '</code>';
                })
                ->editColumn('guard_name', function ($row) {
                    return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-medium px-2 py-0.5" style="font-size: 0.725rem;">' . e($row->guard_name) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex align-items-center gap-1">
                        ' . view('components.table.action', ['type' => 'edit', 'href' => route('admin.permissions.edit', $row->id)])->render() . '
                        ' . view('components.table.action', ['type' => 'delete', 'onclick' => "deletePermission({$row->id})"])->render() . '
                        </div>
                    ';
                })
                ->rawColumns(['name', 'display_name', 'guard_name', 'action'])
                ->make(true);
        }

        return view('backend.access.permission.index');
    }

    public function create()
    {
        return view('backend.access.permission.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|unique:permissions,name|regex:/^[a-z]+(\.[a-z]+)*$/',
            'display_name' => 'required|string|max:100',
        ]);

        // Create for both guards
        Permission::create(['name' => $request->name, 'display_name' => $request->display_name, 'guard_name' => 'web']);
        Permission::create(['name' => $request->name, 'display_name' => $request->display_name, 'guard_name' => 'api']);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully');
    }

    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);

        return view('backend.access.permission.edit', compact('permission'));
    }

    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);
        $request->validate([
            'name'         => 'required|unique:permissions,name,' . $permission->id . '|regex:/^[a-z]+(\.[a-z]+)*$/',
            'display_name' => 'required|string|max:100',
        ]);

        // Update both guards (web + api)
        Permission::where('name', $permission->name)->update([
            'name'         => $request->name,
            'display_name' => $request->display_name,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully');
    }

    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        // Delete both guards
        Permission::where('name', $permission->name)->delete();
        return response()->json(['status' => true, 'message' => 'Permission deleted successfully']);
    }
}
