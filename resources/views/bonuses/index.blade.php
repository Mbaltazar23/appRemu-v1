@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3>{{ __('Seleccione qué acción desea realizar') }}</h3>
                </div>
                <div class="card-body">
                    <!-- Selector de acción -->
                    <div class="form-group mb-4">
                        <label for="actionSelect" class="form-label">{{ __('Seleccione la acción a realizar') }}</label>
                        <select id="actionSelect" class="form-control">
                            <option value="">{{ __('Seleccione...') }}</option>
                            <option value="{{ route('bonuses.partials.list', 'bonusesList') }}">{{ __('Construccion') }}
                            </option>
                            @can('parametersGen', App\Models\Bonus::class)
                                <option value="{{ route('bonuses.partials.params', 'generalParams') }}">
                                    {{ __('Params. Generales') }}</option>
                            @endcan
                            @can('viewWorkers', App\Models\Bonus::class)
                                <option value="{{ route('bonuses.partials.worker', 'worker') }}">{{ __('Trabajador') }}</option>
                            @endcan
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom_scripts')
    <script>
        document.getElementById('actionSelect').addEventListener('change', function() {
            window.location.href = this.value; // Redirige a la ruta seleccionada
        });
    </script>
@endpush
