<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;

class PayrollPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return in_array('PLANREMU', $user->role->permissions);
    }

    public function view(User $user, Payroll $payroll): bool
    {
        // Verifica si el usuario tiene permiso
        return in_array('PLANREMU', $user->role->permissions) ;
    }

    public function create(User $user): bool
    {
        return in_array('PLANREMU', $user->role->permissions);
    }
}
