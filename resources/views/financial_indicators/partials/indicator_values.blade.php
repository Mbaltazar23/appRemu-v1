<table class="table">
    <thead>
        <tr>
            <th>Indicador</th>
            <th>Valor Actual</th>
            <th>Valor del Mes Pasado</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>UF</td>
            <td>${{ number_format($currentValues['uf'], 0, ',', '.') }}</td>
            <td>${{ number_format($previousValues['uf'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>UTM</td>
            <td>${{ number_format($currentValues['utm'], 0, ',', '.') }}</td>
            <td>${{ number_format($previousValues['utm'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Dólar</td>
            <td>${{ number_format($currentValues['dolar'], 0, ',', '.') }}</td>
            <td>${{ number_format($previousValues['dolar'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Euro</td>
            <td>${{ number_format($currentValues['euro'], 0, ',', '.') }}</td>
            <td>${{ number_format($previousValues['euro'], 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>