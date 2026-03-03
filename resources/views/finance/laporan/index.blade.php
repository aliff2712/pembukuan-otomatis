@extends('layouts-main.app')

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@section('content')
<div class="container-fluid">

    <div class="row g-4">

        <!-- Laporan Bulanan -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Laporan Bulanan</h5>

                    <form action="{{ route('finance.laporan.bulanan') }}" method="GET">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Bulan</label>
                                <select name="bulan" class="form-select">
                                    @foreach(range(1,12) as $b)
                                        <option value="{{ $b }}">
                                            {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tahun</label>
                                <input type="number" name="tahun" 
                                       class="form-control" 
                                       value="{{ now()->year }}">
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary w-100">
                                    Lihat Laporan
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Laporan Tahunan -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Laporan Tahunan</h5>

                    <form action="{{ route('finance.laporan.tahunan') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" 
                                   class="form-control" 
                                   value="{{ now()->year }}">
                        </div>

                        <button class="btn btn-success w-100">
                            Lihat Laporan
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection