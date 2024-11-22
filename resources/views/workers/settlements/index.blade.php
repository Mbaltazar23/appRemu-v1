<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabajadores Finiquitados</title>

    <!-- Inclusión de los estilos de Vite y custom_styles -->
    @vite('resources/sass/app.scss')
    @stack('custom_styles')

</head>

<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Trabajadores Finiquitados</h2>

        <!-- Verificar si no hay trabajadores -->
        @if ($workers->isEmpty())
            <p class="text-center">No hay trabajadores finiquitados en este momento.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Tipo de Trabajador</th>
                            <th>Fecha de Finiquito</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($workers as $worker)
                            <tr>
                                <td>{{ $worker->name }}</td>
                                <td>{{ $worker->last_name }}</td>
                                <td>{{ $worker->getWorkerTypes()[$worker->worker_type] }}</td>
                                <td>{{ \Carbon\Carbon::parse($worker->settlement_date)->format('d-m-Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación con Bootstrap -->
            <div class="d-flex justify-content-center">
                {{ $workers->links('pagination::bootstrap-5') }}
            </div>
        @endif

        <!-- Botón de Cerrar al final -->
        <div class="text-center mt-4">
            <button class="btn btn-primary" onclick="window.close()">Cerrar</button>
        </div>
    </div>
</body>

</html>
