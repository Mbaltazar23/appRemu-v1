@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Anexos del Contrato de {{ $worker->name }} {{ $worker->last_name }}
                </span>
                <div>
                    @can('create', App\Models\Worker::class)
                        <a class="d-inline ml-2 text-decoration-none" href="{{ route('contracts.createAnnex', $worker) }}">
                            <button class="btn btn-primary rounded-3 px-3 py-1">
                                Crear
                            </button>
                        </a>
                        &nbsp;
                    @endcan
                    <a class="d-inline ml-2 text-decoration-none" href="{{ route('workers.index') }}">
                        <button class="btn btn-secondary rounded-3 px-3 py-1">Volver al Inicio</button>
                    </a>
                </div>
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card p-5">
                <div class="table-responsive">
                    <table class="table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)" class="sort-table">Nombre</th>
                                <th onclick="sortTable(1)" class="sort-table">Descripción</th>
                                <th onclick="sortTable(2)" class="sort-table">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($annexes as $annex)
                                <tr>
                                    <td>{{ $annex['annex_name'] }}</td>
                                    <td>{{ $annex['annex_description'] }}</td>
                                    <td>
                                        @can('update', $worker)
                                            <a href="{{ route('contracts.editAnnex', ['worker' => $worker, 'annex' => $annex['id']]) }}"
                                                class="text-decoration-none"> <button class="btn btn-primary rounded-3 px-3">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('delete', $worker)
                                            <form method="POST"
                                                action="{{ route('contracts.deleteAnnex', ['worker' => $worker, 'annex' => $annex['id']]) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar este anexo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger rounded-3 px-3">
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
@include('commons.sort-table')
