<?php

namespace App\Policies;

use App\Models\User;

class CostCenterPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array('CCOST', $user->role->permissions);
    }

}
