@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3>{{ __('Mantenedor de bonos y descuentos por trabajador') }}</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-group mb-4">
                        <label for="worker_id" class="h5">
                            Acá se colocan los montos de los bonos o descuentos que fueron
                            definidos como dependientes en forma fija del trabajador
                        </label>
                        <select name="worker_id" id="worker_id" class="form-control" required
                            onchange="fetchWorkerParameters(this.value)">
                            <option value="">Seleccionar trabajador...</option>
                            @foreach ($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }} {{ $worker->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Botón para regresar al índice de bonos -->
                    <a href="{{ route('bonuses.index') }}" class="btn btn-secondary">
                        Regresar al Inicio
                    </a>
                </div>
            </div>
        </div>

        <!-- Contenedor donde se mostrarán los detalles del trabajador después de seleccionar uno -->
        <div id="worker-details"></div>
    </div>
</div>
@endsection


@push('custom_scripts')
    <script>
        function fetchWorkerParameters(workerId) {
            if (!workerId) {
                document.getElementById('worker-details').innerHTML = '';
                return;
            }

            axios.get(`/api/workers/${workerId}/parameters`)
                .then(response => {
                    const data = response.data;
                    let html = `<div id="parameter-card" class="card mt-4">
                                    <div class="card-header">
                                        <h3>Datos del Trabajador y sus Bonos Asociados</h3>
                                    </div>
                                    <form method="POST" action="{{ route('bonuses.updateBonus') }}" id="worker-form">
                                        @csrf
                                        <input type="hidden" name="worker_id" id="worker_id" value="${workerId}">
                                        <div class="card-body" id="worker-parameters-body">
                                            <table class="table">
                                                <tbody>
                                                    <!-- Fila para el nombre y tipo de trabajador -->
                                                    <tr>
                                                        <td><strong>Nombre:</strong></td>
                                                        <td>${data.name}</td>
                                                        <td><strong>Tipo trabajador:</strong></td>
                                                        <td>${data.type}</td>
                                                    </tr>`;

                    // Agregar los inputs de bonos como filas de tabla
                    if (data.bonuses.length > 0) {
                        data.bonuses.forEach((bonus, index) => {
                            if (bonus.aplicable == 1) {
                                // Si hay un solo bono, ocupa el mismo espacio que los textos de nombre y tipo de trabajador
                                if (data.bonuses.length === 1) {
                                    html += `<tr>
                            <td colspan="2"><label for="bonus${bonus.id}" class="form-label">${bonus.title}</label></td>
                            <td colspan="2"><input type='text' name='ID${bonus.id}' value='${bonus.value}' class="form-control" onblur='numerovalido(this)'></td>
                          </tr>`;
                                } else {
                                    // Si hay más de un bono, agrega una nueva fila por cada bono
                                    html += `<tr>
                            <td><label for="bonus${bonus.id}" class="form-label">${bonus.title}</label></td>
                            <td><input type='text' name='ID${bonus.id}' value='${bonus.value}' class="form-control form-control-lg" onblur='numerovalido(this)'></td>
                          </tr>`;
                                }
                            } else {
                                // Si no existen bonos, agregar un mensaje indicando que no hay bonos asociados
                                html += `<tr>
                <td colspan="4" class="text-center text-muted">No existen bonos o descuentos para este trabajador.</td>
             </tr>`;
                            }
                        });
                    }

                    html += `</tbody>
                            </table>`;

                    if (data.bonuses.length > 0) {
                        html += "<div class='d-flex justify-content-end'>";
                        html += "<button type='submit' id='submit-button' class='btn btn-primary'>Modificar</button>";
                        html += "</div>";
                        html +=
                            "<div class='text-center'><small class='text-muted'>(*) Campos son obligatorios</small></div>";
                    } else {
                        html += "<p>No existen bonos o descuentos para este trabajador.</p>";
                    }

                    html += `</div>
                            </form>
                        </div>`; // Cierre del card
                    document.getElementById('worker-details').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching worker parameters:', error);
                });
        }

        function numerovalido(input) {
            const value = input.value;

            // Verifica si el valor es un número
            if (isNaN(value) || value.trim() === '') {
                // Muestra un mensaje de error
                alert('Por favor, ingrese un número válido.');

                // Limpia el campo
                input.value = '';
                input.focus();
            }
        }

        // Ejecutar fetchWorkerParameters al cargar la página si el worker_id está en la sesión
        window.onload = function() {
            const workerId = "{{ session('worker_id') }}";
            if (workerId) {
                fetchWorkerParameters(workerId);
                document.getElementById('worker_id').value = workerId;
            }
        }
    </script>
@endpush

@push('custom_styles')
    <style>
        .form-control-lg {
            width: 100%;
            /* Asegura que los inputs ocupen todo el ancho disponible */
            max-width: 100%;
            /* Asegura que no se desborden */
        }

        .table td,
        .table th {
            vertical-align: middle;
            /* Alinea el contenido de las celdas al centro */
        }

        .form-label {
            font-weight: 500;
            /* Mejora la legibilidad de los labels */
        }

        .card-body {
            padding: 1.5rem;
            /* Espaciado adecuado para el contenido dentro del card */
        }
    </style>
@endpush
