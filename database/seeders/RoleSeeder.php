<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'guard_name' => 'api',
            ],
            [
                'name' => 'Security',
                'guard_name' => 'api',
            ],
            [
                'name' => 'Employee',
                'guard_name' => 'api',
            ],
        ];

        $permissions = [
            "Admin" => [
                [
                    'name' => 'dashboard',
                    'guard_name' => 'api',
                ],
                [
                    'name' => 'employee',
                    'guard_name' => 'api',
                ],
                [
                    'name' => 'role',
                    'guard_name' => 'api',
                ],
                [
                    'name' => 'company',
                    'guard_name' => 'api',
                ],
                [
                    'name' => 'report',
                    'guard_name' => 'api',
                ],
            ],
            "Security" => [
                [
                    'name' => 'dashboard',
                    'guard_name' => 'api',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            // Create or fetch the role
            $role = Role::firstOrCreate(['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']]);

            // Assign permissions to role if available
            if (isset($permissions[$role->name])) {
                foreach ($permissions[$role->name] as $permissionData) {
                    // Create or fetch the permission
                    $permission = Permission::firstOrCreate(
                        ['name' => $permissionData['name']],
                        ['guard_name' => $permissionData['guard_name']]
                    );

                    // Assign the permission to the role
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}
