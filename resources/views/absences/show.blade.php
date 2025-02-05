@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <h2 class="page-title">
                Inasistencia
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-4">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td><strong>Trabajador:</strong></td>
                                <td> {{ $absence->worker->name }} {{ $absence->worker->last_name }} </td>
                                <td><strong>Fecha de Ausencia:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($absence->date)->format('d-m-Y') }} </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Motivo:</strong>
                                </td>
                                <td>{{ $absence->reason }}</td>
                                <td><strong>Duración:</strong></td>
                                <td>{{ $absence->minutes }} minuto(s)</td>
                            </tr>
                            <tr>
                                <td> <strong>Con goce de sueldo:</strong></td>
                                <td>{{ $absence->with_consent ? 'Sí' : 'No' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <span class="mt-4 d-block">
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('absences.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
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
