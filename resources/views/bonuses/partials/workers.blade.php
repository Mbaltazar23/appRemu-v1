@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Seleccionar Trabajadores para El Bono
                    ({{ $bonus->school->tuitions->where('tuition_id', $bonus->title)->first()->title }})
                </span>
                <div>
                    <a class="d-inline ml-2 text-decoration-none" href="{{ route('bonuses.partials.list') }}">
                        <button class="btn btn-secondary rounded-3 px-3 py-1">
                            Volver a los Bonos
                        </button>
                    </a>
                </div>
            </h2>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-body">
                    <form id="workersForm" method="POST" action="{{ route('bonuses.update-workers', $bonus->id) }}">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-5">
                                <!-- Contenedor del Select de Trabajadores No Asociados -->
                                <div class="form-group">
                                    <label for="nonAppliedWorkersSelect" class="form-label">Trabajadores No Asociados al Bono</label>
                                    <select id="nonAppliedWorkersSelect" class="form-control custom-select" multiple onchange="moveWorker(this)">
                                        @foreach ($nonAppliedWorkers as $worker)
                                            <option value="{{ $worker->id }}">
                                                {{ $worker->name }} {{ $worker->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <!-- Contenedor del Select de Trabajadores Asociados -->
                                <div class="form-group">
                                    <label for="appliedWorkersSelect" class="form-label">Trabajadores Asociados</label>
                                    <select id="appliedWorkersSelect" class="form-control custom-select" name="workers[]" multiple onchange="moveWorker(this)">
                                        @foreach ($appliedWorkers as $workerId)
                                            @php $worker = $workers->find($workerId); @endphp
                                            <option value="{{ $worker->id }}" selected>
                                                {{ $worker->name }} {{ $worker->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom_scripts')
    <script>
        // Función para mover trabajadores entre selects
        function moveWorker(selectElement) {
            // Obtenemos el ID de los selectores
            const nonAppliedSelect = document.getElementById('nonAppliedWorkersSelect');
            const appliedSelect = document.getElementById('appliedWorkersSelect');

            // Verificamos si el cambio fue en el select de trabajadores no asociados
            if (selectElement.id === 'nonAppliedWorkersSelect') {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                // Movemos la opción seleccionada al select de trabajadores asociados
                if (selectedOption) {
                    appliedSelect.appendChild(selectedOption);
                }
            }

            // Verificamos si el cambio fue en el select de trabajadores asociados
            if (selectElement.id === 'appliedWorkersSelect') {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                // Movemos la opción seleccionada al select de trabajadores no asociados
                if (selectedOption) {
                    nonAppliedSelect.appendChild(selectedOption);
                }
            }
        }
    </script>
@endpush
