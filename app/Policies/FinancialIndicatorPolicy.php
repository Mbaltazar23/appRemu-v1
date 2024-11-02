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
        return $user->isContador() || in_array('MANICOM', $user->permissions);
    }

    public function view(User $user, FinancialIndicator $financialIndicator): bool
    {
        // Verifica si el usuario tiene permiso
        return $user->isContador() || in_array('MANICOM', $user->permissions);
    }

}
