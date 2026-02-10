<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="sidebar-brand-text mx-3">DHS Finance</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Transaksi
    </div>

    <!-- Nav Item - Beat Invoices -->
    <li class="nav-item {{ request()->routeIs('beat-invoices*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('beat-invoices.index') }}">
            <i class="fas fa-fw fa-file-invoice"></i>
            <span>Invoice Beat</span>
        </a>
    </li>

    <!-- Nav Item - Voucher Sales -->
    <li class="nav-item {{ request()->routeIs('voucher-sales*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('voucher-sales.index') }}">
            <i class="fas fa-fw fa-ticket-alt"></i>
            <span>Voucher Sales</span>
        </a>
    </li>

    <!-- Nav Item - Payments -->
    <li class="nav-item {{ request()->routeIs('payments*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('payments.index') }}">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Payments</span>
        </a>
    </li>

    <!-- Nav Item - Expenses -->
    <li class="nav-item {{ request()->routeIs('expenses*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('expenses.index') }}">
            <i class="fas fa-fw fa-receipt"></i>
            <span>Expenses</span>
        </a>
    </li>

    <!-- Nav Item - Other Income -->
    <li class="nav-item {{ request()->routeIs('other-incomes*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('other-incomes.index') }}">
            <i class="fas fa-fw fa-wallet"></i>
            <span>Other Income</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Akuntansi
    </div>

    <!-- Nav Item - Chart of Accounts -->
    <li class="nav-item {{ request()->routeIs('chart-of-accounts*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('chart-of-accounts.index') }}">
            <i class="fas fa-fw fa-list-alt"></i>
            <span>Chart of Accounts</span>
        </a>
    </li>

    <!-- Nav Item - Journal Entries -->
    <li class="nav-item {{ request()->routeIs('journal-entries*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('journal-entries.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Journal Entries</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Laporan
    </div>

    <!-- Nav Item - Reports Collapse Menu -->
    <li class="nav-item {{ request()->routeIs('reports*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseReports"
            aria-expanded="true" aria-controls="collapseReports">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Reports</span>
        </a>
        <div id="collapseReports" class="collapse {{ request()->routeIs('reports*') ? 'show' : '' }}" 
            aria-labelledby="headingReports" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Financial Reports:</h6>
                <a class="collapse-item {{ request()->routeIs('reports.ledger') ? 'active' : '' }}" 
                    href="{{ route('reports.ledger') }}">
                    <i class="fas fa-angle-right"></i> Ledger
                </a>
                <a class="collapse-item {{ request()->routeIs('reports.ar-aging') ? 'active' : '' }}" 
                    href="{{ route('reports.ar-aging') }}">
                    <i class="fas fa-angle-right"></i> AR Aging
                </a>
                <a class="collapse-item {{ request()->routeIs('reports.income-statement') ? 'active' : '' }}" 
                    href="{{ route('reports.income-statement') }}">
                    <i class="fas fa-angle-right"></i> Income Statement
                </a>
                <a class="collapse-item {{ request()->routeIs('reports.balance-sheet') ? 'active' : '' }}" 
                    href="{{ route('reports.balance-sheet') }}">
                    <i class="fas fa-angle-right"></i> Balance Sheet
                </a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle" 
            style="background-color: rgba(255, 255, 255, 0.2); width: 2.5rem; height: 2.5rem;">
            <i class="fas fa-angle-left text-white"></i>
        </button>
    </div>
</ul>

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle the side navigation
        $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
            $("body").toggleClass("sidebar-toggled");
            $(".sidebar").toggleClass("toggled");
            if ($(".sidebar").hasClass("toggled")) {
                $('.sidebar .collapse').collapse('hide');
            }
        });

        // Close any open menu accordions when window is resized below 768px
        $(window).resize(function() {
            if ($(window).width() < 768) {
                $('.sidebar .collapse').collapse('hide');
            }
        });

        // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
        $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
            if ($(window).width() > 768) {
                var e0 = e.originalEvent,
                    delta = e0.wheelDelta || -e0.detail;
                this.scrollTop += (delta < 0 ? 1 : -1) * 30;
                e.preventDefault();
            }
        });
    });
</script>
@endpush