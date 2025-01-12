<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificados de Remuneraciones</title>
    <style>
        /* General */
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            /* Reducir el tamaño de la fuente */
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 10px;
        }

        /* Para separar los certificados */
        .certificate {
            page-break-after: always;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        th,
        td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 14px;
            /* Tamaño de fuente más pequeño para la tabla */
        }

        th {
            background-color: #f2f2f2;
        }

        td {
            text-align: right;
        }

        /* Estilo para la fila total */
        tr.total-row td {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        /* Espaciado adicional entre celdas */
        td,
        th {
            padding: 10px 15px;
        }
    </style>
</head>

<body>
    @foreach ($workersData as $workerData)
        <div class="certificate">
            <h2>CERTIFICADO DE REMUNERACIONES</h2>
            <p><strong>Empleador: </strong>{{ $workerData['sustainer_name'] }}</p>
            <p><strong>RUT º: </strong>{{ $workerData['sustainer_rut'] }}</p>
            <p><strong>Dirección: </strong>{{ $workerData['sustainer_address'] }}</p>
            <p><strong>Giro o Actividad: </strong>{{ $workerData['sustainer_legal_nature'] }}</p>
            <p>Certifica que a {{ $workerData['worker_name'] }}, Rut {{ $workerData['worker_rut'] }} se le pagaron las
                siguientes rentas por concepto de Remuneraciones, correspondientes al año {{ $workerData['year'] }} y
                sobre las cuales se le practicaron las retenciones de impuestos que se señalan:</p>

            <!-- Tabla de los datos mensuales -->
            <table>
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Renta Imponible</th>
                        <th>Descuentos Legales</th>
                        <th>Renta Tributaria</th>
                        <th>Impuesto a la Renta</th>
                        <th>Renta Neta Actualizada</th>
                        <th>Impuesto Único Retenido Actualizado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($workerData['months_data'] as $month)
                        <tr>
                            <td>{{ $month['month'] }}</td>
                            <td>{{ $month['income'] }}</td>
                            <td>{{ $month['legal_deductions'] }}</td>
                            <td>{{ $month['taxable_salary'] }}</td>
                            <td>{{ $month['tax_amount'] }}</td>
                            <td>{{ $month['adjusted_salary'] }}</td>
                            <td>{{ $month['adjusted_tax'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td><strong>Total</strong></td>
                        <td><strong>{{ number_format(floatval($workerData['total_values']['income_total']), 0, ',', ',') }}</strong>
                        </td>
                        <td><strong>{{ number_format(floatval($workerData['total_values']['deductions_total']), 0, ',', ',') }}</strong>
                        </td>
                        <td><strong>{{ number_format(floatval($workerData['total_values']['taxable_salary_total']), 0, ',', ',') }}</strong>
                        </td>
                        <td><strong>{{ number_format(floatval($workerData['total_values']['tax_amount_total']), 0, ',', ',') }}</strong>
                        </td>
                        <td><strong>{{ number_format(floatval($workerData['total_values']['adjusted_salary_total']), 0, ',', ',') }}</strong>
                        </td>
                        <td><strong>{{ number_format(floatval($workerData['total_values']['adjusted_tax_total']), 0, ',', ',') }}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p>Se extiende el presente Certificado en cumplimiento de lo dispuesto en la Resolución Exenta Nº 6509 del
                Servicio de Impuestos Internos, publicada en el Diario Oficial de fecha 20 de diciembre de 1993.</p>
        </div>
    @endforeach
    <script>
        window.print();
    </script>
</body>

</html>
