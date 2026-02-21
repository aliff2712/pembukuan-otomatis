@extends('layouts-main.test')

@section('title', 'Register')

@section('content')
<div class="login-wrapper">
    <div class="card login-card shadow-lg border-0">
        <div class="card-body p-4">

            <div class="text-center mb-4">
                <h3 class="fw-bold">Buat Akun Baru 🚀</h3>
                <p class="text-muted small mb-0">Daftar untuk mengakses DHS Finance</p>
            </div>

            {{-- Alert --}}
            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" name="name"
                        class="form-control rounded-3"
                        value="{{ old('name') }}"
                        placeholder="John Doe"
                        required>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email"
                        class="form-control rounded-3"
                        value="{{ old('email') }}"
                        placeholder="admin@dhsfinance.com"
                        required>
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <input type="password"
                            name="password"
                            id="password"
                            class="form-control rounded-start-3"
                            placeholder="Minimal 8 karakter"
                            required>
                        <button type="button"
                            class="btn btn-light border"
                            id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Confirm --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Konfirmasi Password</label>
                    <div class="input-group">
                        <input type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="form-control rounded-start-3"
                            placeholder="Ulangi password"
                            required>
                        <button type="button"
                            class="btn btn-light border"
                            id="togglePasswordConfirmation">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-grid">
                    <button type="submit"
                        class="btn btn-primary rounded-3 fw-semibold"
                        id="registerBtn">
                        <span class="btn-text">
                            <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                        </span>
                        <span class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>

                {{-- Login Link --}}
                <div class="text-center mt-4">
                    <small class="text-muted">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="fw-semibold">
                            Login di sini
                        </a>
                    </small>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
document.addEventListener("DOMContentLoaded", function(){

    // Toggle password
    function toggle(inputId, btnId){
        const btn = document.getElementById(btnId);
        if(!btn) return;

        btn.addEventListener("click", function(){
            const input = document.getElementById(inputId);
            const icon = this.querySelector("i");

            if(input.type === "password"){
                input.type = "text";
                icon.classList.replace("fa-eye","fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye-slash","fa-eye");
            }
        });
    }

    toggle("password","togglePassword");
    toggle("password_confirmation","togglePasswordConfirmation");

    // Loading aman (tanpa overlay)
    const form = document.getElementById("registerForm");

    form?.addEventListener("submit", function(){
        const btn = document.getElementById("registerBtn");
        const text = btn.querySelector(".btn-text");
        const spinner = btn.querySelector(".spinner-border");

        btn.disabled = true;
        text.classList.add("d-none");
        spinner.classList.remove("d-none");
    });

});
</script>
@endsection