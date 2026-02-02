<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Throwable;

class UserService
{
    public function list()
    {
        return User::with('roles')->get();
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
