<?php

namespace App\Http\Controllers\Web\Backend\Access;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Services\FileService;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct(private FileService $fileService) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Filter out Admin and Super Admin and eager-load roles
            $data = User::with('roles')
                ->whereDoesntHave('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'super_admin']);
                })->latest();

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
                    return '<div class="flex items-center gap-1.5">
                                <a href="' . route('admin.users.edit', $row->id) . '" class="inline-flex items-center justify-center p-2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors duration-150" title="Edit"><i class="fa fa-edit text-sm leading-none"></i></a>
                                <button type="button" onclick="confirmDeleteUser(' . $row->id . ', \'' . addslashes($row->name) . '\')" class="inline-flex items-center justify-center p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors duration-150" title="Delete"><i class="fa fa-trash text-sm leading-none"></i></button>
                            </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.access.user.index');
    }


    public function create()
    {
        $roles = Role::all();

        return view('backend.access.user.create', compact('roles'));
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $this->fileService->upload($request->file('avatar'), 'profile', 'public', $request->name);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'avatar' => $avatarPath,
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

        return view('backend.access.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('backend.access.user.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'nullable|exists:roles,name',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_subscribed' => 'nullable|boolean',
            'subscription_ends_at' => 'nullable|date',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_subscribed' => $request->has('is_subscribed') ? $request->is_subscribed : $user->is_subscribed,
            'subscription_ends_at' => $request->has('subscription_ends_at') ? $request->subscription_ends_at : $user->subscription_ends_at,
        ];

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->fileService->replace($request->file('avatar'), $user->getRawOriginal('avatar'), 'profile');
        }

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

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
