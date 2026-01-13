<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;

class ProgramPolicy
{
    /**
     * Determine whether the user can view any programs.
     */
    public function viewAny(User $user): bool
    {
        // User must have at least one of these roles
        $allowedRoles = ['admin', 'editor', 'publisher'];

        // Check if user has any of these roles
        return $user->roles->pluck('name')->intersect($allowedRoles)->isNotEmpty();
    }

    /**
     * Determine whether the user can view a specific program.
     */
    public function view(User $user, Program $program): bool
    {
        // Everyone can view
        return true;
    }

    /**
     * Determine whether the user can create programs.
     */
    public function create(User $user): bool
    {
        return $user->roles->pluck('name')->intersect(['admin', 'editor'])->isNotEmpty();
    }

    /**
     * Determine whether the user can update a program.
     */
    public function update(User $user, Program $program): bool
    {
        return $user->roles->pluck('name')->intersect(['admin', 'editor'])->isNotEmpty();
    }

    /**
     * Determine whether the user can delete a program.
     */
    public function delete(User $user, Program $program): bool
    {
        return $user->roles->pluck('name')->contains('admin');
    }
}
