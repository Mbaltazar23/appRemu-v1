@php
    $selectedYear = request()->input('year', '2024');
    // Verifica si el año seleccionado existe en los datos
    if (isset($data[$selectedYear])) {
        $dataToShow = $data[$selectedYear];
    } else {
        $dataToShow = []; // O asigna un arreglo vacío, o muestra un mensaje de error
    }
    // Ordena los años de mayor a menor
    $years = array_keys($data);
    rsort($years);
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>{{ __('Porcentajes de Actualización Corrección Monetaria (Término de Giro)') }}</h4>
    <form id="yearSelectForm" method="GET" action="{{ request()->url() }}" class="form-inline">
        <input type="hidden" name="index" value="{{ request()->input('index') }}">
        <div class="form-group">
            <label for="yearSelect" class="form-label sr-only">{{ __('Seleccione un Año') }}</label>
            <select class="form-control" id="yearSelect" name="year" onchange="this.form.submit()">
                @foreach ($years as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

<h5>Año: {{ $selectedYear }}</h5>

@if (empty($dataToShow))
    <p>{{ __('No hay datos disponibles para el año seleccionado.') }}</p>
@else
    <table class="table table-sm">
        <thead>
            <tr>
                <th></th>
                <th>{{ __('Ene') }}</th>
                <th>{{ __('Feb') }}</th>
                <th>{{ __('Mar') }}</th>
                <th>{{ __('Abr') }}</th>
                <th>{{ __('May') }}</th>
                <th>{{ __('Jun') }}</th>
                <th>{{ __('Jul') }}</th>
                <th>{{ __('Ago') }}</th>
                <th>{{ __('Sep') }}</th>
                <th>{{ __('Oct') }}</th>
                <th>{{ __('Nov') }}</th>
                <th>{{ __('Dic') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataToShow as $key => $values)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                    @foreach ($values as $value)
                        <td>{{ $value ?? '' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
