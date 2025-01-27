<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta de Liquidación</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Estilo para cabecera centrada */
        .header-title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        td,
        th {
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        /* Estilo para la caja que rodea la info de la escuela */
        .school-info {
            border: 1px solid #333;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        /* Estilo para los datos del trabajador */
        .worker-info-container {
            margin-bottom: 20px;
        }

        /* Tabla para los datos del trabajador (nombre y rut) */
        .worker-info-container table {
            width: 100%;
            margin-top: 10px;
        }

        .worker-info-container td {
            padding: 8px;
            text-align: left;
        }

        .worker-info-container .worker-name {
            width: 50%;
            font-weight: bold;
            /* Negrita en nombre */
        }

        .worker-info-container .worker-rut {
            width: 50%;
            text-align: right;
            font-weight: bold;
            /* Negrita en RUT */
        }

        /* Estilo para la liquidación */
        .liquidation-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 15px;
        }

        /* Estilos de recibi conforme */
        .recibi-conforme {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 30px;
            font-size: 14px;
            font-weight: bold;
        }

        .recibi-conforme-line {
            display: inline-block;
            width: 250px;
            border-top: 1px solid #333;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Cabecera de la escuela con recuadro -->
        <div class="school-info">
            <div class="header-title">{!! $headerData['school_name'] !!}</div>
            <br>
            <table>
                <tr>R.U.T: {!! $headerData['school_rut'] !!} &nbsp;&nbsp; &nbsp;R.B.D: {!! $headerData['school_rbd'] !!}</tr>
            </table>
        </div>

        <!-- Datos de la liquidación, mes y año en tabla -->
        <table>
            <tr>
                <td class="liquidation-title" colspan="2">LIQUIDACIÓN TRABAJADOR
                    &nbsp;&nbsp;&nbsp;&nbsp;{!! $headerData['month_txt'] !!} / {!! $headerData['year'] !!}</td>
            </tr>
        </table>

        <!-- Datos del trabajador (Nombre, RUT, Carga Horaria, Función, Días Trabajados) en tabla -->
        <table>
            <tr>
                <td class="worker-name">Nombre: {!! $headerData['worker_name'] !!} {!! $headerData['worker_last_name'] !!}</td>
                <td class="worker-rut">R.U.T.: {!! $headerData['worker_rut'] !!}</td>
                <td>Carga horaria (horas): {!! $headerData['workload'] !!}</td>
            </tr>
            <tr>
                <td>{!! $headerData['worker_function'] !!}</td>
                <td>Días trabajados: {!! $headerData['days_worked'] !!}</td>
                <td>Dias de Ausencia: {!! $headerData['absent_days'] !!}</td>
            </tr>
        </table>

        <!-- Detalles de la liquidación -->
        <table>
            <tbody>
                {!! $details !!}
            </tbody>
        </table>

        <!-- Firma Recibi Conforme -->
        <table style="width: 60%; margin: 30px auto;">
            <tr>
                <td style="text-align: center; padding-right: 10px;">
                    <span><strong>Recibí conforme</strong></span>
                </td>
                <td style="width: 150px; text-align: center; vertical-align: middle;">
                    <div class="recibi-conforme-line"></div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
