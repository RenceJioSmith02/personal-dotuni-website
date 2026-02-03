<?php

namespace App\Services\User;

use Throwable;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function list()
    {
        return Role::all();
    }

    public function datatable(Request $request)
    {
        $query = Role::query();

        $total = $query->count();

        /* ===============================
           SEARCH
        =============================== */
        if ($search = $request->input('search.value')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $filtered = $query->count();

        /* ===============================
           ORDER
        =============================== */
        $columns = ['name'];
        $orderColIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');
        $orderCol = $columns[$orderColIndex] ?? 'name';

        $query->orderBy($orderCol, $orderDir);

        /* ===============================
           PAGINATION
        =============================== */
        $roles = $query
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
            'data' => $roles->map(function ($role) {
                return [
                    'name' => e($role->name),
                    'actions' => view('admin.user_management.roles.partials.actions', compact('role'))->render(),
                ];
            }),
        ]);
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
