<ul class="navbar-nav sidebar modern-sidebar accordion" id="accordionSidebar">

    <!-- BRAND -->
    <div class="sidebar-brand d-flex align-items-center justify-content-center">
        <div class="sidebar-brand-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="sidebar-brand-text ms-2">DHS Finance</div>
    </div>

    <hr class="sidebar-divider my-3">

    <!-- DASHBOARD -->
    <li class="nav-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Transaksi
    </div>

    <li class="nav-item {{ request()->routeIs('finance*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('finance.transaksi.index') }}">
            <i class="fas fa-ticket-alt"></i>
            <span>Transaksi</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('voucher-sales*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('voucher-sales.index') }}">
            <i class="fas fa-receipt"></i>
            <span>Voucher Sales</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('payments*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('payments.index') }}">
            <i class="fas fa-money-bill-wave"></i>
            <span>Payments</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('expenses*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('expenses.index') }}">
            <i class="fas fa-wallet"></i>
            <span>Expenses</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('other-incomes*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('other-incomes.index') }}">
            <i class="fas fa-coins"></i>
            <span>Other Income</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Akuntansi
    </div>

    <li class="nav-item {{ request()->routeIs('chart-of-accounts*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('chart-of-accounts.index') }}">
            <i class="fas fa-list"></i>
            <span>Chart of Accounts</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('journal-entries*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('journal-entries.index') }}">
            <i class="fas fa-book"></i>
            <span>Journal Entries</span>
        </a>
    </li>

</ul>
<style>

/* ================================
   PREMIUM MODERN NAVY SIDEBAR
================================ */

.modern-sidebar {
    background: #0f172a;
    min-height: 100vh;
    padding-top: 1rem;
    transition: all 0.3s ease;
    border-right: 1px solid rgba(255,255,255,0.05);
}

/* BRAND */
.modern-sidebar .sidebar-brand {
    font-weight: 600;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
    color: #ffffff;
    padding: 1rem;
    border-radius: 14px;
    margin: 0 12px;
    background: rgba(255,255,255,0.03);
    transition: 0.3s ease;
}

.modern-sidebar .sidebar-brand:hover {
    background: rgba(59,130,246,0.15);
}

.modern-sidebar .sidebar-brand-icon i {
    font-size: 1.2rem;
    color: #3b82f6;
}

/* SECTION HEADING */
.modern-sidebar .sidebar-heading {
    font-size: 0.65rem;
    font-weight: 600;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    padding: 0 1.5rem;
    margin-top: 1.2rem;
    margin-bottom: 0.5rem;
}

/* NAV ITEM */
.modern-sidebar .nav-link {
    color: rgba(255,255,255,0.75);
    padding: 0.75rem 1rem;
    margin: 4px 12px;
    border-radius: 12px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
}

/* ICON */
.modern-sidebar .nav-link i {
    margin-right: 10px;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.5);
    transition: 0.2s ease;
}

/* HOVER */
.modern-sidebar .nav-link:hover {
    background: rgba(59,130,246,0.12);
    color: #ffffff;
    transform: translateX(4px);
}

.modern-sidebar .nav-link:hover i {
    color: #3b82f6;
}

/* ACTIVE */
.modern-sidebar .nav-item.active .nav-link {
    background: #1e293b;
    color: #ffffff;
    font-weight: 500;
    box-shadow: inset 3px 0 0 #3b82f6;
}

.modern-sidebar .nav-item.active i {
    color: #3b82f6;
}

/* DIVIDER */
.modern-sidebar .sidebar-divider {
    border-top: 1px solid rgba(255,255,255,0.06);
    margin: 1rem 1rem;
}

/* SMOOTH SCROLLBAR */
.modern-sidebar::-webkit-scrollbar {
    width: 6px;
}

.modern-sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
}

</style>