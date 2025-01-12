<div class="mb-3">
    <label for="annex_name" class="form-label">Nombre del Anexo</label>
    <input type="text" name="annex_name" class="form-control" required value="{{ old('annex_name') ?? $annexData['annex_name'] ?? '' }}">
</div>

<div class="mb-3">
    <label for="annex_description" class="form-label">Descripción del Anexo</label>
    <textarea name="annex_description" class="form-control" required>{{ old('annex_description') ?? $annexData['annex_description'] ?? '' }}</textarea>
</div>
