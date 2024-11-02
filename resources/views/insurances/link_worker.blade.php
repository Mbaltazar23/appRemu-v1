@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">
                Vincular Trabajador a ({{ $insurance->name }})
            </h2>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card p-4">
                <div class="form-group mb-4">
                    <label for="worker_id" class="h5">Selecciona un Trabajador a Asociar al Seguro</label>
                    <select name="worker_id" id="worker_id" class="form-control" required
                        onchange="fetchWorkerParameters(this.value)">
                        <option value="">Seleccionar trabajador...</option>
                        @foreach ($workers as $worker)
                            <option value="{{ $worker->id }}">{{ $worker->name }} {{ $worker->last_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="parameter-card" class="card mt-4" style="display:none;">
                <div class="card-header">
                    <h3>Datos del Trabajador</h3>
                </div>
                <form method="POST" action="{{ route('insurances.attach_worker', $insurance->id) }}" id="worker-form">
                    @csrf
                    <input type="hidden" name="type" id="type" value="{{ $insurance->type }}">
                    <input type="hidden" name="worker_id" id="worker_id_hidden">

                    <div class="card-body" id="worker-parameters-body">
                        <!-- Parámetros se insertarán aquí -->
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" id="submit-button" class="btn btn-primary"
                            style="display:none;">Vincular</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('custom_scripts')
    <script>
        function fetchWorkerParameters(workerId) {
            if (!workerId) {
                document.getElementById('worker-parameters-body').innerHTML = '';
                document.getElementById('submit-button').style.display = 'none';
                document.getElementById('parameter-card').style.display = 'none';
                document.getElementById('worker_id_hidden').value = ''; // Limpiar el campo oculto
                return;
            }

            // Establecer el valor del campo oculto
            document.getElementById('worker_id_hidden').value = workerId;

            fetch(`/insurances/${workerId}/${document.getElementById('type').value}/parameters`)
                .then(response => response.json())
                .then(data => {
                    let html = '<div class="row">';

                    if (data.success) {
                        // Verificar si el trabajador tiene parámetros de AFP o ISAPRE
                        const hasParameters = (data.insuranceType === '1' && data.cotizacionAFP) || (data
                            .insuranceType === '2' && data.institucionSalud);

                        if (hasParameters) {
                            const confirmEdit = confirm('Este trabajador ya tiene una AFP ¿Deseas Modificarla?');
                            if (!confirmEdit) {
                                location.reload(); // Recargar la página si se cancela
                                return;
                            }
                        }

                        if (data.insuranceType === '1') {
                            html += ` 
                            <div class="col-md-4 mb-3">
                                <label><strong>Tipo de Trabajador:</strong> ${data.workerType}</label>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label><strong>Cotización AFP:</strong></label>
                                <input type="text" name="cotization_afp" value="${data.cotizationAFP || ''}" class="form-control" onchange="checkModification('cotization_afp', '${data.cotizationAFP || ''}')"/>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label><strong>APV:</strong></label>
                                <input type="text" name="apv" value="${data.apv || ''}" class="form-control" onchange="checkModification('apv', '${data.apv || ''}')"/>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label><strong>Otros Descuentos:</strong></label>
                                <input type="text" name="others_discounts" value="${data.othersDiscounts || ''}" class="form-control" onchange="checkModification('others_discounts', '${data.othersDiscounts || ''}')"/>
                            </div>
                        `;
                        } else {
                            html += `
                            <div class="col-md-4 mb-3">
                                <label><strong>Institución de Salud:</strong></label>
                                <input type="text" name="institution_health" value="${data.institutionHealth || ''}" class="form-control" onchange="checkModification('institution_health', '${data.institutionHealth || ''}')"/>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label><strong>Precio Plan:</strong></label>
                                <input type="text" name="price_plan" value="${data.pricePlan || ''}" class="form-control" onchange="checkModification('price_plan', '${data.pricePlan || ''}')"/>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label><strong>Unidad:</strong></label>
                                <select name="unit" id="unit" class="form-control" onchange="checkModification('unit', '${data.unit || ''}')">
                                    <option value="UF" ${data.unit === 'UF' ? 'selected' : ''}>UF</option>
                                    <option value="Pesos" ${data.unit === 'Pesos' ? 'selected' : ''}>Pesos</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label><strong>Cotización:</strong></label>
                                <input type="text" name="cotization_health" value="${data.cotizacionSalud || ''}" class="form-control" onchange="checkModification('cotization_health', '${data.cotizacionSalud || ''}')"/>
                            </div>
                        `;
                        }
                        html += '</div>';
                        document.getElementById('worker-parameters-body').innerHTML = html;

                        document.getElementById('submit-button').innerText =
                            (data.cotizacionAFP || data.apv || data.othersDiscounts || data.institucionSalud || data
                                .precioPlan || data.cotizacionSalud) ?
                            'Modificar' :
                            'Vincular';

                        document.getElementById('submit-button').style.display = 'inline-block';
                        document.getElementById('parameter-card').style.display = 'block';
                    } else {
                        html += '<p>No se encontraron parámetros para este trabajador.</p>';
                        document.getElementById('worker-parameters-body').innerHTML = html;
                        document.getElementById('submit-button').style.display = 'none';
                        document.getElementById('parameter-card').style.display = 'none';
                    }
                });
        }

        let modifiedFields = {};

        function checkModification(fieldName, oldValue) {
            const input = document.querySelector(`[name="${fieldName}"]`);
            const newValue = input.value;

            if (oldValue && oldValue !== newValue) {
                if (confirm('Este trabajador ya posee datos previos. ¿Deseas modificarlo?')) {
                    // Si el usuario confirma, se enviará el formulario
                    document.getElementById('worker-form').submit();
                } else {
                    // Si el usuario cancela, revertimos el valor al antiguo
                    input.value = oldValue;
                }
            }
        }
    </script>
@endpush
