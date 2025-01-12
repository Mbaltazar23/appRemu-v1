@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <h2 class="page-title">
                Crear Anexo para el Contrato de {{ $worker->name }} {{ $worker->last_name }}
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-5">
                <form action="{{ route('contracts.storeAnnex', $worker) }}" method="POST">
                    @csrf
                    @include('contracts.annexes.form')
                    <div class="d-flex justify-content-between mt-4">
                        <a class="text-decoration-none" href="{{ route('contracts.showAnnexes', $worker) }}">
                            <button type="button" class="btn btn-primary rounded-2 px-3 py-1">Regresar</button>
                        </a>
                        <button type="submit" class="btn btn-warning rounded-2 px-3 py-1">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
