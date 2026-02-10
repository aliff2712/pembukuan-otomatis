@extends('layouts-main.app')

@section('title', 'Chat dengan ' . $receiver->name)
@section('page-title', 'Pesan')

@section('content')
<div class="row">
    <!-- Sidebar - List Kontak -->
    <div class="col-lg-4 col-xl-3 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3" style="background-color: #4e73df; color: white;">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-users me-2"></i>Kontak Admin
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($users as $user)
                        <a href="{{ route('messages.show', $user->id) }}" 
                           class="list-group-item list-group-item-action {{ $user->id == $receiver->id ? 'active' : '' }}">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4e73df&color=fff" 
                                     class="rounded-circle me-3" 
                                     style="width: 40px; height: 40px;"
                                     alt="{{ $user->name }}"
                                     loading="lazy">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                                @if($user->unread_count > 0)
                                    <span class="badge bg-danger">{{ $user->unread_count }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="col-lg-8 col-xl-9">
        <div class="card shadow">
            <!-- Chat Header -->
            <div class="card-header py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($receiver->name) }}&background=ffffff&color=667eea" 
                         class="rounded-circle me-3" 
                         style="width: 45px; height: 45px; border: 2px solid white;"
                         alt="{{ $receiver->name }}"
                         loading="lazy">
                    <div>
                        <h6 class="m-0 font-weight-bold">{{ $receiver->name }}</h6>
                        <small style="opacity: 0.9;">{{ $receiver->email }}</small>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="card-body" style="height: 500px; overflow-y: auto;" id="chatMessages">
                @forelse($messages as $message)
                    <div class="mb-3 {{ $message->sender_id == auth()->id() ? 'text-end' : '' }}" 
                         data-message-id="{{ $message->id }}">
                        <div class="d-inline-block" style="max-width: 70%;">
                            <div class="p-3 rounded {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}"
                                 style="{{ $message->sender_id == auth()->id() ? 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;' : '' }}">
                                @if($message->sender_id != auth()->id())
                                    <strong class="d-block mb-1">{{ $message->sender->name }}</strong>
                                @endif
                                <p class="mb-0">{{ $message->message }}</p>
                            </div>
                            <small class="text-muted d-block mt-1">
                                {{ $message->formatted_time }}
                                @if($message->sender_id == auth()->id())
                                    @if($message->is_read)
                                        <i class="fas fa-check-double text-primary" title="Dibaca"></i>
                                    @else
                                        <i class="fas fa-check text-muted" title="Terkirim"></i>
                                    @endif
                                @endif
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5" id="emptyState">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>Belum ada percakapan. Mulai chat sekarang!</p>
                    </div>
                @endforelse
            </div>

            <!-- Chat Input -->
            <div class="card-footer">
                <form method="POST" action="{{ route('messages.store') }}" id="messageForm">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $receiver->id }}">
                    <div class="input-group">
                        <input type="text" 
                               name="message" 
                               id="messageInput"
                               class="form-control" 
                               placeholder="Ketik pesan..." 
                               required
                               autocomplete="off"
                               maxlength="1000">
                        <button class="btn btn-primary" type="submit" id="sendBtn">
                            <i class="fas fa-paper-plane me-1"></i> Kirim
                        </button>
                    </div>
                    <div class="form-text mt-1" id="charCount">0 / 1000 karakter</div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Configuration
    const CONFIG = {
        userId: {{ auth()->id() }},
        receiverId: {{ $receiver->id }},
        refreshInterval: 5000, // 5 seconds
        routes: {
            store: '{{ route("messages.store") }}',
            new: '{{ route("messages.new", $receiver->id) }}'
        }
    };

    let lastMessageTime = '{{ $messages->last()?->created_at ?? now() }}';
    let isScrolledToBottom = true;

    // Auto-scroll to bottom on page load
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom();
        setupCharCounter();
        setupScrollDetection();
    });

    // Setup character counter
    function setupCharCounter() {
        const input = document.getElementById('messageInput');
        const counter = document.getElementById('charCount');
        
        if (input && counter) {
            input.addEventListener('input', function() {
                counter.textContent = `${this.value.length} / 1000 karakter`;
            });
        }
    }

    // Setup scroll detection
    function setupScrollDetection() {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.addEventListener('scroll', function() {
                const threshold = 50;
                isScrolledToBottom = this.scrollHeight - this.scrollTop - this.clientHeight < threshold;
            });
        }
    }

    // Scroll to bottom function
    function scrollToBottom(force = false) {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages && (isScrolledToBottom || force)) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    // Auto-refresh messages
    const messageRefreshInterval = setInterval(function() {
        fetch(`${CONFIG.routes.new}?last_timestamp=${encodeURIComponent(lastMessageTime)}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    const chatMessages = document.getElementById('chatMessages');
                    const emptyState = document.getElementById('emptyState');
                    
                    // Remove empty state if exists
                    if (emptyState) {
                        emptyState.remove();
                    }
                    
                    // Add new messages
                    data.forEach(message => {
                        // Check if message already exists
                        if (!document.querySelector(`[data-message-id="${message.id}"]`)) {
                            const messageDiv = createMessageElement(message);
                            chatMessages.insertAdjacentHTML('beforeend', messageDiv);
                            lastMessageTime = message.created_at;
                        }
                    });
                    
                    // Scroll to bottom
                    scrollToBottom();
                }
            })
            .catch(error => console.error('Error fetching new messages:', error));
    }, CONFIG.refreshInterval);

    // Create message element
    function createMessageElement(message) {
        const isOwn = message.sender_id === CONFIG.userId;
        const alignment = isOwn ? 'text-end' : '';
        const bgClass = isOwn ? 'bg-primary text-white' : 'bg-light';
        const style = isOwn ? 'style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;"' : '';
        
        const senderName = isOwn ? '' : `<strong class="d-block mb-1">${escapeHtml(message.sender.name)}</strong>`;
        const time = new Date(message.created_at).toLocaleString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const readStatus = isOwn ? (message.is_read 
            ? '<i class="fas fa-check-double text-primary" title="Dibaca"></i>'
            : '<i class="fas fa-check text-muted" title="Terkirim"></i>') : '';
        
        return `
            <div class="mb-3 ${alignment}" data-message-id="${message.id}">
                <div class="d-inline-block" style="max-width: 70%;">
                    <div class="p-3 rounded ${bgClass}" ${style}>
                        ${senderName}
                        <p class="mb-0">${escapeHtml(message.message)}</p>
                    </div>
                    <small class="text-muted d-block mt-1">
                        ${time}
                        ${readStatus}
                    </small>
                </div>
            </div>
        `;
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // Form submission with optimistic UI
    document.getElementById('messageForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const input = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const message = input.value.trim();
        
        if (!message) return;
        
        // Disable form
        input.disabled = true;
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengirim...';
        
        // Send via fetch
        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                receiver_id: CONFIG.receiverId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear input
                input.value = '';
                document.getElementById('charCount').textContent = '0 / 1000 karakter';
                
                // Add message to chat
                const chatMessages = document.getElementById('chatMessages');
                const emptyState = document.getElementById('emptyState');
                if (emptyState) emptyState.remove();
                
                const messageDiv = createMessageElement(data.message);
                chatMessages.insertAdjacentHTML('beforeend', messageDiv);
                lastMessageTime = data.message.created_at;
                
                // Scroll to bottom
                scrollToBottom(true);
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Gagal mengirim pesan. Silakan coba lagi.');
        })
        .finally(() => {
            // Re-enable form
            input.disabled = false;
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Kirim';
            input.focus();
        });
    });

    // Enter to send
    document.getElementById('messageInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('messageForm')?.dispatchEvent(new Event('submit'));
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        clearInterval(messageRefreshInterval);
    });
</script>
@endpush

@endsection