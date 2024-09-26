@extends('layouts.app')

@section('content')
<h1 class="h3 mb-3">Ini Halaman Dashboard Untuk Admin</h1>

<div class="row gx-3">
    <!-- Total Pasien -->
    <div class="col-xl-6 col-sm-12 col-12">
        <div class="card mb-3 card-custom background-gradient-1">
            <div class="card-body">
                <div class="circle-shape shape-1"></div>
                <div class="circle-shape shape-2"></div>
                <div class="circle-shape shape-3"></div>
                <div class="mb-2">
                    <i class="bi bi-person-badge fs-1 text-white lh-1"></i>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="m-0 text-white fw-normal">Total Pasien</h5>
                    <h3 class="m-0 text-white">{{ $totalPatients }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Rata-Rata Usia -->
    <div class="col-xl-6 col-sm-12 col-12">
        <div class="card mb-3 card-custom background-gradient-2">
            <div class="card-body">
                <div class="circle-shape shape-1"></div>
                <div class="circle-shape shape-2"></div>
                <div class="circle-shape shape-3"></div>
                <div class="mb-2">
                    <i class="bi bi-clock-history fs-1 text-white lh-1"></i>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="m-0 text-white fw-normal">Rata-Rata Usia</h5>
                    <h3 class="m-0 text-white">{{ $averageAge }} Tahun</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row gx-3">
    <!-- Distribusi Jenis Kelamin (Donut Chart) -->
    <div class="col-xl-6 col-sm-12 col-12">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Distribusi Jenis Kelamin</h5>
            </div>
            <div class="card-body">
                <div id="genderChart"></div>
            </div>
        </div>
    </div>

    <!-- Distribusi Kategori Risiko (Donut Chart) -->
    <div class="col-xl-6 col-sm-12 col-12">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Distribusi Kategori Risiko</h5>
            </div>
            <div class="card-body">
                <div id="riskCategoryChart"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/customcard.css') }}">
@endpush

@push('javascript')
<script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Donut Chart untuk Distribusi Jenis Kelamin
        var genderChartOptions = {
            series: [{{ $genderDistribution->get('male', 0) }}, {{ $genderDistribution->get('female', 0) }}],
            chart: {
                type: 'donut',
                height: 350,
            },
            labels: ['Laki-laki', 'Perempuan'],
            colors: ['#1E90FF', '#FF69B4'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%'
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var genderChart = new ApexCharts(document.querySelector("#genderChart"), genderChartOptions);
        genderChart.render();

        // Donut Chart untuk Distribusi Kategori Risiko
        var riskCategoryChartOptions = {
            series: @json(array_values($riskCategoryDistribution->toArray())),
            chart: {
                type: 'donut',
                height: 350,
            },
            labels: @json(array_keys($riskCategoryDistribution->toArray())),
            colors: ['#FF5733', '#33FF57', '#3357FF', '#FF33A1'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%'
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var riskCategoryChart = new ApexCharts(document.querySelector("#riskCategoryChart"), riskCategoryChartOptions);
        riskCategoryChart.render();
    });
</script>
@endpush
