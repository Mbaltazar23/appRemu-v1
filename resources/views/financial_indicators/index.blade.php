@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-3" id="selectionCard">
                <div class="card-header">
                    <h3>{{ __('Seleccione un Índice Económico') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-4">
                        <label for="economicIndices" class="form-label">{{ __('Índices Económicos') }}</label>
                        <select class="form-control" id="economicIndices" name="index" required>
                            <option value="">{{ __('Seleccione un Índice economico...') }}</option>
                            @foreach ($indices as $index)
                                <option value="{{ route('financial-indicators.show', $index['value']) }}">
                                    {{ $index['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('economicIndices').addEventListener('change', function() {
            window.location.href = this.value; // Redirige a la ruta seleccionada
        });
    </script>
@endsection
