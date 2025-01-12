@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                {{ __('User') }}
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-4">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tr>
                            <th class="w-25">Nombre:</th>
                            <td>{{ $user->name }}</td>
                            <th>Email:</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Rol:</th>
                            <td>{{ $user->role->name }}</td>
                        </tr>
                        <tr>
                            <th>Colegios Asociados:</th>
                            <td>
                                @if ($schools->isEmpty())
                                    <span>No hay colegios asociados.</span>
                                @else
                                    <ul>
                                        @foreach ($schools as $school)
                                            <li>{{ $school->name }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="mt-4">
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('users.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
                    </a>
                    @can('update', $user)
                        <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('users.edit', $user) }}">
                            <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
