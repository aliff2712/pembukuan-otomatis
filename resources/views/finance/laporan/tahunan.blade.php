@extends('layouts-main.app')

@section('title', 'Laporan Tahunan')
@section('page-title', 'Laporan Tahunan ' . $tahun)

@section('content')
<div class="container-fluid">

    {{-- SUMMARY --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <small>Total Pendapatan</small>
                    <h4 class="fw-bold">
                        Rp {{ number_format($summary['totalPendapatan'],0,',','.') }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- EXPORT --}}
    <div class="mb-3">
        <a href="{{ route('finance.laporan.export.excel.tahunan', ['tahun'=>$tahun]) }}"
           class="btn btn-outline-success btn-sm">
            Export Excel
        </a>

        <a href="{{ route('finance.laporan.export.pdf.tahunan', ['tahun'=>$tahun]) }}"
           class="btn btn-outline-danger btn-sm">
            Export PDF
        </a>
    </div>

    {{-- TABLE PER BULAN --}}
    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Bulan</th>
                        <th>Member Paid</th>
                        <th>Member Unpaid</th>
                        <th>Voucher</th>
                        <th>Other</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($perBulan as $row)
                        <tr>
                            <td>{{ $row['bulan'] }}</td>
                            <td>Rp {{ number_format($row['member_paid'],0,',','.') }}</td>
                            <td>Rp {{ number_format($row['member_unpaid'],0,',','.') }}</td>
                            <td>Rp {{ number_format($row['voucher'],0,',','.') }}</td>
                            <td>Rp {{ number_format($row['other'],0,',','.') }}</td>
                            <td class="fw-bold">
                                Rp {{ number_format($row['total'],0,',','.') }}
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection