@extends('layouts.app')

@section('content')
<h1 class="h3 mb-3">Ini Halaman Dashboard Untuk Admin</h1>

<div class="row gx-3">
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
                    <h3 class="m-0 text-white">{{ number_format($averageAge, 0) }} Tahun</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row gx-3">
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
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Distribusi Risiko Berdasarkan Usia</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-12 mb-3 text-center">
                        <div id="riskUnder30Chart"></div>
                        <p><strong>Usia &lt;30 tahun</strong></p>
                    </div>
                    <div class="col-md-4 col-12 mb-3 text-center">
                        <div id="riskBetween30and50Chart"></div>
                        <p><strong>Usia 30-50 tahun</strong></p>
                    </div>
                    <div class="col-md-4 col-12 mb-3 text-center">
                        <div id="riskOver50Chart"></div>
                        <p><strong>Usia &gt;50 tahun</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row gx-3">
</div>

<div class="row gx-3">
    <div class="col-xl-6 col-sm-12 col-12">
        <div class="card mb-3">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">Distribusi Pasien Berdasarkan BMI</h5>
            </div>
            <div class="card-body">
                <div id="bmiChart"></div>
            </div>
        </div>
    </div>
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

</div>
<div class="row gx-3">
    <div class="col-12">
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
        function renderDonutChart(element, series, labels, colors) {
            if (!series || series.length === 0 || !labels || labels.length === 0) {
                document.querySelector(element).innerHTML = '<p class="text-center">Data tidak diketahui</p>';
                return;
            }
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

        function renderFullWidthDonutChart(element, series, labels, colors) {
            if (!series || series.length === 0 || !labels || labels.length === 0) {
                document.querySelector(element).innerHTML = '<p class="text-center">Data tidak diketahui</p>';
                return;
            }
            var options = {
                series: series,
                chart: {
                    type: 'donut',
                    height: 400,
                    width: '100%',
                },
                labels: labels,
                colors: colors,
                plotOptions: {
                    pie: {
                        donut: {
                            size: '80%'
                        }
                    }
                },
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 350,
                            width: '100%',
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

        renderDonutChart(
            "#genderChart",
            @json($genderCounts), // Menggunakan hitungan gender dari database
            @json($genderLabels), // Menggunakan label gender dari database
            ['#1E90FF', '#FF69B4'] // Anda dapat menambahkan lebih banyak warna sesuai jumlah jenis kelamin
        );

        renderDonutChart(
            "#riskCategoryChart",
            [
                {{ $riskCategoryDistribution->get(0, 0) }},
                {{ $riskCategoryDistribution->get(1, 0) }},
                {{ $riskCategoryDistribution->get(2, 0) }},
                {{ $riskCategoryDistribution->get(3, 0) }}
            ],
            ['Tidak Berisiko', 'Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'],
            ['#28a745', '#FFC107', '#FD7E14', '#DC3545']
        );

        renderFullWidthDonutChart(
            "#riskUnder30Chart",
            [
                {{ $under30->where('kategori_risiko', 1)->sum('count') }},
                {{ $under30->where('kategori_risiko', 2)->sum('count') }},
                {{ $under30->where('kategori_risiko', 3)->sum('count') }}
            ],
            ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'],
            ['#FF5733', '#33FF57', '#3357FF']
        );

        renderFullWidthDonutChart(
            "#riskBetween30and50Chart",
            [
                {{ $between30and50->where('kategori_risiko', 1)->sum('count') }},
                {{ $between30and50->where('kategori_risiko', 2)->sum('count') }},
                {{ $between30and50->where('kategori_risiko', 3)->sum('count') }}
            ],
            ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'],
            ['#FF5733', '#33FF57', '#3357FF']
        );

        renderFullWidthDonutChart(
            "#riskOver50Chart",
            [
                {{ $over50->where('kategori_risiko', 1)->sum('count') }},
                {{ $over50->where('kategori_risiko', 2)->sum('count') }},
                {{ $over50->where('kategori_risiko', 3)->sum('count') }}
            ],
            ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'],
            ['#FF5733', '#33FF57', '#3357FF']
        );

        renderDonutChart(
            "#bmiChart",
            @json(array_values($bmiDistribution->toArray())),
            ['Underweight < 18.5', 'Normal 18.5 - 24.9', 'Overweight 25 - 29.9', 'Obese >= 30'],
            ['#FF5733', '#33FF57', '#3357FF', '#FF33A1']
        );

        renderDonutChart(
            "#dmTherapyChart",
            @json($dmTherapyCounts), // Menggunakan hitungan terapi dari database
            @json($dmTherapyNames),  // Menggunakan nama terapi dari database
            ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#33FFA1'] // Pastikan jumlah warna sesuai dengan jumlah item // Pastikan jumlah warna sesuai dengan jumlah item
    );

    });
</script>
@endpush