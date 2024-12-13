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
        return in_array('MANLIQ', $user->role->permissions);
    }

    /**
     * Determine whether the user can view the specific absence record.
     */
    public function view(User $user, Template $template): bool
    {
        return in_array('MANLIQ', $user->role->permissions);
    }
    /**
     * Determine whether the user can create an absence.
     */
    public function create(User $user): bool
    {
        // Lógica de autorización: el usuario debe ser contador para crear ausencias.
        return in_array('MANLIQ', $user->role->permissions);
    }

    /**
     * Determine whether the user can update the absence record.
     */
    public function update(User $user, Template $template): bool
    {
        return in_array('MANLIQ', $user->role->permissions);
    }

    /**
     * Determine whether the user can delete the absence record.
     */
    public function delete(User $user, Template $template): bool
    {
        // Lógica de autorización: el usuario debe ser contador para eliminar una ausencia.
        return in_array('MANLIQ', $user->role->permissions);
    }

    /**
     * Determine whether the user can restore the absence record.
     */
    public function restore(User $user, Template $template): bool
    {
        // Lógica de autorización: el usuario debe ser contador para restaurar ausencias.
        return in_array('MANLIQ', $user->role->permissions);
    }

    /**
     * Determine whether the user can permanently delete the absence record.
     */
    public function forceDelete(User $user, Template $template): bool
    {
        // Lógica de autorización: el usuario debe ser contador para eliminar permanentemente una ausencia.
        return in_array('MANLIQ', $user->role->permissions);
    }
}
