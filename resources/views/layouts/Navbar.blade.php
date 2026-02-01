<nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow topbar-silver">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle me-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Title/Breadcrumb -->
    <div class="d-none d-sm-inline-block">
        <h1 class="h5 mb-0 text-gray-800">
            @yield('page-title', 'Dashboard')
        </h1>
    </div>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ms-auto">
        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-end p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline me-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small"
                            placeholder="Search for..." aria-label="Search"
                            aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter" 
                    style="position: absolute; top: 0.5rem; right: 0.25rem; font-size: 0.7rem; padding: 0.25rem 0.4rem; background-color: #e74a3b;">
                    3+
                </span>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                aria-labelledby="alertsDropdown" style="min-width: 20rem;">
                <h6 class="dropdown-header" style="background-color: #4e73df; color: white; font-weight: 700;">
                    Alerts Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="me-3">
                        <div class="icon-circle bg-primary">
                            <i class="fas fa-file-alt text-white" style="font-size: 0.85rem;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 12, 2024</div>
                        <span class="font-weight-bold">3 Invoice belum dibayar bulan ini</span>
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="me-3">
                        <div class="icon-circle bg-success">
                            <i class="fas fa-donate text-white" style="font-size: 0.85rem;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 7, 2024</div>
                        Pembayaran Rp 2.900.000 telah diterima
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="me-3">
                        <div class="icon-circle bg-warning">
                            <i class="fas fa-exclamation-triangle text-white" style="font-size: 0.85rem;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 2, 2024</div>
                        Saldo kas menipis, segera isi ulang
                    </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
            </div>
        </li>

        <!-- Nav Item - Messages -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter"
                    style="position: absolute; top: 0.5rem; right: 0.25rem; font-size: 0.7rem; padding: 0.25rem 0.4rem; background-color: #e74a3b;">
                    7
                </span>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                aria-labelledby="messagesDropdown" style="min-width: 20rem;">
                <h6 class="dropdown-header" style="background-color: #4e73df; color: white; font-weight: 700;">
                    Message Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image me-3">
                        <img class="rounded-circle" src="https://ui-avatars.com/api/?name=Admin&background=4e73df&color=fff"
                            alt="..." style="width: 3rem; height: 3rem;">
                        <div class="status-indicator bg-success"></div>
                    </div>
                    <div class="font-weight-bold">
                        <div class="text-truncate">Invoice dari pelanggan A sudah lunas!</div>
                        <div class="small text-gray-500">Admin · 58m</div>
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image me-3">
                        <img class="rounded-circle" src="https://ui-avatars.com/api/?name=Finance&background=1cc88a&color=fff"
                            alt="..." style="width: 3rem; height: 3rem;">
                        <div class="status-indicator"></div>
                    </div>
                    <div>
                        <div class="text-truncate">Data voucher bulan ini sudah diupdate</div>
                        <div class="small text-gray-500">Finance Team · 1d</div>
                    </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="me-2 d-none d-lg-inline text-gray-600 small">Admin User</span>
                <img class="img-profile rounded-circle"
                    src="https://ui-avatars.com/api/?name=Admin&background=858796&color=fff" 
                    style="width: 2.5rem; height: 2.5rem;">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
                    Settings
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>
                    Activity Log
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>

<!-- Logout Modal (Optional) -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="#">Logout</a>
            </div>
        </div>
    </div>
</div>

<style>
    .topbar-silver {
    background: linear-gradient(
        180deg,
        #f2f2f2 0%,
        #e6e6e6 50%,
        #d9d9d9 100%
    );
    border-bottom: 1px solid #cfcfcf;
}
.topbar-silver .nav-link,
.topbar-silver .text-gray-800,
.topbar-silver .text-gray-600 {
    color: #5a5c69 !important;
}

.topbar-silver .nav-link:hover {
    color: #ea6f0a !important; /* aksen orange */
}


    .icon-circle {
        height: 2.5rem;
        width: 2.5rem;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-primary {
        background-color: #4e73df !important;
    }

    .bg-success {
        background-color: #1cc88a !important;
    }

    .bg-warning {
        background-color: #f6c23e !important;
    }

    .dropdown-list-image {
        position: relative;
    }

    .dropdown-list-image .status-indicator {
        background-color: #eaecf4;
        height: 0.75rem;
        width: 0.75rem;
        border-radius: 100%;
        position: absolute;
        bottom: 0;
        right: 0;
        border: 0.125rem solid #fff;
    }

    .dropdown-list-image .status-indicator.bg-success {
        background-color: #1cc88a !important;
    }
</style>