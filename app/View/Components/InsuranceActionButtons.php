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
     * Returns a list of buttons with their respective routes and permissions.
     */
    public function getActions()
    {
        return [
            [
                'name' => 'Ver detalles',
                'route' => route('insurances.show', [$this->insurance, 'type' => $this->type]),
                'icon' => 'bx bx-show',
                'permission' => 'view',  // Permission to view
                'type' => 'info'
            ],
            [
                'name' => 'Editar',
                'route' => route('insurances.edit', [$this->insurance, 'type' => $this->type]),
                'icon' => 'bx bx-edit',
                'permission' => 'update',  // Permission to edit
                'type' => 'warning'
            ],
            [
                'name' => 'Eliminar',
                'route' => route('insurances.destroy', [$this->insurance, 'type' => $this->type]),
                'icon' => 'bx bx-trash',
                'permission' => 'delete',  // Permission to delete
                'type' => 'danger',
                'method' => 'DELETE' // Action method
            ],
            [
                'name' => 'Agregar Trabajadores',
                'route' => route('insurances.link_worker', [$this->insurance, 'type' => $this->type]),
                'icon' => 'bx bxs-user-plus',
                'permission' => 'linkWorker',  // Custom permission to link workers
                'type' => 'dark',
            ]
        ];
    }

    public function render()
    {
        return view('components.insurance-action-buttons');
    }
}
