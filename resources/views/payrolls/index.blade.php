@extends('layouts.app')

@section('content')

    <div class="page-body">
        <div class="container-xl">
            <div class="card" id="selectionCard">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Emisión de planilla de remuneraciones</h3>
                    <!-- Botón de crear (con permisos de 'create') -->
                    @can('create', App\Models\Payroll::class)
                        <form action="{{ route('payrolls.store') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                Crear
                            </button>
                        </form>
                    @endcan
                </div>
                <div class="card-body">
                    @if ($payrolls->isNotEmpty())
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Año</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payrolls as $payroll)
                                    <tr>
                                        <td>{{ \App\Helpers\MonthHelper::integerToMonth($payroll->month) }}</td>
                                        <td>{{ $payroll->year }}</td>
                                        <td>
                                            <!-- Botón de ver (con ícono de impresora) -->
                                            @can('view', $payroll)
                                                <a href="{{ route('payrolls.show', $payroll) }}" class="text-decoration-none"
                                                    target="_blank" onclick="openPopup(event, 'Planilla de Remuneraciones')">
                                                    <button class="btn btn-info">
                                                        <i class='bx bx-printer'></i>
                                                    </button>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center">
                            {!! $payrolls->links() !!}
                        </div>
                    @else
                        <p>No hay planillas almacenadas.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
@include('commons.sort-table')
