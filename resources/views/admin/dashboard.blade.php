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
                    <h5 class="m-0 text-white fw-normal">Rata-Rata Usia Pasien</h5>
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
                <h5 class="card-title mb-0">Distribusi Pasien Berdasarkan Jenis Kelamin</h5>
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
                <h5 class="card-title mb-0">Distribusi Pasien Berdasarkan Kategori Risiko</h5>
            </div>
            <div class="card-body">
                <div id="riskCategoryChart"></div>
            </div>
        </div>
    </div>
</div>

<div class="row gx-3">
    <!-- Distribusi Kategori Risiko Berdasarkan Usia -->
    <div class="col-xl-6 col-sm-12 col-12">
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Distribusi Risiko Berdasarkan Usia</h5>
            </div>
            <div class="card-body">
                <div id="riskByAgeChart"></div>
            </div>
        </div>
    </div>

    <!-- Distribusi BMI (Donut Chart) -->
    <div class="col-xl-6 col-sm-12 col-12">
        <div class="card mb-3">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">Distribusi Pasien Berdasarkan BMI</h5>
            </div>
            <div class="card-body">
                <div id="bmiChart"></div>
                <p><strong>Kategori BMI:</strong></p>
                <ul>
                    <li><strong>Underweight:</strong> BMI < 18.5</li>
                    <li><strong>Normal:</strong> BMI 18.5 - 24.9</li>
                    <li><strong>Overweight:</strong> BMI 25 - 29.9</li>
                    <li><strong>Obese:</strong> BMI >= 30</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row gx-3">
    <!-- Distribusi Terapi DM -->
    <div class="col-xl-6 col-sm-12 col-12">
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Distribusi Terapi DM pada Pasien</h5>
            </div>
            <div class="card-body">
                <div id="dmTherapyChart"></div>
            </div>
        </div>
    </div>

    <!-- Korelasi GDS dan HbA1c (Scatter Plot) -->
    <div class="col-xl-6 col-sm-12 col-12">
        <div class="card mb-3">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">Korelasi GDS dan HbA1c pada Pasien</h5>
            </div>
            <div class="card-body">
                <div id="gdsHba1cChart"></div>
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
        // Helper function to render Donut Chart
        function renderDonutChart(element, series, labels, colors) {
            var options = {
                series: series,
                chart: {
                    type: 'donut',
                    height: 350,
                },
                labels: labels,
                colors: colors,
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

            var chart = new ApexCharts(document.querySelector(element), options);
            chart.render();
        }

        // Donut Chart untuk Distribusi Jenis Kelamin
        renderDonutChart(
            "#genderChart",
            [{{ $genderDistribution->get('male', 0) }}, {{ $genderDistribution->get('female', 0) }}],
            ['Laki-laki', 'Perempuan'],
            ['#1E90FF', '#FF69B4']
        );

        // Donut Chart untuk Distribusi Kategori Risiko
       // Donut Chart untuk Distribusi Kategori Risiko
renderDonutChart(
    "#riskCategoryChart",
    [
        {{ $riskCategoryDistribution->get(0, 0) }}, // Tidak Berisiko
        {{ $riskCategoryDistribution->get(1, 0) }}, // Risiko Rendah
        {{ $riskCategoryDistribution->get(2, 0) }}, // Risiko Sedang
        {{ $riskCategoryDistribution->get(3, 0) }}  // Risiko Tinggi
    ],
    ['Tidak Berisiko', 'Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'], // Labels
    ['#28a745', '#FFC107', '#FD7E14', '#DC3545'] // Warna untuk tiap kategori
);


        // Donut Chart untuk Distribusi Kategori Risiko Berdasarkan Usia
        function prepareRiskByAgeData(data) {
            var result = {
                '<30': [0, 0, 0],
                '30-50': [0, 0, 0],
                '>50': [0, 0, 0]
            };

            data.forEach(function(item) {
                var ageGroup = item.age_group;
                var riskCategory = item.kategori_risiko;

                if (riskCategory === 1) {
                    result[ageGroup][0]++;
                } else if (riskCategory === 2) {
                    result[ageGroup][1]++;
                } else if (riskCategory === 3) {
                    result[ageGroup][2]++;
                }
            });

            return result;
        }

        var riskByAgeData = prepareRiskByAgeData(@json($riskCategoryByAgeGroup->flatten()));

        renderDonutChart(
            "#riskByAgeChart",
            [
                riskByAgeData['<30'][0] + riskByAgeData['30-50'][0] + riskByAgeData['>50'][0], 
                riskByAgeData['<30'][1] + riskByAgeData['30-50'][1] + riskByAgeData['>50'][1], 
                riskByAgeData['<30'][2] + riskByAgeData['30-50'][2] + riskByAgeData['>50'][2],
            ],
            ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'],
            ['#FF5733', '#33FF57', '#3357FF']
        );

        // Donut Chart untuk Distribusi BMI
        renderDonutChart(
            "#bmiChart",
            @json(array_values($bmiDistribution->toArray())),
            ['Underweight', 'Normal', 'Overweight', 'Obese'],
            ['#FF5733', '#33FF57', '#3357FF', '#FF33A1']
        );

        // Donut Chart untuk Distribusi Terapi DM
        renderDonutChart(
            "#dmTherapyChart",
            @json(array_values($dmTherapyDistribution->toArray())),
            ['Obat', 'Insulin'],
            ['#FF5733', '#33FF57']
        );

        // Scatter Plot untuk Korelasi GDS dan HbA1c
        var gdsHba1cChartOptions = {
            series: [{
                name: 'Korelasi GDS dan HbA1c',
                data: @json($gdsHba1cCorrelation->map(function($item) {
                    return [$item->gds, $item->hba1c];
                }))
            }],
            chart: {
                type: 'scatter',
                height: 350,
            },
            xaxis: {
                title: {
                    text: 'GDS'
                }
            },
            yaxis: {
                title: {
                    text: 'HbA1c'
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

        var gdsHba1cChart = new ApexCharts(document.querySelector("#gdsHba1cChart"), gdsHba1cChartOptions);
        gdsHba1cChart.render();
    });
</script>
@endpush