@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card" id="selectionCard">
                <div class="card-header d-flex align-items-center text-center">
                    <!-- Título centrado con mes y año en español -->
                    <h3 class="mx-auto">
                        Valores calculados - {{ \App\Helpers\MonthHelper::integerToMonth(now()->month) }}
                        {{ now()->year }}
                    </h3>

                    <!-- Botón Volver al costado derecho -->
                    <a href="{{ route('liquidations.workerLiquidation', [$worker->id]) }}" class="btn btn-secondary mt-2">
                        Volver
                    </a>
                </div>

                <div class="card-body">
                    <!-- Información del trabajador centrada en el body -->
                    <div class="d-flex justify-content-center align-items-center w-100 mt-3 mb-4">
                        <!-- Contenedor con más espacio entre los elementos, usando flexbox para que se distribuyan en fila -->
                        <div class="mx-4">
                            <p class="mb-1"><strong>{{ $worker->name }}</strong></p>
                        </div>
                        <div class="mx-4">
                            <p class="mb-1"><strong>Rut:</strong> {{ $worker->rut }}</p>
                        </div>
                        <div class="mx-4">
                            <p class="mb-1"><strong>Tipo Trabajador:</strong> {{ $worker->getDescriptionWorkerTypes() }}
                            </p>
                        </div>
                    </div>
                    <!-- Texto explicativo sobre el formulario -->
                    <p>
                        Los siguientes parámetros solo deben ser modificados en casos de <br>
                        extrema necesidad. El último parámetro (días trabajados) debe ser llenado con la cantidad efectiva
                        de días trabajados y solo se ingresa para efectos de despliegue en la liquidación.
                    </p>
                    <br>
                    <!-- Formulario junto al botón Volver -->
                    <div class="d-flex align-items-center justify-content-between">
                        <!-- Formulario que contiene los datos de la sesión -->
                        <form action="{{ route('liquidations.store', $worker) }}" method="POST" class="w-100">
                            @csrf
                            <!-- Inputs editables con valores de sesión -->
                            <div class="row">
                                <input type="hidden" name="school_id" value="{{ auth()->user()->school_id_session }}" />
                                <input type="hidden" name="month" value="{{ now()->month }}">
                                <input type="hidden" name="year" value="{{ now()->year }}">
                                @foreach ($tmp as $liquidationRecord)
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="input_{{ $liquidationRecord->id }}" class="form-label">
                                                {{ $liquidationRecord->title }}
                                            </label>
                                            <input type="text" class="form-control"
                                                name="VALID{{ $liquidationRecord->id }}"
                                                value="{{ number_format($liquidationRecord->value, 0, '.', ',') }}"
                                                @if ($liquidationRecord->title === 'Días Trabajados' && $liquidationRecord->value == 0) required @endif>
                                            <input type="hidden" name="TITID{{ $liquidationRecord->id }}"
                                                value="{{ $liquidationRecord->title }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Botón Guardar alineado a la derecha -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
