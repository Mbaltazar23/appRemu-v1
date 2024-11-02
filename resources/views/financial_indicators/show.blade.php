@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3>
                        @if ($index === 'correccion_monetaria')
                            {{ __('Índice de Corrección Monetaria') }}
                        @elseif ($index === 'uf')
                            {{ __('Mantenedor de indices económicos y previsionales') }}
                        @elseif ($index === 'impuesto_renta')
                            {{ __('Mantenedor de valores y topes de impuesto a la renta') }}
                        @elseif ($index === 'asignacion_familiar')
                            {{ __('Mantenedor de valores y topes para asignación familiar') }}
                        @endif
                    </h3>
                    <br>
                </div>
                <div class="card-body">
                    @if ($index === 'correccion_monetaria')
                        @include('financial_indicators.partials.monetary_correction', ['data' => $data])
                    @elseif ($index === 'uf')
                        <p><strong>UF:</strong> <span>${{ $values['uf'] }}</span></p>
                        <p><strong>UTM:</strong> <span>${{ $values['utm'] }}</span></p>
                    @elseif($index === 'impuesto_renta' || $index === 'asignacion_familiar')
                        <form name="forma" action="{{ route('financial-indicators.modify') }}" method="POST">
                            @csrf
                            @if ($index === 'impuesto_renta')
                                @include('financial_indicators.partials.incomeTax', [
                                    'minLimits' => $minLimits,
                                    'maxLimits' => $maxLimits,
                                    'impValues' => $impValues,
                                    'rebValues' => $rebValues,
                                ])
                            @elseif ($index === 'asignacion_familiar')
                                @include('financial_indicators.partials.family_all', [
                                    'minLimits' => $minLimits,
                                    'maxLimits' => $maxLimits,
                                    'impValues' => $impValues,
                                ])
                            @endif
                            <div class="text-center mt-3">
                                <h5>(*) Todos los campos son obligatorios</h5>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <div></div>
                                <input type="button" class="btn btn-primary" value="Modificar"
                                    onclick='alert("Recuerde de las modificaciones tributarias dispuestas por el Estado"); document.forma.submit()'>
                            </div>
                        </form>
                    @endif
                    <button id="backButton" class="btn btn-secondary mt-3">{{ __('Volver') }}</button>
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
