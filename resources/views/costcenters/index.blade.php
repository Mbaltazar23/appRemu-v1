@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">Centro de Costos</h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-5">
                <form id="costCenterForm" action="{{ route('costcenters.store') }}" method="POST"
                    onsubmit="return validateForm(event)">
                    @csrf
                    <!-- Fila 1 (2 primeros select) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="school" class="form-label">Colegio</label>
                            <select name="school" id="school" class="form-control">
                                <option value="">*** Seleccionar colegio ***</option>
                                @foreach ($schools as $school)
                                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="item" class="form-label">Item</label>
                            <select name="item" id="item" class="form-control">
                                <option value="">*** Seleccionar item ***</option>
                                @foreach ($itemOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Fila 2 (2 select restantes) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="periodo" class="form-label">Período</label>
                            <select name="periodo" id="periodo" class="form-control">
                                <option value="">*** Seleccione un período ***</option>
                                @foreach ($periodOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="year" class="form-label">Año</label>
                            <select name="year" id="year" class="form-control">
                                <option value="0">*** Seleccionar año ***</option>
                                @foreach ($distincYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Botón de acción -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>

                <!-- Mostrar mensaje de error en caso de validación fallida -->
                <div id="error-message" class="alert alert-danger mt-3" style="display: none;">
                    Todos los campos son obligatorios.
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom_scripts')
    <script>
        // Función para crear un campo de formulario oculto
        const createHiddenInput = (name, value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            return input;
        };

        // Función principal para validar el formulario
        function validateForm(event) {
            event.preventDefault(); // Evita que el formulario se envíe de inmediato

            // Recuperar los valores de los campos
            const school = document.getElementById('school').value;
            const item = document.getElementById('item').value;
            const periodo = document.getElementById('periodo').value;
            const year = document.getElementById('year').value;

            // Validar si los campos están vacíos
            if (!school || !item || !periodo || year === "0") {
                document.getElementById('error-message').style.display = 'block';
                return false; // Evita el envío del formulario
            } else {
                document.getElementById('error-message').style.display = 'none';
                // Crear el formulario dinámico
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('costcenters.store') }}';
                form.target = 'popupWindow'; // Define la ventana emergente como destino
                // CSRF token
                form.appendChild(createHiddenInput('_token', '{{ csrf_token() }}'));
                // Campos del formulario
                form.appendChild(createHiddenInput('school', school));
                form.appendChild(createHiddenInput('item', item));
                form.appendChild(createHiddenInput('periodo', periodo));
                form.appendChild(createHiddenInput('year', year));
                // Crear la ventana emergente
                const popupWindow = window.open('', 'popupWindow', 'width=820,height=600,scrollbars=yes');
                // Enviar el formulario en la ventana emergente
                document.body.appendChild(form);
                form.submit();
                return true; // Permite el envío del formulario
            }
        }
    </script>
@endpush
