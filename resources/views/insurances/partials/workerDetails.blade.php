<form action="{{ route('insurances.setParameters') }}" method="POST">
    <input type="hidden" name="type" value="{{ $insurance->type }}" />
    <input type="hidden" name="insurance_id" value="{{ $insurance->id }}" />
    <input type="hidden" name="worker_id" value="{{ $worker_id }}" />
    @csrf
    <table class="table ">
        <tbody>
            <tr>
                <td><strong>Trabajador</strong></td>
                <td>
                    <select class="form-control" onchange="window.location.href=this.value;">
                        @foreach ($workers as $worker)
                            <option
                                value="{{ route('insurances.index', ['insurance_id' => $insurance->id, 'type' => $type, 'worker_id' => $worker->id]) }}"
                                {{ $worker_id == $worker->id || (!$worker_id && $loop->first) ? 'selected' : '' }}>
                                {{ $worker->name }} {{ $worker->last_name }} -
                                ({{ $worker->getDescriptionWorkerTypes() }})
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            @foreach ($fields as $field)
                <tr>
                    <td><strong>{{ $field['label'] }}</strong></td>
                    <td>
                        @if (isset($field['options']))
                            <div class="d-flex">
                                <select name="{{ $field['name'] }}" class="form-control mr-2">
                                    @foreach ($field['options'] as $option)
                                        <option value="{{ $option }}"
                                            {{ $field['selected'] == $option ? 'selected' : '' }}>{{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="text" name="{{ $field['name'] }}" class="form-control"
                                value="{{ $field['value'] }}" {{ $field['readonly'] ?? false ? 'readonly' : '' }}>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-between mt-4">
        <a class="mr-4 rounded-2 text-decoration-none">
            <button type="submit" name="operation" value="desvincular"
                class="btn btn-sm btn-primary rounded-2 px-3 py-1">Desvincular</button>
        </a>
        <a class="mr-4 rounded-2 text-decoration-none">
            <button type="submit" name="operation" value="modificar"
                class="btn btn-sm btn-warning rounded-2 px-3 py-1">Modificar</button>
        </a>
    </div>
</form>
