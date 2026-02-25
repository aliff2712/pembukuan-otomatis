<nav class="navbar navbar-expand-lg modern-topbar px-4">

    <!-- Sidebar Toggle -->
    <button class="btn btn-toggle d-md-none me-3" id="sidebarToggleTop">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Page Title -->
    <div class="me-auto">
        <h5 class="mb-0 page-title">
            Dashboard
        </h5>
    </div>

    <!-- Right Menu -->
    <ul class="navbar-nav align-items-center gap-2">

        <!-- Notifications -->
        <li class="nav-item dropdown">
            <a class="nav-link icon-button position-relative"
               href="#"
               data-bs-toggle="dropdown">

                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </a>

            <ul class="dropdown-menu dropdown-menu-end modern-dropdown">
                <li class="dropdown-header-modern">
                    Notifications
                </li>

                <li>
                    <a class="dropdown-item modern-item" href="#">
                        <small>New order received</small>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item modern-item" href="#">
                        <small>Server backup completed</small>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Messages -->
        <li class="nav-item dropdown">
    <a class="nav-link icon-button position-relative"
       href="#"
       id="messagesDropdown"
       role="button"
       data-bs-toggle="dropdown"
       aria-expanded="false">

        <i class="fas fa-envelope"></i>

        @php $unreadCount = auth()->user()->unread_count ?? 0; @endphp
        @if($unreadCount > 0)
            <span class="notification-badge">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-end modern-dropdown"
         aria-labelledby="messagesDropdown"
         style="min-width: 20rem;"> <h6 class="dropdown-header bg-primary text-white font-weight-bold"> Message Center </h6> @php $recentMessages = auth()->user() ->receivedMessages() ->with('sender') ->latest() ->take(3) ->get(); @endphp @forelse($recentMessages as $msg) <a class="dropdown-item d-flex align-items-center" href="{{ route('messages.show', $msg->sender_id) }}"> <div class="dropdown-list-image me-3"> <img class="rounded-circle" src="https://ui-avatars.com/api/?name={{ urlencode($msg->sender->name) }}&background=4e73df&color=fff" alt="{{ $msg->sender->name }}" style="width: 3rem; height: 3rem;"> @if(!$msg->is_read) <div class="status-indicator bg-success"></div> @endif </div> <div class="font-weight-bold"> <div class="text-truncate" style="max-width: 200px;"> {{ Str::limit($msg->message, 50) }} </div> <div class="small text-gray-500"> {{ $msg->sender->name }} · {{ $msg->created_at->diffForHumans() }} </div> </div> </a> @empty <div class="text-center py-4 text-muted"> <i class="fas fa-inbox fa-2x mb-2"></i> <p class="mb-0 small">Tidak ada pesan</p> </div> @endforelse <a class="dropdown-item text-center small text-gray-500" href="{{ route('messages.index') }}"> <i class="fas fa-arrow-right me-1"></i> Lihat Semua Pesan </a> </div> </li>

        <!-- Settings -->
        <li class="nav-item">
            <a class="nav-link icon-button"
               href="{{ route('finance.setting.edit') }}">
                <i class="fas fa-cog"></i>
            </a>
        </li>

        <!-- Divider -->
        <div class="vr mx-2 d-none d-lg-block"></div>

        <!-- Profile -->
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center"
               href="#"
               data-bs-toggle="dropdown">

                <span class="me-2 d-none d-lg-inline user-name">
                    {{ auth()->user()->name }}
                </span>

                <div class="modern-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </a>

            <ul class="dropdown-menu dropdown-menu-end modern-dropdown">
                <li class="dropdown-header-modern">
                    <strong>{{ auth()->user()->name }}</strong>
                    <div class="small text-muted">
                        {{ auth()->user()->email }}
                    </div>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item modern-item"
                       href="{{ route('profile.edit') }}">
                        <i class="fas fa-user me-2"></i>
                        Profile
                    </a>
                </li>

                <li>
                    <a class="dropdown-item modern-item text-danger"
                       href="#"
                       data-bs-toggle="modal"
                       data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</nav>
<style>
    /* =============================
   PREMIUM NAVBAR v2
============================= */

/* =============================
   NAVY TOPBAR (MATCH SIDEBAR)
============================= */

.modern-topbar {
    background: linear-gradient(90deg, #0f172a, #111827);
    border-bottom: 1px solid rgba(255,255,255,0.05);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    padding: 0.75rem 1.5rem;
}

/* Title */
.page-title {
    font-weight: 600;
    font-size: 1rem;
    color: #ffffff;
    letter-spacing: 0.3px;
}

/* Toggle button */
.btn-toggle {
    background: rgba(255,255,255,0.08);
    border-radius: 10px;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #cbd5e1;
    transition: 0.2s ease;
}

.btn-toggle:hover {
    background: rgba(59,130,246,0.25);
    color: #ffffff;
}

/* Icon circle buttons */
.icon-button {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #cbd5e1;
    transition: 0.2s ease;
}

.icon-button:hover {
    background: rgba(59,130,246,0.25);
    color: #ffffff;
}

/* Username */
.user-name {
    font-size: 0.85rem;
    font-weight: 500;
    color: #e2e8f0;
}

/* Avatar */
.modern-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    transition: 0.3s ease;
}

.modern-avatar:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.4);
}

/* Notification badge */
.notification-badge {
    position: absolute;
    top: 2px;
    right: 2px;
    background: #3b82f6;
    color: #fff;
    font-size: 0.6rem;
    padding: 3px 6px;
    border-radius: 20px;
    font-weight: 600;
}

/* Dropdown tetap light biar kontras */
.modern-dropdown {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 20px 45px rgba(15,23,42,0.15);
    padding: 0.5rem;
    min-width: 240px;
}
</style>