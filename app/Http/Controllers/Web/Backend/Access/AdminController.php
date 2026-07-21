<?php

namespace App\Http\Controllers\Web\Backend\Access;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::role(['admin', 'super_admin'])->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('role', function ($row) {
                    return $row->getRoleNames()->first() ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status == 'active' ? 'success' : 'danger';
                    return '<span class="badge bg-' . $status . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group">
                                <a href="' . route('admin.stuff.edit', $row->id) . '" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                <button type="button" onclick="deleteAdmin(' . $row->id . ')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.access.admin.index');
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['admin', 'super_admin'])->get();
        return view('backend.access.admin.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'slug' => Helper::makeSlug(User::class, $request->name),
            'status' => 'active'
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.stuff.index')->with('t-success', 'Admin created successfully');
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::whereIn('name', ['admin', 'super_admin'])->get();
        return view('backend.access.admin.edit', compact('user', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->password) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        $user->syncRoles($request->role);

        return redirect()->route('admin.stuff.index')->with('t-success', 'Admin updated successfully');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->id == auth()->id()) {
            return response()->json(['status' => false, 'message' => 'You cannot delete yourself!']);
        }
        $user->delete();
        return response()->json(['status' => true, 'message' => 'Admin deleted successfully']);
    }
}
