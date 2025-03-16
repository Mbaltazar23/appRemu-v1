<div class="row col-12 mb-2">
    <div class="col-md-6 mb-3"> <!-- Aumentado el margen inferior -->
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $user->name ?? '') }}" required>
        </div>
    </div>

    <div class="col-md-6 mb-3"> <!-- Aumentado el margen inferior -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email', $user->email ?? '') }}"
                required>
        </div>
    </div>

    <div class="col-md-6 mb-3"> <!-- Aumentado el margen inferior -->
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" class="form-control" name="password" {{ isset($user) ? '' : 'required' }}>
            <input type="hidden" name="id" value="{{ $user->id ?? null }}">
        </div>
    </div>

    <div class="col-md-6 mb-3"> <!-- Aumentado el margen inferior -->
        <div class="form-group">
            <label for="role">Rol a designar</label>
            <select class="form-control" name="role_id" id="role_id" required>
                <option value="">Seleccione un Rol</option>
                @foreach ($roles as $key)
                    <option value="{{ $key->id }}"
                        {{ old('role', $user->role_id ?? '') == $key->id ? 'selected' : '' }}>
                        {{ $key->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

   <div class="col-md-12 mb-3">
    <div class="form-group">
        <label class="mb-3">Colegios a los que tendrá acceso</label>
        <div class="row py-2">
            @foreach ($schools as $school)
                <div class="col-md-3 mb-2">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="school_ids[]"
                            value="{{ $school->id }}"
                            id="school_{{ $school->id }}"
                            {{ $user->schools->contains($school->id) ? 'checked' : '' }}
                            {{ in_array($school->id, $associatedSchoolIds) ? 'disabled checked' : '' }}
                        >
                        <label class="form-check-label" for="school_{{ $school->id }}">
                            {{ $school->name }}
                        </label>
                        @if (in_array($school->id, $associatedSchoolIds))
                            <small class="text-danger">(Ya asociado a otro usuario)</small>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

</div>
