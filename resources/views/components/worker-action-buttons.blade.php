<!-- resources/views/components/worker-action-buttons.blade.php -->
<td>
    @foreach ($actions as $action)
        @if (isset($action['condition']) ? $action['condition'] : true)
            @can($action['permission'], $worker)
                <!-- Botón de acción -->
                @if ($action['method'] ?? null === 'DELETE')
                    <!-- Si es método DELETE, se usa un formulario -->
                    <form method="POST" action="{{ $action['route'] }}" class="d-inline"
                        onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?')">
                        @csrf
                        @method($action['method'])
                        <button type="submit" class="btn btn-{{ $action['class'] }} rounded-3 px-3"
                            title="{{ $action['title'] }}">
                            <i class="{{ $action['icon'] }}"></i>
                        </button>
                    </form>
                @else
                    <!-- Si no es DELETE, es un enlace normal -->
                    <a class="text-decoration-none" href="{{ $action['route'] }}"
                        @if ($action['title'] === 'Imprimir Contrato' || $action['title'] === 'Ver Anexos de Contrato') target="_blank" onclick="openPopup(event, '{{ $action['title'] }}')" @endif>
                        <!-- Botón para ver detalles o anexos -->
                        <button class="btn btn-{{ $action['class'] }} rounded-3 px-3" title="{{ $action['title'] }}"
                            @if ($action['title'] === 'Ver Anexos de Contrato' && (!$worker->contract || !$worker->contract->details)) disabled @endif>
                            <i class="{{ $action['icon'] }}"></i>
                        </button>
                    </a>
                @endif
            @endcan
        @endif
    @endforeach
</td>
