@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="page-body">
            <div class="card-body">
                <div class="container-xl">
                    <div class="card mb-4 p-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Emisión de certificado de remuneraciones</h3>
                        </div>
                        <div class="card-body">
                            @if ($certificates->isNotEmpty())
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Año</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($certificates as $certificate)
                                            <tr>
                                                <td>{{ $certificate->year }}</td>
                                                <td>
                                                    @can('view', $certificate)
                                                        <!-- Acción para ver los certificados de un año -->
                                                        <a href="{{ route('certificates.view', $certificate->year) }}"
                                                            target="_blank"
                                                            onclick="openPopup(event, 'Certificados de Remuneraciones')"
                                                            class="btn btn-primary">
                                                            <i class='bx bx-show'></i>
                                                        </a>
                                                    @endcan
                                                   
                                                    @can('view', $certificate)
                                                        <!-- Acción para ver los certificados de un año -->
                                                        <a href="{{ route('certificates.print', $certificate->year) }}"
                                                            target="_blank"
                                                            onclick="openPopup(event, 'Certificados de Remuneraciones')"
                                                            class="btn btn-info">
                                                            <i class="bx bx-printer"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $certificate)
                                                        <!-- Acción para eliminar los certificados de un año -->
                                                        <form action="{{ route('certificates.destroy', $certificate->year) }}"
                                                            method="POST" class="d-inline-block"
                                                            onsubmit="return confirm('¿Desea eliminar los certificados de este año?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No hay certificados almacenados.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Card 2: Formulario para Crear Certificado -->
                    <div class="card mb-4 p-4" id="createCertificateCard">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Crear certificado para todos los trabajadores para el año {{ now()->year }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('certificates.store') }}" method="POST">
                                @csrf
                                <div class="form-group row">
                                    <!-- Reducción aún más del ancho del input y el botón -->
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="year" name="year"
                                            value="{{ date('Y') }}" required>
                                    </div>
                                    @can('create', App\Models\Certificate::class)
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-success w-100">Crear</button>
                                        </div>
                                    @endcan
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@include('commons.sort-table')
