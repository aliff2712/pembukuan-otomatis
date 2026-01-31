@extends('layouts.app')

@section('title', 'Voucher Sales')
@section('page-title', 'Voucher Sales - Mikhmon')

@section('content')
<div class="row">
    <!-- Summary Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Days
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_days'], 0) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Transactions
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_transactions'], 0) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Amount
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Average/Day
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp {{ number_format($stats['average_per_day'] ?? 0, 0, ',', '.') }}
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

<!-- Filter & Action Bar -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Filter & Actions</h6>
        <div>
            <a href="{{ route('voucher-sales.reimport-form') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-sync-alt"></i> Re-import
            </a>
            <a href="{{ route('voucher-sales.export', request()->all()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export CSV
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('voucher-sales.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" 
                    value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" 
                    value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Month</label>
                <select name="month" class="form-control form-control-sm">
                    <option value="">All</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Year</label>
                <select name="year" class="form-control form-control-sm">
                    <option value="">All</option>
                    @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('voucher-sales.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Voucher Sales Data</h6>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Sale Date</th>
                        <th>Total Transactions</th>
                        <th>Total Amount</th>
                        <th>Source</th>
                        <th>Import Batch</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->id }}</td>
                            <td>
                                <i class="far fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ number_format($sale->total_transactions) }}</span>
                            </td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $sale->source }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $sale->import_batch_id ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($sale->updated_at)->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('voucher-sales.show', $sale->id) }}" 
                                    class="btn btn-info btn-sm" title="View Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" 
                                    onclick="voidSale({{ $sale->id }}, '{{ $sale->sale_date }}')" 
                                    title="Void">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>No voucher sales data found.</p>
                                <a href="{{ route('voucher-sales.reimport-form') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-upload"></i> Import Data
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($sales->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2" class="text-end">Total (This Page):</th>
                            <th class="text-center">
                                {{ number_format($sales->sum('total_transactions')) }}
                            </th>
                            <th class="text-end">
                                Rp {{ number_format($sales->sum('total_amount'), 0, ',', '.') }}
                            </th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Showing {{ $sales->firstItem() ?? 0 }} to {{ $sales->lastItem() ?? 0 }} 
                of {{ $sales->total() }} entries
            </div>
            <div>
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Info Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-info-circle"></i> Information
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="font-weight-bold">About Voucher Sales</h6>
                <p class="text-muted small">
                    Data penjualan voucher diimpor otomatis dari sistem Mikhmon. 
                    Setiap hari akan ada 1 record yang merangkum total transaksi dan total penjualan.
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="font-weight-bold">Last Import</h6>
                <p class="text-muted small">
                    <i class="far fa-clock"></i> 
                    {{ $stats['last_import'] ? \Carbon\Carbon::parse($stats['last_import'])->diffForHumans() : 'Never' }}
                </p>
                <p class="text-muted small">
                    <i class="fas fa-coins"></i> This Month Total: 
                    <strong>Rp {{ number_format($stats['this_month_total'], 0, ',', '.') }}</strong>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function voidSale(id, date) {
    Swal.fire({
        title: 'Void Voucher Sale?',
        html: `Are you sure you want to void voucher sale for date: <strong>${date}</strong>?<br><br>This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Yes, void it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/voucher-sales/${id}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush