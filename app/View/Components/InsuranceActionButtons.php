<?php
namespace App\View\Components;

use Illuminate\View\Component;

class InsuranceActionButtons extends Component
{
    public $actions;
    public $insurance;
    public $type;

    public function __construct($insurance, $type)
    {
        $this->insurance = $insurance;
        $this->type = $type;
        $this->actions = $this->getActions();
    }

    /**
     * Retorna una lista de botones con sus respectivas rutas y permisos.
     */
    public function getActions()
    {
        return [
            [
                'name' => 'Ver detalles',
                'route' => route('insurances.show', [$this->insurance, 'type' => $this->type]),
                'icon' => 'bx bx-show',
                'permission' => 'view',  // Permiso para ver
                'type' => 'info'
            ],
            [
                'name' => 'Editar',
                'route' => route('insurances.edit', [$this->insurance, 'type' => $this->type]),
                'icon' => 'bx bx-edit',
                'permission' => 'update',  // Permiso para editar
                'type' => 'warning'
            ],
            [
                'name' => 'Eliminar',
                'route' => route('insurances.destroy', [$this->insurance, 'type' => $this->type]),
                'icon' => 'bx bx-trash',
                'permission' => 'delete',  // Permiso para eliminar
                'type' => 'danger',
                'method' => 'DELETE' // Método de la acción
            ],
            [
                'name' => 'Agregar Trabajadores',
                'route' => route('insurances.link_worker', [$this->insurance, 'type' => $this->type]),
                'icon' => 'bx bxs-user-plus',
                'permission' => 'linkWorker',  // Permiso personalizado para linkear trabajadores
                'type' => 'dark',
                'popup' => true,  // Indicamos que este botón debe abrir un popup
            ]
        ];
    }

    public function render()
    {
        return view('components.insurance-action-buttons');
    }
}
