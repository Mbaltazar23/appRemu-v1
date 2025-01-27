@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="card-body">
            <form id="reportForm">
                @csrf <!-- Token de seguridad -->
                <input type="hidden" name="typeInsurance" value="{{ $typeInsurance }}" />
                <div class="container-xl">
                    <!-- Card 1: Selección de Mes y Año -->
                    <div class="card mb-4" id="dateSelectionCard">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Informe Previsional - {{ $nameInsurance }}</h3>
                            <a class="btn btn-secondary px-4 py-2" href="{{ route('reports.index') }}">Volver</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Select de Mes -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="month" class="form-label">Mes</label>
                                        <select name="month" id="month" required class="form-control">
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">
                                                    {{ App\Helpers\MonthHelper::integerToMonth($i) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <!-- Select de Año -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="year" class="form-label">Año</label>
                                        <select name="year" id="year" required class="form-control">
                                            <option value="0">Seleccione el año para la emisión</option>
                                            @foreach (\App\Models\Liquidation::getDistinctYears() as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Selección de Seguro (Insurance) -->
                    <div class="card mb-4" id="insuranceSelectionCard">
                        <div class="card-body">
                            <div class="row">
                                <!-- Select de Insurance -->
                                <div class="col-md-12 mb-3">
                                    <div class="form-group m-2">
                                        <label for="insurance" class="form-label">Seleccione un Seguro </label>
                                        <select name="insurance" id="insurance" class="form-control">
                                            <option value="">Seleccione
                                                {{ App\Models\Insurance::getInsuranceTypes()[$typeInsurance] }} </option>
                                            @foreach ($insurancesType as $insurance)
                                                <option value="{{ $insurance->description }}">
                                                    ({{ $insurance->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Botón para ejecutar la acción -->
                            <div class="d-flex justify-content-end mt-3">
                                <a id="generateReportLink" class="btn btn-primary px-4 py-2" onclick="openPopup(event)">
                                    Imprimir
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('custom_scripts')
    <script>
        function openPopup(event) {
            event.preventDefault(); // Evita el comportamiento por defecto del enlace
            const month = document.getElementById('month').value;
            const year = document.getElementById('year').value;
            const insurance = document.getElementById('insurance').value;
            const typeInsurance = document.querySelector('input[name="typeInsurance"]').value;

            // Construir la URL con los parámetros del formulario
            const url = `{{ url('reports/generate') }}/${typeInsurance}/${month}/${year}/${insurance}`;

            // Crear un enlace <a> dinámicamente
            const link = document.createElement('a');
            link.href = url;
            link.target = '_blank'; // Abrir en nueva pestaña
            link.rel = 'noopener noreferrer'; // Seguridad adicional

            // Simular el clic en el enlace para abrir la URL en nueva pestaña
            link.click();
        }
    </script>
@endpush
