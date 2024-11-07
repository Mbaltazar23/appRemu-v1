<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anexos de Contrato</title>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs"></script>
    <!-- Agregar Vite y los estilos CSS globales -->
    @vite('resources/sass/app.scss')

    <!-- Agregar los estilos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    @push('custom_styles')
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f8f9fa;
                color: #495057;
            }

            .container-xl {
                margin-top: 20px;
            }

            .card-header {
                background-color: #007bff;
                color: white;
                font-size: 1.25rem;
                font-weight: bold;
            }

            .card-body {
                background-color: #ffffff;
                border-radius: 10px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            /* Tabla de anexos */
            .table {
                width: 100%;
                border-collapse: collapse;
            }

            .table th,
            .table td {
                padding: 10px;
                border: 1px solid #dee2e6;
                text-align: left;
            }

            .table th {
                background-color: #007bff;
                color: white;
            }

            .table-striped tr:nth-child(even) {
                background-color: #f8f9fa;
            }

            .form-label {
                font-weight: bold;
                color: #495057;
            }

            .form-control {
                border-radius: 5px;
            }

            .mb-3 {
                margin-bottom: 15px;
            }

            /* Botones */
            .btn-primary {
                background-color: #007bff;
                border-color: #007bff;
            }

            .btn-primary:hover {
                background-color: #0056b3;
                border-color: #004085;
            }

            .btn-success {
                background-color: #28a745;
                border-color: #28a745;
            }

            .btn-success:hover {
                background-color: #218838;
                border-color: #1e7e34;
            }

            .btn-secondary {
                background-color: #6c757d;
                border-color: #6c757d;
            }

            .btn-secondary:hover {
                background-color: #5a6268;
                border-color: #545b62;
            }
        </style>
    @endpush
</head>

<body>
    <div class="container-xl">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Anexos del Contrato de {{ $worker->name }} {{ $worker->last_name }}</h5>
                    </div>
                    <div class="card-body">

                        {{-- Botón para abrir el modal --}}
                        <button id="toggle-form-btn" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#addAnnexModal">
                            Añadir Anexo
                        </button>

                        {{-- Tabla de anexos --}}
                        <div id="annexes-table" class="table-container mt-4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($annexes as $index => $annex)
                                        <tr>
                                            <td>{{ $annex['annex_name'] }}</td>
                                            <td>{{ $annex['annex_description'] }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('contracts.deleteAnnex', $worker) }}"
                                                    class="d-inline"
                                                    onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="annex_id" value="{{ $annex['id'] }}" />
                                                    <button type="submit" class="btn btn-danger rounded-3 px-3"
                                                        title="Eliminar Anexo">
                                                        <i class='bx bx-trash'></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Bootstrap para agregar un anexo -->
    <div class="modal fade" id="addAnnexModal" tabindex="-1" aria-labelledby="addAnnexModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAnnexModalLabel">Agregar Anexo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('contracts.storeAnnex', $worker) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="annex_name" class="form-label">Nombre del Anexo</label>
                            <input type="text" id="annex_name" name="annex_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="annex_description" class="form-label">Descripción del Anexo</label>
                            <textarea id="annex_description" name="annex_description" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar Anexo</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
