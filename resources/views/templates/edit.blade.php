@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                <h3>{{ __('Agregar ítem de liquidaciones para ') }}{{ $typeTitle }}</h3>
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-5">
                <form method="POST" action="{{ route('templates.update', $template) }}">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="type" value="{{ $typeItem }}" />
                    <input type="hidden" name="school_id" value="{{ auth()->user()->school_id_session }}" />
                    @include('templates.form')
                    <div class="d-flex justify-content-between mt-3">
                        <a class="text-decoration-none" href="{{ route('templates.index', ['typeItem' => $typeItem]) }}">
                            <button type="button" class="btn btn-primary rounded-2 px-3 py-1">Regresar</button>
                        </a>
                        <button type="submit" class="btn btn-warning rounded-2 px-3 py-1">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
