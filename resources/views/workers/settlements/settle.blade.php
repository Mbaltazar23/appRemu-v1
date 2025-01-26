@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                {{ $worker->settlement_date ? "Actualizar" : "Asignar" }} Fecha de Finiquito
            </h2>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card p-5">
                <form method="POST" action="{{ route('workers.updateSettle', $worker) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="settlement_date" class="mb-3">Fecha de Finiquito</label>
                        <!-- Agregamos mb-3 para espacio inferior -->
                        <input type="date" name="settlement_date" id="settlement_date" class="form-control mt-2" required
                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" value="{{ old('settlement_date') ?? $worker->settlement_date }}">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <!-- Botón de cancelar -->
                        <a href="{{ route('workers.index') }}" class="text-decoration-none">
                            <button type="button" class="btn btn-secondary rounded-2 px-3 py-1">Cancelar</button>
                        </a>
                        <!-- Botón de guardar -->
                        <button type="submit" class="btn btn-primary rounded-2 px-3 py-1">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
