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
        .custom-select {
            height: 200px; /* Ajusta la altura del select */
        }
        h2 {
            margin-bottom: 20px; /* Espacio debajo del título */
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Seleccionar Trabajadores para El Bono {{ $bonus->school->tuitions->where('tuition_id', $bonus->tuition_id)->first()->title }}</h2>
        <form id="workersForm" method="POST" action="{{ route('bonuses.update-workers', $bonus->id) }}">
            @csrf
            <div class="d-flex justify-content-center">
                <select id="workersSelect" name="workers[]" class="form-control custom-select mt-3" multiple>
                    @foreach ($workers as $worker)
                        <option value="{{ $worker->id }}" 
                            @if(in_array($worker->id, $appliedWorkers)) selected @endif>
                            {{ $worker->name }} {{ $worker->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex justify-content-between mt-3">
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
