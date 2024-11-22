<div class="btn-group ml-3" role="group" aria-label="Acciones sobre seguro">
    @foreach ($actions as $action)
        @can($action['permission'], $insurance)
            <!-- Si el permiso es válido, generamos el botón -->
            @if ($action['method'] ?? null === 'DELETE')
                <!-- Si el método es DELETE, usamos un formulario -->
                <form action="{{ $action['route'] }}" method="POST" style="display:inline;">
                    @csrf
                    @method($action['method'])
                    <button type="submit" class="btn btn-{{ $action['type'] }} rounded-3 px-4"
                            onclick="return confirm('¿Estás seguro de eliminar este seguro?')" title="{{ $action['name'] }}">
                        <i class="{{ $action['icon'] }}"></i>
                    </button>
                </form>
            @else
                <!-- Si no es DELETE, generamos un enlace normal -->
                <a href="{{ $action['route'] }}" 
                   class="btn btn-{{ $action['type'] }} rounded-3 px-4" 
                   title="{{ $action['name'] }}"
                   @if (isset($action['popup']) && $action['popup'] === true)
                       onclick="openPopup(event, '{{ $action['name'] }}', '{{ $action['route'] }}')"
                       target="_blank"
                   @endif>
                    <i class="{{ $action['icon'] }}"></i>
                </a>
            @endif
            <!-- Espaciado entre los botones -->
            @if (!$loop->last)
                &nbsp;&nbsp;
            @endif
        @endcan
    @endforeach
</div>