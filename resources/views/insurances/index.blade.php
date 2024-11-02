@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Lista de {{ __('Seguros') }}
                    @if ($type == App\Models\Insurance::AFP)
                        <small class="text-muted">({{ App\Models\Insurance::getInsuranceTypes()[$type] }})</small>
                    @else
                        <small class="text-muted">({{ App\Models\Insurance::getInsuranceTypes()[$type] }})</small>
                    @endif
                </span>
                @can('create', App\Models\Insurance::class)
                    <a class="d-inline ml-5 text-decoration-none" href="{{ route('insurances.create', ['type' => $type]) }}">
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
                                <th onclick="sortTable(0)" class="sort-table">{{ __('RUT') }}</th>
                                <th onclick="sortTable(1)" class="sort-table">{{ __('Nombre') }}</th>
                                <th onclick="sortTable(2)" class="sort-table">{{ __('Cotización') }}</th>
                                <th onclick="sortTable(3)" class="sort-table">Actualizado</th>
                                <th>{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($insurances as $insurance)
                                <tr>
                                    <td>{{ $insurance->rut }}</td>
                                    <td>{{ $insurance->name }}</td>
                                    <td>{{ $insurance->cotizacion }}</td>
                                    <td>{{ $insurance->updated_at->diffForHumans() }}</td>

                                    <td>
                                        @can('view', $insurance)
                                            <a class="text-decoration-none"
                                                href="{{ route('insurances.show', [$insurance, 'type' => $type]) }}">
                                                <button class="btn btn-success rounded-3 px-3" title="Ver Seguro">
                                                    <i class='bx bx-show'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('update', $insurance)
                                            <a class="text-decoration-none"
                                                href="{{ route('insurances.edit', [$insurance, 'type' => $type]) }}">
                                                <button class="btn btn-primary rounded-3 px-3" title="Editar Seguro">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('linkWorker', $insurance)
                                            <!-- Usando el tipo -->
                                            <a class="text-decoration-none"
                                                href="{{ route('insurances.link_worker', [$insurance, 'type' => $type]) }}">
                                                <button class="btn btn-dark rounded-3 px-3" title="Añadir Trabajadores">
                                                    <i class='bx bx-file'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('delete', $insurance)
                                            <form method="POST" action="{{ route('insurances.destroy', $insurance) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger rounded-3 px-3"
                                                    title="Eliminar Seguro">
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
                @if ($insurances->hasPages())
                    <div class="card-footer pb-0">
                        {{ $insurances->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@include('commons.sort-table')
