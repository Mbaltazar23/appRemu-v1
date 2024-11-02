<table class="table table-bordered">
    <thead>
        <tr>
            <th>Tramo</th>
            <th>Mínimo (UTM)</th>
            <th>Máximo (UTM)</th>
            <th>Valor (%)</th>
            <th>Rebaja (UTM)</th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 2; $i <= 8; $i++)
            <tr>
                <td>{{ $i }}</td>
                <td>
                    <input type="text" class="form-control text-center" name="MIN{{ $i }}" value="{{ $minLimits[$i] }}" onblur="this.value=Trim(this.value);if (this.value==''){alert('Debe ingresar un valor');this.focus();return false;}numerovalido(this)">
                </td>
                <td>
                    <input type="text" class="form-control text-center" name="MAX{{ $i }}" value="{{ $maxLimits[$i] }}" onblur="this.value=Trim(this.value);if (this.value==''){alert('Debe ingresar un valor');this.focus();return false;}numerovalido(this)">
                </td>
                <td>
                    <input type="text" class="form-control text-center" name="IMP{{ $i }}" value="{{ $impValues[$i] }}" onblur="this.value=Trim(this.value);if (this.value==''){alert('Debe ingresar un valor');this.focus();return false;}numerovalido(this)">
                </td>
                <td>
                    <input type="text" class="form-control text-center" name="REB{{ $i }}" value="{{ $rebValues[$i] }}" onblur="this.value=Trim(this.value);if (this.value==''){alert('Debe ingresar un valor');this.focus();return false;}numerovalido(this)">
                </td>
            </tr>
        @endfor
    </tbody>
</table>
