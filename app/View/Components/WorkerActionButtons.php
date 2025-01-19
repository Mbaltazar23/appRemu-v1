<?php

namespace App\View\Components;

use Illuminate\View\Component;

class WorkerActionButtons extends Component
{
    public $worker;

    public function __construct($worker)
    {
        $this->worker = $worker;
    }

    public function actions()
    {
        $hasContract = $this->worker->contract && $this->worker->contract->details;

        return [
            'view' => [
                'permission' => 'view',
                'route' => route('workers.show', $this->worker),
                'icon' => 'bx bx-show',
                'title' => 'Ver Trabajador',
                'class' => 'success'
            ],
            'edit' => [
                'permission' => 'update',
                'route' => route('workers.edit', $this->worker),
                'icon' => 'bx bx-edit',
                'title' => 'Editar Trabajador',
                'class' => 'primary'
            ],
            'viewContract' => [
                'permission' => 'viewContract',
                'route' => route('contracts.print', $this->worker),
                'icon' => 'bx bxs-printer',
                'title' => 'Imprimir Contrato',
                'class' => 'info',
                'disabled' => !$hasContract,
            ],
            'createContract' => [
                'permission' => 'viewContract',
                'route' => route('contracts.create', $this->worker),
                'icon' => $hasContract ? 'bx bx-edit-alt' : 'bx bx-book-content', // Cambiar ícono
                'title' => $hasContract ? 'Actualizar Contrato' : 'Crear Contrato', // Cambiar título
                'class' => $hasContract ?  'warning': 'purple', // Cambiar color
            ],
            'viewAnnexes' => [
                'permission' => 'viewContract',
                'route' => route('contracts.showAnnexes', $this->worker),
                'icon' => 'bx bx-link',
                'title' => 'Ver Anexos de Contrato',
                'class' => 'secondary',
                'disabled' => !$hasContract,
            ],
            'settle' => [
                'permission' => 'settlement',
                'route' => route('workers.settle', $this->worker),
                'icon' => 'bx bx-calendar-check',
                'title' => 'Asignar Fecha Finiquito',
                'class' => 'dark'
            ],
            'delete' => [
                'permission' => 'delete',
                'route' => route('workers.destroy', $this->worker),
                'icon' => 'bx bx-trash',
                'title' => 'Eliminar Contrato',
                'class' => 'danger',
                'method' => 'DELETE'
            ],
        ];
    }

    public function render()
    {
        return view('components.worker-action-buttons', [
            'actions' => $this->actions(),
        ]);
    }
}
