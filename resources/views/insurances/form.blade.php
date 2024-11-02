<div class="table-responsive">
    <div class="row col-12 mb-2">
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="rut" class="form-label">RUT</label>
                <input type="text" class="form-control" name="rut" id="rut"
                    value="{{ old('rut', $insurance->rut ?? '') }}" maxlength="12" required>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="name"
                    value="{{ old('name', $insurance->name ?? '') }}" required>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="cotizacion" class="form-label">Cotización</label>
                <input type="text" class="form-control" name="cotizacion" id="cotizacion"
                    value="{{ old('cotizacion', $insurance->cotizacion ?? '') }}" required
                    placeholder="Ejemplo: 1234.56 o 1.234,56">
            </div>
        </div>
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
    </script>
@endpush
