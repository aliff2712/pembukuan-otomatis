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
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fc;
            color: #858796;
        }

        #wrapper {
            display: flex;
        }

        #content-wrapper {
            background-color: #f8f9fc;
            width: 100%;
            overflow-x: hidden;
        }

        #content {
            flex: 1 0 auto;
        }

        /* Sidebar Styles */
        .sidebar {
        position: relative;
        width: 14rem;
        min-height: 100vh;
        background-color: #ea6f0a; /* warna dasar sidebar */
        overflow: hidden;
    }

    /* background logo */
    .sidebar::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: url('/assets/img/dhs-logo.png');
        background-repeat: no-repeat;
        background-position: center top;
        background-position: center 25%;
        background-size: 100%;
        opacity: 0.12; /* jangan lebih dari ini */
        pointer-events: none;
    }


        .sidebar .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar .sidebar-brand-icon i {
            font-size: 2rem;
        }

        .sidebar .sidebar-brand-text {
            margin-left: 0.5rem;
        }

        .sidebar hr.sidebar-divider {
            margin: 0 1rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }

        .sidebar .sidebar-heading {
            text-align: center;
            padding: 0 1rem;
            font-weight: 800;
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }

        .sidebar .nav-item {
            position: relative;
        }

        .sidebar .nav-item .nav-link {
            text-align: left;
            padding: 1rem;
            width: 14rem;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .sidebar .nav-item .nav-link:hover,
        .sidebar .nav-item .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-item .nav-link i {
            font-size: 0.85rem;
            margin-right: 0.5rem;
        }

        .sidebar .nav-item .nav-link span {
            font-size: 0.85rem;
            display: inline;
        }

        /* Collapse styles */
        .sidebar .nav-item .collapse {
            position: absolute;
            left: calc(14rem - 1.5rem);
            z-index: 1;
            top: 2px;
        }

        .sidebar .nav-item .collapse .collapse-inner {
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            background-color: #fff;
            padding: 0.5rem 0;
            min-width: 10rem;
            font-size: 0.85rem;
        }

        .sidebar .nav-item .collapse .collapse-inner .collapse-header {
            margin: 0;
            white-space: nowrap;
            padding: 0.5rem 1.5rem;
            text-transform: uppercase;
            font-weight: 800;
            font-size: 0.65rem;
            color: #b7b9cc;
        }

        .sidebar .nav-item .collapse .collapse-inner .collapse-item {
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            display: block;
            color: #3a3b45;
            text-decoration: none;
            border-radius: 0.35rem;
            white-space: nowrap;
        }

        .sidebar .nav-item .collapse .collapse-inner .collapse-item:hover {
            background-color: #eaecf4;
        }

        .sidebar .nav-item .collapse .collapse-inner .collapse-item.active {
            color: var(--primary-color);
            font-weight: 700;
        }

        /* Topbar */
        .topbar {
            height: 4.375rem;
            background-color: #ffffff8a;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .topbar .navbar-search {
            width: 25rem;
        }

        .topbar .navbar-search input {
            font-size: 0.85rem;
            height: auto;
        }

        .topbar .topbar-divider {
            width: 0;
            border-right: 1px solid #e3e6f0;
            height: calc(4.375rem - 2rem);
            margin: auto 1rem;
        }

        .topbar .nav-item .nav-link {
            height: 4.375rem;
            display: flex;
            align-items: center;
            padding: 0 0.75rem;
        }

        .topbar .nav-item .nav-link:focus {
            outline: none;
        }

        /* Cards */
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: none;
        }

        .card .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }

        .border-left-primary {
            border-left: 0.25rem solid var(--primary-color) !important;
        }

        .border-left-success {
            border-left: 0.25rem solid var(--success-color) !important;
        }

        .border-left-info {
            border-left: 0.25rem solid var(--info-color) !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid var(--warning-color) !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid var(--danger-color) !important;
        }

        /* Text colors */
        .text-primary {
            color: var(--primary-color) !important;
        }

        .text-success {
            color: var(--success-color) !important;
        }

        .text-info {
            color: var(--info-color) !important;
        }

        .text-warning {
            color: var(--warning-color) !important;
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        /* Footer */
        .footer {
            background-color: #fff;
            padding: 2rem 0;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 6.5rem;
            }

            .sidebar .nav-item .nav-link span {
                display: none;
            }

            .sidebar .sidebar-brand-text {
                display: none;
            }

            .topbar .navbar-search {
                width: 15rem;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 0.5rem;
        }

        ::-webkit-scrollbar-track {
            background-color: #f8f9fc;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #d1d3e2;
            border-radius: 0.5rem;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #b7b9cc;
        }
    </style>

    @stack('styles')
</head>

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