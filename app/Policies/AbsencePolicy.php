<?php

namespace App\Policies;

use App\Models\Absence;
use App\Models\User;

class AbsencePolicy
{
    /**
     * Determine whether the user can view any absence records.
     */
    public function viewAny(User $user): bool
    {
        // Lógica de autorización: el usuario debe ser contador para ver ausencias.
        return in_array('MANINAS', $user->role->permissions);
    }

    /**
     * Determine whether the user can view the specific absence record.
     */
    public function view(User $user, Absence $absence): bool
    {
        return in_array('MANINAS', $user->role->permissions);
    }
    /**
     * Determine whether the user can create an absence.
     */
    public function create(User $user): bool
    {
        // Lógica de autorización: el usuario debe ser contador para crear ausencias.
        return in_array('MANINAS', $user->role->permissions);
    }

    /**
     * Determine whether the user can update the absence record.
     */
    public function update(User $user, Absence $absence): bool
    {
        return in_array('MANINAS', $user->role->permissions);
    }

    /**
     * Determine whether the user can delete the absence record.
     */
    public function delete(User $user, Absence $absence): bool
    {
        return in_array('MANINAS', $user->role->permissions);
    }

    /**
     * Determine whether the user can restore the absence record.
     */
    public function restore(User $user, Absence $absence): bool
    {
        return in_array('MANINAS', $user->role->permissions);
    }

    /**
     * Determine whether the user can permanently delete the absence record.
     */
    public function forceDelete(User $user, Absence $absence): bool
    {
        return in_array('MANINAS', $user->role->permissions);
    }
}
