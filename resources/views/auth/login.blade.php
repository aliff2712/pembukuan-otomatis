@extends('layouts-main.guest')

@section('title', 'Login')

@section('content')
<div class="row gx-0">
    <div class="col-md-6 d-none d-md-flex align-items-stretch">
        <div class="w-100 h-100" style="background: linear-gradient(135deg,#4e73df 0%, #11cdef 100%); display:flex; align-items:center; justify-content:center; color:#fff; padding:2.5rem;">
            <div class="text-center">
                <img src="/assets/img/dhs-logo-white.png" alt="DHS" style="max-height:72px; margin-bottom:1rem; opacity:0.95;">
                <h2 class="fw-bold" style="letter-spacing:0.02em;">DHS FINANCE</h2>
                <p class="lead mt-2" style="opacity:0.95;">Sistem Pembukuan ISP yang cepat, akurat, dan mudah digunakan.</p>
                <div class="mt-4">
                    <span class="badge bg-light text-dark me-2">Keamanan</span>
                    <span class="badge bg-light text-dark me-2">Integrasi</span>
                    <span class="badge bg-light text-dark">Realtime</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 d-flex align-items-center justify-content-center">
        <div class="card shadow-sm guest-card">
            <div class="card-body p-4">
                <h4 class="mb-3 text-center">Masuk ke akun Anda</h4>

                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input id="password" name="password" type="password" class="form-control" required autocomplete="current-password">
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword" title="Tampilkan password">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
                        @endif
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">Log in</button>
                    </div>

                    <div class="text-center small text-muted">
                        Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('togglePassword')?.addEventListener('click', function(){
        const pwd = document.getElementById('password');
        if(!pwd) return;
        if(pwd.type === 'password'){ pwd.type = 'text'; this.innerHTML = '<i class="fa fa-eye-slash"></i>'; }
        else { pwd.type = 'password'; this.innerHTML = '<i class="fa fa-eye"></i>'; }
    });
</script>
@endpush

@endsection
