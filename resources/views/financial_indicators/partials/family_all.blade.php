<table class="table table-bordered">
    <thead>
        <tr>
            <th>Tramo</th>
            <th>Mínimo ($pesos)</th>
            <th>Máximo ($pesos)</th>
            <th>Valor ($pesos)</th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 1; $i <= 3; $i++)
            <tr>
                <td>{{ $i }}</td>
                <td>
                    <input type="text" class="form-control text-center" name="MIN{{ $i }}"
                        value="{{ $minLimits[$i] }}"
                        onblur="this.value=Trim(this.value);if (this.value==''){alert('Debe ingresar un valor');this.focus();return false;}numerovalido(this)">
                </td>
                <td>
                    <input type="text" class="form-control text-center" name="MAX{{ $i }}"
                        value="{{ $maxLimits[$i] }}"
                        onblur="this.value=Trim(this.value);if (this.value==''){alert('Debe ingresar un valor');this.focus();return false;}numerovalido(this)">
                </td>
                <td>
                    <input type="text" class="form-control text-center" name="VAL{{ $i }}"
                        value="{{ $impValues[$i] }}"
                        onblur="this.value=Trim(this.value);if (this.value==''){alert('Debe ingresar un valor');this.focus();return false;}numerovalido(this)">
                </td>
            </tr>
        @endfor
    </tbody>
</table>
