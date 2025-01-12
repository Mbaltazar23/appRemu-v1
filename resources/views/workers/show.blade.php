@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                Trabajador
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-5">
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <td><strong>Nombre:</strong></td>
                            <td>{{ $worker->name }} {{ $worker->last_name }}</td>
                            <td><strong>RUT:</strong></td>
                            <td>{{ $worker->rut }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dirección:</strong></td>
                            <td>{{ $worker->address }}</td>
                            <td><strong>Comuna:</strong></td>
                            <td>{{ $worker->commune }}</td>
                        </tr>
                        <tr>
                            <td><strong>Región:</strong></td>
                            <td>{{ $worker->region }}</td>
                            <td><strong>Fecha de Nacimiento:</strong></td>
                            <td>{{ $worker->birth_date }}</td>
                        </tr>
                        <tr>
                            <td><strong>Teléfono:</strong></td>
                            <td>{{ $worker->phone }}</td>
                            <td><strong>Nacionalidad:</strong></td>
                            <td>{{ $worker->nationality ?? 'No especificado' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Estado Civil:</strong></td>
                            <td>{{ $worker->getMaritalStatusDescription() }}</td>
                            <td><strong>Tipo de Trabajador:</strong></td>
                            <td>{{ $worker->getWorkerTypes()[$worker->worker_type] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Función del Trabajador:</strong></td>
                            <td colspan="3">{{ $worker->getFunctionWorkerDescription() }}</td>
                        </tr>

                        @php
                            $insuranceNames = $worker->getInsuranceNames();
                        @endphp

                        @if ($insuranceNames['insurance_AFP'] || $insuranceNames['insurance_ISAPRE'])
                            <tr>
                                <td><strong>AFP:</strong></td>
                                <td>{{ $insuranceNames['insurance_AFP'] ?? 'No Cuenta con este Seguro' }}</td>
                                <td><strong>SALUD:</strong></td>
                                <td>{{ $insuranceNames['insurance_ISAPRE'] ?? 'No Cuenta con este Seguro' }}</td>
                            </tr>
                        @endif

                        <tr>
                            <td><strong>N° Cargas Familiar:</strong></td>
                            <td>{{ optional($worker->parameters->where('name', 'CARGASFAMILIARES')->first())->value ?? 'No especificado' }}</td>
                            <td><strong>Carga Horaria:</strong></td>
                            <td>{{ optional($worker->parameters->where('name', 'CARGAHORARIA')->first())->value ?? 'No especificado' }}</td>
                        </tr>

                        <tr>
                            <td><strong>Horas por Día:</strong></td>
                            <td colspan="3">
                                <table class="table table-sm table-bordered mb-1">
                                    <tbody>
                                        <tr>
                                            <td><strong>Lunes:</strong></td>
                                            <td>{{ json_decode($worker->load_hourly_work)->lunes ?? 0 }} horas</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Martes:</strong></td>
                                            <td>{{ json_decode($worker->load_hourly_work)->martes ?? 0 }} horas</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Miércoles:</strong></td>
                                            <td>{{ json_decode($worker->load_hourly_work)->miercoles ?? 0 }} horas</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jueves:</strong></td>
                                            <td>{{ json_decode($worker->load_hourly_work)->jueves ?? 0 }} horas</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Viernes:</strong></td>
                                            <td>{{ json_decode($worker->load_hourly_work)->viernes ?? 0 }} horas</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sábado:</strong></td>
                                            <td>{{ json_decode($worker->load_hourly_work)->sabado ?? 0 }} horas</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                        @if ($worker->contract)
                            <tr>
                                <td><strong>Fecha Inicio Contrato:</strong></td>
                                <td>{{ $worker->contract->hire_date ?? 'No especificado' }}</td>
                                <td><strong>Fecha Término Contrato:</strong></td>
                                <td>{{ $worker->contract->termination_date ?? 'No especificado' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Motivo de Reemplazo:</strong></td>
                                <td>{{ $worker->contract->replacement_reason ?? 'No especificado' }}</td>
                                <td><strong>Trabajador Titular:</strong></td>
                                <td>{{ optional($workers->where('id', $worker->worker_titular)->first())->name ?? 'No especificado' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tipo de Contrato:</strong></td>
                                <td>{{ $worker->contract->getContractTypes()[$worker->contract->contract_type] ?? 'No especificado' }}</td>
                            </tr>
                        @else
                            <tr>
                                <td><strong>Contrato:</strong></td>
                                <td colspan="3">No especificado</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <!-- Botones de acción -->
                <div class="mt-4">
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('workers.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al Inicio</button>
                    </a>
                    @can('update', $worker)
                        <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('workers.edit', $worker) }}">
                            <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
