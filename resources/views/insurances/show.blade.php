@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                {{ __('Seguro') }} &nbsp;<small class="text-muted">({{ $insuranceName }})</small>
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-3">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tr>
                            <td> <strong>RUT:</strong></td>
                            <td>{{ $insurance->rut }}</td>
                            <td> <strong>Nombre:</strong></td>
                            <td> {{ $insurance->name }}</td>
                        </tr>
                        <tr>
                            <td> <strong>Tipo de Seguro:</strong></td>
                            <td> {{ $insuranceTypes[$insurance->type] }}</td>
                            <td><strong>Cotización:</strong></td>
                            <td> {{ $insurance->cotizacion }}</td>
                        </tr>
                    </table>
                </div>
                <span class="mt-4">
                    <a class="mr-4 rounded-2 text-decoration-none"
                        href="{{ route('insurances.index', ['insurance_id' => $insurance->id, 'type' => request()->input('type')]) }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
                    </a>
                    @can('update', $insurance)
                        <a class="mr-4 rounded-2 text-decoration-none"
                            href="{{ route('insurances.edit', [$insurance, 'type' => request()->input('type')]) }}">
                            <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                        </a>
                    @endcan
                </span>
            </div>
        </div>
    </div>
@endsection
