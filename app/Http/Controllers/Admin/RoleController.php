<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Store a newly created role (AJAX modal).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'name'),
            ],
        ]);

        $role = Role::create($validated);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role
        ], 201);
    }

    /**
     * Get role data for editing (AJAX modal).
     */
    public function edit(Role $role)
    {
        return response()->json($role);
    }

    /**
     * Update the specified role (AJAX modal).
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
        ]);

        $role->update($validated);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }

    /**
     * Delete the specified role (AJAX modal).
     */
    public function destroy(Role $role)
    {
        // Prevent deleting roles assigned to users
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Role is assigned to users and cannot be deleted'
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
            'id' => $role->id
        ]);
    }
}
