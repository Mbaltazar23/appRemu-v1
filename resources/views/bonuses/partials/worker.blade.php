@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3>{{ __('Mantenedor de bonos y descuentos por trabajador') }}</h3>
                </div>
                <div class="card-body">
                    <!-- Contenedor para el select del trabajador y el botón de regresar -->
                    <form method="POST" action="{{ route('bonuses.select.worker') }}" class="d-flex justify-content-between align-items-center" style="flex-wrap: wrap;">
                        @csrf
                        <div class="form-group mb-4" style="flex-grow: 1; margin-right: 20px; max-width: 100%;">
                            <label for="worker_id" class="h5">
                                Acá se colocan los montos de los bonos o descuentos que fueron definidos como dependientes en forma fija del trabajador
                            </label>
                            <select name="worker_id" id="worker_id" class="form-control" required onchange="this.form.submit()">
                                <option value="">Seleccionar trabajador...</option>
                                @foreach ($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ isset($selectedWorker) && $selectedWorker->id == $worker->id ? 'selected' : '' }}>
                                        {{ $worker->name }} {{ $worker->last_name }} - ({{ $worker->getDescriptionWorkerTypes() }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <a href="{{ route('bonuses.index') }}" class="btn btn-secondary">
                            Regresar al Inicio
                        </a>
                    </form>
                </div>
            </div>

            <!-- Detalles del trabajador -->
            @if (isset($selectedWorker))
                <div id="parameter-card" class="card mt-4">
                    <div class="card-header">
                        <h3>Datos del Trabajador y sus Bonos Asociados</h3>
                    </div>
                    <form method="POST" action="{{ route('bonuses.updateBonus') }}" id="worker-form">
                        @csrf
                        <input type="hidden" name="worker_id" id="worker_id" value="{{ $selectedWorker->id }}">
                        <div class="card-body">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><strong>Nombre:</strong></td>
                                        <td>{{ $selectedWorker->name }} {{ $selectedWorker->last_name }}</td>
                                        <td><strong>Tipo trabajador:</strong></td>
                                        <td>{{ $selectedWorker->getWorkerTypes()[$selectedWorker->worker_type] }}</td>
                                    </tr>
                                    @forelse ($bonusData as $bonus)
                                        <tr>
                                            <td><label for="bonus{{ $bonus['id'] }}" class="form-label">{{ ucwords(strtolower($bonus['title'])) }}</label></td>
                                            <td colspan="3">
                                                <input type="text" name="ID{{ $bonus['id'] }}" value="{{ $bonus['value'] }}" class="form-control" onblur="numerovalido(this)">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted" style="padding-top: 20px;">
                                                No existen bonos a designar para este trabajador o todos sus bonos son fijos.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($bonusData)
                                <div class="d-flex justify-content-end">
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
