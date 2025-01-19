<td>
    @foreach ($actions as $action)
        @can($action['permission'], $worker)
            @if ($action['method'] ?? null === 'DELETE')
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
                <a class="text-decoration-none {{ $action['disabled'] ?? false ? 'disabled' : '' }}"
                    href="{{ $action['disabled'] ?? false ? '#' : $action['route'] }}"
                    @if ($action['title'] === 'Imprimir Contrato' && !$action['disabled']) target="_blank" @endif>
                    <button class="btn btn-{{ $action['class'] }} rounded-3 px-3"
                        title="{{ $action['title'] }}" {{ $action['disabled'] ?? false ? 'disabled' : '' }}>
                        <i class="{{ $action['icon'] }}"></i>
                    </button>
                </a>
            @endif
        @endcan
    @endforeach
</td>
