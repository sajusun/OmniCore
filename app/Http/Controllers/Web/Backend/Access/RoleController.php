<?php

namespace App\Http\Controllers\Web\Backend\Access;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimization: Eager load permissions to avoid N+1 query issue
            $data = Role::with('permissions')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('permissions', function ($row) {
                    if ($row->permissions->isEmpty()) {
                        return '<span class="text-muted fst-italic" style="font-size: 0.75rem;">No permissions</span>';
                    }

                    // Bootstrap flex layout with gap, ensuring zero rounding
                    $badges = '<div class="d-flex flex-wrap gap-1">';
                    foreach ($row->permissions as $permission) {
                        $badges .= '<span class="bg-primary text-white fw-medium rounded-0" style="font-size: 0.75rem; padding: 0.35em 0.65em;">' . e($permission->name) . '</span>';
                    }
                    $badges .= '</div>';

                    return $badges;
                })
                ->addColumn('action', function ($row) {
                    return '
                    <div class="d-flex align-items-center gap-1">
                    ' . view('components.table.action', ['type' => 'edit', 'href' => route('admin.roles.edit', $row->id)])->render() . '
                    ' . view('components.table.action', ['type' => 'delete', 'onclick' => "deleteRole({$row->id})"])->render() . '
                    </div>
                ';
                })->rawColumns(['permissions', 'action'])->make(true);
        }

        return view('backend.access.role.index');
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('backend.access.role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        // $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
    }

    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        return view('backend.access.role.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        $request->validate([
            'name' => [
                'required',
                Rule::unique('roles', 'name')
                    ->ignore($role->id)
                    ->where(function ($query) use ($role) {
                        return $query->where('guard_name', $role->guard_name);
                    }),
            ],
            'permissions' => 'nullable|array'
        ]);

        $role->update(['name' => $request->name]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        if ($role->name == 'super_admin' || $role->name == 'admin') {
            return response()->json(['status' => false, 'message' => 'Cannot delete system roles!']);
        }
        $role->delete();
        return response()->json(['status' => true, 'message' => 'Role deleted successfully']);
    }
}
