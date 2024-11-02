@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                Detalles del Trabajador
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-3">
                <div class="table-responsive">
                    <p>
                        <strong>Nombre:</strong> {{ $worker->name }} {{ $worker->last_name }} <br />
                        <strong>RUT:</strong> {{ $worker->rut }} <br />
                        <strong>Dirección:</strong> {{ $worker->address }} <br />
                        <strong>Comuna:</strong> {{ $worker->commune }} <br />
                        <strong>Región:</strong> {{ $worker->region }} <br />
                        <strong>Fecha de Nacimiento:</strong> {{ $worker->birth_date }} <br />
                        <strong>Teléfono:</strong> {{ $worker->phone }} <br />
                        <strong>Correo Electrónico:</strong> {{ $worker->email }} <br />
                        <strong>Nacionalidad:</strong> {{ $worker->nationality ?? 'No especificado' }} <br />
                        <strong>Estado Civil:</strong> {{ $worker->getMaritalStatusDescription() }} <br />
                        <strong>Tipo de Trabajador:</strong> {{ $worker->getWorkerTypes()[$worker->worker_type] }} <br />
                        <strong>Función del Trabajador:</strong> {{ $worker->getFunctionWorkerDescription() }} <br />

                        @php
                            $insuranceNames = $worker->getInsuranceNames();
                        @endphp

                        @if ($insuranceNames['insurance_AFP'] || $insuranceNames['insurance_ISAPRE'])
                            <strong>AFP:</strong> {{ $insuranceNames['insurance_AFP'] ?? 'No Cuenta con este Seguro' }}
                            <br />
                            <strong>ISAPRE:</strong>
                            {{ $insuranceNames['insurance_ISAPRE'] ?? 'No Cuenta con este Seguro' }} <br />
                        @endif

                        <strong>N° Cargas Familiar:</strong>
                        {{ optional($worker->parameters->where('name', 'CARGASFAMILIARES')->first())->value ?? 'No especificado' }}
                        <br />
                        <strong>Carga Horaria:</strong>
                        {{ optional($worker->parameters->where('name', 'CARGAHORARIA')->first())->value ?? 'No especificado' }}
                        <br />
                        <strong>Horas por Día:</strong>
                    <ul>
                        @foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'] as $day)
                            <li>{{ ucfirst($day) }}: {{ json_decode($worker->load_hourly_work)->$day ?? 0 }} horas</li>
                        @endforeach
                    </ul>

                    @if ($worker->contract)
                        <strong>Fecha Inicio Contrato:</strong> {{ $worker->contract->hire_date ?? 'No especificado' }}
                        <br />
                        <strong>Fecha Término Contrato:</strong>
                        {{ $worker->contract->termination_date ?? 'No especificado' }} <br />
                        <strong>Motivo de Reemplazo:</strong>
                        {{ $worker->contract->replacement_reason ?? 'No especificado' }} <br />
                        <strong>Trabajador Titular:</strong>
                        {{ optional($workers->where('id', $worker->worker_titular)->first())->name ?? 'No especificado' }}
                        <br />
                        <strong>Tipo de Contrato:</strong>
                        {{ App\Models\Contract::getContractTypes()[$worker->contract->contract_type] ?? 'No especificado' }}
                        <br />
                    @else
                        <strong>Contrato:</strong> No especificado <br />
                    @endif
                    </p>
                </div>
                <span>
                    <a class="mr-2 rounded-2 text-decoration-none" href="{{ route('workers.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Regresar</button>
                    </a>
                    <a class="mr-2 rounded-2 text-decoration-none" href="{{ route('workers.edit', $worker) }}">
                        <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                    </a>
                </span>
            </div>
        </div>
    </div>
@endsection
