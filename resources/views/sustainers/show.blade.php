@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                {{ __('Sostenedor') }}
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-3">
                <div class="table-responsive">
                    <p>
                        <strong>RUT:</strong> {{ $sustainer->rut }} <br />
                        <strong>Razón Social:</strong> {{ $sustainer->business_name }} <br />
                        <strong>Dirección:</strong> {{ $sustainer->address }} <br />
                        <strong>Comuna:</strong> {{ $sustainer->commune }} <br />
                        <strong>Región:</strong> {{ $sustainer->region }} <br />
                        <strong>Representante Legal:</strong> {{ $sustainer->legal_representative }} <br />
                        <strong>RUT del Representante:</strong> {{ $sustainer->rut_legal_representative }} <br />
                        <strong>Email:</strong> {{ $sustainer->email }} <br />
                        <strong>Teléfono:</strong> {{ $sustainer->phone }} <br />
                    </p>
                </div>
                <span>
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('sustainers.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
                    </a>
                    <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('sustainers.edit', $sustainer) }}">
                        <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                    </a>
                </span>
            </div>
        </div>
    </div>
@endsection
