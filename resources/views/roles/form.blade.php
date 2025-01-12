<div class="table-responsive">
    <div class="row col-12 mb-3">
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $role->name ?? '') }}" required>
        </div>
        <div class="form-group">
            <br>
            <label class="mb-3">Permisos a Designar</label>
            <div class="row py-2">
                @foreach ($permissions as $key => $permission)
                    <div class="col-md-4 col-sm-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                value="{{ $key }}" id="permission_{{ $key }}"
                                {{ in_array($key, $role->permissions ?? []) ? 'checked' : '' }}>
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
