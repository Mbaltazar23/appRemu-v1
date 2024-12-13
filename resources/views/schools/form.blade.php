    <!-- Fila para Nombre, RBD y RUT -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="rut" class="form-label">RUT</label>
            <input id="rut" type="text" class="form-control" name="rut" placeholder="RUT de la escuela"
                value="{{ old('rut') ?? $school->rut }}" maxlength="12" />
        </div>
        <div class="col-md-4">
            <label for="name" class="form-label">Nombre</label>
            <input id="name" type="text" class="form-control" name="name" placeholder="Nombre de la escuela"
                value="{{ old('name') ?? $school->name }}" />
        </div>
        <div class="col-md-4">
            <label for="rbd" class="form-label">RBD (RUT del establecimiento)</label>
            <input id="rbd" type="text" class="form-control" name="rbd"
                value="{{ old('rbd') ?? $school->rbd }}" />
        </div>
    </div>

    <!-- Campo Dirección -->
    <div class="mb-3">
        <label for="address" class="form-label">Dirección</label>
        <textarea id="address" class="form-control" name="address" placeholder="Dirección de la escuela" rows="3">{{ old('address') ?? $school->address }}</textarea>
    </div>

    <!-- Fila para Comuna y Región -->
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="commune" class="form-label">Comuna</label>
            <select id="commune" class="form-select" name="commune">
                <option value="">Seleccione una comuna</option>
                @foreach (config('communes_region.COMMUNE_OPTIONS') as $commune)
                    <option value="{{ $commune }}"
                        {{ (old('commune') ?? $school->commune) == $commune ? 'selected' : '' }}>
                        {{ $commune }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="region" class="form-label">Región</label>
            <select id="region" class="form-select" name="region">
                <option value="">Seleccione una región</option>
                @foreach (config('communes_region.REGIONES_OPTIONS') as $region)
                    <option value="{{ $region }}"
                        {{ (old('region') ?? $school->region) == $region ? 'selected' : '' }}>
                        {{ $region }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Fila para Director y RUT del Director -->
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="director" class="form-label">Director</label>
            <input id="director" type="text" class="form-control" name="director"
                value="{{ old('director') ?? $school->director }}" />
        </div>
        <div class="col-md-6">
            <label for="rut_director" class="form-label">RUT del Director</label>
            <input id="rut_director" type="text" class="form-control" name="rut_director"
                value="{{ old('rut_director') ?? $school->rut_director }}" />
        </div>
    </div>

    <!-- Fila para Teléfono, Correo, Dependencia y Subvención -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="phone" class="form-label">Teléfono</label>
            <input id="phone" type="text" class="form-control" name="phone"
                value="{{ old('phone') ?? $school->phone }}" />
        </div>
        <div class="col-md-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input id="email" type="email" class="form-control" name="email"
                value="{{ old('email') ?? $school->email }}" />
        </div>
        <div class="col-md-3">
            <label for="dependency" class="form-label">Dependencia</label>
            <select id="dependency" class="form-select" name="dependency">
                <option value="">Seleccione una dependencia</option>
                @foreach ($dependencyOptions as $key => $value)
                    <option value="{{ $key }}"
                        {{ (old('dependency') ?? $school->dependency) == $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="grantt" class="form-label">Subvención</label>
            <select id="grantt" class="form-select" name="grantt">
                <option value="">Seleccione una subvención</option>
                @foreach ($granttOptions as $key => $value)
                    <option value="{{ $key }}"
                        {{ (old('grantt') ?? $school->grantt) == $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Fila para Sostenedor -->
    <div class="row mb-4">
        <div class="col-md-12">
            <label for="sustainer_id" class="form-label">Sostenedor</label>
            <select id="sustainer_id" class="form-select" name="sustainer_id">
                <option value="">Seleccione un sostenedor</option>
                @foreach ($sustainers as $sustainer)
                    <option value="{{ $sustainer->id }}"
                        {{ (old('sustainer_id') ?? $school->sustainer_id) == $sustainer->id ? 'selected' : '' }}>
                        RUT: {{ $sustainer->rut }} - Nombre Comercial: {{ $sustainer->business_name }}
                    </option>
                @endforeach
            </select>
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
            validadorRut('rut');
            validadorRut('rut_director');
        </script>
    @endpush
