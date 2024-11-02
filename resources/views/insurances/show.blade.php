@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                {{ __('Seguro') }}
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-3">
                <div class="table-responsive">
                    <p>
                        <strong>RUT:</strong> {{ $insurance->rut }} <br />
                        <strong>Nombre:</strong> {{ $insurance->name }} <br />
                        <strong>Tipo de Seguro:</strong> {{ $insuranceTypes[$insurance->type] ?? 'N/A' }} <br />
                        <!-- Usamos $types aquí -->
                        <strong>Cotización:</strong> {{ $insurance->cotizacion }} <br />
                    </p>
                </div>
                <span>
                    <a class="mr-4 rounded-2 text-decoration-none"
                        href="{{ route('insurances.index', ['type' => request()->input('type')]) }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
                    </a>
                    <a class="mr-4 rounded-2 text-decoration-none"
                        href="{{ route('insurances.edit', [$insurance, 'type' => request()->input('type')]) }}">
                        <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                    </a>
                </span>
            </div>
        </div>
    </div>
@endsection
