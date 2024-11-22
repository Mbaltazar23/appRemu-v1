@extends('layouts.app')

<!--views/bonuses/partials/list.blade.php-->
@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Lista de {{ __('Bonos') }}
                </span>
                <div>
                    @can('create', App\Models\Bonus::class)
                        <a class="d-inline ml-2 text-decoration-none" href="{{ route('bonuses.create') }}">
                            <button class="btn btn-primary rounded-3 px-3 py-1">
                                Crear
                            </button>
                        </a>
                        &nbsp;
                        <a class="d-inline ml-2 text-decoration-none" href="{{ route('bonuses.index') }}">
                            <button class="btn btn-secondary rounded-3 px-3 py-1">
                                Regresar al inicio
                            </button>
                        </a>
                    @endcan
                </div>
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
                                <th onclick="sortTable(0)" class="sort-table">{{ __('Nombre') }}</th>
                                <th onclick="sortTable(1)" class="sort-table">{{ __('Aplicado para') }}</th>
                                <th onclick="sortTable(3)" class="sort-table">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bonuses as $bonus)
                                <tr>
                                    <td>{{ $bonus->school->tuitions->where('tuition_id', $bonus->title)->first()->title ?? '' }}
                                    </td>
                                    <td>{{ $bonus->getTypeLabel($bonus->type) }}</td>
                                    <td>
                                        @can('view', $bonus)
                                            <a class="text-decoration-none" href="{{ route('bonuses.show', $bonus) }}">
                                                <button class="btn btn-success rounded-3 px-3">
                                                    <i class='bx bx-show'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('update', $bonus)
                                            <a class="text-decoration-none" href="{{ route('bonuses.edit', $bonus) }}">
                                                <button class="btn btn-primary rounded-3 px-3">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('workers', $bonus)
                                            <a class="text-decoration-none" href="{{ route('bonuses.workers', $bonus) }}"
                                                target="_blank" onclick="openPopup(event, 'Agregar Trabajador')">
                                                <button class="btn btn-info rounded-3 px-3" title="Añadir Trabajadores">
                                                    <i class='bx bx-user'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('delete', $bonus)
                                            <form method="POST" action="{{ route('bonuses.destroy', $bonus) }}"
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
                @if ($bonuses->hasPages())
                    <div class="card-footer pb-0">
                        {{ $bonuses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@include('commons.sort-table')
