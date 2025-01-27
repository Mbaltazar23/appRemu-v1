@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-4">
                <div class="card-header d-flex align-items-center text-center">
                    <!-- Título centrado con mes y año en español -->
                    <h3 class="mx-auto">
                        Valores calculados &nbsp;&nbsp; {{ \App\Helpers\MonthHelper::integerToMonth(now()->month) }}
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
                        Los siguientes parámetros solo deben ser modificados en casos de extrema necesidad. <br><br>
                        Los valores pueden ser modificados únicamente si se ajustan los indicadores
                        financieros, bonos o sueldos de los trabajadores. El último parámetro (días trabajados) debe ser
                        llenado con la cantidad efectiva de días trabajados y solo se ingresa para efectos de despliegue en
                        la liquidación, cargando por defecto 0 y los dias de ausencia con el dia de cierre de cada mes.
                    </p>
                    <br>
                    <!-- Formulario junto al botón Volver -->
                    <div class="d-flex align-items-center justify-content-between">
                        <!-- Formulario que contiene los datos de la sesión -->
                        <form action="{{ route('liquidations.store', $worker) }}" method="POST" class="w-100"
                            id="form-liquidation">
                            @csrf
                            <!-- Inputs editables con valores de sesión -->
                            <div class="row">
                                <input type="hidden" name="school_id" value="{{ auth()->user()->school_id_session }}" />
                                <input type="hidden" name="month" value="{{ now()->month }}">
                                <input type="hidden" name="year" value="{{ now()->year }}">
                                @foreach ($tmp as $liquidationRecord)
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="input_{{ $liquidationRecord->tuition_id }}" class="form-label">
                                                {{ $liquidationRecord->title }}
                                            </label>

                                            <!-- Condición para el campo 'Días Trabajados' -->
                                            @if ($liquidationRecord->tuition_id === 'DIASTRABAJADOS')
                                                <input type="number" class="form-control"
                                                    id="input_{{ $liquidationRecord->tuition_id }}"
                                                    name="VALID{{ $liquidationRecord->tuition_id }}"
                                                    value="{{ number_format($liquidationRecord->value, 0, '.', ',') }}"
                                                    required>
                                            @elseif($liquidationRecord->tuition_id === 'DIASNOTRABAJADOS')
                                                <input type="number" class="form-control"
                                                    id="input_{{ $liquidationRecord->tuition_id }}"
                                                    name="VALID{{ $liquidationRecord->tuition_id }}"
                                                    value="{{ number_format($liquidationRecord->value, 0, '.', ',') }}"
                                                    readonly>
                                            @else
                                                <input type="text" class="form-control"
                                                    name="VALID{{ $liquidationRecord->tuition_id }}"
                                                    value="{{ number_format($liquidationRecord->value, 0, '.', ',') }}"
                                                    readonly>
                                            @endif

                                            <input type="hidden" name="TITID{{ $liquidationRecord->tuition_id }}"
                                                value="{{ $liquidationRecord->title }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Botón Guardar alineado a la derecha -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary"
                                    @if (!in_array('DIASTRABAJADOS', array_column($tmp->toArray(), 'tuition_id'))) disabled @endif>
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

@push('custom_scripts')
    <!-- Agregar Script para Validación -->
    <script>
          document.addEventListener('DOMContentLoaded', function() {
        const diasTrabajadosInput = document.querySelector('input[name="VALIDDIASTRABAJADOS"]');
        const diasNoTrabajadosInput = document.querySelector('input[name="VALIDDIASNOTRABAJADOS"]');

        // Guardamos el valor original de DIASNOTRABAJADOS al cargar el formulario
        let diasNoTrabajadosOriginal = parseInt(diasNoTrabajadosInput.value.replace(/,/g, ''), 10) || 0;

        // Función para hacer la resta de días de forma segura
        function updateDiasNoTrabajados() {
            let diasTrabajados = parseInt(diasTrabajadosInput.value.replace(/,/g, ''), 10) || 0;

            // Comprobamos que el valor de DIASTRABAJADOS sea válido (mayor que 0)
            if (isNaN(diasTrabajados) || diasTrabajados < 0) {
                // Si el valor no es válido, restauramos el valor original de DIASNOTRABAJADOS
                diasNoTrabajadosInput.value = diasNoTrabajadosOriginal;
                return; // Salimos de la función para evitar realizar una resta incorrecta
            }

            // Resta el valor de DIASNOTRABAJADOS con los DIASTRABAJADOS
            let diasNoTrabajados = diasNoTrabajadosOriginal - diasTrabajados;

            // Actualizamos el valor de DIASNOTRABAJADOS
            diasNoTrabajadosInput.value = diasNoTrabajados;

            // Formateamos el valor con comas para mejor visualización
            diasNoTrabajadosInput.value = diasNoTrabajados.toLocaleString();
        }

        // Escuchamos cambios en el campo de días trabajados
        if (diasTrabajadosInput) {
            diasTrabajadosInput.addEventListener('input', updateDiasNoTrabajados);
        }
    });

        document.getElementById("form-liquidation").addEventListener("submit", function(event) {
            // Buscar el campo de Días Trabajados
            var diasTrabajadosField = document.querySelector('input[name="VALIDDIASTRABAJADOS"]');

            if (diasTrabajadosField) {
                var diasTrabajados = diasTrabajadosField.value;

                // Validar que el campo no esté vacío
                if (!diasTrabajados || diasTrabajados == 0 || diasTrabajados.trim() === "") {
                    alert("Por favor, ingresa el número de días trabajados.");
                    event.preventDefault(); // Impide el envío del formulario
                }
            } else {
                // Si el campo no existe, puedes agregar otra lógica o dejarlo vacío
                console.error('El campo de días trabajados no se encuentra en el formulario.');
            }
        });
    </script>
@endpush
