<?php

namespace App\Policies;

use App\Models\Bonus;
use App\Models\User;

class BonusPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array('MANBODESCOL', $user->role->permissions);
    }

    public function viewWorkers(User $user): bool
    {
        return in_array('MANBODESTRA', $user->role->permissions);
    }

    public function parametersGen(User $user): bool
    {
        return in_array('MANBODESGEN', $user->role->permissions);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bonus $bonuses): bool
    {
        return in_array('MANBODESCOL', $user->role->permissions);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array('MANBODESCOL', $user->role->permissions);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bonus $bonuses): bool
    {
        return in_array('MANBODESCOL', $user->role->permissions);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bonus $bonuses): bool
    {
        return in_array('MANBODESCOL', $user->role->permissions);
    }

    public function workers(User $user, Bonus $bonuses): bool
    {
        return in_array('MANBODESTRA', $user->role->permissions);

    }
    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bonus $bonuses): bool
    {
        return in_array('MANBODESCOL', $user->role->permissions);
    }
}
