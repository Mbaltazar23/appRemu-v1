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
            <div class="card p-3">
                <div class="table-responsive">
                    <p>
                        <strong>Nombre:</strong> {{ $user->name }} <br />
                        <strong>Email:</strong> {{ $user->email }} <br />
                        <strong>Rol:</strong> {{ $user->roleName }} <br />

                        <strong>Colegios Asociados:</strong>
                        @if ($schools->isEmpty())
                            <span>No hay colegios asociados.</span>
                        @else
                            <ul>
                                @foreach ($schools as $school)
                                    <li>{{ $school->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </p>
                </div>
                <span>
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('users.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
                    </a>
                    @can('update', $user)
                        <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('users.edit', $user) }}">
                            <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                        </a>
                    @endcan
                </span>
            </div>
        </div>
    </div>
@endsection
