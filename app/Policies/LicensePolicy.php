<?php

namespace App\Policies;

use App\Models\License;
use App\Models\User;

class LicensePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array('MANLIC', $user->role->permissions);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, License $license): bool
    {
        return in_array('MANLIC', $user->role->permissions);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array('MANLIC', $user->role->permissions);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, License $license): bool
    {
        return in_array('MANLIC', $user->role->permissions);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, License $license): bool
    {
        return in_array('MANLIC', $user->role->permissions);
    }
}
