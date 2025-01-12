@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                Registrar {{ __('School') }}
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-5">
                <form method="POST" action="{{ route('schools.store') }}">
                    @csrf
                    @include('schools.form')
                   <div class="d-flex justify-content-between mt-3">
                        <a class="text-decoration-none" href="{{ route('schools.index') }}">
                            <button type="button" class="btn btn-primary rounded-2 px-3 py-1">Regresar</button>
                        </a>
                        <button type="submit" class="btn btn-warning rounded-2 px-3 py-1">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
