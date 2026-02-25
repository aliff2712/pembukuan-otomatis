<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="DHS FINANCE - Sistem Pembukuan ISP">
    <meta name="author" content="DHS Finance">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - DHS FINANCE</title>

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- SB Admin 2 Custom CSS -->
    <style>
:root {
    --primary-color: #3b82f6;
    --success-color: #22c55e;
    --info-color: #06b6d4;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;

    --dark-bg: #0f172a;
    --dark-sidebar: #0b1120;
    --dark-card: #1e293b;
    --dark-topbar: #111827;
    --dark-border: #1f2937;

    --text-main: #e2e8f0;
    --text-muted: #94a3b8;
}

/* ================= BODY ================= */

body {
    font-family: 'Nunito', sans-serif;
    background-color: var(--dark-bg);
    color: var(--text-main);
}

#wrapper {
    display: flex;
}

#content-wrapper {
    background-color: var(--dark-bg);
    width: 100%;
    overflow-x: hidden;
}

#content {
    flex: 1 0 auto;
}

/* ================= SIDEBAR ================= */

.sidebar {
    position: relative;
    width: 14rem;
    min-height: 100vh;
    background: linear-gradient(180deg, #0b1120, #111827);
    border-right: 1px solid var(--dark-border);
    overflow: hidden;
}

.sidebar::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image: url('/assets/img/dhs-logo.png');
    background-repeat: no-repeat;
    background-position: center 25%;
    background-size: 100%;
    opacity: 0.05;
    pointer-events: none;
}

.sidebar .sidebar-brand {
    height: 4.375rem;
    font-weight: 700;
    color: #fff;
}

.sidebar hr.sidebar-divider {
    border-top: 1px solid rgba(255,255,255,0.05);
}

.sidebar .sidebar-heading {
    color: #64748b;
}

.sidebar .nav-item .nav-link {
    padding: 0.9rem 1rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    transition: 0.2s ease;
}

.sidebar .nav-item .nav-link i {
    margin-right: 0.6rem;
    font-size: 0.9rem;
}

.sidebar .nav-item .nav-link:hover,
.sidebar .nav-item .nav-link.active {
    background-color: rgba(59,130,246,0.15);
    color: #fff;
    border-left: 3px solid var(--primary-color);
}

/* ================= TOPBAR ================= */

.topbar {
    height: 4.375rem;
    background-color: var(--dark-topbar);
    border-bottom: 1px solid var(--dark-border);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.topbar .nav-item .nav-link {
    color: var(--text-muted);
    transition: 0.2s ease;
}

.topbar .nav-item .nav-link:hover {
    color: #fff;
}

.topbar-divider {
    border-right: 1px solid var(--dark-border);
}

/* ================= CARDS ================= */

.card {
    background-color: var(--dark-card);
    border: 1px solid var(--dark-border);
    border-radius: 14px;
    box-shadow: 0 12px 25px rgba(0,0,0,0.35);
    color: var(--text-main);
}

.card .card-header {
    background-color: transparent;
    border-bottom: 1px solid var(--dark-border);
}

/* Left border variants */
.border-left-primary { border-left: 4px solid var(--primary-color) !important; }
.border-left-success { border-left: 4px solid var(--success-color) !important; }
.border-left-info { border-left: 4px solid var(--info-color) !important; }
.border-left-warning { border-left: 4px solid var(--warning-color) !important; }
.border-left-danger { border-left: 4px solid var(--danger-color) !important; }

/* ================= TEXT ================= */

.text-primary { color: var(--primary-color) !important; }
.text-success { color: var(--success-color) !important; }
.text-info { color: var(--info-color) !important; }
.text-warning { color: var(--warning-color) !important; }
.text-danger { color: var(--danger-color) !important; }

.text-gray-800 { color: var(--text-main) !important; }
.text-gray-300 { color: #64748b !important; }

/* ================= DROPDOWN ================= */

.dropdown-menu {
    background-color: var(--dark-card);
    border: 1px solid var(--dark-border);
    border-radius: 12px;
}

.dropdown-item {
    color: var(--text-muted);
    border-radius: 8px;
    transition: 0.2s ease;
}

.dropdown-item:hover {
    background-color: #334155;
    color: #fff;
}

/* ================= FOOTER ================= */

.footer {
    background-color: var(--dark-topbar);
    border-top: 1px solid var(--dark-border);
    color: var(--text-muted);
}

/* ================= SCROLLBAR ================= */

::-webkit-scrollbar {
    width: 0.5rem;
}

::-webkit-scrollbar-track {
    background-color: #0f172a;
}

::-webkit-scrollbar-thumb {
    background-color: #334155;
    border-radius: 0.5rem;
}

::-webkit-scrollbar-thumb:hover {
    background-color: #475569;
}

/* ================= RESPONSIVE ================= */

@media (max-width: 768px) {
    .sidebar {
        width: 6.5rem;
    }

    .sidebar .nav-item .nav-link span,
    .sidebar .sidebar-brand-text {
        display: none;
    }
}
</style>

    @stack('styles')
</head>
@if(session('success'))
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        @include('layouts-main.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                @include('layouts-main.navbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid py-4">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('layouts-main.footer')
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top" style="display: none; position: fixed; right: 1rem; bottom: 1rem; width: 2.75rem; height: 2.75rem; text-align: center; color: #fff; background: rgba(90, 92, 105, 0.5); line-height: 46px; border-radius: 0.35rem;">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scroll to top -->
    <script>
        $(document).ready(function() {
            // Scroll to top button appear
            $(document).on('scroll', function() {
                var scrollDistance = $(this).scrollTop();
                if (scrollDistance > 100) {
                    $('.scroll-to-top').fadeIn();
                } else {
                    $('.scroll-to-top').fadeOut();
                }
            });

            // Smooth scrolling
            $('.scroll-to-top').on('click', function(e) {
                var target = $(this).attr('href');
                $('html, body').animate({
                    scrollTop: $(target).offset().top
                }, 1000);
                e.preventDefault();
            });
        });
    </script>

    @stack('scripts')
</body>
</html>