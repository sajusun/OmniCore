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
                    if ($row->permissions->isEmpty()) {
                        return '<span class="text-xs text-gray-400 dark:text-gray-500 italic">No permissions</span>';
                    }
                    $badges = '<div class="flex flex-wrap gap-1">';
                    foreach ($row->permissions as $permission) {
                        $badges .= '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">' . e($permission->name) . '</span>';
                    }
                    $badges .= '</div>';
                    return $badges;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="flex items-center gap-1.5">
                            <a href="' . route('admin.roles.edit', $row->id) . '"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-colors"
                                title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <button type="button" onclick="deleteRole(' . $row->id . ')"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors"
                                title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['permissions', 'action'])
                ->make(true);
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

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('t-success', 'Role created successfully');
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
        if ($role->name == 'super_admin' || $role->name == 'admin') {
            return response()->json(['status' => false, 'message' => 'Cannot delete system roles!']);
        }
        $role->delete();
        return response()->json(['status' => true, 'message' => 'Role deleted successfully']);
    }
}
