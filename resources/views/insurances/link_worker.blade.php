@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                Trabajadores a Asociar al Seguro ({{ $insurance->name }})
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <!-- Contenido principal -->
            <div class="card p-4">
                <div class="form-group mb-4">
                    <label for="worker_id_select" class="h5">Selecciona un Trabajador</label>
                    <select name="worker_id_select" id="worker_id_select" class="form-control" required>
                        <option value="">Seleccionar trabajador...</option>
                        @foreach ($workers as $worker)
                            <option value="{{ $worker->id }}">
                                {{ $worker->name }} {{ $worker->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botón de acción -->
                <div class="action-btn">
                    <form id="associateWorkerForm" action="{{ route('insurances.attach_worker', $insurance->id) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="type" id="type" value="{{ $type }}">
                        <input type="hidden" name="insurance_id" id="insurance_id" value="{{ $insurance->id }}">
                        <input type="hidden" name="worker_id" id="worker_id_input">
                        <input type="hidden" name="force_update" id="force_update" value="false">
                        <button id="submitButton" type="submit" class="btn btn-primary rounded-3 px-4">
                            Asociar Trabajador
                        </button>
                    </form>
                </div>

                <!-- Contenedor para mensajes dinámicos -->
                <div id="messages" class="mt-4"></div>
            </div>
        </div>
    </div>
@endsection

@push('custom_scripts')
    <script>
        document.getElementById('worker_id_select').addEventListener('change', function () {
            const workerId = this.value;
            document.getElementById('worker_id_input').value = workerId;
        });

        // Manejar el envío del formulario
        function submitForm(event) {
            event.preventDefault();
            const form = document.getElementById('associateWorkerForm');
            const formData = new FormData(form);
            const messagesContainer = document.getElementById('messages');
            const submitButton = document.getElementById('submitButton');
            submitButton.disabled = true;

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                messagesContainer.innerHTML = ''; // Limpiar mensajes previos

                if (data.confirm) {
                    const confirmMessage = document.createElement('div');
                    confirmMessage.className = 'alert alert-warning d-flex justify-content-between align-items-center';
                    confirmMessage.innerHTML = `
                        <span>${data.message}</span>
                        <div>
                            <button id="confirmButton" class="btn btn-primary btn-sm me-2">Confirmar</button>
                            <button id="cancelButton" class="btn btn-secondary btn-sm">Cancelar</button>
                        </div>
                    `;

                    messagesContainer.appendChild(confirmMessage);

                    document.getElementById('confirmButton').addEventListener('click', () => {
                        document.getElementById('force_update').value = 'true';
                        submitForm(event);
                    });

                    document.getElementById('cancelButton').addEventListener('click', () => {
                        messagesContainer.innerHTML = ''; // Limpiar mensajes y no hacer nada
                    });
                } else {
                    const successMessage = document.createElement('div');
                    successMessage.className = 'alert alert-success';
                    successMessage.textContent = data.message;
                    messagesContainer.appendChild(successMessage);

                    setTimeout(() => {
                        const redirectUrl = `{{ url('insurances') }}?insurance_id=${formData.get('insurance_id')}&worker_id=${formData.get('worker_id')}&type=${formData.get('type')}`;
                        window.location.href = redirectUrl;
                    }, 2000);
                }
            })
            .catch(error => {
                const errorMessage = document.createElement('div');
                errorMessage.className = 'alert alert-danger';
                errorMessage.textContent = 'Ocurrió un error al procesar la solicitud.';
                messagesContainer.appendChild(errorMessage);
            })
            .finally(() => {
                submitButton.disabled = false;
            });
        }

        // Agregar listener al formulario
        document.getElementById('associateWorkerForm').addEventListener('submit', submitForm);
    </script>
@endpush
