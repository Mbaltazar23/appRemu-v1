@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Listado de {{ __('Schools') }}
                </span>
                <a class="d-inline ml-3 text-decoration-none" href="{{ route('schools.create') }}">
                    <button class="btn btn-primary rounded-3 px-3 py-1">
                        Crear
                    </button>
                </a>
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
                                <th onclick="sortTable(0)" class="sort-table">{{ __('Name') }}</th>
                                <th onclick="sortTable(1)" class="sort-table">{{ __('Created at') }}</th>
                                <th onclick="sortTable(2)" class="sort-table">{{ __('Updated ') }}</th>
                                <th onclick="sortTable(3)" class="sort-table">{{ __('Acctions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schools as $school)
                                <tr class="">
                                    <td>{{ $school->name }}</td>
                                    <td>{{ $school->created_at }}</td>
                                    <td>{{ $school->updated_at->diffForhumans() }}</td>
                                    <td>
                                        @can('view', $school)
                                            <a class="text-decoration-none" href="{{ route('schools.show', $school) }}">
                                                <button class="btn btn-success rounded-3 px-3">
                                                    <i class='bx bx-show'></i>
                                                </button>
                                            </a>
                                        @endcan
                                        @can('update', $school)
                                            <a class="text-decoration-none" href="{{ route('schools.edit', $school) }}">
                                                <button class="btn btn-primary rounded-3 px-3">
                                                    <i class='bx bx-edit'></i>

                                                </button>
                                            </a>
                                        @endcan
                                        @can('delete', $school)
                                            <form method="POST" action={{ route('schools.destroy', $school) }}
                                                class="d-inline"
                                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?')">
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
                @if ($schools->hasPages())
                    <div class="card-footer pb-0">
                        {{ $schools->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@include('commons.sort-table')
