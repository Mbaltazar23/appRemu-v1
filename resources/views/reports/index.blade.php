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
                        <label class="form-label">Seleccione el tipo de Seguro</label>
                        <select class="form-control" onchange="window.location.href=this.value;" required>
                            <option value="">Seleccione un tipo</option>
                            @foreach ($accessibleInsurances as $key => $type)
                                <option value="{{ route('reports.type', $key) }}">{{ $type['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
