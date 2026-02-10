@extends('layouts-main.guest')

@section('title', 'Login')

@section('content')
<div class="text-center mb-4">
    <h3>Selamat Datang! 👋</h3>
    <p class="text-muted small mb-0">Silakan login untuk melanjutkan</p>
</div>

<!-- Session Status -->
@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Validation Errors -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Oops!</strong> Terjadi kesalahan:
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input 
            id="email" 
            name="email" 
            type="email" 
            class="form-control @error('email') is-invalid @enderror" 
            value="{{ old('email') }}" 
            placeholder="admin@dhsfinance.com"
            required 
            autofocus
            autocomplete="username"
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <input 
                id="password" 
                name="password" 
                type="password" 
                class="form-control @error('password') is-invalid @enderror" 
                placeholder="Masukkan password"
                required 
                autocomplete="current-password"
            >
            <button 
                type="button" 
                class="btn" 
                id="togglePassword" 
                title="Tampilkan password"
                tabindex="-1"
            >
                <i class="fas fa-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input 
                class="form-check-input" 
                type="checkbox" 
                name="remember" 
                id="remember" 
                {{ old('remember') ? 'checked' : '' }}
            >
            <label class="form-check-label" for="remember">
                Ingat saya
            </label>
        </div>

        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="small">
                Lupa password?
            </a>
        @endif
    </div>

    <!-- Submit Button -->
    <div class="d-grid mb-3">
        <button 
            type="submit" 
            class="btn btn-primary" 
            id="loginBtn"
        >
            <span class="btn-text">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk
            </span>
            <span class="spinner-border spinner-border-sm d-none" role="status">
                <span class="visually-hidden">Loading...</span>
            </span>
        </button>
    </div>

    <!-- Register Link (Optional) -->
    @if (Route::has('register'))
        <div class="text-center">
            <p class="text-muted small mb-0">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="fw-semibold">
                    Hubungi Administrator
                </a>
            </p>
        </div>
    @endif
</form>

@push('scripts')
<script>
    // Toggle Password Visibility
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (!passwordInput) return;
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            this.title = 'Sembunyikan password';
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            this.title = 'Tampilkan password';
        }
    });

    // Form Submit Loading State
    document.getElementById('loginForm')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('loginBtn');
        const btnText = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.spinner-border');
        
        btn.disabled = true;
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush

@endsection