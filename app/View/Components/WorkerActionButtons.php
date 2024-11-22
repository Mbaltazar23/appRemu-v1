<?php
namespace App\View\Components;

use Illuminate\View\Component;

class WorkerActionButtons extends Component
{
    public $worker;

    /**
     * Crear una nueva instancia del componente.
     *
     * @param  \App\Models\Worker  $worker
     * @return void
     */
    public function __construct($worker)
    {
        $this->worker = $worker;
    }

    /**
     * Obtener las acciones disponibles para el trabajador.
     *
     * @return array
     */
    public function actions()
    {
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
                'condition' => $this->worker->contract && $this->worker->contract->details
            ],
            'createContract' => [
                'permission' => 'viewContract',
                'route' => route('contracts.create', $this->worker),
                'icon' => 'bx bx-book-content',
                'title' => 'Crear Contrato',
                'class' => 'warning',
                'condition' => !$this->worker->contract || !$this->worker->contract->details
            ],
            'viewAnnexes' => [
                'permission' => 'viewContract',
                'route' => route('contracts.showAnnexes', $this->worker),
                'icon' => 'bx bx-link',
                'title' => 'Ver Anexos de Contrato',
                'class' => 'secondary',
                'condition' => true // Se muestra siempre, se deshabilita si no hay detalles
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

    /**
     * Renderiza la vista del componente.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('components.worker-action-buttons', [
            'actions' => $this->actions(),
        ]);
    }
}
