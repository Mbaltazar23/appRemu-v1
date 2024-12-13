<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Centro de Costos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }

        h3 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
            font-size: 12px;
            /* Tamaño más pequeño */
        }

        th {
            background-color: #f2f2f2;
            color: #555;
        }

        td {
            background-color: #fff;
        }

        tfoot {
            font-weight: bold;
            background-color: #f7f7f7;
        }

        .table-container {
            overflow-x: auto;
        }

        .total-row {
            background-color: #f7f7f7;
        }

        .no-workers-message {
            text-align: center;
            font-size: 18px;
            color: #ff0000;
        }
    </style>
</head>

<body>
    <h3>Centro de Costos para {{ $school->name }}, {{ $titperiodo }} {{ $year }}</h3>

    @if (empty($workers) || count($workers) === 0)
        <div class="no-workers-message">
            <p>No hay trabajadores disponibles para los filtros seleccionados.</p>
        </div>
    @else
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Trabajador</th>
                        <th>Renta Imponible</th>
                        <th>Descuentos Legales</th>
                        <th>Impuesto Renta</th>
                        <th>Descuentos Voluntarios</th>
                        <th>Líquido a Pagar</th>
                        <th>AFP</th>
                        <th>Salud</th>
                        <th>Seguro Cesantía</th>
                        <th>L/M</th>
                        <th>Inasistencias</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($workers as $worker)
                        <tr>
                            <td>{{ $worker->name }}</td> <!-- Nombre del trabajador -->
                            <td>{{ number_format($worker->totals['RENTAIMPONIBLE'], 0, ',', '.') }}</td>
                            <td>{{ number_format($worker->totals['DESCUENTOSLEGALES'], 0, ',', '.') }}</td>
                            <td>{{ number_format($worker->totals['IMPUESTORENTA'], 0, ',', '.') }}</td>
                            <td>{{ number_format($worker->totals['DESCUENTOSVOLUNTARIOS'], 0, ',', '.') }}</td>
                            <td>{{ number_format($worker->totals['TOTALAPAGAR'], 0, ',', '.') }}</td>
                            <td>{{ number_format($worker->totals['AFP'], 0, ',', '.') }}</td>
                            <td>{{ number_format($worker->totals['SALUD'], 0, ',', '.') }}</td>
                            <td>{{ number_format($worker->totals['SEGUROCESANTIA'], 0, ',', '.') }}</td>
                            <td>{{ number_format($worker->totals['LICENCIA'], 0, ',', '.') }}</td>
                            <td>{{ number_format($worker->totals['INASISTENCIA'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td><strong>Total</strong></td>
                        <td>{{ number_format($workers->sum(fn($worker) => $worker->totals['RENTAIMPONIBLE']), 0, ',', '.') }}
                        </td>
                        <td>{{ number_format($workers->sum(fn($worker) => $worker->totals['DESCUENTOSLEGALES']), 0, ',', '.') }}
                        </td>
                        <td>{{ number_format($workers->sum(fn($worker) => $worker->totals['IMPUESTORENTA']), 0, ',', '.') }}
                        </td>
                        <td>{{ number_format($workers->sum(fn($worker) => $worker->totals['DESCUENTOSVOLUNTARIOS']), 0, ',', '.') }}
                        </td>
                        <td>{{ number_format($workers->sum(fn($worker) => $worker->totals['TOTALAPAGAR']), 0, ',', '.') }}
                        </td>
                        <td>{{ number_format($workers->sum(fn($worker) => $worker->totals['AFP']), 0, ',', '.') }}</td>
                        <td>{{ number_format($workers->sum(fn($worker) => $worker->totals['SALUD']), 0, ',', '.') }}</td>
                        <td>{{ number_format($workers->sum(fn($worker) => $worker->totals['SEGUROCESANTIA']), 0, ',', '.') }}
                        </td>
                        <td>{{ number_format($workers->sum(fn($worker) => $worker->totals['LICENCIA']), 0, ',', '.') }}
                        </td>
                        <td>{{ number_format($workers->sum(fn($worker) => $worker->totals['INASISTENCIA']), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    <script>
        window.print();
    </script>
</body>

</html>
