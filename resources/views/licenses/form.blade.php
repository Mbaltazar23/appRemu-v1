<div class="row mb-3">
    <div class="col-md-6">
        <div class="form-group">
            <label for="worker_id" class="form-label">Trabajador</label>
            <select id="worker_id" class="form-select" name="worker_id" required>
                <option value="">Seleccione un trabajador</option>
                @foreach ($workers as $worker)
                    <option value="{{ $worker->id }}"
                        {{ isset($license) && $worker->id == $license->worker_id ? 'selected' : '' }}>
                        {{ $worker->name }} {{ $worker->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="issue_date" class="form-label">Fecha de Emisión</label>
            <input type="date" class="form-control" name="issue_date"
                value="{{ old('issue_date', $license->issue_date ?? '') }}" required>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="form-group">
            <label for="reason" class="form-label">Motivo</label>
            <input type="text" class="form-control" name="reason"
                value="{{ old('reason', $license->reason ?? '') }}" required>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="days" class="form-label">Días</label>
            <input type="number" class="form-control" name="days" value="{{ old('days', $license->days ?? '') }}"
                required>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="form-group">
            <label for="institution" class="form-label">Institución</label>
            <input type="text" class="form-control" name="institution"
                value="{{ old('institution', $license->institution ?? '') }}">
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="receipt_number" class="form-label">Número de Recibo</label>
            <input type="text" class="form-control" name="receipt_number"
                value="{{ old('receipt_number', $license->receipt_number ?? '') }}" required>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="form-group">
            <label for="receipt_date" class="form-label">Fecha de Recibo</label>
            <input type="date" class="form-control" name="receipt_date"
                value="{{ old('receipt_date', $license->receipt_date ?? '') }}" required>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="processing_date" class="form-label">Fecha de Procesamiento</label>
            <input type="date" class="form-control" name="processing_date"
                value="{{ old('processing_date', $license->processing_date ?? '') }}">
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="form-group">
            <label for="responsible_person" class="form-label">Persona Responsable</label>
            <input type="text" class="form-control" name="responsible_person"
                value="{{ old('responsible_person', $license->responsible_person ?? '') }}">
        </div>
    </div>
</div>
