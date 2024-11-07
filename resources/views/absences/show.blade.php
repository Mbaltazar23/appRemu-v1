@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <h2 class="page-title">
                Detalles de la Inasistencia
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-3">
                <p>
                    <strong>Trabajador:</strong> {{ $absence->worker->name }} {{ $absence->worker->last_name }} <br />
                    <strong>Fecha de Ausencia:</strong> {{ $absence->day }}/{{ $absence->month }}/{{ $absence->year }} <br />
                    <strong>Motivo:</strong> {{ $absence->reason }} <br />
                    <strong>Duración:</strong> {{ $absence->minutes }} minutos <br />
                    <strong>Con consentimiento:</strong> {{ $absence->with_consent ? 'Sí' : 'No' }} <br />
                </p>
                <span>
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('absences.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver</button>
                    </a>
                    @can('update', $absence)
                        <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('absences.edit', $absence) }}">
                            <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                        </a>
                    @endcan
                </span>
            </div>
        </div>
    </div>
@endsection
