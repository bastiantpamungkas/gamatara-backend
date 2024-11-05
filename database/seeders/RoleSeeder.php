<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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
                'guard_name' => 'Admin',
            ],
            [
                'name' => 'Security',
                'guard_name' => 'Security',
            ],
            [
                'name' => 'Employee',
                'guard_name' => 'Employee',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
