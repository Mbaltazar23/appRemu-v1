@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card p-3" id="selectionCard">
            <div class="card-header">
                <h3>Emisión y visualización de liquidaciones</h3>
            </div>
            <div class="card-body">
                <div class="form-group mb-4">
                    <label for="worker_type" class="form-label">Seleccione el tipo de trabajador</label>
                    <select id="worker_type" class="form-control">
                        <option value="">Seleccionar tipo de trabajador</option>
                        @foreach ($workerTypes as $key => $type)
                            <option value="{{ route('liquidations.selectWorker', $key) }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('worker_type').addEventListener('change', function() {
        window.location.href = this.value;  // Redirige a la ruta seleccionada
    });
</script>
@endsection
