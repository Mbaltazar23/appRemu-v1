@extends('layouts.app')

@section('content')
<div class="container-xl">
    <!-- Page title -->
    <div class="page-header d-print-none">
        <h2 class="page-title">Mantenedor de parámetros de bonos y descuentos</h2>
    </div>
    
    <div class="page-body">
        <div class="card p-5">
            <form action="{{ route('bonuses.updateParams') }}" method="POST">
                @csrf
                <div class="mb-3 row">
                    <label for="CIERREMES" class="col-sm-4 col-form-label">Día del cierre de mes</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="CIERREMES" name="CIERREMES" 
                               value="{{ old('CIERREMES', $params['CIERREMES'] ?? '') }}" required>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="FACTORRBMNBASICA" class="col-sm-4 col-form-label">Factor RBMN Básica (en pesos)</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="FACTORRBMNBASICA" name="FACTORRBMNBASICA" 
                               value="{{ old('FACTORRBMNBASICA', $params['FACTORRBMNBASICA'] ?? '') }}" required>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="VALORIMD" class="col-sm-4 col-form-label">Valor IMD (en pesos)</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="VALORIMD" name="VALORIMD" 
                               value="{{ old('VALORIMD', $params['VALORIMD'] ?? '') }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('bonuses.index') }}" class="text-decoration-none">
                        <button type="button" class="btn btn-secondary">Regresar</button>
                    </a>
                    <button type="submit" class="btn btn-primary">Ingresar/Modificar</button>
                </div>

                <div class="text-center mt-3">
                    <h5 class="text-danger">Todos los campos son obligatorios</h5>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
