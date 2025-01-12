@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                {{ __('School') }}
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-4">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <th class="w-25">Nombre:</th>
                                <td>{{ $school->name }}</td>
                                <th class="w-25">RUT:</th>
                                <td>{{ $school->rut }}</td>
                            </tr>
                            <tr>
                                <th class="w-25">Dirección:</th>
                                <td>{{ $school->address }}</td>
                                <th class="w-25">Sostenedor:</th>
                                <td>
                                    RUT: {{ $school->sustainer->rut }} - Nombre Comercial:
                                    {{ $school->sustainer->business_name }}
                                </td>
                            </tr>
                            <tr>
                                <th class="w-25">RBD:</th>
                                <td>{{ $school->rbd }}</td>
                                <th class="w-25">Comuna:</th>
                                <td>{{ $school->commune }}</td>
                            </tr>
                            <tr>
                                <th class="w-25">Región:</th>
                                <td>{{ $school->region }}</td>
                                <th class="w-25">Director:</th>
                                <td>{{ $school->director }}</td>
                            </tr>
                            <tr>
                                <th class="w-25">RUT del Director:</th>
                                <td>{{ $school->rut_director }}</td>
                                <th class="w-25">Teléfono:</th>
                                <td>{{ $school->phone }}</td>
                            </tr>
                            <tr>
                                <th class="w-25">Correo Electrónico:</th>
                                <td>{{ $school->email }}</td>
                                <th class="w-25">Dependencia:</th>
                                <td>{{ $school->dependency_text }}</td>
                            </tr>
                            <tr>
                                <th class="w-25">Subvención:</th>
                                <td>{{ $school->grantt_text }}</td>
                                <td></td> <!-- Celda vacía para mantener la estructura -->
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <a class="mr-2 rounded-2 text-decoration-none" href="{{ route('schools.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
                    </a>
                    @can('update', $school)
                        <a class="mr-2 rounded-2 text-decoration-none" href="{{ route('schools.edit', $school) }}">
                            <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
