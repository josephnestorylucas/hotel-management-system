{{-- Notification Bell — include in all module navbars --}}
{{-- Real-time updates via WebSocket (Laravel Reverb) with fallback to optimized polling --}}
<div class="relative" x-data="notificationBell()" x-init="init()">
    <button @click.stop="open = !open" type="button" class="relative text-gray-600 hover:text-blue-600 focus:outline-none">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full
                     w-4 h-4 flex items-center justify-center"
              style="font-size: 10px; line-height: 1;">
        </span>
    </button>

    <div x-show="open" 
         x-transition
         @click.outside="open = false"
         @keydown.escape.window="open = false"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-50 max-h-96 overflow-y-auto">
        <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-700">Notifications</span>
            <span x-show="!wsConnected" class="text-xs text-yellow-600" title="Using polling fallback">
                <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </span>
        </div>
        @if(auth()->user()->latestNotifications && auth()->user()->latestNotifications->count() > 0)
            @foreach(auth()->user()->latestNotifications as $notif)
            <a href="{{ route('notifications.read', $notif) }}"
               class="block px-4 py-3 border-b hover:bg-gray-50 transition {{ $notif->is_read ? 'opacity-60' : '' }}">
                <div class="flex items-start gap-2">
                    @if(!$notif->is_read)
                    <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"></span>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="text-sm text-gray-800 {{ $notif->is_read ? '' : 'font-medium' }}">{{ $notif->title }}</div>
                        <div class="text-xs text-gray-400 mt-0.5 truncate">{{ $notif->body }}</div>
                        <div class="text-xs text-gray-300 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </a>
            @endforeach
        @else
            <div class="px-4 py-6 text-center text-sm text-gray-400">No notifications</div>
        @endif
        <a href="{{ route('notifications.index') }}"
           class="block px-4 py-3 text-center text-xs text-blue-600 hover:bg-blue-50 border-t font-medium">
            View all notifications
        </a>
    </div>
</div>

{{-- Load Pusher and Laravel Echo from CDN (only once per page) --}}
@once
@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
    // Initialize Echo globally for WebSocket connections
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: '{{ config("broadcasting.connections.reverb.key") }}',
        wsHost: '{{ config("broadcasting.connections.reverb.options.host") }}',
        wsPort: {{ config("broadcasting.connections.reverb.options.port", 8080) }},
        wssPort: {{ config("broadcasting.connections.reverb.options.port", 443) }},
        forceTLS: {{ config("broadcasting.connections.reverb.options.scheme", "http") === "https" ? 'true' : 'false' }},
        enabledTransports: ['ws', 'wss'],
    });
</script>
@endpush
@endonce

<script>
function notificationBell() {
    return {
        open: false,
        unreadCount: {{ auth()->user()->unreadNotificationCount() ?? 0 }},
        wsConnected: false,
        pollingInterval: null,
        visibilityHandler: null,

        init() {
            // Try to connect to WebSocket first
            this.setupWebSocket();
            
            // Setup visibility change handler for optimized polling
            this.setupVisibilityHandler();
            
            // Initial fetch
            this.fetchCount();
        },

        setupWebSocket() {
            // Check if Echo is available
            if (typeof window.Echo !== 'undefined') {
                try {
                    const userId = {{ auth()->id() }};
                    const self = this;
                    
                    window.Echo.private('notifications.' + userId)
                        .listen('.notification.created', function(e) {
                            self.unreadCount = e.unread_count;
                            
                            // Show browser notification if permission granted
                            if (e.notification && Notification.permission === 'granted') {
                                new Notification(e.notification.title, {
                                    body: e.notification.body,
                                    icon: '/favicon.ico'
                                });
                            }
                        });
                    
                    self.wsConnected = true;
                    console.log('Notification WebSocket connected');
                    
                    // Stop polling if WebSocket is connected
                    self.stopPolling();
                } catch (error) {
                    console.warn('WebSocket connection failed, falling back to polling:', error);
                    this.startPolling();
                }
            } else {
                // Echo not available, use polling
                console.log('Laravel Echo not available, using polling fallback');
                this.startPolling();
            }
        },

        setupVisibilityHandler() {
            const self = this;
            this.visibilityHandler = function() {
                if (document.hidden) {
                    // Tab is hidden, stop polling to save resources
                    self.stopPolling();
                } else {
                    // Tab is visible again
                    self.fetchCount(); // Fetch immediately
                    if (!self.wsConnected) {
                        self.startPolling(); // Resume polling if not using WebSocket
                    }
                }
            };
            
            document.addEventListener('visibilitychange', this.visibilityHandler);
        },

        startPolling() {
            const self = this;
            // Only start if not already polling and tab is visible
            if (!this.pollingInterval && !document.hidden) {
                // Poll every 2 minutes (120000ms) instead of 30 seconds
                this.pollingInterval = setInterval(function() {
                    if (!document.hidden) {
                        self.fetchCount();
                    }
                }, 120000);
            }
        },

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },

        fetchCount() {
            const self = this;
            fetch('{{ route("notifications.count") }}')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    self.unreadCount = data.count;
                })
                .catch(function() {});
        },

        destroy() {
            this.stopPolling();
            if (this.visibilityHandler) {
                document.removeEventListener('visibilitychange', this.visibilityHandler);
            }
        }
    };
}
</script>
