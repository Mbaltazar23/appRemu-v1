@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                {{ __('Sostenedor') }}
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
                                <th class="w-25">RUT:</th>
                                <td>{{ $sustainer->rut }}</td>
                                <th class="w-25">Razón Social:</th>
                                <td>{{ $sustainer->business_name }}</td>
                            </tr>
                            <tr>
                                <th class="w-25">Dirección:</th>
                                <td>{{ $sustainer->address }}</td>
                                <th class="w-25">Comuna:</th>
                                <td>{{ $sustainer->commune }}</td>
                            </tr>
                            <tr>
                                <th class="w-25">Región:</th>
                                <td>{{ $sustainer->region }}</td>
                                <th class="w-25">Representante Legal:</th>
                                <td>{{ $sustainer->legal_representative }}</td>
                            </tr>
                            <tr>
                                <th class="w-25">RUT del Representante:</th>
                                <td>{{ $sustainer->rut_legal_representative }}</td>
                                <th class="w-25">Email:</th>
                                <td>{{ $sustainer->email }}</td>
                            </tr>
                            <tr>
                                <th class="w-25">Teléfono:</th>
                                <td>{{ $sustainer->phone }}</td>
                                <td></td> <!-- Celda vacía para mantener la estructura -->
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('sustainers.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
                    </a>
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('sustainers.edit', $sustainer) }}">
                        <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
