<?php

namespace App\Policies;

use App\Models\FinancialIndicator;
use App\Models\User;

class FinancialIndicatorPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return in_array('MANIECO', $user->role->permissions) || in_array('MANASIGFAM', $user->role->permissions);
    }

    public function view(User $user, FinancialIndicator $financialIndicator): bool
    {
        // Verifica si el usuario tiene permiso
        return in_array('MANIECO', $user->role->permissions) || in_array('MANASIGFAM', $user->role->permissions);;
    }

}
