@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Lista de {{ __('Licencias Medicas') }}
                </span>
                @can('create', App\Models\License::class)
                    <a class="d-inline ml-5 text-decoration-none" href="{{ route('licenses.create') }}">
                        <button class="btn btn-primary rounded-3 px-3 py-1">
                            Crear
                        </button>
                    </a>
                @endcan
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)" class="sort-table">{{ __('Trabajador') }}</th>
                                <th onclick="sortTable(1)" class="sort-table">{{ __('Fecha de Emisión') }}</th>
                                <th onclick="sortTable(2)" class="sort-table">{{ __('Motivo') }}</th>
                                <th onclick="sortTable(3)" class="sort-table">{{ __('Días') }}</th>
                                <th onclick="sortTable(4)" class="sort-table">{{ __('Última Actualización') }}</th>
                                <!-- Nueva columna -->
                                <th class="sort-table">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($licenses as $license)
                                <tr>
                                    <td>{{ $license->worker->name }} {{ $license->worker->last_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($license->issue_date)->format('d-m-Y') }}</td>
                                    <!-- Fecha formateada -->
                                    <td>{{ $license->reason }}</td>
                                    <td>{{ $license->days }}</td>
                                    <td>{{ $license->updated_at->diffForHumans() }}</td>
                                    <!-- Tiempo desde la última actualización -->
                                    <td>
                                        @can('view', $license)
                                            <a class="text-decoration-none" href="{{ route('licenses.show', $license) }}">
                                                <button class="btn btn-success rounded-3 px-3">
                                                    <i class='bx bx-show'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('update', $license)
                                            <a class="text-decoration-none" href="{{ route('licenses.edit', $license) }}">
                                                <button class="btn btn-primary rounded-3 px-3">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('delete', $license)
                                            <form method="POST" action="{{ route('licenses.destroy', $license) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger rounded-3 px-3">
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
                @if ($licenses->hasPages())
                    <div class="card-footer pb-0">
                        {{ $licenses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@include('commons.sort-table')
