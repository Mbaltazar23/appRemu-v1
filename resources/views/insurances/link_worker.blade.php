<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabajadores a Asociar al Seguro - {{ $insurance->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Estilos de la página -->
    @vite('resources/sass/app.scss')

    <!-- Custom Styles -->
    @stack('custom_styles')

    <style>
        /* Estilos personalizados */
        .container-xl {
            max-width: 800px;
            margin: auto;
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        /* Estilos para el botón de acción */
        .action-btn {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
    </style>
</head>

<body>
    <br><br>
    <!-- Contenedor principal -->
    <div class="container-xl">
        <!-- Título de la página -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                Trabajadores a Asociar al Seguro ({{ $insurance->name }})
            </h2>
        </div>

        <!-- Formulario para asociar trabajador -->
        <div class="card p-4">
            <div class="form-group mb-4">
                <label for="worker_id_select" class="h5">Selecciona un Trabajador</label>
                <select name="worker_id_select" id="worker_id_select" class="form-control" required>
                    <option value="">Seleccionar trabajador...</option>
                    @foreach ($workers as $worker)
                        <option value="{{ $worker->id }}" @if (request('worker_id') == $worker->id) selected @endif>
                            {{ $worker->name }} {{ $worker->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Botón de asociar trabajador -->
            <div class="action-btn">
                <form id="associateWorkerForm" action="{{ route('insurances.attach_worker', $insurance->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" id="type" value="{{ $type }}">
                    <input type="hidden" name="insurance_id" id="insurance_id" value="{{ $insurance->id }}">
                    <input type="hidden" name="worker_id" id="worker_id_input" value="{{ request('worker_id') }}">
                    <input type="hidden" name="force_update" id="force_update" value="false">
                    <button id="submitButton" type="submit" class="btn btn-primary rounded-3 px-4">
                        Asociar Trabajador
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <script>
        document.getElementById('worker_id_select').addEventListener('change', function() {
            const workerId = this.value;
            document.getElementById('worker_id_input').value = workerId;
            localStorage.setItem('worker_id', workerId);
        });

        // Función que maneja el envío del formulario
        function submitForm(event) {
            event.preventDefault(); // Prevenir el envío por defecto
            const form = document.getElementById('associateWorkerForm');
            const formData = new FormData(form);

            // Deshabilitar el botón mientras se procesa
            const submitButton = document.getElementById('submitButton');
            submitButton.disabled = true;

            // Hacer la solicitud al backend con los datos del formulario
            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then(response => response.json())  // Convierte la respuesta a JSON
                .then(data => {
                    if (data.confirm) {
                        // Si se necesita confirmación, mostrar el mensaje
                        if (window.confirm(data.message)) {
                            document.getElementById('force_update').value = "true"; // Marcamos la actualización forzada
                            submitForm(event); // Reenvía el formulario si es necesario
                        }
                    } else {
                        // Si no se necesita confirmación, manejar el mensaje de éxito
                        alert(data.message);

                        // Redirigir según la lógica que ya tienes
                        let insuranceId = document.getElementById('insurance_id').value;
                        let workerId = document.getElementById('worker_id_input').value;
                        let type = document.getElementById('type').value;
                        let redirectUrl =
                            `{{ url('insurances') }}?insurance_id=${insuranceId}&worker_id=${workerId}&type=${type}`;

                        // Verificar si la ventana tiene un "opener"
                        if (window.opener) {
                            window.opener.location.href = redirectUrl;
                            window.close();
                        } else {
                            window.location.href = redirectUrl;
                        }
                    }
                })
                .catch(error => {
                    alert('Ocurrió un error al procesar la solicitud.');
                })
                .finally(() => {
                    // Volver a habilitar el botón después de la respuesta
                    submitButton.disabled = false;
                });
        }

        // Agregar el listener al formulario
        document.getElementById('associateWorkerForm').addEventListener('submit', submitForm);
    </script>
</body>

</html>
