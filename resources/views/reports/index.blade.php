@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card" id="selectionCard">
                <div class="card-header">
                    <h3>Seleccione el Tipo de Seguro para generar Informes</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-4">
                        <label for="typeInsurance" class="form-label">Seleccione el tipo de Seguro</label>
                        <select id="typeInsurance" class="form-control">
                            <option value="">Seleccione un tipo</option>
                            @foreach ($typeInsurances as $key => $type)
                                <option value="{{ route('reports.type', $key) }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('typeInsurance').addEventListener('change', function() {
            window.location.href = this.value; // Redirige a la ruta seleccionada
        });
    </script>
@endsection
