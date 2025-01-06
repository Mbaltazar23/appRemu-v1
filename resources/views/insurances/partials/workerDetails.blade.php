@props(['worker', 'insurance', 'type'])
<div class="mt-4">
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
        <!-- Campos específicos según el tipo de seguro -->
        @if ($type == \App\Models\Insurance::AFP)
            @php
                $cotizacionafp = $worker->parameters->where('name', 'COTIZACIONAFP')->first()->value ?? 0;
                $apv = $worker->parameters->where('name', 'APV')->first()->value ?? 0;
                $others_discounts = $worker->parameters->where('name', 'AFPOTRO')->first()->value ?? 0;
                $unidad = $worker->parameters->where('name', 'APV')->value('unit') ?? 'Pesos';
            @endphp

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="cotizacionafp">Cotización AFP (%)</label>
                    <input type="text" name="cotizacionafp" class="form-control" value="{{ $cotizacionafp }}"
                        readonly>
                </div>

                <div class="col-md-6 form-group">
                    <label for="apv">APV</label>
                    <input type="text" name="apv" class="form-control" value="{{ $apv }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="unit">Unidad APV</label>
                    <select name="unit" class="form-control">
                        <option value="" {{ $unidad == '' ? 'selected' : '' }}>Pesos</option>
                        <option value="UF" {{ $unidad == 'UF' ? 'selected' : '' }}>UF</option>
                    </select>
                </div>

                <div class="col-md-6 form-group">
                    <label for="others_discounts">Otros Descuentos</label>
                    <input type="text" name="others_discounts" class="form-control" value="{{ $others_discounts }}">
                </div>
            </div>
        @else
            @php
                $cotizacionisapre = $worker->parameters->where('name', 'COTIZACIONISAPRE')->first()->value ?? 0;
                $others_discounts = $worker->parameters->where('name', 'ISAPREOTRO')->first()->value ?? 0;
                $cotizacionpactada = $worker->parameters->where('name', 'COTIZACIONPACTADA')->first()->value ?? 0;
                $unidad = $worker->parameters->where('name', 'COTIZACIONPACTADA')->first()->unit ?? 0;
            @endphp

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="cotizacionisapre">Cotización Legal ISAPRE (%)</label>
                    <input type="text" name="cotizacionisapre" class="form-control" value="{{ $cotizacionisapre }}"
                        readonly>
                </div>

                <div class="col-md-6 form-group">
                    <label for="cotization">Cotización Pactada</label>
                    <input type="text" name="cotization" class="form-control" value="{{ $cotizacionpactada }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="unit">Unidad</label>
                    <select name="unit" class="form-control">
                        <option value="" {{ $unidad == '' ? 'selected' : '' }}>Pesos</option>
                        <option value="UF" {{ $unidad == 'UF' ? 'selected' : '' }}>UF</option>
                    </select>
                </div>

                <div class="col-md-6 form-group">
                    <label for="others_discounts">Otros Descuentos</label>
                    <input type="text" name="others_discounts" class="form-control" value="{{ $others_discounts }}">
                </div>
            </div>
        @endif

        <!-- Botones para guardar cambios o cancelar -->
        <div class="form-group mt-4 text-right">
            <!-- Botones para modificar o desvincular -->
            <button type="submit" name="operation" value="modificar" class="btn btn-primary">Modificar</button>
            <button type="submit" name="operation" value="desvincular" class="btn btn-danger">Desvincular</button>
        </div>
    </form>
</div>
