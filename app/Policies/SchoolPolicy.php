<?php

namespace App\Policies;

use App\Models\School;
use App\Models\SchoolUser;
use App\Models\User;

class SchoolPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array('MANCOL', $user->role->permissions);
    }

    public function viewSchools(User $user): bool
    {
        // Verificamos si el usuario tiene registros asociados en SchoolUser
        return SchoolUser::where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, School $school): bool
    {
        return in_array('MANCOL', $user->role->permissions);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array('MANCOL', $user->role->permissions);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, School $school): bool
    {
        return in_array('MANCOL', $user->role->permissions);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, School $school): bool
    {
        return in_array('MANCOL', $user->role->permissions);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, School $school): bool
    {
        return in_array('MANCOL', $user->role->permissions);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, School $school): bool
    {
        return in_array('MANCOL', $user->role->permissions);
    }
}
