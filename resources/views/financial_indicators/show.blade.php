@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>
                        @if ($index === 'correccion_monetaria')
                            {{ __('Índice de Corrección Monetaria') }}
                        @elseif ($index === 'uf')
                            {{ __('Mantenedor de índices económicos y previsionales') }}
                        @elseif ($index === 'impuesto_renta')
                            {{ __('Mantenedor de valores y topes de impuesto a la renta') }}
                        @else
                            {{ __('Mantenedor de valores y topes para Asignación familiar') }}
                        @endif
                    </h3>
                    <button id="backButton" class="btn btn-secondary">{{ __('Volver') }}</button>
                </div>
                <div class="card-body">
                    @if ($index === 'correccion_monetaria')
                        @include('financial_indicators.partials.monetary_correction', ['data' => $data])
                    @elseif ($index === 'uf')
                        @include('financial_indicators.partials.indicator_values', [
                            'currentValues' => $currentValues,
                            'previousValues' => $previousValues,
                        ])
                    @elseif($index === 'impuesto_renta' || $index === 'asignacion_familiar')
                        <form name="forma" action="{{ route('financial-indicators.modify') }}" method="POST">
                            <input type="hidden" name="index" value="{{ $index }}" />
                            @csrf
                            @if ($index === 'impuesto_renta')
                                @include('financial_indicators.partials.incomeTax', [
                                    'minLimits' => $minLimits,
                                    'maxLimits' => $maxLimits,
                                    'impValues' => $impValues,
                                    'rebValues' => $rebValues,
                                ])
                            @else
                                @include('financial_indicators.partials.family_all', [
                                    'minLimits' => $minLimits,
                                    'maxLimits' => $maxLimits,
                                    'impValues' => $impValues,
                                ])
                            @endif
                            <div class="text-center mt-4">
                                <h5>(*) Todos los campos son obligatorios</h5>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div></div>
                                <input type="button" class="btn btn-primary" value="Modificar"
                                    onclick='alert("Recuerde de las modificaciones tributarias dispuestas por el Estado"); document.forma.submit()'>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('backButton').addEventListener('click', function() {
            window.location.href = "{{ route('financial-indicators.index') }}";
        });
    </script>
@endsection
