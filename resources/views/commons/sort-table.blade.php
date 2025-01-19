@push('custom_scripts')
    <script src="{{ asset('js/sort-table.js') }}"></script>

    <script>
        function openPopup(event, titulo) {
            event.preventDefault(); // Evita el comportamiento por defecto
            const url = event.currentTarget.href;
            // Crear un enlace dinámico
            const link = document.createElement('a');
            link.href = url;
            link.target = '_blank'; // Asegura que se abra en una nueva pestaña
            // Simular un clic en el enlace
            link.click();
        }
    </script>
@endpush

@push('custom_styles')
    <style>
        .sort-table {
            cursor: pointer;
        }

        .sort-table:after {
            content: ' ▲';
            font-size: 14px;
            font-weight: bold;
            line-height: 1;
        }
    </style>
@endpush
