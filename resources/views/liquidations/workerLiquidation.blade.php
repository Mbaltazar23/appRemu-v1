@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card mb-4" id="selectionCard">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Liquidaciones emitidas para {{ $worker->name }} {{ $worker->last_name }}</h3>
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
                    <p><strong>Tipo de trabajador:</strong> {{ $worker->getFunctionWorkerDescription() }}</p>
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
                                        <td><!-- Aquí puedes agregar botones de acción si los necesitas --></td>
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
