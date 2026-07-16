<?php

namespace App\Http\Controllers\Web\Backend\Access;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Filter out Admin and Super Admin
            $data = User::whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Super Admin']);
            })->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('role', function ($row) {
                    return $row->getRoleNames()->first() ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status == 'active' ? 'success' : 'danger';

                    return '<span class="badge bg-'.$status.'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group">
                                <a href="'.route('admin.users.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                <a href="'.route('admin.users.show', $row->id).'" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                <button type="button" onclick="deleteUser('.$row->id.')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.access.user.index');
    }

    /**
     * Display Grid.js demo.
     */
    public function gridDemo(Request $request)
    {
        if ($request->ajax()) {
            $limit = $request->get('limit', 3);

            // Filter out Admin and Super Admin and paginate
            $users = User::whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Super Admin']);
            })->latest()->paginate($limit);

            $data = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first() ?? 'N/A',
                    'status' => $user->status,
                ];
            });

            return response()->json([
                'data' => $data,
                'total' => $users->total(),
            ]);
        }

        return view('backend.layouts.access.user.grid_demo');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();

        return view('backend.layouts.access.user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'slug' => Helper::makeSlug(User::class, $request->name),
            'status' => 'active',
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('t-success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
        public function show(string $id)
    {
        $user = User::findOrFail($id);

        // Load related data
        $foodScans = $user->foodScans()->latest()->take(7)->get();
        $foodLogs = $user->foodLogs()->latest()->take(7)->get();
        $activityLogs = $user->activityLogs()->latest()->take(7)->get();

        return view('backend.layouts.access.user.show', compact('user', 'foodScans', 'foodLogs', 'activityLogs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('backend.layouts.access.user.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->password) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        $user->syncRoles($request->role);

        return redirect()->route('admin.users.index')->with('t-success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->id == auth()->id()) {
            return response()->json(['status' => false, 'message' => 'You cannot delete yourself!']);
        }
        $user->delete();

        return response()->json(['status' => true, 'message' => 'User deleted successfully']);
    }

    public function status($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status == 'active' ? 'inactive' : 'active';
        $user->save();

        return response()->json(['status' => true, 'message' => 'User status updated successfully']);
    }
}
