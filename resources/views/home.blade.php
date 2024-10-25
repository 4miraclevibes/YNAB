@extends('layouts.main')

@section('content')
    <div class="container">
        <h1>Home</h1>
        
        <div class="chart-container mb-4">
            <div class="btn-group mb-3" role="group" aria-label="Rentang Waktu">
                @foreach(['1D', '1M', '3M', 'YTD', '1Y', '3Y', '5Y', '10Y', 'All'] as $range)
                    <button type="button" class="btn btn-outline-secondary range-selector" data-range="{{ $range }}">{{ $range }}</button>
                @endforeach
            </div>
            <div id="transactionChart" style="height: 300px; min-width: 100%;"></div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Rata-rata Transaksi per Hari</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($averagePerDay as $category)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $category->name }}
                                    <span>{{ number_format($category->avg_per_day, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Rata-rata Transaksi per Bulan</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($averagePerMonth as $category)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $category->name }}
                                    <span>{{ number_format($category->avg_per_month, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Rata-rata Transaksi per Tahun</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($averagePerYear as $category)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $category->name }}
                                    <span>{{ number_format($category->avg_per_year, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .chart-container {
        overflow-x: auto;
        margin-bottom: 20px;
    }
    .chart-container::-webkit-scrollbar {
        display: none;
    }
    .chart-container {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var transactions = @json($transactions);
    var currentRange = '1M'; // Default range
    var chart;

    function updateChart(range) {
        var data = transactions[range];
        chart.updateOptions({
            series: [{
                name: 'Total Transaksi',
                data: data.map(t => t.total)
            }],
            xaxis: {
                categories: data.map(t => t.date),
                labels: {
                    show: false // Menyembunyikan label tanggal
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            title: {
                text: 'Grafik Total Transaksi (' + range + ')'
            }
        });
    }

    var options = {
        series: [{
            name: 'Total Transaksi',
            data: transactions['1M'].map(t => t.total)
        }],
        chart: {
            type: 'line',
            height: 500,
            width: '100%',
            zoom: {
                enabled: false
            },
            toolbar: {
                show: false
            }
        },
        xaxis: {
            categories: transactions['1M'].map(t => t.date),
            labels: {
                show: false // Menyembunyikan label tanggal
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
                }
            }
        },
        title: {
            text: 'Grafik Total Transaksi (1M)'
        },
        tooltip: {
            x: {
                format: 'dd MMM yyyy' // Format tanggal pada tooltip
            },
            y: {
                formatter: function (value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        }
    };

    chart = new ApexCharts(document.querySelector("#transactionChart"), options);
    chart.render();

    document.querySelectorAll('.range-selector').forEach(button => {
        button.addEventListener('click', function() {
            var range = this.getAttribute('data-range');
            updateChart(range);
            document.querySelectorAll('.range-selector').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>
@endpush
