@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                {{ __('School') }}
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-3">
                <div class="table-responsive">
                    <p>
                        <strong>Nombre:</strong> {{ $school->name }} <br />
                        <strong>Dirección:</strong> {{ $school->address }} <br />
                        
                        <!-- Sostenedor con RUT y Nombre Comercial -->
                        <strong>Sostenedor:</strong> 
                        @if($school->sustainer)
                            RUT: {{ $school->sustainer->rut }} - Nombre Comercial: {{ $school->sustainer->business_name }}
                        @else
                            No asignado
                        @endif
                        <br />

                        <strong>RUT:</strong> {{ $school->rut }} <br />
                        <strong>RBD:</strong> {{ $school->rbd }} <br />
                        <strong>Comuna:</strong> {{ $school->commune }} <br />
                        <strong>Región:</strong> {{ $school->region }} <br />
                        <strong>Director:</strong> {{ $school->director }} <br />
                        <strong>RUT del Director:</strong> {{ $school->rut_director }} <br />
                        <strong>Teléfono:</strong> {{ $school->phone }} <br />
                        <strong>Correo Electrónico:</strong> {{ $school->email }} <br />
                        <strong>Dependencia:</strong> {{ $school->dependency_text }} <br />
                        <strong>Subvención:</strong> {{ $school->grantt_text }} <br />
                    </p>
                </div>
                <span>
                    <a class="mr-2 rounded-2 text-decoration-none" href="{{ route('schools.index') }}">
                        <button class="btn btn-sm btn-info rounded-2">Regresar</button>
                    </a>
                    <a class="mr-2 rounded-2 text-decoration-none" href="{{ route('schools.edit', $school) }}">
                        <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                    </a>
                </span>
            </div>
        </div>
    </div>
@endsection
