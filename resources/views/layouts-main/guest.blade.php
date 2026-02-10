<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Login') - DHS FINANCE</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fc; color: #5a5c69; }
        .guest-wrapper { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding:2rem; }
        .guest-card { width: 100%; max-width: 520px; }
        .brand { text-align:center; margin-bottom:1rem; }
        .brand img { max-height:60px; }
    </style>

    @stack('styles')
</head>
<body>
    <div class="guest-wrapper">
        <div class="guest-card">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="brand mb-3">
                        <a href="{{ url('/') }}">
                            <img src="/assets/img/dhs-logo.png" alt="DHS Finance">
                        </a>
                    </div>

                    @yield('content')

                </div>
            </div>
            <p class="text-center text-muted mt-3 small">&copy; {{ date('Y') }} DHS FINANCE</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
