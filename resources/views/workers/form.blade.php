    <input type="hidden" name="school_id" value="{{ auth()->user()->school_id_session }}" />

    <!-- Datos del Trabajador -->
    <h3 class="mb-4" style="font-weight: bold; color: #333;">Datos del Trabajador</h3>

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="rut" class="form-label" style="opacity: 0.7;">RUT</label>
            <input id="rut" type="text" class="form-control" name="rut"
                value="{{ old('rut') ?? $worker->rut }}" maxlength="15" />
        </div>
        <div class="col-md-4">
            <label for="name" class="form-label" style="opacity: 0.7;">Nombre</label>
            <input id="name" type="text" class="form-control" name="name"
                value="{{ old('name') ?? $worker->name }}" />
        </div>
        <div class="col-md-4">
            <label for="last_name" class="form-label" style="opacity: 0.7;">Apellido</label>
            <input id="last_name" type="text" class="form-control" name="last_name"
                value="{{ old('last_name') ?? $worker->last_name }}" />
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="address" class="form-label" style="opacity: 0.7;">Dirección</label>
            <input id="address" type="text" class="form-control" name="address"
                value="{{ old('address') ?? $worker->address }}" />
        </div>
        <div class="col-md-4">
            <label for="commune" class="form-label" style="opacity: 0.7;">Comuna</label>
            <select id="commune" class="form-select" name="commune">
                <option value="">Seleccione una comuna</option>
                @foreach (config('communes_region.COMMUNE_OPTIONS') as $commune)
                    <option value="{{ $commune }}"
                        {{ (old('commune') ?? $worker->commune) == $commune ? 'selected' : '' }}>
                        {{ $commune }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="region" class="form-label" style="opacity: 0.7;">Región</label>
            <select id="region" class="form-select" name="region">
                <option value="">Seleccione una región</option>
                @foreach (config('communes_region.REGIONES_OPTIONS') as $region)
                    <option value="{{ $region }}"
                        {{ (old('region') ?? $worker->region) == $region ? 'selected' : '' }}>
                        {{ $region }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="nationality" class="form-label" style="opacity: 0.7;">Nacionalidad</label>
            <input id="nationality" type="text" class="form-control" name="nationality"
                value="{{ old('nationality') ?? $worker->nationality }}" />
        </div>
        <div class="col-md-4">
            <label for="phone" class="form-label" style="opacity: 0.7;">Teléfono</label>
            <input id="phone" type="text" class="form-control" name="phone"
                value="{{ old('phone') ?? $worker->phone }}" />
        </div>
        <div class="col-md-4">
            <label for="num_load_family" class="form-label" style="opacity: 0.7;">N° Cargas Familiar</label>
            <input id="num_load_family" type="number" class="form-control" name="num_load_family"
                value="{{ old('num_load_family') ?? optional($worker->parameters->where('name', 'CARGASFAMILIARES')->first())->value }}" />
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="birth_date" class="form-label" style="opacity: 0.7;">Fecha de Nacimiento</label>
            <input id="birth_date" type="date" class="form-control" name="birth_date"
                value="{{ old('birth_date') ?? $worker->birth_date }}" />
        </div>
        <div class="col-md-4">
            <label for="marital_status" class="form-label" style="opacity: 0.7;">Estado civil</label>
            <select id="marital_status" class="form-select" name="marital_status">
                @foreach ($maritalStatus as $key => $type)
                    <option value="{{ $key }}"
                        {{ (old('marital_status') ?? $worker->marital_status) == $key ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="worker_type" class="form-label" style="opacity: 0.7;">Tipo de Trabajador</label>
            <select id="worker_type" class="form-select" name="worker_type" onchange="toggleInputs()">
                <option value="">Seleccione un tipo de trabajador</option>
                @foreach ($workerTypes as $key => $type)
                    <option value="{{ $key }}"
                        {{ (old('worker_type') ?? $worker->worker_type) == $key ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="function_worker" class="form-label" style="opacity: 0.7;">Función del Trabajador</label>
            <select id="function_worker" class="form-select" name="function_worker">
                <option value="">Seleccione una función</option>
                @foreach ($functionWorkerTypes as $key => $description)
                    <option value="{{ $key }}"
                        {{ (old('function_worker') ?? $worker->function_worker) == $key ? 'selected' : '' }}>
                        {{ $description }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Seleccione la función del trabajador.</small>
        </div>
    </div>

    <!-- Datos del Contrato -->
    <h3 class="mb-4" style="font-weight: bold; color: #333;">Datos del Contrato</h3>

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="hire_date" class="form-label" style="opacity: 0.7;">Fecha Inicio Contrato</label>
            <input id="hire_date" type="date" class="form-control" name="hire_date"
                value="{{ old('hire_date') ?? optional($worker->contract)->hire_date }}" />
        </div>
        <div class="col-md-4">
            <label for="termination_date" class="form-label" style="opacity: 0.7;">Fecha Término Contrato</label>
            <input id="termination_date" type="date" class="form-control" name="termination_date"
                value="{{ old('termination_date') ?? optional($worker->contract)->termination_date }}" />
        </div>
        <div class="col-md-4">
            <label for="worker_titular" class="form-label" style="opacity: 0.7;">Trabajador Titular</label>
            <select id="worker_titular" class="form-select" name="worker_titular">
                <option value="">Seleccione un trabajador titular</option>
                @foreach ($workers as $titular)
                    <option value="{{ $titular->id }}"
                        {{ (old('worker_titular') ?? $worker->worker_titular) == $titular->id ? 'selected' : '' }}>
                        {{ $titular->name }} {{ $titular->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <label for="replacement_reason" class="form-label" style="opacity: 0.7;">Motivo de Reemplazo</label>
            <input id="replacement_reason" type="text" class="form-control" name="replacement_reason"
                value="{{ old('replacement_reason') ?? optional($worker->contract)->replacement_reason }}" />
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="hourly_load" class="form-label" style="opacity: 0.7;">Carga Horaria</label>
            <input id="hourly_load" type="number" class="form-control" name="hourly_load" min="1"
                max="45"
                value="{{ old('hourly_load') ?? optional($worker->parameters->where('name', 'CARGAHORARIA')->first())->value }}" />
        </div>
        <div class="col-md-4">
            <label for="contract_type" class="form-label" style="opacity: 0.7;">Tipo de Contrato</label>
            <select id="contract_type" class="form-select" name="contract_type">
                <option value="">Seleccione un tipo de contrato</option>
                @foreach ($contractTypes as $key => $type)
                    <option value="{{ $key }}"
                        {{ (old('contract_type') ?? optional($worker->contract)->contract_type) == $key ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label">Carga Horaria por Día</label>
        <div class="row" id="horas_dia">
            @foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'] as $day)
                <div class="col-md-2 mb-3">
                    <label class="form-label">{{ ucfirst($day) }}</label>
                    <input type="number" id="carga_{{ $day }}" name="carga_{{ $day }}"
                        class="form-control" value="{{ json_decode($worker->load_hourly_work)->$day ?? '' }}" />
                </div>
            @endforeach
        </div>
        <small class="form-text text-muted">En caso de que el trabajador sea docente inserte la carga horaria por día
            (en horas).</small>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="unemployment_insurance" class="form-label" style="opacity: 0.7;">Adhiere a seguro de
                cesantía?</label>
            <select id="unemployment_insurance" class="form-select" name="unemployment_insurance">
                <option value="1"
                    {{ (old('unemployment_insurance') ?? optional($worker->parameters->where('name', 'ADHIEREASEGURO')->first())->value) == 1 ? 'selected' : '' }}>
                    Sí</option>
                <option value="0"
                    {{ (old('unemployment_insurance') ?? optional($worker->parameters->where('name', 'ADHIEREASEGURO')->first())->value) == 0 ? 'selected' : '' }}>
                    No</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="retired" class="form-label" style="opacity: 0.7;">¿Es Jubilado?</label>
            <select id="retired" class="form-select" name="retired">
                <option value="0"
                    {{ (old('retired') ?? optional($worker->parameters->where('name', 'JUBILADO')->first())->value) == 0 ? 'selected' : '' }}>
                    No</option>
                <option value="1"
                    {{ (old('retired') ?? optional($worker->parameters->where('name', 'JUBILADO')->first())->value) == 1 ? 'selected' : '' }}>
                    Sí</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="service_start_year" class="form-label" style="opacity: 0.7;">Año inicio labor docente (si
                corresponde)</label>
            <input id="service_start_year" type="text" class="form-control" name="service_start_year"
                maxlength="4"
                value="{{ old('service_start_year') ?? optional($worker->parameters->where('name', 'YEARINICIOSERVICIO')->first())->value }}" />
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <label for="base_salary" class="form-label" style="opacity: 0.7;">Sueldo base (en caso de ser un
                trabajador no docente)</label>
            <input id="base_salary" type="text" class="form-control" name="base_salary" maxlength="7"
                value="{{ old('base_salary') ?? optional($worker->parameters->where('name', 'SUELDOBASEB')->first())->value }}"
                onblur="if (!isInteger(this.value)) { alert('El número es inválido'); this.focus(); }" />
        </div>
    </div>

    @push('custom_scripts')
        <script>
            function validadorRut(txtRut) {
                document.getElementById(txtRut).addEventListener('input', function(evt) {
                    let value = this.value.replace(/\./g, '').replace('-', '');
                    if (value.match(/^(\d{2})(\d{3}){2}(\w{1})$/)) {
                        value = value.replace(/^(\d{2})(\d{3})(\d{3})(\w{1})$/, '$1.$2.$3-$4');
                    } else if (value.match(/^(\d)(\d{3}){2}(\w{0,1})$/)) {
                        value = value.replace(/^(\d)(\d{3})(\d{3})(\w{0,1})$/, '$1.$2.$3-$4');
                    } else if (value.match(/^(\d)(\d{3})(\d{0,2})$/)) {
                        value = value.replace(/^(\d)(\d{3})(\d{0,2})$/, '$1.$2.$3');
                    } else if (value.match(/^(\d)(\d{0,2})$/)) {
                        value = value.replace(/^(\d)(\d{0,2})$/, '$1.$2');
                    }
                    this.value = value;
                });
            }
            // Llama a la función para validar el RUT
            validadorRut('rut');
        </script>
    @endpush
