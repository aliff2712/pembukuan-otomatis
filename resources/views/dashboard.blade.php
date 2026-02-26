@extends('layouts-main.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Keuangan DHS Dipanet Hotspot Solution')

@section('content')

    <!-- Baris Kartu Ringkasan Saldo -->
    <div class="row">
    <div class="row">

<!-- SALDO KAS -->
<div class="col-xl-4 col-md-6 mb-4">
    <a href="#" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small fw-semibold text-primary mb-1">
                            Saldo Kas (1101)
                        </div>
                        <div class="fs-4 fw-bold text-primary">
                            Rp {{ number_format($cashBalance, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="icon-circle bg-primary-soft">
                        <i class="fas fa-money-bill-wave text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- SALDO BANK -->
<div class="col-xl-4 col-md-6 mb-4">
    <a href="#" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small fw-semibold text-success mb-1">
                            Saldo Bank (1102)
                        </div>
                        <div class="fs-4 fw-bold text-primary">
                            Rp {{ number_format($bankBalance, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="icon-circle bg-success-soft">
                        <i class="fas fa-university text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<<<<<<< HEAD
<!-- PIUTANG USAHA -->
<div class="col-xl-4 col-md-6 mb-4">
    <a href="#" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small fw-semibold text-info mb-1">
                            Piutang Usaha
=======
        <!-- Card Piutang Usaha -->
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="{{ route('finance.transaksi.index', ['status' => 'unpaid']) }}" class="text-decoration-none">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Belum bayar (Piutang Usaha - 1103)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($arBalance, 0, ',', '.') }}
                            </div>
>>>>>>> 79db817 (penyesuaian method dashboard)
                        </div>
                        <div class="fs-4 fw-bold text-primary">
                            Rp {{ number_format($arBalance, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="icon-circle bg-info-soft">
                        <i class="fas fa-file-invoice-dollar text-info"></i>
                    </div>
                </div>
            </div>
        </div>
<<<<<<< HEAD
    </a>
</div>
=======
    </div>
     <!-- Card Piutang Usaha -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                             Pembayaran Bulan ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($paid, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
>>>>>>> 79db817 (penyesuaian method dashboard)

</div>
    <!-- Baris Kartu Ringkasan Bulanan & Grafik -->
    <div class="row">
        <!-- Kolom Grafik -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Statistik Pendapatan vs Beban (6 Bulan Terakhir)
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="#">Download Report</a>
                            <a class="dropdown-item" href="#">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="monthlyStatsChart"></canvas>
                    </div>
                    <hr>
                    <div class="small text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Pendapatan berasal dari pembayaran invoice dan penjualan voucher. 
                        Beban berasal dari pengeluaran operasional.
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Pendapatan & Beban -->
        <div class="col-xl-4 col-lg-5">
            <div class="row">
                <!-- Kartu Pendapatan Bulan Ini -->
                <div class="col-12">
                    <div class="card border-left-warning shadow mb-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Pendapatan (Bulan Ini)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}
                                    </div>
                                    <div class="mt-2 mb-0 text-muted text-xs">
                                        <span class="text-success mr-2">
                                            <i class="fas fa-arrow-up"></i> 
                                            {{ now()->isoFormat('MMMM YYYY') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kartu Other Income Bulan Ini -->
                <div class="col-12">
                <a href="{{ route('other-incomes.index') }}" class="text-decoration-none">
                    <div class="card border-left-primary shadow mb-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Other Income (Bulan Ini)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($otherIncomeThisMonth ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="mt-2 mb-0 text-muted text-xs">
                                        <small>Sumber pendapatan selain invoice & voucher</small>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wallet fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kartu Beban Bulan Ini -->
                <div class="col-12">
                        <a href="{{ route('expenses.index') }}" class="text-decoration-none">
                    <div class="card border-left-danger shadow mb-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Pengeluaran (Bulan Ini)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}
                                    </div>
                                    <div class="mt-2 mb-0 text-muted text-xs">
                                        <span class="text-danger mr-2">
                                            <i class="fas fa-arrow-down"></i> 
                                            {{ now()->isoFormat('MMMM YYYY') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-receipt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kartu Laba Bulan Ini -->
                <div class="col-12">
                    <div class="card border-left-{{ ($revenueThisMonth - $expenseThisMonth) >= 0 ? 'success' : 'danger' }} shadow mb-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-{{ ($revenueThisMonth - $expenseThisMonth) >= 0 ? 'success' : 'danger' }} text-uppercase mb-1">
                                        Laba/Rugi (Bulan Ini)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($revenueThisMonth - $expenseThisMonth, 0, ',', '.') }}
                                    </div>
                                    @php
                                        $profitMargin = $revenueThisMonth > 0 
                                            ? (($revenueThisMonth - $expenseThisMonth) / $revenueThisMonth) * 100 
                                            : 0;
                                    @endphp
                                    <div class="mt-2 mb-0 text-muted text-xs">
                                        <span class="mr-2">
                                            Margin: {{ number_format($profitMargin, 1) }}%
                                        </span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris Informasi Tambahan -->
    <div class="row">
        <!-- Quick Links -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('finance.transaksi.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-invoice text-primary"></i> Kelola Invoice Beat
                        </a>
                        <a href="{{ route('payments.create') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-money-bill-wave text-success"></i> Input Pembayaran
                        </a>
                        <a href="{{ route('expenses.create') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-receipt text-danger"></i> Input Pengeluaran
                        </a>
                        <a href="{{ route('other-incomes.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-wallet text-success"></i> Kelola Other Income
                        </a>
                        <a href="{{ route('voucher-sales.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-ticket-alt text-warning"></i> Penjualan Voucher
                        </a>
                        <a href="{{ route('journal-entries.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-book text-info"></i> Lihat Jurnal
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity / Stats -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Cepat</h6>
                </div>
                <div class="card-body">
                    <h4 class="small font-weight-bold">
                        Saldo Total (Kas + Bank)
                        <span class="float-right">Rp {{ number_format($cashBalance + $bankBalance, 0, ',', '.') }}</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" role="progressbar" 
                            style="width: 100%" 
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>

                    <h4 class="small font-weight-bold">
                        Net Working Capital
                        <span class="float-right">Rp {{ number_format(($cashBalance + $bankBalance) - $arBalance, 0, ',', '.') }}</span>
                    </h4>
                    <div class="progress mb-4">
                        @php
                            $nwcPercentage = ($cashBalance + $bankBalance) > 0 
                                ? ((($cashBalance + $bankBalance) - $arBalance) / ($cashBalance + $bankBalance)) * 100 
                                : 0;
                        @endphp
                        <div class="progress-bar bg-info" role="progressbar" 
                            style="width: {{ max(0, min(100, $nwcPercentage)) }}%" 
                            aria-valuenow="{{ $nwcPercentage }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>

                    <h4 class="small font-weight-bold">
                        Profit Margin Bulan Ini
                        <span class="float-right">{{ number_format($profitMargin, 1) }}%</span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar {{ $profitMargin >= 20 ? 'bg-success' : ($profitMargin >= 10 ? 'bg-warning' : 'bg-danger') }}" 
                            role="progressbar" 
                            style="width: {{ min(100, $profitMargin) }}%" 
                            aria-valuenow="{{ $profitMargin }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Load Chart.js dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('monthlyStatsChart').getContext('2d');
        
        const monthlyStatsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($monthlyStats['labels']),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($monthlyStats['revenue']),
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }, {
                    label: 'Beban',
                    data: @json($monthlyStats['expense']),
                    backgroundColor: 'rgba(231, 74, 59, 0.8)',
                    borderColor: 'rgba(231, 74, 59, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush