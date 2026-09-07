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

        // ── Create permissions (single default guard) from the Permission enum ───
        $allPermissions = PermissionEnum::forSeeding();

        foreach ($allPermissions as $pData) {
            Permission::create([
                'name' => $pData['name'],
                'display_name' => $pData['display_name'],
                'guard_name' => 'web'
            ]);
        }

        // ── Create unique single roles ─────────────────────────────────────────
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web'], ['display_name' => 'Super Admin']);
        $adminRole      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'], ['display_name' => 'Admin']);
        $staffRole      = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web'], ['display_name' => 'Staff']);
        $userRole       = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web'], ['display_name' => 'User']);

        // ── Assign permissions to roles ───────────────────────────────────────
        $webPermissions = Permission::where('guard_name', 'web')->get();

        // Super Admin & Admin get all permissions
        $superAdminRole->syncPermissions($webPermissions);
        $adminRole->syncPermissions($webPermissions);
        $staffRole->syncPermissions(Permission::where('guard_name', 'web')->where('name', 'like', 'ticket.%')->get());

        // User gets minimal dashboard access
        $userRole->syncPermissions(Permission::where('guard_name', 'web')->where('name', 'dashboard.access')->get());

        // ── Create users & assign roles ──────────────────────────────────────
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

        $this->command->info('✅ Single Guard Permissions & Roles Seeded Successfully!');
    }
}
