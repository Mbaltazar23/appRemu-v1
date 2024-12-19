@extends('layouts.app')

@push('custom_styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.css">
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
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .filter-info {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 16px;
            justify-content: center;
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

            <div class="chart-container">
                <div class="filters mb-4">
                    <div class="filter-buttons">
                
                    </div>
                    <div class="filter-info">
                    </div>
                </div>
                <div>
                    <canvas id="delaysChart"></canvas>
                </div>
            </div>

        </div>
    </div>
@endsection

