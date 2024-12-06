<?php

namespace App\Policies;

use App\Models\Liquidation;
use App\Models\User;

class LiquidationPolicy
{
   /**
     * Determine whether the user can view any liquidation records.
     */
    public function viewAny(User $user): bool
    {
        // El usuario debe ser un contador o un administrador para ver cualquier liquidación
        return $user->isContador() || $user->isAdmin() &&  in_array('REMEMI', $user->permissions);
    }

    /**
     * Determine whether the user can view the specific liquidation record.
     */
    public function view(User $user, Liquidation $liquidation): bool
    {
        // El usuario puede ver la liquidación si es un contador o si es el trabajador de esa liquidación
        return $user->isContador() && in_array('REMEMI', $user->permissions);
    }

    /**
     * Determine whether the user can create a liquidation.
     */
    public function create(User $user): bool
    {
        // El usuario debe ser un contador o un administrador para crear una liquidación
        return $user->isContador() || $user->isAdmin() &&  in_array('REMEMI', $user->permissions);
    }

    /**
     * Determine whether the user can delete the liquidation record.
     */
    public function delete(User $user, Liquidation $liquidation): bool
    {
        // El usuario debe ser un contador o un administrador para eliminar la liquidación
        return $user->isContador() || $user->isAdmin() &&  in_array('REMEMI', $user->permissions);
    }
}
