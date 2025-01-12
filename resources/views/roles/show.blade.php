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
                    <table class="table mb-0">
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $role->name }}</td>
                        </tr>
                        <tr>
                            <th>Permisos:</th>
                            <td>
                                @php
                                    // Convertir $role->permissions a colección si es un array
                                    $permissions = collect($role->permissions);
                                    $permissionCount = $permissions->count();
                                @endphp

                                @if ($permissionCount > 8)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="mb-0">
                                                @foreach ($permissions->take($permissionCount / 2) as $permission)
                                                    <li>{{ config('permissions.' . $permission) ?? $permission }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="mb-0">
                                                @foreach ($permissions->skip($permissionCount / 2) as $permission)
                                                    <li>{{ config('permissions.' . $permission) ?? $permission }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @else
                                    <ul class="mb-0">
                                        @foreach ($permissions as $permission)
                                            <li>{{ config('permissions.' . $permission) ?? $permission }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="mt-4">
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('roles.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
                    </a>
                    @can('update', $role)
                        <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('roles.edit', $role) }}">
                            <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
