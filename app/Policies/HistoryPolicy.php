<?php

namespace App\Policies;

use App\Models\History;
use App\Models\User;

class HistoryPolicy
{ 
    public function viewAny(User $user)
    {
        // Permitir a admins y superadmins ver los historys
        return  in_array('VERHISTORIAL', $user->role->permissions);
    }

    public function view(User $user, History $history)
    {
        // Puedes personalizar más la política si quieres permitir ver solo ciertos registros
        return  in_array('VERHISTORIAL', $user->role->permissions);
    }
}
