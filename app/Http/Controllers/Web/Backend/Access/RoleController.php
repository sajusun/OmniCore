<?php

namespace App\Http\Controllers\Web\Backend\Access;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('permissions', function ($row) {
                    $badges = '';
                    foreach ($row->permissions as $permission) {
                        $badges .= '<span class="badge bg-info-transparent text-info m-1">' . $permission->name . '</span>';
                    }
                    return $badges ?: 'No Permissions';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group">
                                <a href="' . route('admin.roles.edit', $row->id) . '" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                <button type="button" onclick="deleteRole(' . $row->id . ')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['permissions', 'action'])
                ->make(true);
        }

        return view('backend.layouts.access.role.index');
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('backend.layouts.access.role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('t-success', 'Role created successfully');
    }

    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        return view('backend.layouts.access.role.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array'
        ]);

        $role->update(['name' => $request->name]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')->with('t-success', 'Role updated successfully');
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        if ($role->name == 'Super Admin' || $role->name == 'Admin') {
            return response()->json(['status' => false, 'message' => 'Cannot delete system roles!']);
        }
        $role->delete();
        return response()->json(['status' => true, 'message' => 'Role deleted successfully']);
    }
}
