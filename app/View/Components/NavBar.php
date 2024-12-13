<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class NavBar extends Component
{
    public $permissions;

    public function __construct()
    {
        $this->permissions = Auth::user() ? Auth::user()->role->permissions : []; // Permisos del usuario
    }

    /**
     * Verifica si el usuario tiene un permiso específico.
     */
    public function hasPermission(string $permission)
    {
        return in_array($permission, $this->permissions);
    }

    public function render()
    {
        return view('components.nav-bar');
    }
}
