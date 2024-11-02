@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-xl">
      <div class="card">
        <div class="card-header">
          <h3>{{ __('Seleccione La Escuela a la que tiene Acceso') }}</h3>
        </div>
        <div class="card-body">
          <!-- Formulario para seleccionar el colegio -->
          <form id="school-select-form" method="POST" action="{{ route('setSchoolSession') }}">
            @csrf
            <div class="form-group mb-4">
                <label for="school_id" class="form-label">{{ __('Schools') }}</label>
                <select class="form-control" id="school_id" name="school_id" required>
                    <option value="">{{ __('Seleccione un Colegio') }}</option>
                    @foreach (auth()->user()->schools as $school)
                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
                <!-- Mostrar error de validación si no selecciona una escuela -->
                @error('school_id')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary mt-3">{{ __('Seleccionar') }}</button>
          </form>
        </div>
    </div>
    </div>
</div>
@endsection

@push('custom_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mover esta lógica a un evento de envío de formulario si se requiere
        const schoolIdSelect = document.getElementById('school_id');
        const selectedSchoolId = "{{ auth()->user()->school_id_session }}";
        if (selectedSchoolId) {
            schoolIdSelect.value = selectedSchoolId; // Cargar el valor seleccionado
        }
    });
</script>
@endpush
