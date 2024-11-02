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
                        <option value="bonusesList">{{ __('Bonos') }}</option>
                        <option value="generalParams">{{ __('Params. Generales') }}</option>
                        <option value="worker">{{ __('Trabajador') }}</option>
                    </select>
                </div>
                <button id="loadActionButton" class="btn btn-primary mt-3" onclick="loadActionView()">Cargar Acción</button>
            </div>
        </div>
        <!-- Sección de contenido dinámico -->
        <div id="actionContent"></div>
    </div>
</div>
@endsection

@push('custom_scripts')
<script>
    function loadActionView() {
        const action = document.getElementById('actionSelect').value;
        let url = '';

        if (action === 'bonusesList') {
            url = "{{ route('bonuses.partials.list') }}";
        } else if (action === 'generalParams') {
            url = "{{ route('bonuses.partials.params') }}";
        } else if (action === 'worker') {
            url = "{{ route('bonuses.partials.worker') }}";
        }

        if (url) {
            window.location.href = url;
        }
    }
</script>
@endpush
