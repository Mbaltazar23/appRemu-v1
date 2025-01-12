@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                Trabajadores Finiquitados
                <div>
                    <a class="d-inline ml-2 text-decoration-none" href="{{ route('workers.index') }}">
                        <button class="btn btn-secondary rounded-3 px-3 py-1">Volver al Inicio</button>
                    </a>
                </div>
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-4">
                <div class="table-responsive">
                    <!-- Verificar si no hay trabajadores -->
                    @if ($workers->isEmpty())
                    <div class="page-wrapper">
                        <div class="container-xl mt-2">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                No hay trabajadores finiquitados en este momento
                            </div>
                        </div>
                    </div>
                    @else
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th onclick="sortTable(0)" class="sort-table">Nombre</th>
                                    <th onclick="sortTable(1)" class="sort-table">Apellido</th>
                                    <th onclick="sortTable(2)" class="sort-table">Tipo de Trabajador</th>
                                    <th onclick="sortTable(3)" class="sort-table">Fecha de Finiquito</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workers as $worker)
                                    <tr>
                                        <td>{{ $worker->name }}</td>
                                        <td>{{ $worker->last_name }}</td>
                                        <td>{{ $worker->getWorkerTypes()[$worker->worker_type] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($worker->settlement_date)->format('d-m-Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Paginación con Bootstrap -->
                        <div class="d-flex justify-content-center">
                            {{ $workers->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


@include('commons.sort-table')
