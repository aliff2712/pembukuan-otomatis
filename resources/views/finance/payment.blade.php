@extends('layouts-main.app')

@section('title', 'Pembayaran Transaksi')

@section('content')

<div class="container py-5 d-flex justify-content-center">

    {{-- NOTIF SUCCESS --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
        <i class="fas fa-check-circle me-1"></i>
        {{ session('success') }}
    </div>

    <script>
        setTimeout(function(){
            let alert = document.querySelector('.alert-success');
            if(alert){
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i>
        {{ session('error') }}
    </div>
@endif


    <div class="card shadow-lg border-0" style="max-width: 500px; width:100%;">
        
        <div class="card-body p-4">

            {{-- HEADER STRUK --}}
            <div class="text-center mb-4">
                <h4 class="fw-bold">DHS FINANCE</h4>
                <small class="text-muted">Bukti Konfirmasi Pembayaran</small>
                <hr>
            </div>

            {{-- DETAIL --}}
            <div class="mb-2 d-flex justify-content-between">
                <span>Kode Transaksi</span>
                <strong>{{ $transaksi->kode_transaksi }}</strong>
            </div>

            <div class="mb-2 d-flex justify-content-between">
                <span>Customer</span>
                <strong>{{ $transaksi->nama_customer }}</strong>
            </div>

            <div class="mb-2 d-flex justify-content-between">
                <span>Tanggal Transaksi</span>
                <strong>{{ $transaksi->tanggal->format('d M Y') }}</strong>
            </div>

            <div class="mb-2 d-flex justify-content-between">
                <span>Jatuh Tempo</span>
                <strong>{{ $transaksi->jatuh_tempo?->format('d M Y') ?? '-' }}</strong>
            </div>

            <hr>

            {{-- TOTAL --}}
            <div class="d-flex justify-content-between fs-5 mb-3">
                <span>Total Pembayaran</span>
                <strong class="text-success">
                    Rp {{ number_format($transaksi->total,0,',','.') }}
                </strong>
            </div>

            <hr>

            {{-- STATUS --}}
            <div class="text-center mb-4">
                @if($transaksi->status === 'paid')
                    <span class="badge bg-success px-3 py-2">
                        SUDAH DIBAYAR
                    </span>
                @else
                    <span class="badge bg-danger px-3 py-2">
                        BELUM DIBAYAR
                    </span>
                @endif
            </div>

            {{-- BUTTON --}}
            @if($transaksi->status !== 'paid')
                <form action="{{ route('finance.transaksi.payment.process', $transaksi->id) }}"
                      method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check me-1"></i>
                            Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            @endif

            <div class="text-center mt-3">
                <a href="{{ route('finance.transaksi.show', $transaksi->id) }}"
                   class="btn btn-outline-secondary btn-sm">
                    Kembali
                </a>
            </div>

        </div>
    </div>

</div>


@endsection