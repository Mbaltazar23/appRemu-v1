<?php

namespace App\Policies;

use App\Models\Sustainer;
use App\Models\User;

class SustainerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sustainer $sustainer): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin() ;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sustainer $sustainer): bool
    {
        return $user->isContador() || in_array('MANISAPRE', $user->permissions) || in_array('MANAFPTR', $user->permissions);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sustainer $sustainer): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin() || in_array('MANISAPRE', $user->permissions) || in_array('MANAFPTR', $user->permissions);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sustainer $sustainer): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin() || in_array('MANISAPRE', $user->permissions) || in_array('MANAFPTR', $user->permissions);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sustainer $sustainer): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin() || in_array('MANISAPRE', $user->permissions) || in_array('MANAFPTR', $user->permissions);
    }
}
