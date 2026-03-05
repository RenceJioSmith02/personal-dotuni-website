<?php

namespace App\Services\User;

use Throwable;
use DomainException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function list()
    {
        return User::with('roles')
            ->whereNull('deleted_at') // ✅ Exclude archived
            ->get();
    }

    public function datatable(Request $request)
    {
        // ✅ Show ALL records including archived
        $query = User::with('roles');

        $total = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas(
                        'roles',
                        fn($q2) =>
                        $q2->where('name', 'like', "%{$search}%")
                    );
            });
        }

        $filtered = $query->count();

        $columns = ['email', 'name', 'roles', 'status', 'created_at', 'updated_at', 'actions'];
        $orderColIndex = $request->input('order.0.column', 0);
        $orderCol = $columns[$orderColIndex] ?? 'email';
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if ($orderCol === 'roles') {
            $query->join('user_roles', 'users.id', '=', 'user_roles.user_id')
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->orderBy('roles.name', $orderDir)
                ->select('users.*');
        } elseif (!in_array($orderCol, ['status', 'actions'])) {
            $query->orderBy('users.' . $orderCol, $orderDir);
        }

        $users = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $users->map(function ($user) {
                return [
                    'email' => e($user->email),
                    'name' => e($user->name),
                    'roles' => $user->roles->map(
                        fn($r) =>
                        '<span class="badge badge-info">' . e($r->name) . '</span>'
                    )->implode(' '),
                    'status' => $user->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>',
                    'created_at' => $user->created_at->toDateTimeString(),
                    'updated_at' => $user->updated_at->toDateTimeString(),
                    'archived' => !is_null($user->deleted_at), // ✅ Pass archive state
                    'actions' => view(
                        'admin.user_management.users.partials.actions',
                        compact('user')
                    )->render(),
                ];
            }),
        ]);
    }

    public function create(array $data, array $roleIds = []): User
    {
        return DB::transaction(function () use ($data, $roleIds) {
            try {
                // ✅ Restore if same email was archived
                $existing = User::where('email', $data['email'])->first();

                if ($existing && !is_null($existing->deleted_at)) {
                    DB::table('users')->where('id', $existing->id)->update([
                        'name' => $data['name'],
                        'password' => Hash::make($data['password']),
                        'is_active' => true,
                        'deleted_at' => null,
                    ]);
                    $existing = $existing->fresh();
                    $existing->roles()->sync($roleIds);
                    return $existing;
                }

                if ($existing) {
                    throw new DomainException('A user with this email already exists.');
                }

                $user = User::create([
                    'email' => $data['email'],
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'is_active' => $data['is_active'] ?? true,
                ]);

                $user->roles()->sync($roleIds);
                return $user;

            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function update(User $user, array $data, array $roleIds = []): User
    {
        return DB::transaction(function () use ($user, $data, $roleIds) {
            try {
                $conflict = User::where('email', $data['email'])
                    ->where('id', '!=', $user->id)
                    ->whereNull('deleted_at')
                    ->first();

                if ($conflict) {
                    throw new DomainException('A user with this email already exists.');
                }

                $user->update([
                    'email' => $data['email'],
                    'name' => $data['name'],
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

    /**
     * ✅ Hard delete — must be inactive first
     */
    public function delete(User $user): void
    {
        try {
            DB::transaction(function () use ($user) {

                // ✅ Guard: prevent self-delete
                if ($user->id === Auth::id()) {
                    throw new DomainException('You cannot delete your own account.');
                }

                // ✅ Guard: must be inactive before hard deleting
                if ($user->is_active) {
                    throw new DomainException(
                        "Cannot delete '{$user->name}'. Please deactivate them before deleting."
                    );
                }

                $user->roles()->detach();
                $user->delete();
            });
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to delete user.');
        }
    }

    /**
     * ✅ Archive — sets is_active = false + deleted_at = now()
     */
    public function archive(User $user): User
    {
        try {
            // ✅ Guard: prevent self-archive
            if ($user->id === Auth::id()) {
                throw new DomainException('You cannot archive your own account.');
            }

            DB::table('users')->where('id', $user->id)->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);

            return $user->fresh();
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to archive user.');
        }
    }

    /**
     * ✅ Unarchive — sets is_active = true + deleted_at = null
     */
    public function unarchive(User $user): User
    {
        try {
            DB::table('users')->where('id', $user->id)->update([
                'is_active' => true,
                'deleted_at' => null,
            ]);

            return $user->fresh();
        } catch (Throwable $e) {
            report($e);
            throw new DomainException('Failed to unarchive user.');
        }
    }
}