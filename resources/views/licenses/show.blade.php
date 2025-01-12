@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                {{ __('Licencia Medica') }}
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
                                <th class="w-25">Trabajador:</th>
                                <td>{{ $license->worker->name }} {{ $license->worker->last_name }}</td>
                                <th class="w-25">Fecha de Emisión:</th>
                                <td>{{ \Carbon\Carbon::parse($license->issue_date)->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <th class="w-25">Motivo:</th>
                                <td>{{ $license->reason }}</td>
                                <th class="w-25">Días:</th>
                                <td>{{ $license->days }}</td>
                            </tr>

                            <!-- Muestra otros campos solo si tienen valor -->
                            @if ($license->institution || $license->receipt_number)
                                <tr>
                                    <th class="w-25">Institución:</th>
                                    <td>{{ $license->institution }}</td>
                                    <th class="w-25">Número de Recibo:</th>
                                    <td>{{ $license->receipt_number }}</td>
                                </tr>
                            @endif

                            @if ($license->receipt_date || $license->processing_date)
                                <tr>
                                    <th class="w-25">Fecha de Recibo:</th>
                                    <td>{{ \Carbon\Carbon::parse($license->receipt_date)->format('d-m-Y') }}</td>
                                    <th class="w-25">Fecha de Procesamiento:</th>
                                    <td>{{ \Carbon\Carbon::parse($license->processing_date)->format('d-m-Y') }}</td>
                                </tr>
                            @endif

                            @if ($license->responsible_person)
                                <tr>
                                    <th class="w-25">Persona Responsable:</th>
                                    <td>{{ $license->responsible_person }}</td>
                                </tr>
                            @endif

                            <!-- Mostrar detalles de las horas solo si el trabajador es docente -->
                            @if ($license->worker->worker_type === \App\Models\Worker::WORKER_TYPE_TEACHER)
                                <tr>
                                    <td colspan="4">
                                        <!-- Subtítulo con el nombre Horas de Licencia y los encabezados Día y Mes y Horas Asignadas -->
                                        <strong class="w-25">Horas de Licencia</strong>
                                        <!-- Los encabezados Día y Mes, y Horas Asignadas ahora están en la misma fila -->
                                        <table class="table table-sm mb-0 mt-2">
                                            <thead>
                                                <tr>
                                                    <th><strong>Día y Mes</strong></th>
                                                    <th><strong>Horas Asignadas</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Mostrar las horas -->
                                                @foreach ($license->hours as $hourLicense)
                                                    <tr>
                                                        <td>{{ $hourLicense->day.'/'.$hourLicense->month }}</td>
                                                        <td>{{ $hourLicense->hours }} horas</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Botones con margen superior -->
                <span class="mt-4 d-block">
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('licenses.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
                    </a>
                    @can('update', $license)
                        <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('licenses.edit', $license) }}">
                            <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                        </a>
                    @endcan
                </span>
            </div>
        </div>
    </div>
@endsection
