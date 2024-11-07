<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Trabajadores</title>
    @vite('resources/sass/app.scss')
    @stack('custom_styles')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .select-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            /* Ocupa todo el ancho disponible */
            max-width: 600px;
            /* Limita el ancho máximo para mantener el diseño */
        }

        .custom-select {
            width: 100%;
            /* Ocupa todo el ancho del contenedor */
            height: 200px;
            /* Ajusta la altura del select */
            margin-bottom: 10px;
            /* Espacio entre los selectores */
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            /* Ocupa todo el ancho disponible */
            max-width: 600px;
            /* Limita el ancho máximo para mantener el diseño */
            margin-top: 20px;
            /* Espacio entre la lista y los botones */
        }

        .button-container button {
            width: 48%;
            /* Cada botón ocupa casi la mitad del contenedor */
            padding: 5px 10px;
            /* Ajusta el padding para compactar el botón */
            font-size: 14px;
            /* Ajusta el tamaño de fuente si es necesario */
        }
    </style>
    <script>
        $(document).ready(function() {
            $('.custom-select').dblclick(function() {
                const selectedOption = $(this).find('option:selected');

                if (selectedOption.length > 0) {
                    const targetSelect = $(this).is('#nonAppliedWorkersSelect') ? '#appliedWorkersSelect' :
                        '#nonAppliedWorkersSelect';
                    selectedOption.each(function() {
                        $(this).appendTo(targetSelect);
                    });
                }
            });
        });
    </script>
</head>

<body>
    <div class="container mt-4">
        <h2 class="text-center">Seleccionar Trabajadores para El Bono
            {{ $bonus->school->tuitions->where('tuition_id', $bonus->tuition_id)->first()->title }}</h2>
        <form id="workersForm" method="POST" action="{{ route('bonuses.update-workers', $bonus->id) }}">
            @csrf
            <div class="select-container">
                <label for="nonAppliedWorkersSelect">Trabajadores No Asociados al Bono</label>
                <select id="nonAppliedWorkersSelect" class="form-control custom-select" multiple>
                    @foreach ($nonAppliedWorkers as $worker)
                        <option value="{{ $worker->id }}">
                            {{ $worker->name }} {{ $worker->last_name }}
                        </option>
                    @endforeach
                </select>

                <label for="appliedWorkersSelect">Trabajadores Asociados</label>
                <select id="appliedWorkersSelect" class="form-control custom-select" name="workers[]" multiple>
                    @foreach ($appliedWorkers as $workerId)
                        @php $worker = $workers->find($workerId); @endphp
                        <option value="{{ $worker->id }}" selected>
                            {{ $worker->name }} {{ $worker->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="button-container">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <button type="button" class="btn btn-secondary" onclick="window.close()">Cerrar</button>
            </div>
        </form>

        @if (session('message'))
            <script>
                alert("{{ session('message') }}");
            </script>
        @endif
    </div>
</body>

</html>
