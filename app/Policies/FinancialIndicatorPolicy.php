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
        return in_array('MANIECO', $user->role->permissions) || in_array('MANIMREN', $user->role->permissions) || in_array('MANICOM', $user->role->permissions) || in_array('MANASIGFAM', $user->role->permissions);
    }

    public function getVisibleIndices(User $user): array
    {
        // Obtén todos los índices económicos
        $indices = FinancialIndicator::getEconomicIndices();

        // Filtra los índices que el usuario tiene permiso para ver
        return array_filter($indices, function ($index) use ($user) {
            // Verifica si el usuario tiene el permiso correspondiente
            return in_array($index['permission'], $user->role->permissions);
        });
    }

}
