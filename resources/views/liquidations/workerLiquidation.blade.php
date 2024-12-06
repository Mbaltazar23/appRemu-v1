@extends('layouts.app')


@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card mb-4" id="selectionCard">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Liquidaciones emitidas a la fecha de {{ $worker->name }} {{ $worker->last_name }}</h3>
                    <div class="d-flex">
                        <!-- Botón Volver -->
                        <button type="button" class="btn btn-secondary px-4 py-2"
                            onclick="window.location.href='{{ route('liquidations.selectWorker', ['workerType' => $worker->worker_type]) }}'">Volver</button>
                        <!-- Botón Crear, al lado del botón Volver -->
                        <button class="btn btn-primary ms-2"
                            onclick="window.location.href='{{ route('liquidations.create', [$worker->id]) }}'">Crear</button>
                    </div>
                </div>
                <div class="card-body">
                    <p><strong>Rut:</strong> {{ $worker->rut }}</p>
                    <p><strong>Tipo de trabajador:</strong> {{ $worker->getDescriptionWorkerTypes() }}</p>
                    <br>
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Año</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($liquidations as $liquidation)
                                    <tr>
                                        <td>{{ App\Helpers\MonthHelper::integerToMonth($liquidation->month) }}</td>
                                        <td>{{ $liquidation->year }}</td>
                                        <td> <!-- Botón "Ver Liquidación" -->
                                            <a class="text-decoration-none">
                                                <button class="btn btn-success rounded-3 px-3"
                                                    onclick="viewLiquidation({{ $liquidation->id }})">
                                                    <i class='bx bx-show'></i>
                                                </button>
                                            </a>
                                            <a class="text-decoration-none">
                                                <button class="btn btn-info rounded-3 px-3"
                                                    onclick="printLiquidation({{ $liquidation->id }})">
                                                    
                                                    <i class='bx bxs-printer'></i>
                                                </button>
                                            </a>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom_scripts')
    <script>
        // Ver Liquidación en una ventana emergente
        function viewLiquidation(liquidationId) {
            // Llamamos a la ruta para obtener la glosa de la liquidación
            fetch(`/liquidations/${liquidationId}/glosa`)
                .then(response => response.text())
                .then(data => {
                    // Abrimos una nueva ventana con la glosa
                    const viewWindow = window.open('', '', 'height=600,width=800');
                    viewWindow.document.write('<html><head><title>Liquidación Detallada</title></head><body>');
                    viewWindow.document.write(data); // Agregamos la glosa obtenida
                    viewWindow.document.write('</body></html>');
                    viewWindow.document.close();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Imprimir Liquidación desde una ventana emergente
        function printLiquidation(liquidationId) {
            // Llamamos a la ruta para obtener la glosa de la liquidación
            fetch(`/liquidations/${liquidationId}/glosa`)
                .then(response => response.text())
                .then(data => {
                    // Abrimos una nueva ventana para impresión
                    const printWindow = window.open('', '', 'height=600,width=800');
                    printWindow.document.write('<html><head><title>Liquidación</title></head><body>');
                    printWindow.document.write(data); // Agregamos la glosa obtenida
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    printWindow.print(); // Ejecuta la impresión automáticamente
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
@endpush
