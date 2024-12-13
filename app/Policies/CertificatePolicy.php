<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return in_array('CERTREMU', $user->role->permissions);
    }

    public function view(User $user, Certificate $certificate): bool
    {
        // Verifica si el usuario tiene permiso
        return in_array('CERTREMU', $user->role->permissions);
    }

    public function create(User $user): bool
    {
        return in_array('CERTREMU', $user->role->permissions);
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        return in_array('CERTREMU', $user->role->permissions);
    }
}
