@extends('layouts.app')

@push('custom_styles')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .chart-container {
            background-color: #fff;
            padding: 30px 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .filters {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        .filter-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-info div {
            font-size: 18px;
            font-weight: bold;
        }

        .percentage-text {
            font-size: 30px;
            font-weight: bold;
            color: #2c3e50;
        }

        .year-select {
            font-size: 16px;
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        @media (max-width: 767px) {
            .filters {
                flex-direction: column;
                align-items: flex-start;
            }

            .filter-info {
                justify-content: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="alert alert-success">
                <div class="alert-title">
                    {{ __('Welcome') }} {{ auth()->user()->name ?? null }}
                </div>
                <div class="text-muted">
                    {{ __('You are logged in!') }}
                </div>
            </div>

            <div class="chart-container ">
                <div class="filters mb-4 p-4">
                    <div class="filter-info">
                        <!-- Texto del porcentaje de Licencias Médicas -->
                        <h3>{{ $HeaderGrafictLicence }}</h3>
                    </div>
                    <div class="filter-buttons">
                        <!-- Verificar si $availableYears tiene datos -->
                        @if (!empty($availableYears))
                            <!-- Formulario con label a la izquierda del select para cambiar de año -->
                            <form action="{{ route('home') }}" method="GET">
                                <div class="d-flex align-items-center">
                                    <label for="year" class="form-label" style="margin-right: 10px;">Seleccione un
                                        año</label>
                                    <select id="year" name="year" class="year-select" onchange="this.form.submit()">
                                        @foreach ($availableYears as $yearOption)
                                            <option value="{{ $yearOption }}"
                                                {{ $year == $yearOption ? 'selected' : '' }}>
                                                {{ $yearOption }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
                <!-- Contenedor para el gráfico -->
                <div id="medicalLeaveChart"></div>
            </div>
        </div>
    </div>
@endsection

@push('custom_scripts')
    <script>
        const monthlyData = @json($monthlyMedicalLeavePercentages);

        const months = monthlyData.map(data => data.month);
        const percentages = monthlyData.map(data => data.percentage);

        var options = {
            chart: {
                type: 'bar',
                height: '350',
                width: '100%',
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '60%',
                    endingShape: 'rounded'
                }
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Licencias Médicas (%)',
                data: percentages
            }],
            xaxis: {
                categories: months,
            },
            yaxis: {
                title: {
                    text: 'Porcentaje'
                },
                max: 100,
                labels: {
                    formatter: function(val) {
                        return val.toFixed(0) + "%";
                    }
                }
            },
            responsive: [{
                breakpoint: 1000,
                options: {
                    chart: {
                        height: '300px'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#medicalLeaveChart"), options);
        chart.render();
    </script>
@endpush
