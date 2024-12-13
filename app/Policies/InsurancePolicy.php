<?php

namespace App\Policies;

use App\Models\Insurance;
use App\Models\User;

class InsurancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ( in_array('MANAFP', $user->role->permissions) || in_array('MANISAPRETR', $user->role->permissions));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Insurance $insurance): bool
    {
        return ( in_array('MANAFP', $user->role->permissions) || in_array('MANISAPRETR', $user->role->permissions));
    }

    /**
     * Determinate Type Insurances for vinculate Worker
     */
    public function linkWorker(User $user, Insurance $insurance)
    {
        // Asegúrate de que la lógica de permisos sea adecuada para tu aplicación
        return $insurance->type !== null;
    }
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  ( in_array('MANAFP', $user->role->permissions) || in_array('MANISAPRETR', $user->role->permissions));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Insurance $insurance): bool
    {
        return ( in_array('MANAFP', $user->role->permissions) || in_array('MANISAPRETR', $user->role->permissions));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Insurance $insurance): bool
    {
        return ( in_array('MANAFP', $user->role->permissions) || in_array('MANISAPRETR', $user->role->permissions));
    }
}
