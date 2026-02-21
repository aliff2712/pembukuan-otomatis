@extends('layouts-main.app')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi DHS Dipanet Hotspot Solution')

@section('content')
<div class="container py-4">

    {{-- NOTIF --}}
    @if(session('success'))
        <div class="position-fixed top-0 start-50 translate-middle-x mt-4"
             style="z-index:9999; min-width:320px;">
            <div id="successAlert" class="alert alert-success shadow text-center">
                <i class="fas fa-check-circle me-1"></i>
                {{ session('success') }}
            </div>
        </div>

        <script>
            setTimeout(function(){
                let alert = document.getElementById('successAlert');
                if(alert){
                    alert.style.transition = "opacity 0.5s";
                    alert.style.opacity = "0";
                    setTimeout(() => alert.remove(), 500);
                }
            }, 3000);
        </script>
    @endif


    @php
        $jatuhTempo = $transaksi->jatuh_tempo;
        $isOverdue = $transaksi->isOverdue();
        $canPay = $transaksi->status === 'unpaid';
    @endphp

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('finance.transaksi.index') }}"
           class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>

        <span class="badge {{ $transaksi->status_color }} fs-6 px-4 py-2 rounded-pill">
            {{ strtoupper($transaksi->status) }}
        </span>
    </div>


    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-5">

            {{-- HEADER CUSTOMER --}}
            <div class="mb-4 text-center">
                <h3 class="fw-bold mb-1">{{ $transaksi->nama_customer }}</h3>
                <small class="text-muted">
                    Kode Transaksi: {{ $transaksi->kode_transaksi }}
                </small>
            </div>

            <hr class="mb-4">

            {{-- INFORMASI UTAMA --}}
            <div class="row g-4 mb-4">

                <div class="col-md-4">
                    <div class="bg-light p-4 rounded-4 text-center h-100 shadow-sm">
                        <small class="text-muted d-block mb-1">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Tanggal Transaksi
                        </small>
                        <div class="fw-semibold fs-5">
                            {{ $transaksi->tanggal->format('d M Y') }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bg-light p-4 rounded-4 text-center h-100 shadow-sm">
                        <small class="text-muted d-block mb-1">
                            <i class="fas fa-clock me-1"></i>
                            Jatuh Tempo
                        </small>
                        <div class="fw-semibold fs-5 {{ $isOverdue ? 'text-danger' : '' }}">
                            {{ $jatuhTempo->format('d M Y') }}
                        </div>
                        @if($isOverdue)
                            <span class="badge bg-danger mt-2">
                                OVERDUE
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bg-light p-4 rounded-4 text-center h-100 shadow-sm">
                        <small class="text-muted d-block mb-1">
                            <i class="fas fa-money-bill-wave me-1"></i>
                            Total Pembayaran
                        </small>
                        <div class="fw-bold fs-4 text-success">
                            Rp {{ number_format($transaksi->total,0,',','.') }}
                        </div>
                    </div>
                </div>

            </div>


            {{-- DESKRIPSI CUSTOMER --}}
            @if($transaksi->deskripsi)
                <div class="mb-4">
                    <h5 class="fw-semibold mb-3 text-center">
                        <i class="fas fa-user-circle me-2"></i>
                        Detail Informasi Customer
                    </h5>

                    <div class="card border-0 bg-light rounded-4 shadow-sm">
                        <div class="card-body px-4 py-3">

                            @foreach($transaksi->deskripsi as $key => $value)
                                <div class="row border-bottom py-2">
                                    <div class="col-md-6 text-muted text-capitalize">
                                        {{ str_replace('_', ' ', $key) }}
                                    </div>
                                    <div class="col-md-6 fw-semibold text-md-end">
                                        {{ $value }}
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            @endif


            {{-- ACTION BUTTON --}}
            <div class="text-center mt-4">

                @if($canPay)
                    <a href="{{ route('finance.transaksi.payment.form', $transaksi->id) }}"
                       class="btn btn-success btn-lg rounded-pill px-4 me-2">
                        <i class="fas fa-credit-card me-1"></i>
                        Proses Pembayaran
                    </a>
                @endif

                @if($transaksi->status === 'paid')
                    <a href="{{ route('finance.transaksi.receipt', $transaksi->id) }}"
                       class="btn btn-outline-primary btn-lg rounded-pill px-4"
                       target="_blank">
                        <i class="fas fa-print me-1"></i>
                        Print / Download Receipt
                    </a>
                @endif

            </div>

        </div>
    </div>

</div>
@endsection