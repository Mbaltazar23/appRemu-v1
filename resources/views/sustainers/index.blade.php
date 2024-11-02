@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Lista de {{ __('Sostenedores') }}
                </span>
                @can('create', App\Models\Sustainer::class)
                    <a class="d-inline ml-5 text-decoration-none" href="{{ route('sustainers.create') }}">
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
                                <th onclick="sortTable(1)" class="sort-table">{{ __('Razón Social') }}</th>
                                <th onclick="sortTable(2)" class="sort-table">{{ __('Email') }}</th>
                                <th onclick="sortTable(3)" class="sort-table">{{ __('Teléfono') }}</th>
                                <th onclick="sortTable(4)" class="sort-table">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sustainers as $sustainer)
                                <tr>
                                    <td>{{ $sustainer->rut }}</td>
                                    <td>{{ $sustainer->business_name }}</td>
                                    <td>{{ $sustainer->email }}</td>
                                    <td>{{ $sustainer->phone }}</td>
                                    <td>
                                        @can('view', $sustainer)
                                            <a class="text-decoration-none" href="{{ route('sustainers.show', $sustainer) }}">
                                                <button class="btn btn-success rounded-3 px-3">
                                                    <i class='bx bx-show'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('update', $sustainer)
                                            <a class="text-decoration-none" href="{{ route('sustainers.edit', $sustainer) }}">
                                                <button class="btn btn-primary rounded-3 px-3">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('delete', $sustainer)
                                            <form method="POST" action={{ route('sustainers.destroy', $sustainer) }}
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
                @if ($sustainers->hasPages())
                    <div class="card-footer pb-0">
                        {{ $sustainers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@include('commons.sort-table')
