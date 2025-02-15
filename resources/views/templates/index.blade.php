@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <!-- Primer Card: Selector de Tipo -->
            <div class="card p-3 mb-4">
                <div class="card-header">
                    <h4>{{ __('Mantenedor de ítems de liquidaciones para ') }}{{ $typeTitle }}</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="form-group mb-4 w-75">
                            <label for="typeItem" class="h4">Selecciona el tipo de ítem de Liquidación</label>
                            <select name="typeItem" class="form-control" onchange="window.location.href=this.value;">
                                @foreach ($templateTypes as $value => $label)
                                    <option value="{{ route('templates.index', ['typeItem' => $value]) }}"
                                        {{ request('typeItem') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @can('create', App\Models\Template::class)
                            <a href="{{ route('templates.create', ['typeItem' => $typeItem]) }}"
                                class="btn btn-primary ml-3">Agregar Línea</a>
                        @endcan
                    </div>
                </div>
            </div>
            <!-- Segundo Card: Listado de Plantillas -->
            <div class="card p-3">
                <div class="card-header">
                    <h4>{{ __('Listado de Plantillas de Liquidación') }}</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)" class="sort-table">Item</th>
                                <th onclick="sortTable(1)" class="sort-table">Actualizado</th>
                                <th onclick="sortTable(2)" class="sort-table">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($templates as $template)
                                <tr>
                                    <!-- Mostrar el título procesado -->
                                    <td>{{ $template->tuition->title ?? $template->tuition_id }}</td>
                                    <!-- Mostrar la fecha de actualización -->
                                    <td>{{ \Carbon\Carbon::parse($template['updated_at'])->diffForHumans() }}</td>
                                    <td>
                                        @can('update', $template)
                                            <!-- Botón para editar -->
                                            <a href="{{ route('templates.edit', [$template, 'typeItem' => $typeItem]) }}"
                                                class="btn btn-warning">
                                                <i class="bx bx-edit-alt"></i>
                                            </a>
                                        @endcan
                                        <!-- Mostrar botones de posición según la lógica -->
                                        @if ($template->position > 1)
                                            <!-- Botón para mover hacia arriba -->
                                            <a href="{{ route('templates.moveUp', ['template' => $template, 'position' => $template->position]) }}"
                                                class="btn btn-secondary">
                                                <i class="bx bx-up-arrow-alt"></i>
                                            </a>
                                        @endif
                                        <!-- Botón para mover hacia abajo, si no es el último registro -->
                                        @if ($template->position < $templates->count())
                                            <a href="{{ route('templates.moveDown', ['template' => $template, 'position' => $template->position]) }}"
                                                class="btn btn-secondary">
                                                <i class="bx bx-down-arrow-alt"></i>
                                            </a>
                                        @endif
                                        @can('delete', $template)
                                            <!-- Botón para eliminar plantilla -->
                                            <form
                                                action="{{ route('templates.destroy', [$template, 'typeItem' => $typeItem]) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('¿Estás seguro de eliminar esta plantilla?')">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
