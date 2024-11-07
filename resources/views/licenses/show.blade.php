@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                Detalle de la {{ __('Licencia Medica') }}
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-3">
                <div class="table-responsive">
                    <p>
                        <strong>Trabajador:</strong> {{ $license->worker->name }} {{ $license->worker->last_name }} <br />
                        <strong>Fecha de Emisión:</strong>
                        {{ \Carbon\Carbon::parse($license->issue_date)->format('d-m-Y') }} <br />
                        <strong>Motivo:</strong> {{ $license->reason }} <br />
                        <strong>Días:</strong> {{ $license->days }} <br />

                        <!-- Muestra otros campos solo si tienen valor -->
                        @if ($license->institution)
                            <strong>Institución:</strong> {{ $license->institution }} <br />
                        @endif

                        @if ($license->receipt_number)
                            <strong>Número de Recibo:</strong> {{ $license->receipt_number }} <br />
                        @endif

                        @if ($license->receipt_date)
                            <strong>Fecha de Recibo:</strong>
                            {{ \Carbon\Carbon::parse($license->receipt_date)->format('d-m-Y') }} <br />
                        @endif

                        @if ($license->processing_date)
                            <strong>Fecha de Procesamiento:</strong>
                            {{ \Carbon\Carbon::parse($license->processing_date)->format('d-m-Y') }} <br />
                        @endif

                        @if ($license->responsible_person)
                            <strong>Persona Responsable:</strong> {{ $license->responsible_person }} <br />
                        @endif
                    </p>
                </div>
                <span>
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
