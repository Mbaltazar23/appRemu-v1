<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquidaciones Emitidas en el Mes de {{ $mountText }} de {{ $year }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .glosa {
            margin-bottom: 30px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .glosa-content {
            font-size: 14px;
            line-height: 1.5;
        }

        /* Estilo para el mensaje de no hay glosas */
        .no-glosas-message {
            font-size: 30px;
            font-weight: bold;
            color: #ff0000;
            text-align: center;
            margin-top: 50px;
        }

        /* Estilo para el botón de imprimir */
        .print-button-container {
            text-align: center;
            margin-top: 30px;
        }

        .print-button {
            font-size: 18px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .print-button:hover {
            background-color: #0056b3;
        }

        /* Ocultar el contenedor después de la impresión */
        .hide-container {
            display: none;
        }
    </style>
</head>

<body>
    @if (count($glosas) == 0)
        <div class="no-glosas-message">
            No hay liquidaciones emitidas en este mes y año
        </div>
    @else
        <!-- Mostrar las glosas de las liquidaciones -->
        @foreach ($glosas as $glosa)
            <div class="glosa">
                <div class="glosa-content">
                    {!! $glosa !!}
                </div>
            </div>
        @endforeach
    @endif
    <!-- Script para ocultar el botón y ejecutar la impresión -->
    <script>
        // Imprimir automáticamente al cargar la ventana emergente
        window.print();
    </script>
</body>

</html>
