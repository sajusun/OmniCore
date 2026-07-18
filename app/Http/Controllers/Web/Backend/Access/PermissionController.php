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
                ->addColumn('display_name', fn ($row) => $row->display_name ?? '—')
                ->addColumn('action', function ($row) {
                    return '
                        <div class="flex items-center gap-1.5">
                            <a href="'.route('admin.permissions.edit', $row->id).'"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-colors"
                                title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <button type="button" onclick="deletePermission('.$row->id.')"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors"
                                title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
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
