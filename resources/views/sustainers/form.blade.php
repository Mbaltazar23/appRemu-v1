<div class="table-responsive">
    <div class="row col-12 mb-2">
        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="rut" class="form-label">RUT</label>
                <input type="text" class="form-control" name="rut" id="rut"
                    value="{{ old('rut', $sustainer->rut ?? '') }}" maxlength="12" required>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="business_name" class="form-label">Razón Social</label>
                <input type="text" class="form-control" name="business_name"
                    value="{{ old('business_name', $sustainer->business_name ?? '') }}" required>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="address" class="form-label">Dirección</label>
                <input type="text" class="form-control" name="address"
                    value="{{ old('address', $sustainer->address ?? '') }}" required>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="commune" class="form-label">Comuna</label>
                <select id="commune" class="form-select" name="commune">
                    <option value="">Seleccione una comuna</option>
                    @foreach (config('communes_region.COMMUNE_OPTIONS') as $commune)
                        <option value="{{ $commune }}"
                            {{ (old('commune') ?? $sustainer->commune) == $commune ? 'selected' : '' }}>
                            {{ $commune }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="region" class="form-label">Región</label>
                <select id="region" class="form-select" name="region">
                    <option value="">Seleccione una región</option>
                    @foreach (config('communes_region.REGIONES_OPTIONS') as $region)
                        <option value="{{ $region }}"
                            {{ (old('region') ?? $sustainer->region) == $region ? 'selected' : '' }}>
                            {{ $region }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="legal_nature" class="form-label">Naturaleza Jurídica</label>
                <input type="text" class="form-control" name="legal_nature"
                    value="{{ old('legal_nature', $sustainer->legal_nature ?? '') }}" required>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="legal_representative" class="form-label">Representante Legal</label>
                <input type="text" class="form-control" name="legal_representative"
                    value="{{ old('legal_representative', $sustainer->legal_representative ?? '') }}" required>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="rut_legal_representative" class="form-label">RUT del Representante Legal</label>
                <input type="text" class="form-control" name="rut_legal_representative" id="rut_legal_representative"
                    maxlength="12"
                    value="{{ old('rut_legal_representative', $sustainer->rut_legal_representative ?? '') }}" required>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="phone" class="form-label">Teléfono</label>
                <input type="text" class="form-control" name="phone"
                    value="{{ old('phone', $sustainer->phone ?? '') }}">
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email"
                    value="{{ old('email', $sustainer->email ?? '') }}">
            </div>
        </div>
    </div>
</div>

@push('custom_scripts')
    <script>
        function validadorRut(txtRut) {
            document.getElementById(txtRut).addEventListener('input', function(evt) {
                let value = this.value.replace(/\./g, '').replace('-', '');

                // Limitar a 12 caracteres (formato completo)
                if (value.length > 12) {
                    this.value = this.value.substring(0, 12);
                    return;
                }
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
        validadorRut('rut_legal_representative');
    </script>
@endpush
