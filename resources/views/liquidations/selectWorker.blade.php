@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <!-- Card 1: Selección de trabajador -->
            <div class="card mb-4 p-3" id="workerSelectionCard">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Emisión y visualización de liquidaciones para
                        {{ App\Models\Worker::getWorkerTypes()[$workerType] }}s</h3>
                    <a class="btn btn-secondary px-4 py-2" href="{{ route('liquidations.index') }}">Volver</a>
                </div>
                <div class="card-body">
                    @if ($workers->isEmpty())
                        <div class="alert alert-warning">
                            <h4>No existe ningún trabajador en este tipo</h4>
                            <form action="{{ route('liquidations.index') }}" method="GET">
                                <button type="submit" class="btn btn-secondary px-4 py-2">Volver</button>
                            </form>
                        </div>
                    @else
                        <div class="form-group mb-4">
                            <label for="worker_id" class="form-label">Seleccione el trabajador para revisar liquidaciones o
                                emitirla</label>
                            <select id="worker_id" class="form-control mt-3">
                                <option value="0">Seleccionar a un trabajador</option>
                                @foreach ($workers as $worker)
                                    <option value="{{ route('liquidations.workerLiquidation', [$worker->id]) }}">
                                        {{ $worker->name }} {{ $worker->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Card 2: Selección de mes y año -->
            <div class="card p-3" id="monthYearSelectionCard">
                <div class="card-header">
                    <h3>O Seleccione un mes y año para ver todas las liquidaciones</h3>
                </div>
                <div class="card-body">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="month" class="form-label">Mes</label>
                                <select name="month" id="month" required class="form-control">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">
                                            {{ App\Helpers\MonthHelper::integerToMonth($i) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="year" class="form-label">Año</label>
                                <select name="year" id="year" required class="form-control">
                                    <option value="0">Seleccione el año para la emisión</option>
                                    @foreach ($distincYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Aquí usamos el href con parámetros de mes y año -->
                    <a href="{{ route('liquidations.printGlosas', ['type' => $workerType]) }}"
                        class="btn btn-primary px-4 py-2" onclick="openPopup(event, 'Liquidaciones')">Imprimir</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom_scripts')
    <script>
        function openPopup(event, titulo) {
            event.preventDefault(); // Evita el comportamiento por defecto
            // Obtener los valores de los campos de mes y año
            var month = document.getElementById("month").value;
            var year = document.getElementById("year").value;

            // Verificar que se haya seleccionado mes y año
            if (month && year != "0") {
                // Construir la URL con los parámetros
                var url = `{{ route('liquidations.printGlosas', ['type' => $workerType]) }}?month=${month}&year=${year}`;
                // Abrir la ventana emergente
                window.open(url, titulo, 'width=800,height=600');
            } else {
                alert("Por favor, seleccione el mes y el año.");
            }
        }
        document.getElementById('worker_id').addEventListener('change', function() {
            window.location.href = this.value; // Redirige a la ruta seleccionada
        });
    </script>
@endpush
