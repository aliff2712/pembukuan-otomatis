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
           class="btn btn-outline-success btn-sm"
           onclick="startExport()">
            Export Excel
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
<!-- EXPORT MODAL -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 text-center">

            <h5 class="mb-3">
                <i class="fas fa-file-export me-2"></i> Exporting Data
            </h5>

            <div class="d-flex justify-content-center mb-3">
                <div class="spinner-border" role="status"></div>
            </div>

            <div class="progress mb-2">
                <div id="exportProgress"
                     class="progress-bar progress-bar-striped progress-bar-animated"
                     style="width:0%">
                     0%
                </div>
            </div>

            <div id="progressText">Menyiapkan export...</div>

        </div>
    </div>
</div>
<style>

/* MODAL */
#exportModal .modal-content{
    border-radius:14px;
    border:none;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
}

/* TITLE */
#exportModal h5{
    color:#0b2a4a;
    font-weight:600;
}

/* SPINNER */
.spinner-border{
    color:#0b2a4a;
}

/* PROGRESS CONTAINER */
.progress{
    height:22px;
    border-radius:30px;
    background:#e9edf2;
}

/* PROGRESS BAR CERAH */
.progress-bar{
    font-weight:600;
    font-size:13px;
}

/* progress warna cerah + stripes */
.progress-bar-striped{
    background-image: linear-gradient(
        45deg,
        rgba(255,255,255,.25) 25%,
        transparent 25%,
        transparent 50%,
        rgba(255,255,255,.25) 50%,
        rgba(255,255,255,.25) 75%,
        transparent 75%,
        transparent
    ),
    linear-gradient(90deg,#38bdf8,#0ea5e9);

    background-size: 1rem 1rem, 100% 100%;
}

/* TEXT */
#progressText{
    font-size:14px;
    color:#0b2a4a;
}

</style>
<script>

function startExport(){

    var modal = new bootstrap.Modal(document.getElementById('exportModal'));
    modal.show();

    let progress = 0;
    let bar = document.getElementById("exportProgress");

    let interval = setInterval(function(){

        progress += 10;

        bar.style.width = progress + "%";
        bar.innerText = progress + "%";

        if(progress >= 100){

            clearInterval(interval);

            document.getElementById("progressText").innerText = "Export selesai";

            setTimeout(function(){
                modal.hide();
            },1000);

        }

    },300);

}

</script>
@endsection