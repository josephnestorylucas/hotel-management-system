{{-- Notification Bell — include in all module navbars --}}
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative text-gray-600 hover:text-blue-600 focus:outline-none">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span id="notif-badge"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full
                     w-4 h-4 flex items-center justify-center hidden"
              style="font-size: 10px; line-height: 1;">
            0
        </span>
    </button>

    <div x-show="open" @click.away="open = false" x-transition
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-50 max-h-96 overflow-y-auto">
        <div class="px-4 py-3 border-b bg-gray-50">
            <span class="text-sm font-semibold text-gray-700">Notifications</span>
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

<script>
    function fetchNotifCount() {
        fetch('{{ route("notifications.count") }}')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('notif-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            })
            .catch(() => {});
    }
    fetchNotifCount();
    setInterval(fetchNotifCount, 30000);
</script>
