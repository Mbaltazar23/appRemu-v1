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
        El trabajador prestará servicios en {{ $worker->school->name }}, ubicada en {{ $worker->school->address }},
        Comuna de {{ $worker->school->commune }}, como {{ $worker->getFunctionWorkerDescription() }} en el nivel
        {{ $contractDetails['levels'] }}. El empleador podrá modificar la naturaleza de los servicios o el lugar de
        trabajo, siempre que sean labores similares y dentro de la misma ciudad, sin perjuicio para el trabajador. El
        reglamento interno indicará los deberes y obligaciones del trabajador.
    </p>

    @if ($worker->worker_type == App\Models\Worker::WORKER_TYPE_TEACHER)
        <p>
            No obtante El trabajador se compromete a realizar las siguientes actividades curriculares no lectivas como
            docente de aula:
        </p>
        <ul>
            <li>Clases de reforzamiento individual y colectivas a las asignaturas del plan de estudios</li>
            <li>Funcionamiento de talleres</li>
            <li>Investigación, estudio y elaboración de planes y programas de estudio</li>
            <li>Anotación de datos y constancia en formularios oficiales</li>
            <li>Régimen escolar y comportamiento de los alumnos</li>
            <li>Planificación de clases</li>
            <li>Atención individual de alumnos y apoderados</li>
            <li>Consejo de profesores</li>
            <li>Consejo de curso</li>
            <li>Reuniones periódicas con apoderados</li>
            <li>Preparación, selección y confección de material didáctico</li>
            <li>Otras actividades señaladas en el artículo 20 del Decreto Supremo número 453 de 1991 del Ministerio de
                Educación</li>
        </ul>
    @endif

    <p><b>SEGUNDO:</b><br>
        La jornada ordinaria de trabajo será de <b>{{ $worker->working_hours }}</b> horas cronológicas semanales.
        Las que se conforman según anexos por jornada.
    </p>

    <p><b>TERCERO:</b><br>
        El empleador pagará al
        @if ($worker->worker_type == App\Models\Worker::WORKER_TYPE_TEACHER)
            {{ $worker->getDescriptionWorkerTypes() }}
        @elseif ($worker->worker_type == App\Models\Worker::WORKER_TYPE_NON_TEACHER)
            {{ $worker->getDescriptionWorkerTypes() }}
        @endif
        una remuneración aprox (variará segun lo trabajado) de
        <b>${{ number_format($contractDetails['total_remuneration'], 0, ',', '.') }}
            ({{ $contractDetails['remuneration_gloss'] }})</b>, la que incluye el pago de la Remuneración base y demás
        remuneraciones legales y convencionales.
    </p>

    <p><b>CUARTO:</b><br><br>
        El contrato de trabajo tiene una duración de {{ $contractDetails['duration'] }}.
    </p>

    <p>
        El trabajador ingresó al servicio el
        {{ \Carbon\Carbon::parse($worker->contract->hire_date)->format('d-m-Y') }}.
    </p>

    <p>
        El trabajador confirma que la dirección proporcionada es su domicilio actual y se compromete a informar
        cualquier cambio al Colegio.
    </p>

    <p>
        Se incorporan al contrato todas las disposiciones legales que se dicten posteriormente y que lo afecten.
    </p>

    <p>
        El contrato se extiende en dos ejemplares, uno para el empleador y otro para el trabajador, quien declara
        recibirlo a su satisfacción y como reflejo de la relación laboral.
    </p>

    <!-- Agregar los anexos en la sección correspondiente -->
    @if (!empty($contractDetails['annexes']) && count($contractDetails['annexes']) > 0)
        <h5>ANEXOS DEL CONTRATO</h5>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha de Creación</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contractDetails['annexes'] as $annex)
                    <tr>
                        <td>{{ $annex['annex_name'] }}</td>
                        <td>{{ $annex['annex_description'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($annex['created_at'])->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

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
