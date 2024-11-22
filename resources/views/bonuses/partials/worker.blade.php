@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3>{{ __('Mantenedor de bonos y descuentos por trabajador') }}</h3>
                </div>
                <div class="card-body">
                    <!-- Contenedor para el select del trabajador y el botón de regresar en la misma línea -->
                    <div class="d-flex justify-content-between align-items-center" style="flex-wrap: wrap;">
                        <!-- Contenedor para el select con más espacio entre el botón -->
                        <div class="form-group mb-4" style="flex-grow: 1; margin-right: 20px; max-width: 100%;">
                            <label for="worker_id" class="h5">
                                Acá se colocan los montos de los bonos o descuentos que fueron
                                definidos como dependientes en forma fija del trabajador
                            </label>
                            <select name="worker_id" id="worker_id" class="form-control" required
                                onchange="fetchWorkerParameters(this.value)">
                                <option value="">Seleccionar trabajador...</option>
                                @foreach ($workers as $worker)
                                    <option value="{{ $worker->id }}">{{ $worker->name }} {{ $worker->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Botón Regresar -->
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
                            html += `<tr>
                                      <td><label for="bonus${bonus.id}" class="form-label">${capitalizeFirstLetters(bonus.title)}</label></td>
                                        <td colspan="${data.bonuses.length === 1 ? 3 : 2}">
                                            <div class="input-container">
                                                <input type='text' name='ID${bonus.id}' value='${bonus.value}' class="form-control" onblur='numerovalido(this)'>
                                            </div>
                                        </td>
                                    </tr>`;
                        }
                    });
                } else {
                    html += `<tr>
                                <td colspan="4" class="text-center text-muted" style="padding-top: 20px;">
                                    No existen bonos a designar para este trabajador o todos sus bonos son fijos.
                                </td>
                                </tr>`;
                }

                html += `</tbody>
                        </table>`;

                if (data.bonuses.length > 0) {
                    html += "<div class='d-flex justify-content-end'>";
                    html += "<button type='submit' id='submit-button' class='btn btn-primary'>Modificar</button>";
                    html += "</div>";
                    html +=
                        "<div class='text-center'><small class='text-muted'>(*) Campos son obligatorios</small></div>";
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

        function capitalizeFirstLetters(str) {
            return str
                .split(' ') // Divide el texto por los espacios
                .map(word => word.charAt(0).toUpperCase() + word.slice(1)
            .toLowerCase()) // Convierte la primera letra a mayúscula y el resto a minúscula
                .join(' '); // Vuelve a juntar las palabras con espacios
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
        .form-control {
            width: 100%;
            max-width: 100%;
        }

        .input-container {
            width: 80%;
            /* Ajusta el ancho del contenedor del input */
            margin: 0 auto;
            /* Centra el input */
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .form-label {
            font-weight: 500;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Opcional: para mejorar la legibilidad del mensaje */
        .text-muted {
            font-size: 1rem;
            font-weight: 500;
        }

        /* Añadir espacio adicional en la fila de "No hay bonos" */
        .text-muted {
            padding-top: 20px;
        }

        /* Ajustar el ancho de los inputs de bono cuando haya más de uno */
        .input-container input {
            width: 75%;
            /* Ajustar el ancho del input para que no esté tan grande */
            max-width: 75%;
        }

        /* Asegura que el select y el botón estén en la misma línea */
        .d-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            /* Añadir espacio entre el select y el botón */
        }

        /* Para el select, darle más espacio a los lados */
        .form-group select {
            width: 100%;
            /* Asegura que el select ocupe todo el ancho */
            max-width: 600px;
            /* Limitar el ancho máximo */
        }

        /* Responsividad: Asegurarse que en pantallas pequeñas se mantenga todo bien */
        @media (max-width: 767px) {
            .d-flex {
                flex-direction: column;
                align-items: stretch;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .btn {
                margin-top: 10px;
            }

            .input-container input {
                width: 100%;
                /* En pantallas pequeñas, el input de bono debe ocupar todo el espacio */
            }
        }
    </style>
@endpush
