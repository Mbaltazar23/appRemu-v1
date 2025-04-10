<!--views/workers/index.blade.php-->
@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Listado de Trabajadores
                </span>
                <div>
                    <a class="d-inline ml-2 text-decoration-none">
                        <button title="Importar Datos de Trabajadores" class="import-btn btn btn-success rounded-3 px-3 py-1">
                            Importar &nbsp;<i class='bx bxs-file-import'></i>
                        </button>
                    </a>
                    &nbsp;
                    @can('create', App\Models\Worker::class)
                        <a class="d-inline ml-2 text-decoration-none" href="{{ route('workers.create') }}">
                            <button class="btn btn-primary rounded-3 px-3 py-1">
                                Crear
                            </button>
                        </a>
                    @endcan
                    &nbsp;
                    <a class="d-inline ml-2 text-decoration-none" href="{{ route('workers.settlements') }}">
                        <button class="btn btn-secondary rounded-3 px-3 py-1">Listar Finiquitados</button>
                    </a>
                </div>
                <form id="import-form" method="POST" action="{{ route('workers.import') }}" enctype="multipart/form-data"
                    style="display:none;">
                    @csrf
                    <input type="file" name="file">
                </form>
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="table-responsive">
                    <table class="table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)" class="sort-table">Nombre</th>
                                <th onclick="sortTable(2)" class="sort-table">Tipo</th>
                                <th onclick="sortTable(1)" class="sort-table">Creado en</th>
                                <th onclick="sortTable(3)" class="sort-table">Actualizado</th>
                                <th onclick="sortTable(4)" class="sort-table">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dentro del foreach de los trabajadores -->
                            @foreach ($workers as $worker)
                                <tr>
                                    <td>{{ $worker->name }} {{ $worker->last_name }}</td>
                                    <td>{{ $worker->getWorkerTypes()[$worker->worker_type] }}</td>
                                    <td>{{ $worker->created_at }}</td>
                                    <td>{{ $worker->contract->updated_at->diffForHumans() }}</td>
                                    <!-- Llamada al componente para las acciones -->
                                    <x-worker-action-buttons :worker="$worker" />
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($workers->hasPages())
                    <div class="card-footer pb-0">
                        {{ $workers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('custom_scripts')
    <script>
        const importBtn = document.querySelector('.import-btn');
        const importForm = document.querySelector('#import-form');

        importBtn.addEventListener('click', () => {
            const fileInput = importForm.querySelector('input[name="file"]');
            fileInput.click();
        });
        importForm.addEventListener('change', () => {
            importForm.submit();
        });
    </script>
@endpush

@include('commons.sort-table')
