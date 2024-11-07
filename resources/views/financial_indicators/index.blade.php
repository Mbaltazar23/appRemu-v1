@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card" id="selectionCard">
                <div class="card-header">
                    <h3>{{ __('Seleccione un Índice Económico') }}</h3>
                </div>
                <div class="card-body">
                    <form id="economicIndexForm" method="POST" action="{{ route('financial-indicators.show.post') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="economicIndices" class="form-label">{{ __('Índices Económicos') }}</label>
                            <select class="form-control" id="economicIndices" name="index" required>
                                <option value="">{{ __('Seleccione un Índice economico...') }}</option>
                                @foreach ($indices as $index)
                                    <option value="{{ $index['value'] }}">{{ $index['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">{{ __('Seleccionar') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
