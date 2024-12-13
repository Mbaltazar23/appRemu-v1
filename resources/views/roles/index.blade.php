@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Lista de {{ __('Perfiles') }}
                </span>
                @can('create', App\Models\Role::class)
                    <a class="d-inline ml-5 text-decoration-none" href="{{ route('roles.create') }}">
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
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0)" class="sort-table">Nombre</th>
                            <th onclick="sortTable(1)" class="sort-table">{{ __('Updated') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->updated_at->diffForHumans() }}</td>
                                <td>
                                    @can('view', $role)
                                        <a class="text-decoration-none" href="{{ route('roles.show', $role) }}">
                                            <button class="btn btn-success rounded-3 px-3">
                                                <i class='bx bx-show'></i>
                                            </button>
                                        </a>
                                    @endcan
                                    @can('update', $role)
                                        <a class="text-decoration-none" href="{{ route('roles.edit', $role) }}">
                                            <button class="btn btn-primary rounded-3 px-3">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                        </a>
                                    @endcan
                                    @can('delete', $role)
                                        <form method="POST" action={{ route('roles.destroy', $role) }} class="d-inline"
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
            @if ($roles->hasPages())
                <div class="card-footer pb-0">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
@include('commons.sort-table')
