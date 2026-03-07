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

        {{-- BARU: Total Pengeluaran --}}
        <div class="col-md-4">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <small>Total Pengeluaran</small>
                    <h4 class="fw-bold">
                        Rp {{ number_format($summary['totalPengeluaran'],0,',','.') }}
                    </h4>
                    <small>{{ $summary['expenseCount'] }} transaksi</small>
                </div>
            </div>
        </div>

    {{-- EXPORT --}}
    <div class="mb-3">
        <a href="{{ route('finance.laporan.export.excel.tahunan', ['tahun'=>$tahun]) }}"
           class="btn btn-outline-success btn-sm"
           onclick="startExport()">
            Export Excel
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
                        <th>Total Pendapatan</th>
                        {{-- BARU --}}
                        <th>Pengeluaran</th>
                        <th>Laba Kotor</th>
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
                            {{-- BARU --}}
                            <td class="text-danger">
                                Rp {{ number_format($row['pengeluaran'],0,',','.') }}
                            </td>
                            <td class="fw-bold {{ $row['laba_kotor'] >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($row['laba_kotor'],0,',','.') }}
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>

</div>
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