@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Mantenedor de {{ __('Seguros') }}
                    <small class="text-muted">({{ $insuranceName }})</small>
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
            <!-- Card de seguros -->
            <div class="card p-3">
                <div class="card-body">
                    <!-- Contenedor para el label del select -->
                    <div class="form-group mb-4">
                        <label for="insurance_id" class="form-label">Selecciona un seguro ({{ $insuranceName }}):</label>
                    </div>

                    <!-- Tabla para alinear select y botones a la misma altura -->
                    <table class="table-borderless" style="width: 100%; padding-top: 10px;">
                        <tr>
                            <!-- Celda del select -->
                            <td style="width: 40%; vertical-align: middle; padding-right: 20px;">
                                <!-- Espacio a la derecha -->
                                <div class="form-group mb-0" style="min-width: 350px;">
                                    <select class="form-control" onchange="window.location.href=this.value;">
                                        @foreach ($insurances as $insuranceOption)
                                            <option
                                                value="{{ route('insurances.index', ['insurance_id' => $insuranceOption->id, 'type' => $type]) }}"
                                                {{ $insurance && $insurance->id == $insuranceOption->id ? 'selected' : '' }}>
                                                {{ $insuranceOption->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <!-- Celda de los botones -->
                            <td style="vertical-align: middle;">
                                &nbsp;&nbsp;&nbsp;
                                @if ($insurance)
                                    <x-insurance-action-buttons :insurance="$insurance" :type="$type" />
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- Card para los trabajadores asociados -->
            <div class="card mt-4 p-4">
                @if ($workers->isNotEmpty())
                    <div class="card-header">
                        <h4 class="mb-2">&nbsp;&nbsp;Selecciona un trabajador perteneciente al Seguro
                            <strong>{{ $insurance->name }}</strong>
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Incluir el partial que contiene el select worker dentro de la tabla -->
                        @include('insurances.partials.workerDetails', [
                            'workers' => $workers,
                            'insurance' => $insurance,
                            'type' => $type,
                        ])
                    </div>
                @else
                    <div class="card-header">
                        <div class="alert alert-danger mb-2">
                            No hay trabajadores asociados a este seguro.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
