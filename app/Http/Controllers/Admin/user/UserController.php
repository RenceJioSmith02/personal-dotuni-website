<?php

namespace App\Http\Controllers\Admin\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Services\User\UserService;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->datatable($request);
        }

        $roles = Role::all();
        return view('admin.user_management.users.index', compact('roles'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required|min:6',
            'name' => 'required|string',
            'roles' => ['required', 'array', 'min:1'], 
            'is_active' => 'sometimes|boolean',
        ]);


        $user = $this->service->storeOrRestore($validated, $request->roles ?? []);

        return response()->json([
            'message' => 'User created/restored successfully',
            'user' => $user
        ], 201);
    }

    public function edit(User $user)
    {
        $user->load('roles');
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'is_active' => $user->is_active,
            'roles' => $user->roles->pluck('id')->toArray(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'name' => 'required|string',
            'roles' => ['required', 'array', 'min:1'], 
            'is_active' => 'required|boolean',
            'password' => 'nullable|min:6',
        ]);


        try {
            $user = $this->service->updateOrRestore($user, $validated, $request->roles ?? []);
            return response()->json(['message' => 'User updated successfully', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(User $user)
    {
        $this->service->delete($user);
        return response()->json(['message' => 'User deleted successfully', 'id' => $user->id]);
    }
}

