@props(['worker', 'insurance', 'type'])
<div class="mt-2">
    @php
        $selectWorkerId = request('worker_id') ?: $workers->first()->id;
        $worker = \App\Models\Worker::find($selectWorkerId);
    @endphp
    <!-- Formulario de detalles del trabajador -->
    <form action="{{ route('insurances.setParameters') }}" method="POST">
        <input type="hidden" name="type" value="{{ $insurance->type }}" />
        <input type="hidden" name="insurance_id" value="{{ $insurance->id }}" />
        <input type="hidden" name="worker_id" value="{{ $worker->id }}" />
        @csrf

        <table class="table p-2">
            <tbody>
                @if ($type == \App\Models\Insurance::AFP)
                    @php
                        $cotizacionafp = $worker->parameters->where('name', 'COTIZACIONAFP')->first()->value ?? 0;
                        $apv = $worker->parameters->where('name', 'APV')->first()->value ?? 0;
                        $others_discounts = $worker->parameters->where('name', 'AFPOTRO')->first()->value ?? 0;
                        $unidad = $worker->parameters->where('name', 'APV')->value('unit') ?? 'Pesos';
                    @endphp

                    <!-- AFP Fields -->
                    <tr>
                        <td><strong>Cotización AFP (%)</strong></td>
                        <td><input type="text" name="cotizacionafp" class="form-control" value="{{ $cotizacionafp }}"
                                readonly></td>
                    </tr>

                    <tr>
                        <td><strong>APV</strong></td>
                        <td><input type="text" name="apv" class="form-control" value="{{ $apv }}"></td>
                    </tr>

                    <tr>
                        <td><strong>Unidad APV</strong></td>
                        <td>
                            <select name="unit" class="form-control">
                                <option value="" {{ $unidad == '' ? 'selected' : '' }}>Pesos</option>
                                <option value="UF" {{ $unidad == 'UF' ? 'selected' : '' }}>UF</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Otros Descuentos</strong></td>
                        <td><input type="text" name="others_discounts" class="form-control"
                                value="{{ $others_discounts }}"></td>
                    </tr>
                @else
                    @php
                        $cotizacionisapre = $worker->parameters->where('name', 'COTIZACIONISAPRE')->first()->value ?? 0;
                        $others_discounts = $worker->parameters->where('name', 'ISAPREOTRO')->first()->value ?? 0;
                        $cotizacionpactada =
                            $worker->parameters->where('name', 'COTIZACIONPACTADA')->first()->value ?? 0;
                        $unidad = $worker->parameters->where('name', 'COTIZACIONPACTADA')->first()->unit ?? 0;
                    @endphp

                    <!-- ISAPRE Fields -->
                    <tr>
                        <td><strong>Cotización Legal ISAPRE (%)</strong></td>
                        <td><input type="text" name="cotizacionisapre" class="form-control"
                                value="{{ $cotizacionisapre }}" readonly></td>
                    </tr>

                    <tr>
                        <td><strong>Cotización Pactada</strong></td>
                        <td><input type="text" name="cotization" class="form-control"
                                value="{{ $cotizacionpactada }}"></td>
                    </tr>

                    <tr>
                        <td><strong>Unidad</strong></td>
                        <td>
                            <select name="unit" class="form-control">
                                <option value="" {{ $unidad == '' ? 'selected' : '' }}>Pesos</option>
                                <option value="UF" {{ $unidad == 'UF' ? 'selected' : '' }}>UF</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Otros Descuentos</strong></td>
                        <td><input type="text" name="others_discounts" class="form-control"
                                value="{{ $others_discounts }}"></td>
                    </tr>
                @endif
            </tbody>
        </table>
        <!-- Botones de Acción -->
        <div class="card-footer text-right">
            <button type="submit" name="operation" value="modificar" class="btn btn-primary">Modificar</button>
            <button type="submit" name="operation" value="desvincular" class="btn btn-danger">Desvincular</button>
        </div>
    </form>
</div>
