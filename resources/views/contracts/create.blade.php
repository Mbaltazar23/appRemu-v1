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

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="city" class="form-label">Ciudad de creación del contrato</label>
                                    <input type="text" name="city" id="city" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="levels" class="form-label">Niveles en que trabajará (básica, media, etc.)</label>
                                    <input type="text" name="levels" id="levels" class="form-control" 
                                        @if ($worker->worker_type != App\Models\Worker::WORKER_TYPE_TEACHER) disabled @endif>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="duration" class="form-label">Duración del contrato (plazo indefinido, plazo fijo, etc.)</label>
                                    <input type="text" name="duration" id="duration" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="total_remuneration" class="form-label">Remuneración total (ej: $480,000)</label>
                                    <input type="text" name="total_remuneration" id="total_remuneration" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="remuneration_gloss" class="form-label">Glosa de la remuneración total (ej: Cuatrocientos ochenta mil)</label>
                                    <input type="text" name="remuneration_gloss" id="remuneration_gloss" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="origin_city" class="form-label">Ciudad de procedencia del trabajador</label>
                                    <input type="text" name="origin_city" id="origin_city" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="schedule" class="form-label">Conformación de jornada (ej: mañana y nocturna)</label>
                                    <input type="text" name="schedule" id="schedule" class="form-control">
                                </div>
                            </div>

                            @if ($worker->worker_type == App\Models\Worker::WORKER_TYPE_TEACHER)
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="teaching_hours" class="form-label">Horas de docencia de habla</label>
                                        <input type="text" name="teaching_hours" id="teaching_hours" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="curricular_hours" class="form-label">Horas de actividades curriculares no lectivas y recreos</label>
                                        <input type="text" name="curricular_hours" id="curricular_hours" class="form-control">
                                    </div>
                                </div>
                            @endif

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
