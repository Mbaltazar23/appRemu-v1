<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Planilla</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 10px;
            background-color: #f9f9f9;
            color: #333;
        }

        .popup-container {
            width: 100%;
            overflow-x: auto;
            padding: 10px;
        }

        h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        h4 {
            font-size: 16px;
            color: #555;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px; /* Ajuste más pequeño de fuente */
            table-layout: auto;
            margin-top: 15px;
        }

        th,
        td {
            padding: 6px 10px; /* Más espacio para mejorar legibilidad */
            text-align: center;
            border: 1px solid #ccc;
            word-wrap: break-word;
        }

        th {
            background-color: #ccc;
            color: #333;
            font-weight: bold;
            white-space: nowrap;
        }

        td {
            background-color: #fff;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f4f4f4;
        }

        tr:hover {
            background-color: #e2e2e2;
        }

        tfoot {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        tfoot td {
            font-size: 11px;
            color: #333;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            background-color: #6c757d;
            color: #fff;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.3s ease;
            margin-top: 15px;
            text-align: center;
        }

        .btn:hover {
            background-color: #495057;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #495057;
        }

        /* Estilos para impresión */
        @media print {
            body {
                margin: 0;
                padding: 0;
                font-size: 9px;
            }

            table {
                width: 100%;
                font-size: 9px; /* Fuente más pequeña para impresión */
                table-layout: auto;
                overflow: visible;
            }

            th, td {
                padding: 4px;
                text-align: center;
            }

            .popup-container {
                padding: 0;
            }

            h3, h4 {
                font-size: 16px;
            }

            .btn {
                display: none;
            }
        }

        /* Estilo para pantallas más pequeñas */
        @media (max-width: 768px) {
            table {
                font-size: 9px; /* Reducir tamaño de fuente en pantallas pequeñas */
            }

            th,
            td {
                padding: 5px 8px; /* Ajustar padding en pantallas pequeñas */
            }
        }
    </style>
</head>

<body>
    <div class="popup-container">
        <h3>Detalles de la Planilla de Remuneraciones</h3>
        <h4>Mes: {{ \App\Helpers\MonthHelper::integerToMonth($payroll->month) }} - Año: {{ $payroll->year }}</h4>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Rut</th>
                    <th>Días Trabajados</th>
                    <th>Sueldo Mensual</th>
                    <th>Desem. Dificil</th>
                    <th>Bonif DL. 19.410</th>
                    <th>Bonif DL. 19.464</th>
                    <th>Bonif DL. 19.933</th>
                    <th>UMP</th>
                    <th>Remuneración Imponible</th>
                    <th>Asig. Familiar</th>
                    <th>Asig. Voluntaria</th>
                    <th>Total Haber</th>
                    <th>AFP</th>
                    <th>%</th>
                    <th>Cotización AFP</th>
                    <th>Cotización Voluntaria</th>
                    <th>Isapre</th>
                    <th>Descuentos Trabajador</th>
                    <th>Seguro Cesantía</th>
                    <th>Dif. Isapre</th>
                    <th>Impuesto Renta</th>
                    <th>Colegio Profesores</th>
                    <th>Prest. Caja Los Andes</th>
                    <th>Prest. Caja Los Heroes</th>
                    <th>Fundación López Pérez</th>
                    <th>Total Descuentos</th>
                    <th>Líquido</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_days = 0;
                    $total_sueldo = 0;
                    $total_hardPerformance = 0;
                    $total_law19410 = 0;
                    $total_law19464 = 0;
                    $total_law19933 = 0;
                    $total_ump = 0;
                    $total_taxableIncome = 0;
                    $total_familyAllowance = 0;
                    $total_voluntaryAllowance = 0;
                    $total_totalEarnings = 0;
                    $total_afpContribution = 0;
                    $total_apv = 0;
                    $total_healthContribution = 0;
                    $total_unemploymentInsurance = 0;
                    $total_healthSupplement = 0;
                    $total_incomeTax = 0;
                    $total_professorCollege = 0;
                    $total_presLosAndes = 0;
                    $total_presLosHeroes = 0;
                    $total_fundationLp = 0;
                    $total_voluntaryDiscount = 0;
                    $total_totalPayable = 0;
                @endphp
                @foreach (json_decode($payroll->details, true) as $totals)
                    <tr>
                        <td>{{ $totals['name'] }}</td>
                        <td>{{ $totals['rut'] }}</td>
                        <td>{{ $totals['daysWorker'] }}</td>
                        <td>{{ number_format($totals['monthlySalary'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['hardPerformance'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['law19410'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['law19464'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['law19933'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['ump'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['taxableIncome'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['familyAllowance'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['voluntaryAllowance'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['totalEarnings'], 0, 0, ',') }}</td>
                        <td>{{ $totals['afpName'] }}</td>
                        <td>{{ $totals['afpPercentage'] }}</td>
                        <td>{{ number_format($totals['afpContribution'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['apv'], 0, 0, ',') }}</td>
                        <td>{{ $totals['healthName'] }}</td>
                        <td>{{ number_format($totals['healthContribution'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['unemploymentInsurance'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['healthSupplement'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['incomeTax'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['professorCollege'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['presLosAndes'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['presLosHeroes'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['fundationLp'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['voluntaryDiscount'], 0, 0, ',') }}</td>
                        <td>{{ number_format($totals['totalPayable'], 0, 0, ',') }}</td>
                    </tr>
                    @php
                        $total_days += $totals['daysWorker'];
                        $total_sueldo += $totals['monthlySalary'];
                        $total_hardPerformance += $totals['hardPerformance'];
                        $total_law19410 += $totals['law19410'];
                        $total_law19464 += $totals['law19464'];
                        $total_law19933 += $totals['law19933'];
                        $total_ump += $totals['ump'];
                        $total_taxableIncome += $totals['taxableIncome'];
                        $total_familyAllowance += $totals['familyAllowance'];
                        $total_voluntaryAllowance += $totals['voluntaryAllowance'];
                        $total_totalEarnings += $totals['totalEarnings'];
                        $total_afpContribution += $totals['afpContribution'];
                        $total_apv += $totals['apv'];
                        $total_healthContribution += $totals['healthContribution'];
                        $total_unemploymentInsurance += $totals['unemploymentInsurance'];
                        $total_healthSupplement += $totals['healthSupplement'];
                        $total_incomeTax += $totals['incomeTax'];
                        $total_professorCollege += $totals['professorCollege'];
                        $total_presLosAndes += $totals['presLosAndes'];
                        $total_presLosHeroes += $totals['presLosHeroes'];
                        $total_fundationLp += $totals['fundationLp'];
                        $total_voluntaryDiscount += $totals['voluntaryDiscount'];
                        $total_totalPayable += $totals['totalPayable'];
                    @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Totales</td>
                    <td>{{ number_format($total_days, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_sueldo, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_hardPerformance, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_law19410, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_law19464, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_law19933, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_ump, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_taxableIncome, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_familyAllowance, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_voluntaryAllowance, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_totalEarnings, 0, 0, ',') }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($total_afpContribution, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_apv, 0, 0, ',') }}</td>
                    <td></td>
                    <td>{{ number_format($total_healthContribution, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_unemploymentInsurance, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_healthSupplement, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_incomeTax, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_professorCollege, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_presLosAndes, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_presLosHeroes, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_fundationLp, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_voluntaryDiscount, 0, 0, ',') }}</td>
                    <td>{{ number_format($total_totalPayable, 0, 0, ',') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <script>
        window.print();
    </script>
</body>

</html>
