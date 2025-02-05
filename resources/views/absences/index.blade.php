@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Lista de {{ __('Inasistencias') }}
                </span>
                @can('create', App\Models\Absence::class)
                    <a class="d-inline ml-5 text-decoration-none" href="{{ route('absences.create') }}">
                        <button class="btn btn-primary rounded-3 px-3 py-1">
                            Crear
                        </button>
                    </a>
                @endcan
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
                                <th onclick="sortTable(0)" class="sort-table">{{ __('Trabajador') }}</th>
                                <th onclick="sortTable(1)" class="sort-table">{{ __('Fecha de Ausencia') }}</th>
                                <th onclick="sortTable(2)" class="sort-table">{{ __('Motivo') }}</th>
                                <th onclick="sortTable(3)" class="sort-table">{{ __('Duración (minutos)') }}</th>
                                <th onclick="sortTable(4)" class="sort-table">{{ __('Updated') }}</th>
                                <th onclick="sortTable(5)" class="sort-table">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($absences as $absence)
                                <tr>
                                    <td>{{ $absence->worker->name }} {{ $absence->worker->last_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($absence->date)->format('d-m-Y') }}</td>
                                    <td>{{ $absence->reason }}</td>
                                    <td>{{ $absence->minutes }}</td>
                                    <td>{{ $absence->updated_at->diffForHumans() }}</td>
                                    <td>
                                        @can('view', $absence)
                                            <a class="text-decoration-none" href="{{ route('absences.show', $absence) }}">
                                                <button class="btn btn-success rounded-3 px-3">
                                                    <i class='bx bx-show'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('update', $absence)
                                            <a class="text-decoration-none" href="{{ route('absences.edit', $absence) }}">
                                                <button class="btn btn-primary rounded-3 px-3">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('delete', $absence)
                                            <form method="POST" action="{{ route('absences.destroy', $absence) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta ausencia?')">
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
                @if ($absences->hasPages())
                    <div class="card-footer pb-0">
                        {{ $absences->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
