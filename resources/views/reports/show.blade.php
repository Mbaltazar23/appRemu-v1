<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Liquidación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            font-size: 14px; /* Ajusta el tamaño de la fuente global */
        }

        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-before: always;
            table-layout: fixed; /* Hace que las celdas se ajusten mejor */
        }

        th,
        td {
            padding: 10px; /* Aumenta el padding para mejorar la legibilidad */
            text-align: left;
            border: 1px solid #ddd;
            word-wrap: break-word;
            font-size: 14px; /* Aumenta el tamaño de fuente de la tabla */
        }

        /* Estilos para las celdas de la tabla */
        td {
            border-bottom: 1px solid #ddd;
        }

        /* Estilos para las cabeceras */
        th {
            background-color: #f2f2f2;
            border-top: 2px solid #000;
            font-weight: bold;
        }

        /* Línea en el final de la tabla */
        .total-row td {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        /* Cabecera para ISAPRE y AFP sin bordes */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-table td {
            border: none;
            padding: 8px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
        }

        .center-text {
            text-align: center;
            font-weight: bold;
        }

        .report-title {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }

        .subheading {
            font-size: 14px;
            font-weight: normal;
            text-align: center;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
                font-size: 12px; /* Ajustar la fuente para impresión */
            }

            table {
                page-break-before: always;
                page-break-inside: avoid;
            }

            th,
            td {
                page-break-inside: avoid;
                font-size: 12px; /* Reducir el tamaño de la fuente solo en impresión */
            }

            .footer {
                display: none;
            }

            .report-title,
            .subheading {
                page-break-before: always;
            }

            /* Poner espacio entre las secciones */
            .header-table {
                margin-bottom: 20px;
            }

            /* Evitar que el contenido se corte */
            .container {
                margin: 0;
                padding: 0;
            }

            .total-row {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Siempre mostrar la cabecera del reporte -->
        <table class="header-table">
            <tr>
                <td><strong>R.U.T EMPLEADOR:</strong></td>
                <td>{{ $school->rut }}</td>
            </tr>
            <tr>
                <td><strong>NOMBRE O RAZON SOCIAL: </strong></td>
                <td>{{ $school->name }}</td>
            </tr>
        </table>

        <br>

        <!-- Mostrar la cabecera dependiendo del tipo de seguro -->
        @if ($typeInsurance != App\Models\Insurance::AFP)
            <table class="header-table">
                <tr>
                    <td><strong>SEGURO ISAPRE</strong>: {{ App\Models\Insurance::getNameInsurance($insurance) }}</td>
                    <td style="text-align: right;"><strong>Periodo cotizaciones:</strong>
                        {{ App\Helpers\MonthHelper::integerToMonth($month) }} {{ $year }}</td>
                </tr>
            </table>
            <div class="center-text"><strong>DECLARACION Y PAGO COTIZACIONES SALUD</strong></div>
        @else
            <table class="header-table">
                <tr>
                    <td><strong>SEGURO AFP</strong>: {{ App\Models\Insurance::getNameInsurance($insurance) }}</td>
                    <td style="text-align: right;"><strong>Periodo cotizaciones:</strong>
                        {{ App\Helpers\MonthHelper::integerToMonth($month) }} {{ $year }}</td>
                </tr>
            </table>
            <div class="center-text"><strong>DETALLE DE COTIZACIONES PREVISIONALES</strong></div>
        @endif

        <br>

        @if (empty($data))
            <h4>No existen datos para este periodo</h4>
        @else
            <!-- Tabla de datos de los funcionarios -->
            <table>
                <thead>
                    <tr>
                        <th>RUT</th>
                        <th>FUNCIONARIO</th>
                        <th>SUELDO IMPONIBLE</th>
                        @if ($typeInsurance == App\Models\Insurance::AFP)
                            <th>COTIZ. FDO PENSIONES</th>
                            <th>COTIZ. VOLUNTARIA</th>
                            <th>COTIZACIÓN AFILIADO</th>
                            <th>COTIZACIÓN EMPLEADOR</th>
                        @else
                            <th>FONDO SALUD (7%)</th>
                            <th>COTIZ. VOLUNTARIA</th>
                            <th>TOTAL A PAGAR</th>
                            <th>COTIZ. PACTADA</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            <td>{{ $row['id_number'] }}</td>
                            <td>{{ $row['full_name'] }}</td>
                            <td>{{ $row['taxable_income'] }}</td>
                            @if ($typeInsurance == App\Models\Insurance::AFP)
                                <td>{{ $row['contribution'] }}</td>
                                <td>{{ $row['voluntary_contribution'] }}</td>
                                <td>{{ $row['affiliate_contribution'] }}</td>
                                <td>{{ $row['employer_contribution'] }}</td>
                            @else
                                <td>{{ $row['health_fund'] }}</td>
                                <td>{{ $row['additional_health'] }}</td>
                                <td>{{ $row['total_contribution'] }}</td>
                                <td>{{ $row['total_payment'] }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2">TOTALES</td>
                        <td>{{ number_format($totals['total_income'], 0, 0, ',') }}</td>
                        @if ($typeInsurance == App\Models\Insurance::AFP)
                            <td>{{ number_format($totals['total_contribution'], 0, 0, ',') }}</td>
                            <td>{{ number_format($totals['total_voluntary_contribution'], 0, 0, ',') }}</td>
                            <td>{{ number_format($totals['total_affiliate_contribution'], 0, 0, ',') }}</td>
                            <td>{{ number_format($totals['total_employer_contribution'], 0, 0, ',') }}</td>
                        @else
                            <td>{{ number_format($totals['total_health_fund'], 0, 0, ',') }}</td>
                            <td>{{ number_format($totals['total_additional_health'], 0, 0, ',') }}</td>
                            <td>{{ number_format($totals['total_contribution'], 0, 0, ',') }}</td>
                            <td>{{ number_format($totals['total_payment'], 0, 0, ',') }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

    <script>
        window.print();
    </script>

</body>

</html>
