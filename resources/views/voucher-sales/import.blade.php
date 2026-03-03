@extends('layouts-main.app') {{-- sesuaikan dengan layout Anda --}}

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold text-primary">Import Mikhmon CSV</h5>
                    <small class="text-muted">Pipeline akan berjalan otomatis: Import → Transform → Aggregate → Journalize</small>
                </div>

                <div class="card-body p-4">

                    {{-- Alert Error --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Alert Success --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Pipeline Log --}}
                    @if(session('log'))
                        <div class="bg-dark rounded p-3 mb-4 font-monospace small text-success">
                            <div class="text-secondary mb-2">// Pipeline log</div>
                            @foreach(session('log') as $line)
                                <div>{{ $line }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('voucher-sales.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="csv_file" class="form-label fw-medium">File CSV Mikhmon</label>
                            <input type="file"
                                   class="form-control @error('csv_file') is-invalid @enderror"
                                   id="csv_file"
                                   name="csv_file"
                                   accept=".csv,.txt">
                            <div class="form-text">Format: .csv | Maksimal: 10MB</div>
                            @error('csv_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                      
                          <div class="d-flex justify-content-between align-items-center">
                                   <a href="{{ route('voucher-sales.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Kembali
                                    </a>
                            <button type="submit" class="btn btn-primary" id="btn-submit">
                                <i class="bi bi-rocket-takeoff me-2"></i>Jalankan Pipeline
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Disable button saat form submit untuk cegah double submit
    document.querySelector('form').addEventListener('submit', function () {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    });
</script>
@endpush