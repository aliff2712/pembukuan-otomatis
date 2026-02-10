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

        <!-- Nav Item - Messages -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                @php
                    $unreadCount = auth()->user()->unread_count ?? 0;
                @endphp
                @if($unreadCount > 0)
                    <span class="badge badge-danger badge-counter" id="messageCounter"
                        style="position: absolute; top: 0.5rem; right: 0.25rem; font-size: 0.7rem; padding: 0.25rem 0.4rem; background-color: #e74a3b;">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                aria-labelledby="messagesDropdown" style="min-width: 20rem;">
                <h6 class="dropdown-header" style="background-color: #4e73df; color: white; font-weight: 700;">
                    Message Center
                </h6>
                
                @php
                    $recentMessages = auth()->user()
                        ->receivedMessages()
                        ->with('sender')
                        ->latest()
                        ->take(3)
                        ->get();
                @endphp

                @forelse($recentMessages as $msg)
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('messages.show', $msg->sender_id) }}">
                        <div class="dropdown-list-image me-3">
                            <img class="rounded-circle" 
                                src="https://ui-avatars.com/api/?name={{ urlencode($msg->sender->name) }}&background=4e73df&color=fff"
                                alt="{{ $msg->sender->name }}" 
                                style="width: 3rem; height: 3rem;">
                            @if(!$msg->is_read)
                                <div class="status-indicator bg-success"></div>
                            @endif
                        </div>
                        <div class="font-weight-bold">
                            <div class="text-truncate" style="max-width: 200px;">
                                {{ Str::limit($msg->message, 50) }}
                            </div>
                            <div class="small text-gray-500">
                                {{ $msg->sender->name }} · {{ $msg->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0 small">Tidak ada pesan</p>
                    </div>
                @endforelse

                <a class="dropdown-item text-center small text-gray-500" href="{{ route('messages.index') }}">
                    <i class="fas fa-arrow-right me-1"></i>
                    Lihat Semua Pesan
                </a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="me-2 d-none d-lg-inline text-gray-600 small">
                    {{ auth()->user()->name }}
                </span>
                <img class="img-profile rounded-circle"
                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=858796&color=fff" 
                    style="width: 2.5rem; height: 2.5rem;"
                    alt="{{ auth()->user()->name }}">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <div class="dropdown-header">
                    <strong>{{ auth()->user()->name }}</strong>
                    <div class="small text-muted">{{ auth()->user()->email }}</div>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="{{ route('messages.index') }}">
                    <i class="fas fa-envelope fa-sm fa-fw me-2 text-gray-400"></i>
                    Messages
                    @if($unreadCount > 0)
                        <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                    @endif
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

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin keluar dari sistem?
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </button>
                </form>
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
        color: #ea6f0a !important;
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

    .badge-counter {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }

    .dropdown-menu {
        max-height: 400px;
        overflow-y: auto;
    }

    .dropdown-item:hover {
        background-color: #f8f9fc;
    }
</style>

@push('scripts')
<script>
    // Auto-refresh unread message count every 30 seconds
    setInterval(function() {
        fetch('{{ route("messages.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                const counter = document.getElementById('messageCounter');
                const userMenuBadge = document.querySelector('#userDropdown + .dropdown-menu .badge');
                
                if (data.count > 0) {
                    const displayCount = data.count > 99 ? '99+' : data.count;
                    
                    // Update navbar counter
                    if (counter) {
                        counter.textContent = displayCount;
                    } else {
                        // Create counter if doesn't exist
                        const badge = document.createElement('span');
                        badge.id = 'messageCounter';
                        badge.className = 'badge badge-danger badge-counter';
                        badge.style.cssText = 'position: absolute; top: 0.5rem; right: 0.25rem; font-size: 0.7rem; padding: 0.25rem 0.4rem; background-color: #e74a3b;';
                        badge.textContent = displayCount;
                        document.querySelector('#messagesDropdown').appendChild(badge);
                    }
                    
                    // Update user menu badge
                    if (userMenuBadge) {
                        userMenuBadge.textContent = data.count;
                    }
                } else {
                    // Remove counters if no unread messages
                    if (counter) {
                        counter.remove();
                    }
                    if (userMenuBadge) {
                        userMenuBadge.remove();
                    }
                }
            })
            .catch(error => console.error('Error fetching unread count:', error));
    }, 30000); // 30 seconds

    // Show notification sound/visual feedback when new message arrives
    let lastUnreadCount = {{ $unreadCount }};
    
    setInterval(function() {
        fetch('{{ route("messages.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                if (data.count > lastUnreadCount) {
                    // New message arrived
                    showNotification('Pesan Baru', 'Anda memiliki pesan baru!');
                }
                lastUnreadCount = data.count;
            });
    }, 10000); // Check every 10 seconds for new messages

    function showNotification(title, message) {
        // Check if browser supports notifications
        if ("Notification" in window && Notification.permission === "granted") {
            new Notification(title, {
                body: message,
                icon: '/assets/img/dhs-logo.png'
            });
        }
    }

    // Request notification permission on page load
    document.addEventListener('DOMContentLoaded', function() {
        if ("Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }
    });
</script>
@endpush