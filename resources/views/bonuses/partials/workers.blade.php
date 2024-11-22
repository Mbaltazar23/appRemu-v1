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

        /* Contenedor de los selects */
        .select-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            max-width: 1200px;  /* Máxima anchura del contenedor */
            gap: 15px;  /* Espacio pequeño entre los selects */
            margin-bottom: 20px;
        }

        /* Cada div que contiene los selects */
        .select-wrapper {
            flex-grow: 1;  /* Hace que cada select ocupe la misma cantidad de espacio */
            display: flex;
            flex-direction: column;
        }

        /* Los selects dentro de sus divs */
        .select-wrapper select {
            width: 100%;  /* El select ocupa todo el ancho de su contenedor */
            height: 200px;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            width: 100%;
            max-width: 800px;
            margin-top: 20px;
        }

        .button-container button {
            width: auto;
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('.custom-select').dblclick(function() {
                const selectedOption = $(this).find('option:selected');

                if (selectedOption.length > 0) {
                    const targetSelect = $(this).is('#nonAppliedWorkersSelect') ? '#appliedWorkersSelect' : '#nonAppliedWorkersSelect';
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
            ({{ $bonus->school->tuitions->where('tuition_id', $bonus->title)->first()->title }})</h2>
        <form id="workersForm" method="POST" action="{{ route('bonuses.update-workers', $bonus->id) }}">
            @csrf
            <div class="select-container">
                <!-- Contenedor del Select de Trabajadores No Asociados -->
                <div class="select-wrapper">
                    <label for="nonAppliedWorkersSelect" class="form-label">Trabajadores No Asociados al Bono</label>
                    <select id="nonAppliedWorkersSelect" class="form-control custom-select" multiple>
                        @foreach ($nonAppliedWorkers as $worker)
                            <option value="{{ $worker->id }}">
                                {{ $worker->name }} {{ $worker->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Contenedor del Select de Trabajadores Asociados -->
                <div class="select-wrapper">
                    <label for="appliedWorkersSelect" class="form-label">Trabajadores Asociados</label>
                    <select id="appliedWorkersSelect" class="form-control custom-select" name="workers[]" multiple>
                        @foreach ($appliedWorkers as $workerId)
                            @php $worker = $workers->find($workerId); @endphp
                            <option value="{{ $worker->id }}" selected>
                                {{ $worker->name }} {{ $worker->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
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
