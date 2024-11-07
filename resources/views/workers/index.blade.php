<!--views/workers/index.blade.php-->
@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Listado de Trabajadores
                </span>
                <div>
                    @can('create', App\Models\Worker::class)
                        <a class="d-inline ml-2 text-decoration-none" href="{{ route('workers.create') }}">
                            <button class="btn btn-primary rounded-3 px-3 py-1">
                                Crear
                            </button>
                        </a>
                        &nbsp;
                    @endcan
                    <a class="d-inline ml-2 text-decoration-none" href="{{ route('settlements.settlement') }}" target="_blank"
                        onclick="openPopup(event, 'Listar Trabajador Finiquitados')">
                        <button class="btn btn-secondary rounded-3 px-3 py-1">Listar Finiquitados</button>
                    </a>
                </div>
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="table-responsive">
                    <table class="table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)" class="sort-table">Nombre</th>
                                <th onclick="sortTable(1)" class="sort-table">Creado en</th>
                                <th onclick="sortTable(2)" class="sort-table">Docente</th>
                                <th onclick="sortTable(3)" class="sort-table">Actualizado</th>
                                <th onclick="sortTable(4)" class="sort-table">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workers as $worker)
                                <tr>
                                    <td>{{ $worker->name }} {{ $worker->last_name }}</td>
                                    <td>{{ $worker->created_at }}</td>
                                    <td>{{ $worker->getWorkerTypes()[$worker->worker_type] }}
                                    </td>
                                    <td>{{ $worker->updated_at->diffForHumans() }}</td>
                                    <td>
                                        @can('view', $worker)
                                            <a class="text-decoration-none" href="{{ route('workers.show', $worker) }}">
                                                <button class="btn btn-success rounded-3 px-3" title="Ver Trabajador">
                                                    <i class='bx bx-show'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('update', $worker)
                                            <a class="text-decoration-none" href="{{ route('workers.edit', $worker) }}">
                                                <button class="btn btn-primary rounded-3 px-3" title="Editar Trabajador">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('viewContract', $worker)
                                            @if ($worker->contract && $worker->contract->details)
                                                {{-- Botón para Ver Contrato --}}
                                                <a class="text-decoration-none" href="{{ route('contracts.print', $worker) }}"
                                                    target="_blank" onclick="openPopup(event, 'Ver Contrato')">
                                                    <button class="btn btn-info rounded-3 px-3" title="Imprimir Contrato">
                                                        <i class='bx bxs-printer'></i>
                                                    </button>
                                                </a>
                                                {{-- Botón para Anexos (habilitado cuando hay detalles de contrato) --}}
                                                <a class="text-decoration-none"
                                                    href="{{ route('contracts.showAnnexes', $worker) }}" target="_blank"
                                                    onclick="openPopup(event, 'Ver Anexos del Contrato')">
                                                    <button class="btn btn-secondary rounded-3 px-3"
                                                        title="Ver Anexos de Contrato">
                                                        <i class='bx bx-link'></i>
                                                    </button>
                                                </a>
                                            @else
                                                {{-- Botón para Crear el Contrato --}}
                                                <a class="text-decoration-none"
                                                    href="{{ route('contracts.create', $worker) }}">
                                                    <button class="btn btn-warning rounded-3 px-3" title="Crear Contrato">
                                                        <i class='bx bx-book-content'></i>
                                                    </button>
                                                </a>
                                                {{-- Si no tiene detalles de contrato, muestra el botón deshabilitado --}}
                                                <button class="btn btn-secondary rounded-3 px-3" title="Ver Anexos de Contrato"
                                                    disabled>
                                                    <i class='bx bx-link'></i>
                                                </button>
                                            @endif
                                        @endcan

                                        @can('settlement', $worker)
                                            <a href="{{ route('workers.settle', $worker) }}" class="text-decoration-none">
                                                <button class="btn btn-dark rounded-3 px-3" title="Asignar Fecha Finiquito">
                                                    <i class='bx bx-calendar-check'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('delete', $worker)
                                            <form method="POST" action="{{ route('workers.destroy', $worker) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger rounded-3 px-3"
                                                    title="Eliminar Contrato">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($workers->hasPages())
                    <div class="card-footer pb-0">
                        {{ $workers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@include('commons.sort-table')
