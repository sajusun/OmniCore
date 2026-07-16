<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Disable FK checks so we can safely truncate pivot tables first
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        Permission::truncate();
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ── Create permissions (web + api) from the Permission enum ──────────
        $allPermissions = PermissionEnum::forSeeding();  // [ ['name' => '...', 'display_name' => '...'], ... ]

        foreach ($allPermissions as $pData) {
            Permission::create(['name' => $pData['name'], 'display_name' => $pData['display_name'], 'guard_name' => 'web']);
            Permission::create(['name' => $pData['name'], 'display_name' => $pData['display_name'], 'guard_name' => 'api']);
        }

        // ── Create roles (web + api) ──────────────────────────────────────────
        $superAdminWeb = Role::create(['name' => 'super_admin', 'display_name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminApi = Role::create(['name' => 'super_admin', 'display_name' => 'Super Admin', 'guard_name' => 'api']);

        $adminWeb = Role::create(['name' => 'admin', 'display_name' => 'Admin',  'guard_name' => 'web']);
        $adminApi = Role::create(['name' => 'admin', 'display_name' => 'Admin',  'guard_name' => 'api']);

        $userWeb = Role::create(['name' => 'user', 'display_name' => 'User',  'guard_name' => 'web']);
        $userApi = Role::create(['name' => 'user', 'display_name' => 'User',  'guard_name' => 'api']);

        // ── Assign permissions to roles ───────────────────────────────────────

        // Super Admin: all permissions
        $webPermissions = Permission::where('guard_name', 'web')->get();
        $apiPermissions = Permission::where('guard_name', 'api')->get();

        $superAdminWeb->syncPermissions($webPermissions);
        $superAdminApi->syncPermissions($apiPermissions);

        // Admin: all permissions (same as Super Admin per requirements)
        $adminWeb->syncPermissions($webPermissions);
        $adminApi->syncPermissions($apiPermissions);

        // User: only dashboard access (minimal)
        $userWeb->syncPermissions(Permission::where('guard_name', 'web')->where('name', 'dashboard.access')->get());
        $userApi->syncPermissions(Permission::where('guard_name', 'api')->where('name', 'dashboard.access')->get());

        // ── Create users ──────────────────────────────────────────────────────

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $superAdmin->syncRoles(['super_admin']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'slug' => 'admin-user',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $admin->syncRoles(['admin']);

        $regularUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'slug' => 'regular-user',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $regularUser->syncRoles(['user']);

        // Additional test users
        for ($i = 1; $i <= 5; $i++) {
            $testUser = User::firstOrCreate(
                ['email' => "testuser{$i}@example.com"],
                [
                    'name' => "Test User {$i}",
                    'slug' => "test-user-{$i}",
                    'email' => "testuser{$i}@example.com",
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ]
            );
            $testUser->syncRoles(['user']);
        }

        $this->command->info('✅ Permissions seeded: '.count($allPermissions).' permissions × 2 guards');
        $this->command->info('✅ Roles: Super Admin, Admin (all permissions), User (dashboard.access only)');
        $this->command->info('──────────────────────────────────────────────');
        $this->command->info('  Super Admin : superadmin@example.com / password');
        $this->command->info('  Admin       : admin@example.com / password');
        $this->command->info('  User        : user@example.com / password');
    }
}
