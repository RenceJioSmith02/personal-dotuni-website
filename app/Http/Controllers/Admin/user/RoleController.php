<?php

namespace App\Http\Controllers\Admin\user;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\User\RoleService;

class RoleController extends Controller
{
    protected RoleService $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        return view('admin.user_management.roles.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('roles', 'name')],
        ]);

        $role = $this->service->create($validated);

        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }

    public function edit(Role $role)
    {
        return response()->json($role);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('roles', 'name')->ignore($role->id)],
        ]);

        $role = $this->service->update($role, $validated);

        return response()->json(['message' => 'Role updated successfully', 'role' => $role]);
    }

    public function destroy(Role $role)
    {
        try {
            $this->service->delete($role);
            return response()->json(['message' => 'Role deleted successfully', 'id' => $role->id]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}

