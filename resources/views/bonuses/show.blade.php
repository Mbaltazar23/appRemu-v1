@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title">Detalles del Bono</h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-3">
                <div class="table-responsive">
                    <p>
                        <strong>Título de ítem:</strong>
                        {{ $bonus->school->tuitions->where('tuition_id', $bonus->tuition_id)->first()->title }}
                        <br />
                        <strong>Tipo de Trabajador:</strong> {{ $workerOptions[$bonus->type] ?? 'No definido' }} <br />
                        <strong>¿Es un bono o un descuento?:</strong> {{ $bonus->is_bonus == 0 ? 'Bono' : 'Descuento' }}
                        <br />
                        <strong>¿Es imponible?:</strong> {{ $bonus->taxable == 0 ? 'Sí' : 'No' }} <br />
                        <strong>¿Es imputable a la renta mínima?:</strong> {{ $bonus->imputable == 0 ? 'Sí' : 'No' }} <br />
                        <strong>¿Cómo se aplica?:</strong> {{ $applicationOptions[$bonus->application] ?? 'No definido' }}
                        <br />
                        <strong>Monto (en pesos):</strong>
                        {{ $bonus->school ? $bonus->school->parameters->where('name', $bonus->title)->value('value') ?? 0 : 0 }}
                        <br />
                        <strong>Porcentaje a aplicar:</strong> {{ $bonus->factor * 100 ?? 100 }}% <br />
                        <strong>Meses en los que se aplica:</strong>
                        @if (strpos($allChecked, '1') === false)
                            <span>No hay meses seleccionados.</span>
                        @else
                            <ul>
                                @for ($i = 1; $i <= 12; $i++)
                                    @if ($allChecked[$i - 1] == '1')
                                        <li>{{ DateTime::createFromFormat('!m', $i)->format('M') }}</li>
                                    @endif
                                @endfor
                            </ul>
                        @endif
                    </p>
                </div>
                <span>
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
