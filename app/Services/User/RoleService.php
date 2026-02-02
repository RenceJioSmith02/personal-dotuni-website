<?php

namespace App\Services\User;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Throwable;

class RoleService
{
    public function list()
    {
        return Role::all();
    }

    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            try {
                return Role::create($data);
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            try {
                $role->update($data);
                return $role;
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        });
    }

    public function delete(Role $role): void
    {
        DB::transaction(function () use ($role) {
            if ($role->users()->count() > 0) {
                throw new \Exception("Role is assigned to users and cannot be deleted.");
            }
            $role->delete();
        });
    }
}
