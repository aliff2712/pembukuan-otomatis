@extends('layouts-main.app')
@section('title', __('Tambah Income Baru'))
@section('page-title', __('Jumlah Pendapatan Lain Baru'))
@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-plus-circle text-success me-2"></i>Tambah Income Baru
            </h1>
        </div>
        <a href="{{ route('other-incomes.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('other-incomes.store') }}" id="incomeForm">
                @csrf

                <!-- Tanggal Income -->
                <div class="mb-3">
                    <label for="income_date" class="form-label fw-semibold">
                        <i class="fas fa-calendar text-info me-1"></i>Tanggal
                    </label>
                    <input 
                        type="date" 
                        name="income_date" 
                        id="income_date" 
                        class="form-control @error('income_date') is-invalid @enderror"
                        value="{{ old('income_date', date('Y-m-d')) }}"
                        required
                    >
                    @error('income_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">
                        <i class="fas fa-align-left text-info me-1"></i>Deskripsi
                    </label>
                    <input 
                        type="text" 
                        name="description" 
                        id="description" 
                        class="form-control @error('description') is-invalid @enderror" 
                        placeholder="Contoh: Jasa Konsultasi, Sewa Ruangan, etc."
                        value="{{ old('description') }}"
                        required
                    >
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Jumlah -->
                <div class="mb-3">
                    <label for="amount" class="form-label fw-semibold">
                        <i class="fas fa-money-bill-wave text-success me-1"></i>Jumlah
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input 
                            type="number" 
                            name="amount" 
                            id="amount" 
                            class="form-control @error('amount') is-invalid @enderror"
                            placeholder="0"
                            value="{{ old('amount') }}"
                            min="1"
                            required
                        >
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Catatan -->
                <div class="mb-4">
                    <label for="notes" class="form-label fw-semibold">
                        <i class="fas fa-sticky-note text-info me-1"></i>Catatan (Opsional)
                    </label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        class="form-control @error('notes') is-invalid @enderror"
                        rows="3"
                        placeholder="Tambahkan catatan atau keterangan tambahan..."
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                    <a href="{{ route('other-incomes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Format angka dengan pemisah ribuan
document.getElementById('amount').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endsection
