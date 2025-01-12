@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Historial de Acciones
                </span>
            </h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">

                        <thead>
                            <tr>
                                <th onclick="sortTable(0)" class="sort-table">Usuario</th>
                                <th onclick="sortTable(1)" class="sort-table">Acción</th>
                                <th onclick="sortTable(2)" class="sort-table">Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($historys as $history)
                                <tr>
                                    <td><strong>{{ $history->user->name }}</strong></td>
                                    <td>{{ $history->action }}</td>
                                    <td>{{ $history->created_at->format('d-m-Y H:i:s') }}</td>
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
