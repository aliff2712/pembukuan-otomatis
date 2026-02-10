@extends('layouts-main.guest')

@section('title', 'Register')

@section('content')
<div class="text-center mb-4">
    <h3>Register new account</h3>
    <p class="text-muted small mb-0">Daftar untuk mengakses DHS Finance</p>
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

<form method="POST" action="{{ route('register') }}" id="registerForm">
    @csrf

    <!-- Name -->
    <div class="mb-3">
        <label for="name" class="form-label">Nama Lengkap</label>
        <input 
            id="name" 
            name="name" 
            type="text" 
            class="form-control @error('name') is-invalid @enderror" 
            value="{{ old('name') }}" 
            placeholder="John Doe"
            required 
            autofocus
            autocomplete="name"
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

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
                placeholder="Minimal 8 karakter"
                required 
                autocomplete="new-password"
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
        <small class="text-muted">Minimal 8 karakter, kombinasi huruf dan angka</small>
    </div>

    <!-- Confirm Password -->
    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
        <div class="input-group">
            <input 
                id="password_confirmation" 
                name="password_confirmation" 
                type="password" 
                class="form-control" 
                placeholder="Ulangi password"
                required 
                autocomplete="new-password"
            >
            <button 
                type="button" 
                class="btn" 
                id="togglePasswordConfirmation" 
                title="Tampilkan password"
                tabindex="-1"
            >
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="d-grid mb-3">
        <button 
            type="submit" 
            class="btn btn-primary" 
            id="registerBtn"
        >
            <span class="btn-text">
                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
            </span>
            <span class="spinner-border spinner-border-sm d-none" role="status">
                <span class="visually-hidden">Loading...</span>
            </span>
        </button>
    </div>

    <!-- Login Link -->
    <div class="text-center">
        <p class="text-muted small mb-0">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="fw-semibold">
                Login di sini
            </a>
        </p>
    </div>
</form>

@push('scripts')
<script>
    // Toggle Password Visibility
    function setupPasswordToggle(inputId, buttonId) {
        const button = document.getElementById(buttonId);
        if (!button) return;
        
        button.addEventListener('click', function() {
            const passwordInput = document.getElementById(inputId);
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
    }

    // Setup toggle for both password fields
    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('password_confirmation', 'togglePasswordConfirmation');

    // Password Strength Indicator (Optional)
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            // You can add visual feedback here
        });
    }

    function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;
        return strength;
    }

    // Password Match Validation
    const passwordConfirmation = document.getElementById('password_confirmation');
    if (passwordConfirmation) {
        passwordConfirmation.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmation = this.value;
            
            if (confirmation && password !== confirmation) {
                this.classList.add('is-invalid');
                if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Password tidak cocok';
                    this.parentElement.parentElement.appendChild(feedback);
                }
            } else {
                this.classList.remove('is-invalid');
                const feedback = this.parentElement.parentElement.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            }
        });
    }

    // Form Submit Loading State
    document.getElementById('registerForm')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('registerBtn');
        const btnText = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.spinner-border');
        
        // Check if passwords match
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        
        if (password !== confirmation) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok!');
            return;
        }
        
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