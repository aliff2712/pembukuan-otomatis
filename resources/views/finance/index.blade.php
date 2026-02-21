@extends('layouts-main.app')

@section('title', 'Transaksi')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800 mb-0">Manajemen Transaksi</h1>
        <a href="{{ route('finance.transaksi.import.form') }}" class="btn btn-success shadow-sm">
            <i class="fas fa-file-import me-1"></i> Import Transaksi
        </a>
    </div>
    {{-- SUMMARY --}}
@php
    $totalTransaksi = $transaksis->total();

    $totalPaid = $transaksis->where('status', 'paid')->count();
    $totalUnpaid = $transaksis->where('status', 'unpaid')->count();

    $nominalPaid = $transaksis->where('status', 'paid')->sum('total');
    $nominalUnpaid = $transaksis->where('status', 'unpaid')->sum('total');
@endphp

<div class="row mb-4 g-3">

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body text-center">
                <small class="text-muted d-block">Total Transaksi</small>
                <h4 class="fw-bold mb-0">{{ $totalTransaksi }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body text-center">
                <small class="text-muted d-block">Sudah Dibayar</small>
                <h4 class="fw-bold text-success mb-0">{{ $totalPaid }}</h4>
                <small class="text-success">
                    Rp {{ number_format($nominalPaid,0,',','.') }}
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body text-center">
                <small class="text-muted d-block">Belum Dibayar</small>
                <h4 class="fw-bold text-danger mb-0">{{ $totalUnpaid }}</h4>
                <small class="text-danger">
                    Rp {{ number_format($nominalUnpaid,0,',','.') }}
                </small>
            </div>
        </div>
    </div>

</div>
    <!-- FILTER -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Customer</label>
                        <input type="text" name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Cari nama customer">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Dari</label>
                        <input type="date" name="from"
                               value="{{ request('from') }}"
                               class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Sampai</label>
                        <input type="date" name="to"
                               value="{{ request('to') }}"
                               class="form-control">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('finance.transaksi.index') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Daftar Transaksi</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
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
    <td>{{ $trx->kode_transaksi }}</td>
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
    <td class="text-end">{{ number_format($trx->total,0,',','.') }}</td>
    <td>
        @if($trx->status == 'paid')
            <span class="badge bg-success">PAID</span>
        @else
            <span class="badge bg-danger">UNPAID</span>
        @endif
    </td>
    <td class="text-center">
        <a href="{{ route('finance.transaksi.show', $trx->id) }}" class="btn btn-sm btn-info">
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
            <button class="btn btn-sm btn-danger">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi</td>
</tr>
@endforelse

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="card-footer bg-white">
            {{ $transaksis->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

@endsection
