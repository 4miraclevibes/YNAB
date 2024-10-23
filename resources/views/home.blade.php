@extends('layouts.main')

@section('content')
    <div class="container">
        <h1>Home</h1>
        
        <div class="chart-container mb-4">
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
    var options = {
        series: [{
            name: 'Total Transaksi',
            data: transactions.map(t => t.total)
        }],
        chart: {
            type: 'line',
            height: 500,
            width: Math.max(transactions.length * 100, window.innerWidth),
            zoom: {
                enabled: false
            },
            toolbar: {
                show: false
            }
        },
        xaxis: {
            categories: transactions.map(t => t.date),
            tickAmount: 4,
            labels: {
                rotate: -45,
                rotateAlways: true
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
            text: 'Grafik Total Transaksi per Hari'
        },
        tooltip: {
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

    var chart = new ApexCharts(document.querySelector("#transactionChart"), options);
    chart.render();
</script>
@endpush
