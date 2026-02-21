@extends('layouts-main.app')

@section('title', 'Setting Jatuh Tempo')
@section('page-title', 'Setting Jatuh Tempo Finance')

@section('content')
<div class="container py-4">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('successToast');
        var toast = new bootstrap.Toast(toastEl, {
            delay: 3000
        });
        toast.show();
    });
</script>
@endif
    {{-- ERROR MESSAGE --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
            <h5 class="mb-0">
                <i class="fas fa-calendar-alt me-2"></i>
                Pengaturan Default Jatuh Tempo
            </h5>
        </div>

        <div class="card-body p-4">

            <form action="{{ route('finance.setting.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="default_due_day" class="form-label fw-semibold">
                        Tanggal Jatuh Tempo Default
                    </label>

                    <input type="number"
                           id="default_due_day"
                           name="default_due_day"
                           value="{{ old('default_due_day', $setting->default_due_day ?? 10) }}"
                           class="form-control form-control-lg"
                           min="1"
                           max="31"
                           required>

                    <div class="form-text">
                        Sistem akan otomatis mengatur jatuh tempo transaksi
                        ke tanggal ini setiap bulannya.
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i>
                        Simpan Setting
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection