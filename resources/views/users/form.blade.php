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
            <select class="form-control" name="role" id="role" required>
                <option value="">Seleccione un Rol</option>
                @foreach ($roles as $key => $role)
                    <option value="{{ $key }}" {{ old('role', $user->role ?? '') == $key ? 'selected' : '' }}>
                        {{ $role }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if (auth()->user()->isSuperAdmin())
        <div class="col-md-12 mb-3">
            <div class="form-group">
                <label class="mb-3">Colegios a los que tendrá acceso</label>
                <div class="row py-2">
                    @foreach ($schools as $school)
                        <div class="col-md-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="school_ids[]"
                                    value="{{ $school->id }}" id="school_{{ $school->id }}"
                                    {{ $user->schools->contains($school->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="school_{{ $school->id }}">
                                    {{ $school->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="col-md-12 mb-3">
        <div class="form-group">
            <label class="mb-3">Permisos a Designar</label>
            <div class="row py-2">
                @foreach (App\Models\User::getPermissions() as $key => $permission)
                    <div class="col-md-3 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                value="{{ $key }}" id="permission_{{ $key }}"
                                {{ in_array($key, $user->permissions ?? []) ? 'checked' : '' }}>
                            <label class="form-check-label" for="permission_{{ $key }}">
                                {{ $permission }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    

</div>

@push('custom_scripts')
    <script>
        window.onload = () => {
            var role = document.getElementById('role');

            function updateSchoolCheckboxes() {
                var checkboxes = document.querySelectorAll('input[name="school_ids[]"]');
                checkboxes.forEach(function(checkbox) {
                    checkbox.disabled = role.value == "" || role.value == '1' || role.value ==
                        '5'; // Cambia a 0 y 1 para deshabilitar
                });
            }

            role.addEventListener('change', function() {
                updateSchoolCheckboxes();
            });

            updateSchoolCheckboxes();
        };
    </script>
@endpush
