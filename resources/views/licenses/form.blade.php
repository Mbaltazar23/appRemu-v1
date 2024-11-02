<div class="form-group">
    <label for="worker_id" class="form-label">Trabajador</label>
    <select id="worker_id" class="form-select" name="worker_id" required>
        <option value="">Seleccione un trabajador</option>
        @foreach ($workers as $worker)
            <option value="{{ $worker->id }}" {{ (isset($license) && $worker->id == $license->worker_id) ? 'selected' : '' }}>
                {{ $worker->name }} {{ $worker->last_name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="issue_date" class="form-label">Fecha de Emisión</label>
    <input type="date" class="form-control" name="issue_date" value="{{ old('issue_date', $license->issue_date ?? '') }}" required>
</div>

<div class="form-group">
    <label for="reason" class="form-label">Motivo</label>
    <input type="text" class="form-control" name="reason" value="{{ old('reason', $license->reason ?? '') }}" required>
</div>

<div class="form-group">
    <label for="days" class="form-label">Días</label>
    <input type="number" class="form-control" name="days" value="{{ old('days', $license->days ?? '') }}" required>
</div>

