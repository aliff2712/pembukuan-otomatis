@extends('layouts-main.app')

@section('title', 'Transaksi')

@section('content')

<style>
    :root {
        --navy-dark: #0f172a;
        --navy-main: #1e293b;
        --navy-soft: #334155;
        --navy-hover: #1d4ed8;
        --soft-white: #f8fafc;
    }

    body {
        background-color: #f1f5f9;
    }

    .page-title {
        color: var(--navy-dark);
        font-weight: 700;
    }

    .modern-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08);
    }

    .summary-card {
        background: var(--navy-main);
        color: white;
        border-radius: 18px;
        padding: 20px;
        transition: 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
    }

    .summary-card small {
        color: #cbd5e1;
    }

    .summary-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: #ffffff;
    }

    .summary-amount {
        font-size: 0.9rem;
        font-weight: 600;
        color: #93c5fd;
    }

    .btn-navy {
        background: var(--navy-main);
        color: #fff;
        border-radius: 10px;
    }

    .btn-navy:hover {
        background: var(--navy-hover);
        color: #fff;
    }

    .table thead {
        background-color: var(--navy-main);
        color: white;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f5f9;
    }

    .badge-paid {
        background-color: #16a34a;
    }

    .badge-unpaid {
        background-color: #dc2626;
    }

    .filter-card {
    background: #1e293b; /* navy */
    border-radius: 18px;
    border: none;
    box-shadow: 0 8px 25px rgba(15, 23, 42, 0.25);
}

.filter-card .form-label {
    color: #cbd5e1;
    font-weight: 600;
}

.filter-card .form-control {
    background: #0f172a;
    border: 1px solid #334155;
    color: #f8fafc;
    border-radius: 10px;
}

.filter-card .form-control:focus {
    background: #0f172a;
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.15rem rgba(59,130,246,.25);
    color: #fff;
}

.filter-card .form-control::placeholder {
    color: #94a3b8;
}

.btn-navy {
    background: #3b82f6;
    color: #fff;
    border-radius: 10px;
    border: none;
}

.btn-navy:hover {
    background: #2563eb;
    color: #fff;
    
}
    
</style>

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Manajemen Transaksi</h1>
        <a href="{{ route('finance.transaksi.import.form') }}" class="btn btn-navy">
            <i class="fas fa-file-import me-1"></i> Import Transaksi
        </a>
    </div>

@php
$totalTransaksi = $transaksis->total();
$totalPaid = $transaksis->where('status', 'paid')->count();
$totalUnpaid = $transaksis->where('status', 'unpaid')->count();
$nominalPaid = $transaksis->where('status', 'paid')->sum('total');
$nominalUnpaid = $transaksis->where('status', 'unpaid')->sum('total');
@endphp

    <!-- SUMMARY -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="summary-card text-center">
                <small>Total Transaksi</small>
                <div class="summary-number">{{ $totalTransaksi }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card text-center">
                <small>Sudah Dibayar</small>
                <div class="summary-number">{{ $totalPaid }}</div>
                <div class="summary-amount">
                    Rp {{ number_format($nominalPaid,0,',','.') }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card text-center">
                <small>Belum Dibayar</small>
                <div class="summary-number">{{ $totalUnpaid }}</div>
                <div class="summary-amount text-warning">
                    Rp {{ number_format($nominalUnpaid,0,',','.') }}
                </div>
            </div>
        </div>

    </div>

    <!-- FILTER -->
    <div class="card filter-card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label">Customer</label>
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Cari nama customer">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Dari</label>
                    <input type="date" name="from"
                           value="{{ request('from') }}"
                           class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Sampai</label>
                    <input type="date" name="to"
                           value="{{ request('to') }}"
                           class="form-control">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-navy px-4">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('finance.transaksi.index') }}" class="btn btn-outline-light px-4">
                        Reset
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

    <!-- TABLE -->
    <div class="card modern-card">
        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th class="text-center" width="200">Action</th>
                        </tr>
                    </thead>

                    <tbody>
@forelse($transaksis as $trx)
@php
$jatuhTempo = $trx->jatuh_tempo;
$isOverdue = $jatuhTempo && now()->greaterThan($jatuhTempo);
$canPay = $trx->status === 'unpaid' && (!$jatuhTempo || now()->lessThanOrEqualTo($jatuhTempo));
@endphp
                        <tr>
                            <td class="fw-semibold text-dark">{{ $trx->kode_transaksi }}</td>
                            <td>{{ $trx->nama_customer }}</td>
                            <td>{{ $trx->tanggal->format('d M Y') }}</td>
                            <td>
                                @if($jatuhTempo)
                                    <span class="{{ $isOverdue && $trx->status === 'unpaid' ? 'text-danger fw-bold' : '' }}">
                                        {{ $jatuhTempo->format('d M Y') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end fw-bold text-primary">
                                Rp {{ number_format($trx->total,0,',','.') }}
                            </td>
                            <td>
                                @if($trx->status == 'paid')
                                    <span class="badge badge-paid">PAID</span>
                                @else
                                    <span class="badge badge-unpaid">UNPAID</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('finance.transaksi.show', $trx->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($canPay)
                                    <a href="{{ route('finance.transaksi.payment.form', $trx->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-credit-card"></i>
                                    </a>
                                @endif

                                <form action="{{ route('finance.transaksi.destroy', $trx->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
@empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada transaksi
                            </td>
                        </tr>
@endforelse
                    </tbody>
                </table>

            </div>
        </div>

        <div class="card-footer bg-white">
            {{ $transaksis->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

@endsection