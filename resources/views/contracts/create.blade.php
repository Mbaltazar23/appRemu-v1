@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                Contrato para el Trabajador: &nbsp;&nbsp;<span style="opacity: 0.7;">{{ $worker->name }} {{ $worker->last_name }}</span>
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-5">
                <div class="table-responsive">
                    <div class="row col-12 mb-2">
                        <form method="POST" action="{{ route('contracts.store', $worker) }}">
                            @csrf
                            <!-- Fila 1 -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="city" class="form-label">Ciudad de creación</label>
                                    <input type="text" name="city" id="city" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="levels" class="form-label">Niveles (básica, media, etc.)</label>
                                    <select name="levels" id="levels" class="form-control"
                                        @if ($worker->worker_type != App\Models\Worker::WORKER_TYPE_TEACHER) disabled @endif>
                                        <option value="">Seleccione un nivel</option>
                                        @foreach ($levelsOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="duration" class="form-label">Duración del contrato</label>
                                    <select name="duration" id="duration" class="form-control" required>
                                        <option value="">Seleccione duración</option>
                                        @foreach ($durationOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        
                            <!-- Fila 2 -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="total_remuneration" class="form-label">Remuneración total (ej.300.000)</label>
                                    <input type="number" name="total_remuneration" id="total_remuneration" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="remuneration_gloss" class="form-label">Glosa de remuneración (ej: Trecientos mil)</label>
                                    <input type="text" name="remuneration_gloss" id="remuneration_gloss" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="origin_city" class="form-label">Ciudad de procedencia</label>
                                    <input type="text" name="origin_city" id="origin_city" class="form-control">
                                </div>
                            </div>
                        
                            <!-- Fila 3 -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="schedule" class="form-label">Conformación jornada</label>
                                    <select name="schedule" id="schedule" class="form-control">
                                        <option value="">Seleccione jornada</option>
                                        @foreach ($scheduleOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Solo mostrar estos campos si el tipo de trabajador es Docente -->
                                @if ($worker->worker_type == App\Models\Worker::WORKER_TYPE_TEACHER)
                                    <div class="col-md-4">
                                        <label for="teaching_hours" class="form-label">Horas de docencia</label>
                                        <input type="text" name="teaching_hours" id="teaching_hours" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="curricular_hours" class="form-label">Horas actividades curriculares</label>
                                        <input type="text" name="curricular_hours" id="curricular_hours" class="form-control">
                                    </div>
                                @endif
                            </div>
                        
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('workers.index') }}" class="btn btn-secondary">Regresar al índice</a>
                                <button type="submit" class="btn btn-primary">Crear contrato</button>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
