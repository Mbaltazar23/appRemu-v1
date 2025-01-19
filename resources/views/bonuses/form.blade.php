<div class="container">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="title" class="form-label">Título de ítem</label>
            <input type="text" class="form-control" id="title" name="title"
                value="{{ old('title') ?? ($bonus->school && $bonus->school->tuitions->where('tuition_id', $bonus->title)->first() ? $bonus->school->tuitions->where('tuition_id', $bonus->title)->first()->title : '') }}"
                required>
        </div>

        <div class="col-md-6 mb-3">
            <label for="type" class="form-label">Tipo de Trabajador</label>
            <select id="type" class="form-select" name="type">
                @foreach ($workerOptions as $key => $type)
                    <option value="{{ $key }}"
                        {{ (old('type') ?? ($bonus->type ?? '1')) == $key ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label for="is_bonus" class="form-label">¿Es un bono o un descuento?</label>
            <select class="form-select" id="is_bonus" name="is_bonus">
                <option value="0" {{ (old('is_bonus') ?? ($bonus->is_bonus ?? 0)) == 0 ? 'selected' : '' }}>Bono
                </option>
                <option value="1" {{ (old('is_bonus') ?? ($bonus->is_bonus ?? 0)) == 1 ? 'selected' : '' }}>
                    Descuento</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label for="taxable" class="form-label">¿Es imponible?</label>
            <select class="form-select" id="taxable" name="taxable">
                <option value="1" {{ (old('taxable') ?? ($bonus->taxable ?? 1)) == 1 ? 'selected' : '' }}>No
                </option>
                <option value="0" {{ (old('taxable') ?? ($bonus->taxable ?? 1)) == 0 ? 'selected' : '' }}>Sí
                </option>
            </select>
        </div>

        @if (
            (old('type') ?? ($bonus->type ?? 1)) == 1 ||
                ((old('type') ?? ($bonus->type ?? 1)) == 3 &&
                    (old('taxable') ?? ($bonus->taxable ?? 0)) == 0 &&
                    (old('is_bonus') ?? ($bonus->is_bonus ?? 0)) == 0))
            <div class="col-md-6 mb-3">
                <label for="imputable" class="form-label">¿Es imputable a la renta mínima?</label>
                <select class="form-select" id="imputable" name="imputable">
                    <option value="0" {{ (old('imputable') ?? ($bonus->imputable ?? 0)) == 0 ? 'selected' : '' }}>
                        Sí</option>
                    <option value="1" {{ (old('imputable') ?? ($bonus->imputable ?? 1)) == 1 ? 'selected' : '' }}>
                        No</option>
                </select>
            </div>
        @else
            <input type='hidden' name='imputable' value='{{ old('imputable', $bonus->imputable ?? '0') }}'>
        @endif

        <div class="col-md-6 mb-3">
            <label for="application" class="form-label">¿Cómo se aplica?</label>
            <select class="form-select" id="application" name="application">
                @foreach ($applicationOptions as $value => $label)
                    <option value="{{ $value }}"
                        {{ (old('application') ?? ($bonus->application ?? '')) == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label for="amount" class="form-label">Monto (en pesos)</label>
            <input type="number" class="form-control" id="amount" name="amount"
                value="{{ old('amount', $bonus->school ? $bonus->school->parameters->where('name', $bonus->tuition_id)->value('value') ?? '' : '') }}"
                step="0.01">
        </div>

        <div class="col-md-6 mb-3">
            <label for="factor" class="form-label">Porcentaje a aplicar (Ej. 8.33)</label>
            <input type="text" class="form-control" id="factor" name="factor"
                value="{{ old('factor', isset($bonus) ? $bonus->factor * 100 : '') }}" />
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label">Meses en los que se aplica</label><br>
            @for ($i = 1; $i <= 12; $i++)
                <div class="form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" name="months[]" value="{{ $i }}"
                        {{ isset($allChecked) && $allChecked[$i - 1] == '1' ? 'checked' : '' }}>
                    <label class="form-check-label">{{ DateTime::createFromFormat('!m', $i)->format('M') }}</label>
                </div>
            @endfor
        </div>
    </div>
</div>

<!--
   @if ((old('is_bonus') ?? ($bonus->is_bonus ?? 0)) == 0)
<div class="col-md-6 mb-3">
                <label for="taxable" class="form-label">¿Es imponible?</label>
                <select class="form-select" id="taxable" name="taxable">
                    <option value="1" {{ (old('taxable') ?? ($bonus->taxable ?? 1)) == 1 ? 'selected' : '' }}>No
                    </option>
                    <option value="0" {{ (old('taxable') ?? ($bonus->taxable ?? 1)) == 0 ? 'selected' : '' }}>Sí
                    </option>
                </select>
            </div>
@else
<input type='hidden' id="taxable" name='taxable' value='1'>
@endif-->
