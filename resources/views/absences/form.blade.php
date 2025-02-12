<div class="row mb-4">
    <div class="col-md-6 form-group">
        <label for="worker_id" class="form-label">Trabajador</label>
        <select id="worker_id" class="form-select" name="worker_id" required>
            <option value="">Seleccione un trabajador</option>
            @foreach ($workers as $worker)
                <option value="{{ $worker->id }}" {{ $absence->worker_id == $worker->id ? 'selected' : '' }}>
                    {{ $worker->name }} {{ $worker->last_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 form-group">
        <label for="date" class="form-label">Fecha</label>
        <input type="date" class="form-control" name="date" value="{{ old('date', $absence->date) }}" required>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 form-group">
        <label for="reason" class="form-label">Motivo</label>
        <input type="text" class="form-control" name="reason" value="{{ old('reason', $absence->reason) }}"
            required>
    </div>
    <div class="col-md-6 form-group">
        <label for="minutes" class="form-label">Duración (minutos)</label>
        <input type="number" class="form-control" name="minutes" value="{{ old('minutes', $absence->minutes) }}"
            required>
    </div>
</div>

<div class="form-group mb-4">
    <label for="with_consent" class="form-label">Con goce de sueldo</label>
    <select class="form-select" name="with_consent" id="with_consent" required>
        <option value="0" {{ old('with_consent', $absence->with_consent) == 0 ? 'selected' : '' }}>Sí</option>
        <option value="1" {{ old('with_consent', $absence->with_consent) == 1 ? 'selected' : '' }}>No</option>
    </select>
</div>
