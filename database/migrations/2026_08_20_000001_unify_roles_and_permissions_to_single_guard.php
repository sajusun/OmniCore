<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Forget cached permissions
        if (class_exists(PermissionRegistrar::class)) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }

        // 2. Safely remove duplicate api guard roles & permissions
        $apiRoleIds = DB::table('roles')->where('guard_name', 'api')->pluck('id')->toArray();
        if (!empty($apiRoleIds)) {
            DB::table('role_has_permissions')->whereIn('role_id', $apiRoleIds)->delete();
            DB::table('model_has_roles')->whereIn('role_id', $apiRoleIds)->delete();
            DB::table('roles')->whereIn('id', $apiRoleIds)->delete();
        }

        $apiPermIds = DB::table('permissions')->where('guard_name', 'api')->pluck('id')->toArray();
        if (!empty($apiPermIds)) {
            DB::table('role_has_permissions')->whereIn('permission_id', $apiPermIds)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $apiPermIds)->delete();
            DB::table('permissions')->whereIn('id', $apiPermIds)->delete();
        }

        // 3. Assign default 'user' role to any existing users without a role
        $usersWithoutRole = User::doesntHave('roles')->get();
        foreach ($usersWithoutRole as $user) {
            $user->assignRole('user');
        }

        // 4. Re-forget cached permissions
        if (class_exists(PermissionRegistrar::class)) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed for guard cleanup
    }
};
