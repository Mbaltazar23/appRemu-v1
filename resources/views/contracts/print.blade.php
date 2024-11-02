<!DOCTYPE html>
<html lang="ES">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONTRATO DE TRABAJO</title>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        h5 {
            text-align: center;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        p {
            text-align: justify;
            line-height: 1.5;
        }

        .signature-table {
            margin-top: 30px;
        }

        .highlight {
            font-weight: bold;
            color: black;
            /* Color azul para resaltar */
        }

        button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        @media print {
            button {
                display: none;
                /* Oculta el botón al imprimir */
            }
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</head>

<body>
    <div align="center">
        <h5>CONTRATO DE TRABAJO</h5>
    </div>

    <p>En {{ $contractDetails['city'] }}, se conviene el siguiente contrato individual de trabajo entre:</p>

    <p><b>EMPLEADOR:</b></p>
    <table>
        <tr>
            <td class="highlight">Razón Social</td>
            <td>{{ $worker->school->name }}</td>
        </tr>
        <tr>
            <td class="highlight">R.U.T.</td>
            <td>{{ $worker->school->rut }}</td>
        </tr>
        <tr>
            <td class="highlight">R.B.D.</td>
            <td>{{ $worker->school->rbd }}</td>
        </tr>
        <tr>
            <td class="highlight">Domicilio</td>
            <td>{{ $worker->school->address }}</td>
        </tr>
        <tr>
            <td class="highlight">Ciudad</td>
            <td>{{ $worker->school->commune }}</td>
        </tr>
        <tr>
            <td class="highlight">Teléfono</td>
            <td>{{ $worker->school->phone }}</td>
        </tr>
    </table>

    <p><b>TRABAJADOR:</b></p>
    <table>
        <tr>
            <td class="highlight">Nombre</td>
            <td>{{ $worker->name }} {{ $worker->last_name }}</td>
        </tr>
        <tr>
            <td class="highlight">Estado Civil</td>
            <td>{{ $worker->getMaritalStatusDescription() }}</td>
        </tr>
        <tr>
            <td class="highlight">RUT</td>
            <td>{{ $worker->rut }}</td>
        </tr>
        <tr>
            <td class="highlight">Domicilio</td>
            <td>{{ $worker->address }} – {{ $worker->commune }}</td>
        </tr>
        <tr>
            <td class="highlight">Región</td>
            <td>{{ $worker->region }}</td>
        </tr>
        <tr>
            <td class="highlight">Procedente de</td>
            <td>{{ $contractDetails['origin_city'] }}</td>
        </tr>
        <tr>
            <td class="highlight">Nacionalidad</td>
            <td>{{ $worker->nationality }}</td>
        </tr>
    </table>
    <p>
        Entre las partes arriba individualizadas, se suscribe el contrato de trabajo a
        {{ $contractDetails['duration'] }},
        en conformidad a las siguientes cláusulas:
    </p>

    <p>
        El trabajador se compromete a prestar servicios en {{ $worker->school->name }},
        ubicado en {{ $worker->school->address }}, Comuna de {{ $worker->school->commune }},
        en calidad de {{ $worker->getFunctionWorkerDescription() }}.
    </p>

    <p><b>SEGUNDO:</b><br>
        La jornada ordinaria de trabajo será de <b>{{ $worker->working_hours }}</b> horas cronológicas semanales.
        Las que se conforman según anexos por jornada.
    </p>

    <p><b>TERCERO:</b><br>
        El empleador pagará al
        @if ($worker->worktype == App\Models\Worker::WORKER_TYPE_TEACHER)
            {{ $worker->getFunctionWorkerTypes() }}
        @elseif ($worker->worktype == App\Models\Worker::WORKER_TYPE_NON_TEACHER)
            {{ $worker->getFunctionWorkerTypes() }}
        @endif
        una remuneración total de <b>${{ number_format($contractDetails['total_remuneration'], 0, ',', '.') }}
            ({{ $contractDetails['remuneration_gloss'] }})</b>, la que incluye el pago de la Remuneración base y demás
        remuneraciones legales y convencionales.
    </p>

    <p><b>CUARTO:</b><br>
        Este contrato de trabajo es de {{ $contractDetails['duration'] }}.
    </p>

    <p>
        Se deja expresa constancia que el trabajador ingresó al servicio el día {{ $worker->contract->hire_date }}.
    </p>

    <p>
        El trabajador declara que la dirección especificada corresponde a su actual domicilio y será su obligación
        informar por escrito al Colegio cualquier cambio que hubiere en el futuro.
    </p>

    <p>
        Se entiende incorporadas al presente contrato todas las disposiciones legales que se dicten con posterioridad a
        la fecha de suscripción y que tengan relación con él.
    </p>

    <div class="signature-table" align="center">
        <table>
            <tr>
                <td align="center" style="padding: 40px; border-top: 1px solid #000;">
                    {{ $worker->school->sustainer->legal_representative }}<br>
                    <small>REPRESENTANTE LEGAL</small>
                </td>
                <td></td>
                <td align="center" style="padding: 40px; border-top: 1px solid #000;">
                    {{ $worker->name }} {{ $worker->last_name }}<br>
                    <small>TRABAJADOR</small>
                </td>
            </tr>
        </table>
    </div>

    <button onclick="window.print()"> <i class='bx bxs-printer'></i>
        Imprimir Contrato</button>
</body>

</html>
