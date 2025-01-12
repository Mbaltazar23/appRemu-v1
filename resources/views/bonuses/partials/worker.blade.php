@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    {{ __('Mantenedor de bonos y descuentos por trabajador') }}
                </span>
                <div>
                    <a class="d-inline ml-2 text-decoration-none" href="{{ route('bonuses.index') }}">
                        <button class="btn btn-secondary rounded-3 px-3 py-1">Volver al Inicio</button>
                    </a>
                </div>
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <!-- Card para seleccionar trabajador -->
            <div class="card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-group mb-4" style="flex-grow: 1; margin-right: 20px;">
                        <label for="worker_id" class="h3">
                            Selecciona al trabajador para asignar bonos o descuentos
                        </label>
                        <select name="worker_id" id="worker_id" class="form-control" required onchange="this.form.submit()">
                            <option value="">Seleccionar trabajador...</option>
                            @foreach ($workers as $worker)
                                <option value="{{ route('bonuses.partials.worker', $worker->id) }}"
                                    {{ isset($selectedWorker) && $selectedWorker->id == $worker->id ? 'selected' : '' }}>
                                    {{ $worker->name }} {{ $worker->last_name }} -
                                    ({{ $worker->getDescriptionWorkerTypes() }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Si hay un trabajador seleccionado, mostramos sus datos y bonos -->
            @if (isset($selectedWorker))
                <div id="parameter-card" class="card p-2">
                    <div class="card-header">
                        <h3>Datos del Trabajador y sus Bonos Asociados</h3>
                    </div>
                    <form method="POST" action="{{ route('bonuses.updateBonus') }}" id="worker-form">
                        @csrf
                        <input type="hidden" name="worker_id" id="worker_id" value="{{ $selectedWorker->id }}">
                        <div class="card-body">
                            <table class="table p-2">
                                <tbody>
                                    <tr>
                                        <td><strong>Nombre:</strong></td>
                                        <td>{{ $selectedWorker->name }} {{ $selectedWorker->last_name }}</td>
                                        <td><strong>Tipo trabajador:</strong></td>
                                        <td>{{ $selectedWorker->getWorkerTypes()[$selectedWorker->worker_type] }}</td>
                                    </tr>
                                    @forelse ($bonusData as $bonus)
                                        <tr>
                                            <td><label for="bonus{{ $bonus['id'] }}"
                                                    class="form-label">{{ ucwords(strtolower($bonus['title'])) }}</label>
                                            </td>
                                            <td colspan="3">
                                                <input type="text" name="ID{{ $bonus['id'] }}"
                                                    value="{{ $bonus['value'] }}" class="form-control"
                                                    onblur="numerovalido(this)">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted" style="padding-top: 20px;">
                                                No existen bonos a designar para este trabajador o todos sus bonos son
                                                fijos.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($bonusData)
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="submit" class="btn btn-primary">Modificar</button>
                                </div>
                                <div class="text-center"><small class="text-muted">(*) Campos son obligatorios</small></div>
                            @endif
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('custom_scripts')
    <script>
        document.getElementById('worker_id').addEventListener('change', function() {
            window.location.href = this.value; // Redirige a la ruta seleccionada
        });
    </script>
@endpush
