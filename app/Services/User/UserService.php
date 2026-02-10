<?php

namespace App\Services\User;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function list()
    {
        return User::with('roles')->get();
    }

    public function datatable(Request $request)
    {
        $query = User::with('roles');

        $total = $query->count();

        /* ===============================
           SEARCH
        =============================== */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('roles', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $filtered = $query->count();

        /* ===============================
           ORDER
        =============================== */
        $columns = ['email', 'name', 'roles', 'is_active', 'created_at', 'updated_at'];
        $orderColIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');
        $orderCol = $columns[$orderColIndex] ?? 'email';

        if ($orderCol === 'roles') {
            $query->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->orderBy('roles.name', $orderDir)
                ->select('users.*');
        } else {
            $query->orderBy('users.' . $orderCol, $orderDir);
        }

        /* ===============================
           PAGINATION
        =============================== */
        $users = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        /* ===============================
           RESPONSE
        =============================== */
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $users->map(function ($user) {
                return [
                    'email' => e($user->email),
                    'name' => e($user->name),
                    'roles' => $user->roles->map(fn($r) => '<span class="badge badge-info">' . $r->name . '</span>')->implode(' '),
                    'status' => $user->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'created_at' => $user->created_at->toDateTimeString(),
                    'updated_at' => $user->updated_at->toDateTimeString(),
                    'actions' => view('admin.user_management.users.partials.actions', compact('user'))->render(),
                ];
            }),
        ]);
    }


    public function storeOrRestore(array $data, array $roleIds = []): User
    {
        return DB::transaction(function () use ($data, $roleIds) {
            try {
                $existing = User::withTrashed()
                    ->where('email', $data['email'])
                    ->first();

                if ($existing) {
                    if ($existing->trashed())
                        $existing->restore();

                    $existing->update([
                        'name' => $data['name'] ?? $existing->name,
                        'password' => Hash::make($data['password']),
                        'is_active' => 1,
                    ]);

                    $existing->roles()->sync($roleIds);
                    return $existing;
                }

                $user = User::create([
                    'email' => $data['email'],
                    'name' => $data['name'] ?? null,
                    'password' => Hash::make($data['password']),
                    'is_active' => $data['is_active'] ?? 1,
                ]);

                $user->roles()->sync($roleIds);
                return $user;
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function updateOrRestore(User $user, array $data, array $roleIds = []): User
    {
        return DB::transaction(function () use ($user, $data, $roleIds) {
            try {
                // Check soft-deleted conflicts
                $conflict = User::withTrashed()
                    ->where('email', $data['email'])
                    ->where('id', '!=', $user->id)
                    ->first();

                if ($conflict) {
                    throw new \Exception(
                        "A user with this email already exists (including archived records)."
                    );
                }

                $user->update([
                    'email' => $data['email'],
                    'name' => $data['name'] ?? $user->name,
                    'is_active' => $data['is_active'] ?? $user->is_active,
                ]);

                if (!empty($data['password'])) {
                    $user->update(['password' => Hash::make($data['password'])]);
                }

                $user->roles()->sync($roleIds);

                return $user;
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(fn() => $user->delete());
    }
}
