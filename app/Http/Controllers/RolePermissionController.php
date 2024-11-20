<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function role()
    {
        $role = Role::orderBy('id', 'asc')->get();
        
        return response()->json([
            'message' => 'Successfully get all roles',
            'roles' => $role,
        ], 200);
    }

    public function permission()
    {
        $permission = Permission::all()->groupBy('category');
        
        return response()->json([
            'message' => 'Successfully get all permissions',
            'permissions' => $permission,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role,
            'permissions' => $role->permissions
        ], 201);
    }

    public function editRole($id) 
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all()->groupBy('category');
        $rolePermission = $role->permissions->pluck('id')->toArray();
        
        return response()->json([
            'status' => 'Success',
            'role' => $role,
        ], 200);
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role,
        ], 200);
    }
}
