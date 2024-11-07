@push('custom_scripts')
    <script src="{{ asset('js/sort-table.js') }}"></script>

    <script>
        function openPopup(event, titulo) {
            event.preventDefault(); // Evita el comportamiento por defecto
            const url = event.currentTarget.href;
            window.open(url, titulo, 'width=800,height=600');
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
