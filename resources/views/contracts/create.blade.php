@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                Contrato para el Trabajador: &nbsp;<span style="opacity: 0.7;">{{ $worker->name }}
                    {{ $worker->last_name }}</span>
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-5">
                <form method="POST" action="{{ route('contracts.store', $worker) }}">
                    @csrf
                    <!-- Fila 1 -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="city" class="form-label">Ciudad de creación</label>
                            <input type="text" name="city" id="city" class="form-control"
                                value="{{ old('city', $formData['city']) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="levels" class="form-label">Niveles (básica, media, etc.)</label>
                            <select name="levels" id="levels" class="form-control">
                                <option value="">Seleccione un nivel</option>
                                @foreach ($levelsOptions as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('levels', $formData['levels']) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="duration" class="form-label">Duración del contrato</label>
                            <select name="duration" id="duration" class="form-control" required>
                                <option value="">Seleccione duración</option>
                                @foreach ($durationOptions as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('duration', $formData['duration']) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                
                    <!-- Fila 2 -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="total_remuneration" class="form-label">Remuneración aprox. (ej.300.000)</label>
                            <input type="number" name="total_remuneration" id="total_remuneration" class="form-control"
                                value="{{ old('total_remuneration', $formData['total_remuneration']) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="remuneration_gloss" class="form-label">Glosa de remuneración (ej: Trescientos mil)</label>
                            <input type="text" name="remuneration_gloss" id="remuneration_gloss" class="form-control"
                                value="{{ old('remuneration_gloss', $formData['remuneration_gloss']) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="origin_city" class="form-label">Ciudad de procedencia</label>
                            <input type="text" name="origin_city" id="origin_city" class="form-control"
                                value="{{ old('origin_city', $formData['origin_city']) }}">
                        </div>
                    </div>
                
                    <!-- Fila 3 -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="schedule" class="form-label">Conformación jornada</label>
                            <select name="schedule" id="schedule" class="form-control">
                                <option value="">Seleccione jornada</option>
                                @foreach ($scheduleOptions as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('schedule', $formData['schedule']) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Solo mostrar estos campos si el tipo de trabajador es Docente -->
                        <div class="col-md-4">
                            <label for="teaching_hours" class="form-label">Horas de docencia</label>
                            <input type="text" name="teaching_hours" id="teaching_hours" class="form-control"
                                value="{{ old('teaching_hours', $formData['teaching_hours']) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="curricular_hours" class="form-label">Horas actividades curriculares</label>
                            <input type="text" name="curricular_hours" id="curricular_hours" class="form-control"
                                value="{{ old('curricular_hours', $formData['curricular_hours']) }}">
                        </div>
                    </div>
                
                    <div class="d-flex justify-content-between mt-5">
                        <a href="{{ route('workers.index') }}" class="btn btn-primary rounded-2 px-3 py-1">Volver al Inicio</a>
                        <button type="submit" class="btn btn-warning rounded-2 px-3 py-1">
                            {{ $worker->contract->details ? 'Actualizar Contrato' : 'Crear Contrato' }}
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
@endsection
