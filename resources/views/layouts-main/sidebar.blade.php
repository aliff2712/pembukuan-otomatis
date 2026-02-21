<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="sidebar-brand-text mx-3">DHS Finance</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- ===================== -->
    <!-- TRANSAKSI SECTION -->
    <!-- ===================== -->
    <div class="sidebar-heading">
        Transaksi
    </div>

    <!-- Transaksi (Finance Module) -->
    <li class="nav-item {{ request()->routeIs('finance.transaksi.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('finance.transaksi.index') }}">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Transaksi</span>
        </a>
    </li>

    <!-- Beat Invoices -->
    <li class="nav-item {{ request()->routeIs('beat-invoices*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('beat-invoices.index') }}">
            <i class="fas fa-fw fa-file-invoice"></i>
            <span>Invoice Beat</span>
        </a>
    </li>

    <!-- Voucher Sales -->
    <li class="nav-item {{ request()->routeIs('voucher-sales*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('voucher-sales.index') }}">
            <i class="fas fa-fw fa-ticket-alt"></i>
            <span>Voucher Sales</span>
        </a>
    </li>

    <!-- Payments -->
    <li class="nav-item {{ request()->routeIs('payments*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('payments.index') }}">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Payments</span>
        </a>
    </li>

    <!-- Expenses -->
    <li class="nav-item {{ request()->routeIs('expenses*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('expenses.index') }}">
            <i class="fas fa-fw fa-receipt"></i>
            <span>Expenses</span>
        </a>
    </li>

    <!-- Other Income -->
    <li class="nav-item {{ request()->routeIs('other-incomes*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('other-incomes.index') }}">
            <i class="fas fa-fw fa-wallet"></i>
            <span>Other Income</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- ===================== -->
    <!-- AKUNTANSI -->
    <!-- ===================== -->
    <div class="sidebar-heading">
        Akuntansi
    </div>

    <li class="nav-item {{ request()->routeIs('chart-of-accounts*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('chart-of-accounts.index') }}">
            <i class="fas fa-fw fa-list-alt"></i>
            <span>Chart of Accounts</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('journal-entries*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('journal-entries.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Journal Entries</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- ===================== -->
    <!-- LAPORAN -->
    <!-- ===================== -->
    <div class="sidebar-heading">
        Laporan
    </div>

<<<<<<< HEAD
    {{-- <!-- Nav Item - Ledger -->
=======
>>>>>>> a1870fa (:transaksiImport)
    <li class="nav-item {{ request()->routeIs('reports.ledger') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reports.ledger') }}">
            <i class="fas fa-fw fa-book-open"></i>
            <span>Ledger</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('reports.ar-aging') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reports.ar-aging') }}">
            <i class="fas fa-fw fa-hourglass-end"></i>
            <span>AR Aging</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('reports.income-statement') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reports.income-statement') }}">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Income Statement</span>
        </a>
    </li> --}}

<<<<<<< HEAD
    {{-- <!-- Nav Item - Balance Sheet -->
    <li class="nav-item {{ request()->routeIs('reports.balance-sheet') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reports.balance-sheet') }}">
            <i class="fas fa-fw fa-scale-balanced"></i>
            <span>Balance Sheet</span>
        </a>
    </li> --}}
    {{-- <!-- Divider -->
=======
>>>>>>> a1870fa (:transaksiImport)
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggle -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"
            style="background-color: rgba(255, 255, 255, 0.2); width: 2.5rem; height: 2.5rem;">
            <i class="fas fa-angle-left text-white"></i>
        </button>
<<<<<<< HEAD
    </div> --}}
=======
    </div>

>>>>>>> a1870fa (:transaksiImport)
</ul>
