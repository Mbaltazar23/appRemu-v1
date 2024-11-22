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
                            <!-- Dentro del foreach de los trabajadores -->
                            @foreach ($workers as $worker)
                                <tr>
                                    <td>{{ $worker->name }} {{ $worker->last_name }}</td>
                                    <td>{{ $worker->created_at }}</td>
                                    <td>{{ $worker->getWorkerTypes()[$worker->worker_type] }}</td>
                                    <td>{{ $worker->updated_at->diffForHumans() }}</td>
                                    <!-- Llamada al componente para las acciones -->
                                    <x-worker-action-buttons :worker="$worker" />
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
