@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                {{ __('Licencia') }}
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-3">
                <div class="table-responsive">
                    <p>
                        <strong>Trabajador:</strong> {{ $license->worker->name }} {{ $license->worker->last_name }} <br />
                        <strong>Fecha de Emisión:</strong> {{ $license->issue_date }} <br />
                        <strong>Motivo:</strong> {{ $license->reason }} <br />
                        <strong>Días:</strong> {{ $license->days }} <br />
                        <!-- Muestra otros campos como institución, número de recibo, etc. -->
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
