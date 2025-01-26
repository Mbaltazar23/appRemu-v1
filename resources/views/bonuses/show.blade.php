@extends('layouts.app')

@section('content')
<div class="container-xl">
    <!-- Page title -->
    <div class="page-header d-print-none">
        <h2 class="page-title">
            Bono o Decuentos
        </h2>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card p-5">
            <div class="table-responsive">
                <table class="table mb-0"> <!-- Solo se aplica mb-0 a la tabla -->
                    <tbody>
                        <tr>
                            <th>Título de ítem:</th>
                            <td>{{ $bonus->school->tuitions->where('tuition_id', $bonus->title)->first()->title }}</td>
                        </tr>
                        <tr>
                            <th>Tipo de Trabajador</th>
                            <td>{{ $workerOptions[$bonus->type] ?? 'No definido' }}</td>
                        </tr>
                        <tr>
                            <th>¿Es un bono o un descuento?</th>
                            <td>{{ $bonus->is_bonus == 0 ? 'Bono' : 'Descuento' }}</td>
                        </tr>
                        <tr>
                            <th>¿Es imponible?</th>
                            <td>{{ $bonus->taxable == 0 ? 'Sí' : 'No' }}</td>
                        </tr>
                        <tr>
                            <th>¿Es imputable a la renta mínima?</th>
                            <td>{{ $bonus->imputable == 0 ? 'Sí' : 'No' }}</td>
                        </tr>
                        <tr>
                            <th>¿Cómo se aplica?</th>
                            <td>{{ $applicationOptions[$bonus->application] ?? 'No definido' }}</td>
                        </tr>
                        <tr>
                            <th>Monto (en pesos)</th>
                            <td>
                                {{ number_format(
                                        optional(
                                            $bonus->school->parameters->where('name', $bonus->tuition_id)->where('worker_id', 0)->first(),
                                        )->value ?? 0,
                                        0,
                                        ',',
                                        ',',
                                    ) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Porcentaje a aplicar:</td>
                            <td>{{ $bonus->factor * 100 ?? 100 }}%</td>
                        </tr>
                        <tr>
                            <th>Meses en los que se aplica</td>
                            <td>
                                @if (strpos($allChecked, '1') === false)
                                <span>No hay meses seleccionados.</span>
                                @else
                                <ul>
                                    @php
                                    // Configura el locale a español
                                    setlocale(LC_TIME, 'es_ES.UTF-8', 'esp');
                                    @endphp
                                    @for ($i = 1; $i <= 12; $i++)
                                    @if ($allChecked[$i - 1] == '1')
                                    <li>{{ ucfirst(strftime('%B', mktime(0, 0, 0, $i, 1, date('Y')))) }}
                                    </li>
                                    @endif
                                    @endfor
                                </ul>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Botones de acción -->
            <span class="mt-4">
                <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('bonuses.partials.list') }}">
                    <button class="btn btn-sm btn-info rounded-2">Volver al inicio</button>
                </a>
                @can('update', $bonus)
                <a class="mr-4 rounded-2 text-decoration-none" href="{{ route('bonuses.edit', $bonus) }}">
                    <button class="btn btn-sm btn-primary rounded-2">Editar</button>
                </a>
                @endcan
            </span>
        </div>
    </div>
</div>
@endsection
