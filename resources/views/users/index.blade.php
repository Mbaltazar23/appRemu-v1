@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <h2 class="page-title d-flex justify-content-between">
                <span>
                    Lista de {{ __('Users') }}
                </span>
                @can('create', App\Models\User::class)
                    <a class="d-inline ml-5 text-decoration-none" href="{{ route('users.create') }}">
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
                                <th onclick="sortTable(0)" class="sort-table">{{ __('Name') }}</th>
                                <th onclick="sortTable(1)" class="sort-table">{{ __('Email Address') }}</th>
                                <th onclick="sortTable(2)" class="sort-table">{{ __('Created at') }}</th>
                                <th onclick="sortTable(3)" class="sort-table">{{ __('Updated') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at }}</td>
                                <td>{{ $user->updated_at->diffForHumans() }}</td>
                                <td>
                                    @can('view', $user)
                                        <a class="text-decoration-none" href="{{ route('users.show', $user) }}">
                                            <button class="btn btn-success rounded-3 px-3">
                                                <i class='bx bx-show'></i>
                                            </button>
                                        </a>
                                    @endcan
                                    @can('update', $user)
                                        <a class="text-decoration-none" href="{{ route('users.edit', $user) }}">
                                            <button class="btn btn-primary rounded-3 px-3">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                        </a>
                                    @endcan
                                    @can('delete', $user)
                                        <form method="POST" action={{ route('users.destroy', $user) }} class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?')">
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
                @if($users->hasPages())
                    <div class="card-footer pb-0">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@include('commons.sort-table')
