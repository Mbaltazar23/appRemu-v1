@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Mantenedor de {{ __('Seguros') }}
                    <small class="text-muted">({{ App\Models\Insurance::getInsuranceTypes()[$type] }})</small>
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
                        <label for="insurance_id" class="form-label">Selecciona un seguro
                            ({{ App\Models\Insurance::getInsuranceTypes()[$type] }}):</label>
                    </div>

                    <!-- Contenedor de Select y Botones, con más separación entre ellos -->
                    <div class="d-flex justify-content-start align-items-center" style="gap: 40px;">
                        <!-- Formulario con Select para seleccionar seguro -->
                        <form action="{{ route('insurances.index') }}" method="GET" class="d-flex align-items-center">
                            <div class="form-group mb-0" style="min-width: 350px;">
                                <!-- Select con espacio adecuado y alineación -->
                                <input type="hidden" name="type" value="{{ $type }}" />
                                <select name="insurance_id" id="insurance_id" class="form-control"
                                    onchange="this.form.submit()">
                                    @if ($insurances->isEmpty())
                                        <option value="0">No hay seguros disponibles</option>
                                    @else
                                        @foreach ($insurances as $insurance)
                                            <option value="{{ $insurance->id }}"
                                                {{ request('insurance_id') == $insurance->id || (!request('insurance_id') && $loop->first) ? 'selected' : '' }}>
                                                {{ $insurance->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </form>
                        <br>
                        <!-- Contenedor de los botones CRUD -->
                        @php
                            // Si no hay seguros, evitar intentar cargar el primer seguro
                            $selectedInsuranceId = request('insurance_id') ?: $insurances->first()->id ?? null;
                            $insurance = $selectedInsuranceId ? \App\Models\Insurance::find($selectedInsuranceId) : '';
                        @endphp

                        @if ($insurance)
                            <x-insurance-action-buttons :insurance="$insurance" :type="$type" />
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card para los trabajadores asociados -->
            <div class="card mt-4 p-4">
                @if ($workers->isNotEmpty())
                    <div class="card-header">
                        <h4 class="mb-2">Selecciona un trabajador</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('insurances.index') }}" method="GET">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="worker_id" class="form-label mt-2">Trabajadores asociados:</label>
                                    <!-- Select de trabajadores asociados, el formulario se enviará automáticamente al cambiar de opción -->
                                    <input type="hidden" name="type" value="{{ $type }}" />
                                    <select name="worker_id" id="worker_id" class="form-control"
                                        onchange="this.form.submit()">
                                        @foreach ($workers as $worker)
                                            <option value="{{ $worker->id }}"
                                                {{ (isset($worker_id) && $worker_id == $worker->id) || (!isset($worker_id) && $loop->first) ? 'selected' : '' }}>
                                                {{ $worker->name }} {{ $worker->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <strong class="form-label mt-2">Tipo de Trabajador :
                                        {{ \App\Models\Worker::getWorkerTypes()[$worker->worker_type] }}</strong>
                                </div>
                            </div>
                        </form>
                        @if ($worker && $insurance)
                            @include('insurances.partials.workerDetails', [
                                'worker' => $worker,
                                'insurance' => $insurance,
                                'type' => $type,
                            ])
                        @endif
                    </div>
                @else
                    <!-- Mensaje si no hay trabajadores -->
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

@include('commons.sort-table')
