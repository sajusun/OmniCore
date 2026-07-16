<?php

namespace App\Http\Controllers\Web\Backend\Access;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group">
                                <a href="' . route('admin.permissions.edit', $row->id) . '" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                <button type="button" onclick="deletePermission(' . $row->id . ')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->make(true);
        }

        return view('backend.layouts.access.permission.index');
    }

    public function create()
    {
        return view('backend.layouts.access.permission.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name, 'guard_name' => 'web']);

        return redirect()->route('admin.permissions.index')->with('t-success', 'Permission created successfully');
    }

    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);
        return view('backend.layouts.access.permission.edit', compact('permission'));
    }

    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('admin.permissions.index')->with('t-success', 'Permission updated successfully');
    }

    public function destroy(string $id)
    {
        Permission::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Permission deleted successfully']);
    }
}
