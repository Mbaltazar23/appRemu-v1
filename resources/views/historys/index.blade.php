<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Acciones</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Contenedor principal */
        .container {
            width: 80%;
            max-width: 1000px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Título */
        h3 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Estilos de la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            font-weight: bold;
        }

        /* Alternancia de filas */
        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        tr:hover td {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }

            h3 {
                font-size: 20px;
            }

            table {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Historial de Acciones</h3>

        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historys as $history)
                    <tr>
                        <td><strong>{{ $history->user->name }}</strong></td>
                        <td>{{ $history->action }}</td>
                        <td>{{ $history->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
