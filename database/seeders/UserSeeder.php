<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Disable foreign key checks to truncate tables
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing roles and permissions to avoid guard conflicts
        Permission::truncate();
        Role::truncate();

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create permissions for both web and api guards
        $permissions = [
            'user list',
            'user create',
            'user edit',
            'user delete',
            'role list',
            'role create',
            'role edit',
            'role delete',
            'permission list',
            'permission create',
            'permission edit',
            'permission delete',
            'dashboard access',
            'settings access',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        // Create roles for both web and api guards
        $superAdminRoleWeb = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRoleApi = Role::create(['name' => 'Super Admin', 'guard_name' => 'api']);

        $adminRoleWeb = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRoleApi = Role::create(['name' => 'Admin', 'guard_name' => 'api']);

        $userRoleWeb = Role::create(['name' => 'User', 'guard_name' => 'web']);
        $userRoleApi = Role::create(['name' => 'User', 'guard_name' => 'api']);

        // Assign all permissions to Super Admin
        $allPermissions = Permission::all();
        $superAdminRoleWeb->syncPermissions($allPermissions->where('guard_name', 'web'));
        $superAdminRoleApi->syncPermissions($allPermissions->where('guard_name', 'api'));

        // Assign specific permissions to Admin
        $adminPermissionNames = [
            'user list',
            'user create',
            'user edit',
            'dashboard access',
        ];
        $adminPermissionsWeb = Permission::whereIn('name', $adminPermissionNames)->where('guard_name', 'web')->get();
        $adminPermissionsApi = Permission::whereIn('name', $adminPermissionNames)->where('guard_name', 'api')->get();
        $adminRoleWeb->syncPermissions($adminPermissionsWeb);
        $adminRoleApi->syncPermissions($adminPermissionsApi);

        // Assign basic permissions to User
        $userPermissionNames = ['dashboard access'];
        $userPermissionsWeb = Permission::whereIn('name', $userPermissionNames)->where('guard_name', 'web')->get();
        $userPermissionsApi = Permission::whereIn('name', $userPermissionNames)->where('guard_name', 'api')->get();
        $userRoleWeb->syncPermissions($userPermissionsWeb);
        $userRoleApi->syncPermissions($userPermissionsApi);

        // Create users
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
        $superAdmin->assignRole('Super Admin');

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
        $admin->assignRole('Admin');

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
        $regularUser->assignRole('User');

        // Create additional test users
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
            $testUser->assignRole('User');
        }

        $this->command->info('Users seeded successfully with roles and permissions!');
        $this->command->info('Super Admin: superadmin@example.com / password');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('User: user@example.com / password');
    }
}
