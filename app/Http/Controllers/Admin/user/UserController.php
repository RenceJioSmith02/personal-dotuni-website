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

    public function index()
    {
        $users = $this->service->list();
        $roles = Role::all();
        return view('admin.user_management.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required|min:6',
            'name' => 'nullable|string',
            'roles' => 'array',
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
            'name' => 'nullable|string',
            'roles' => 'array',
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


// namespace App\Http\Controllers\Admin\user;

// use App\Http\Controllers\Controller;
// use App\Models\User;
// use App\Models\Role;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\Rule;

// class UserController extends Controller
// {
//     /**
//      * Display a listing of users.
//      */
//     public function index()
//     {
//         $users = User::with('roles')->get();
//         $roles = Role::all(); // For modal role selection

//         return view('admin.user_management.users.index', compact('users', 'roles'));
//     }

//     /**
//      * Store a newly created user (AJAX modal, soft-delete aware).
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'email' => [
//                 'required',
//                 'email',
//                 Rule::unique('users')->whereNull('deleted_at'),
//             ],
//             'password' => 'required|min:6',
//             'name' => 'nullable|string',
//             'roles' => 'array',
//             'is_active' => 'sometimes|boolean',
//         ]);

//         // Check if a soft-deleted user exists
//         $existing = User::withTrashed()
//             ->where('email', $validated['email'])
//             ->first();

//         if ($existing) {
//             // Restore and update soft-deleted user
//             $existing->restore();
//             $existing->update([
//                 'name' => $validated['name'] ?? $existing->name,
//                 'password' => Hash::make($validated['password']),
//                 'is_active' => 1,
//             ]);

//             $existing->roles()->sync($request->roles ?? []);

//             return response()->json([
//                 'message' => 'User restored successfully',
//                 'user' => $existing
//             ]);
//         }

//         // Create new user
//         $user = User::create([
//             'email' => $validated['email'],
//             'password' => Hash::make($validated['password']),
//             'name' => $validated['name'] ?? null,
//             'is_active' => $validated['is_active'] ?? 1,
//         ]);

//         $user->roles()->sync($request->roles ?? []);

//         return response()->json([
//             'message' => 'User created successfully',
//             'user' => $user
//         ], 201);
//     }

//     /**
//      * Get user data for editing (AJAX modal).
//      */
//     public function edit(User $user)
//     {
//         $user->load('roles'); // include roles relationship
//         return response()->json([
//             'id' => $user->id,
//             'email' => $user->email,
//             'name' => $user->name,
//             'is_active' => $user->is_active,
//             'roles' => $user->roles->pluck('id')->toArray(),
//         ]);
//     }

//     /**
//      * Update the specified user (AJAX modal, soft-delete aware).
//      */
//     public function update(Request $request, User $user)
//     {
//         $validated = $request->validate([
//             'email' => [
//                 'required',
//                 'email',
//                 Rule::unique('users')->ignore($user->id)->whereNull('deleted_at'),
//             ],
//             'name' => 'nullable|string',
//             'roles' => 'array',
//             'is_active' => 'required|boolean',
//             'password' => 'nullable|min:6',
//         ]);

//         // Check soft-deleted conflicts
//         $conflict = User::withTrashed()
//             ->where('email', $validated['email'])
//             ->where('id', '!=', $user->id)
//             ->first();

//         if ($conflict) {
//             return response()->json([
//                 'message' => 'A user with this email already exists (including archived records). Please use another email or restore the old user.'
//             ], 422);
//         }

//         // Update user info
//         $user->update([
//             'email' => $validated['email'],
//             'name' => $validated['name'] ?? $user->name,
//             'is_active' => $validated['is_active']
//         ]);

//         // Update password if provided
//         if (!empty($validated['password'])) {
//             $user->update([
//                 'password' => Hash::make($validated['password'])
//             ]);
//         }

//         $user->roles()->sync($request->roles ?? []);

//         return response()->json([
//             'message' => 'User updated successfully',
//             'user' => $user
//         ]);
//     }

//     /**
//      * Soft delete the specified user.
//      */
//     public function destroy(User $user)
//     {
//         $user->delete();

//         return response()->json([
//             'message' => 'User deleted successfully',
//             'id' => $user->id
//         ]);
//     }
// }
