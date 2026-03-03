@extends('layouts-main.app')

@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan ' . $label)

@section('content')
<div class="container-fluid">

    {{-- SUMMARY --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <small>Member Paid</small>
                    <h5 class="fw-bold">
                        Rp {{ number_format($summary['memberPaid'],0,',','.') }}
                    </h5>
                    <small>{{ $summary['memberPaidCount'] }} transaksi</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <small>Member Unpaid</small>
                    <h5 class="fw-bold">
                        Rp {{ number_format($summary['memberUnpaid'],0,',','.') }}
                    </h5>
                    <small>{{ $summary['memberUnpaidCount'] }} transaksi</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <small>Voucher</small>
                    <h5 class="fw-bold">
                        Rp {{ number_format($summary['voucherTotal'],0,',','.') }}
                    </h5>
                    <small>{{ $summary['voucherTransaksi'] }} transaksi</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <small>Total Pendapatan</small>
                    <h4 class="fw-bold">
                        Rp {{ number_format($summary['totalPendapatan'],0,',','.') }}
                    </h4>
                </div>
            </div>
        </div>

    </div>

    {{-- EXPORT BUTTON --}}
    <div class="mb-3">
        <a href="{{ route('finance.laporan.export.excel.bulanan', ['bulan'=>$bulan,'tahun'=>$tahun]) }}"
           class="btn btn-outline-success btn-sm">
            Export Excel
        </a>

        <a href="{{ route('finance.laporan.export.pdf.bulanan', ['bulan'=>$bulan,'tahun'=>$tahun]) }}"
           class="btn btn-outline-danger btn-sm">
            Export PDF
        </a>
    </div>

    {{-- DETAIL TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($transaksis as $t)
                        <tr>
                            <td>{{ $t->tanggal->format('d M Y') }}</td>
                            <td>Member</td>
                            <td>
                                <span class="badge 
                                    {{ $t->status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($t->status) }}
                                </span>
                            </td>
                            <td>Rp {{ number_format($t->total,0,',','.') }}</td>
                        </tr>
                    @endforeach

                    @foreach($vouchers as $v)
                        <tr>
                            <td>{{ $v->sale_date->format('d M Y') }}</td>
                            <td>Voucher</td>
                            <td>-</td>
                            <td>Rp {{ number_format($v->total_amount,0,',','.') }}</td>
                        </tr>
                    @endforeach

                    @foreach($otherIncomes as $o)
                        <tr>
                            <td>{{ $o->income_date->format('d M Y') }}</td>
                            <td>Other Income</td>
                            <td>-</td>
                            <td>Rp {{ number_format($o->amount,0,',','.') }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection