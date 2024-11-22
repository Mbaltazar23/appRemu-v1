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
        return $user->isContador() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the specific liquidation record.
     */
    public function view(User $user, Liquidation $liquidation): bool
    {
        // El usuario puede ver la liquidación si es un contador o si es el trabajador de esa liquidación
        return $user->isContador();
    }

    /**
     * Determine whether the user can create a liquidation.
     */
    public function create(User $user): bool
    {
        // El usuario debe ser un contador o un administrador para crear una liquidación
        return $user->isContador() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the liquidation record.
     */
    public function update(User $user, Liquidation $liquidation): bool
    {
        // El usuario puede actualizar la liquidación si es un contador o si es el trabajador asociado a la liquidación
        return $user->isContador();
    }

    /**
     * Determine whether the user can delete the liquidation record.
     */
    public function delete(User $user, Liquidation $liquidation): bool
    {
        // El usuario debe ser un contador o un administrador para eliminar la liquidación
        return $user->isContador() || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the liquidation record.
     */
    public function restore(User $user, Liquidation $liquidation): bool
    {
        // El usuario debe ser un contador para restaurar la liquidación
        return $user->isContador();
    }

    /**
     * Determine whether the user can permanently delete the liquidation record.
     */
    public function forceDelete(User $user, Liquidation $liquidation): bool
    {
        // El usuario debe ser un contador o un administrador para eliminar permanentemente la liquidación
        return $user->isContador() ;
    }
}
