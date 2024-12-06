<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
     /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return $user->isContador() || in_array('CONPREVISAPRE', $user->permissions) || in_array('CONPREVAFP', $user->permissions);
    }

    public function view(User $user, Report $report): bool
    {
        // Verifica si el usuario tiene permiso
        return $user->isContador() || in_array('CONPREVISAPRE', $user->permissions) || in_array('CONPREVAFP', $user->permissions);;
    }
}
