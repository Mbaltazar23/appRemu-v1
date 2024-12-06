<!-- Fila para Insertar línea después de y Tipo de línea -->
<table class="table table-bordered mb-3" style="width: 100%; table-layout: fixed;">
    <tbody>
        @if ($templates->isEmpty() || $template->position > 0)
            <!-- Si no hay registros de template, asignamos la posición 1 -->
            <input type="hidden" name="position" value="1">
        @else
            <!-- Si existen templates y no tenemos un valor de posición, mostramos el select -->
            <tr>
                <td class="w-50" style="vertical-align: middle; padding: 8px;">
                    <label for="position" class="form-label mb-0">Insertar línea después de</label>
                </td>
                <td style="padding: 8px;">
                    <select name="position" id="position" class="form-select">
                        <option value="0">Seleccione la línea donde irá el ítem</option>
                        @foreach ($templates as $tmpl)
                            <option value="{{ $tmpl->position }}"
                                {{ old('position', $template->position) == $tmpl->position ? 'selected' : '' }}>
                                {{ $tmpl->tuition->title ?? $tmpl->tuition_id }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
        @endif
        <tr>
            <td class="w-50" style="vertical-align: middle; padding: 8px;">
                <label for="code" class="form-label mb-0">Tipo de línea</label>
            </td>
            <td style="padding: 8px;">
                <select name="code" id="code" class="form-select">
                    @foreach ($lineTypes as $code => $description)
                        <option value="{{ $code }}"
                            {{ old('code', $template->code) == $code ? 'selected' : '' }}>
                            {{ $description }}
                        </option>
                    @endforeach
                </select>
            </td>
        </tr>
    </tbody>
</table>
<!-- Fila para Ítem para esta línea -->
<table class="table table-bordered mb-3" style="width: 100%; table-layout: fixed;">
    <tbody>
        <tr>
            <td class="w-50" style="vertical-align: middle; padding: 8px;">
                <label for="tuition_id" class="form-label mb-0">Ítem para esta línea</label>
            </td>
            <td style="padding: 8px;">
                <select name="tuition_id" id="tuition_id" class="form-select">
                    <option value="">Seleccione el item para esta Línea</option>
                    @foreach ($tuitions as $tuition)
                        <option value="{{ $tuition->tuition_id }}"
                            {{ old('tuition_id', $template->tuition_id) == $tuition->tuition_id ? 'selected' : '' }}>
                            {{ $tuition->title }}
                        </option>
                    @endforeach
                </select>
            </td>
        </tr>
    </tbody>
</table>

<!-- Fila para los Checkboxes -->
<!-- Fila para los Checkboxes -->
<table class="table table-bordered mb-3" style="width: 100%; table-layout: fixed;">
    <tbody>
        <tr>
            <td class="w-50" style="vertical-align: middle; padding: 8px;">
                <label for="ignore_zero" class="form-label mb-0">Ignorar si el valor es 0</label>
            </td>
            <td style="padding: 8px;">
                <!-- Campo oculto para enviar 0 cuando no esté marcado -->
                <input type="hidden" name="ignore_zero" value="0">
                <!-- Checkbox que enviará 1 cuando esté marcado -->
                <input type="checkbox" name="ignore_zero" id="ignore_zero" class="form-check-input"
                    {{ old('ignore_zero', $template->ignore_zero ?? false) ? 'checked' : '' }} value="1">
            </td>
        </tr>
        <tr>
            <td class="w-50" style="vertical-align: middle; padding: 8px;">
                <label for="parentheses" class="form-label mb-0">Colocar valor entre paréntesis si va en la 3ra
                    columna</label>
            </td>
            <td style="padding: 8px;">
                <!-- Campo oculto para enviar 0 cuando no esté marcado -->
                <input type="hidden" name="parentheses" value="0">
                <!-- Checkbox que enviará 1 cuando esté marcado -->
                <input type="checkbox" name="parentheses" id="parentheses" class="form-check-input"
                    {{ old('parentheses', $template->parentheses ?? false) ? 'checked' : '' }} value="1">
            </td>
        </tr>
    </tbody>
</table>


<!-- Fila para Texto a mostrar si esta es una línea de tipo texto -->
<table class="table table-bordered mb-3" style="width: 100%; table-layout: fixed;">
    <tbody>
        <tr>
            <td class="w-50" style="vertical-align: middle; padding: 8px;">
                <label for="text" class="form-label mb-0">
                    Texto a mostrar si esta es una línea de tipo texto
                </label>
            </td>
            <td style="padding: 8px;">
                <input type="text" name="text" id="text"
                    value="{{ old('text', $template->code === 'TEX' ? $template->tuition_id : '') }}"
                    class="form-control" @if (old('code', $template->code ?? '') !== 'TEX') disabled @endif>

                <small class="form-text text-muted">
                    Este campo solo es editable si el tipo de línea es "Texto".
                </small>
            </td>
        </tr>
    </tbody>
</table>


@push('custom_styles')
    <style>
        .table td {
            vertical-align: middle;
        }

        .form-check-input-lg {
            transform: scale(1.3);
            /* Aumenta el tamaño de los checkboxes */
            margin-right: 10px;
            /* Espaciado entre el checkbox y su label */
        }

        .form-select,
        .form-control {
            width: 100%;
            /* Asegura que los inputs y selects tengan el mismo tamaño */
        }

        .form-label {
            font-size: 0.9rem;
            /* Ajusta el tamaño de la fuente del label */
        }

        .form-select,
        .form-control {
            font-size: 0.95rem;
            /* Ajusta el tamaño de la fuente del input */
            padding: 0.375rem 0.75rem;
            /* Ajusta el tamaño de padding */
        }

        /* Ajustar la tabla para que sea más compacta */
        .table {
            width: 100%;
            table-layout: fixed;
        }
    </style>
@endpush

@push('custom_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const codeSelect = document.getElementById('code');
            const textField = document.getElementById('text');

            // Función para habilitar o deshabilitar el campo de texto según el valor de 'code'
            function toggleTextField() {
                if (codeSelect.value === 'TEX') {
                    textField.disabled = false; // Habilitar el campo si el tipo es TEX
                } else {
                    textField.disabled = true; // Deshabilitar el campo si el tipo no es TEX
                }
            }

            // Ejecutar la función al cargar la página para manejar el valor inicial
            toggleTextField();

            // Ejecutar la función cada vez que el select 'code' cambie
            codeSelect.addEventListener('change', toggleTextField);
        });
    </script>
@endpush
