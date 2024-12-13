@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                {{ __('Perfiles') }}
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-3">
                <div class="table-responsive">
                    <p><strong>Nombre:</strong> {{ $role->name }}</p>
                    <p><strong>Permisos:</strong></p>
                    <ul>
                        @foreach ($role->permissions as $permission)
                            <li>{{ config('permissions.' . $permission) ?? $permission }}</li>
                        @endforeach
                    </ul>
                </div>
                <span>
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('roles.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
                    </a>
                    @can('update', $role)
                        <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('roles.edit', $role) }}">
                            <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                        </a>
                    @endcan
                </span>
            </div>
        </div>
    </div>
@endsection
