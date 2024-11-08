<?php

namespace App\Policies;

use App\Models\Template;
use App\Models\User;

class TemplatePolicy
{
     /**
     * Determine whether the user can view any absence records.
     */
    public function viewAny(User $user): bool
    {
        // Lógica de autorización: el usuario debe ser contador para ver ausencias.
        return $user->isContador();
    }

    /**
     * Determine whether the user can view the specific absence record.
     */
    public function view(User $user, Template $template): bool
    {
        // Lógica de autorización: el usuario puede ver la ausencia si es el mismo trabajador
        // o si es un contador.
        return $user->isContador();
    }
    /**
     * Determine whether the user can create an absence.
     */
    public function create(User $user): bool
    {
        // Lógica de autorización: el usuario debe ser contador para crear ausencias.
        return $user->isContador();
    }

    /**
     * Determine whether the user can update the absence record.
     */
    public function update(User $user, Template $template): bool
    {
        // Lógica de autorización: el usuario debe ser contador o ser el trabajador que está registrando la ausencia.
        return $user->isContador();
    }

    /**
     * Determine whether the user can delete the absence record.
     */
    public function delete(User $user, Template $template): bool
    {
        // Lógica de autorización: el usuario debe ser contador para eliminar una ausencia.
        return $user->isContador();
    }

    /**
     * Determine whether the user can restore the absence record.
     */
    public function restore(User $user, Template $template): bool
    {
        // Lógica de autorización: el usuario debe ser contador para restaurar ausencias.
        return $user->isContador();
    }

    /**
     * Determine whether the user can permanently delete the absence record.
     */
    public function forceDelete(User $user, Template $template): bool
    {
        // Lógica de autorización: el usuario debe ser contador para eliminar permanentemente una ausencia.
        return $user->isContador();
    }
}
