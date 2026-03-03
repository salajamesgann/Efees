@extends('auth.user_dashboard')

@section('content')
<div class="max-w-4xl mx-auto" x-data="notificationCenter()">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-bell text-blue-600"></i>
                </div>
                Notifications
            </h1>
            <p class="text-gray-600 mt-1">
                @if($unreadCount > 0)
                    You have <span class="font-bold text-blue-600" x-text="unreadCount">{{ $unreadCount }}</span> unread notification{{ $unreadCount > 1 ? 's' : '' }}.
                @else
                    All caught up!
                @endif
            </p>
        </div>
        @if($unreadCount > 0)
            <button @click="markAllRead()" 
                    class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-xl font-medium transition-colors text-sm">
                <i class="fas fa-check-double"></i> Mark All as Read
            </button>
        @endif
    </div>

    <!-- Notification List -->
    <div class="space-y-3">
        @forelse($notifications as $notification)
            <div id="notification-{{ $notification->id }}" 
                 class="bg-white rounded-xl shadow-sm border transition-all duration-300 overflow-hidden
                        {{ is_null($notification->read_at) ? 'border-blue-200 ring-1 ring-blue-100' : 'border-gray-200' }}">
                <div class="p-5 flex items-start gap-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0 mt-0.5">
                        @php
                            $isPayment = str_contains(strtolower($notification->title ?? ''), 'payment');
                            $isAlert = str_contains(strtolower($notification->title ?? ''), 'alert') || str_contains(strtolower($notification->title ?? ''), 'overdue');
                        @endphp
                        @if($isPayment)
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                        @elseif($isAlert)
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-circle text-red-600"></i>
                            </div>
                        @else
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-bell text-blue-600"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm {{ is_null($notification->read_at) ? '' : 'text-gray-700' }}">
                                    {{ $notification->title }}
                                </h3>
                                <p class="text-gray-600 text-sm mt-1 leading-relaxed">{{ $notification->body }}</p>
                                <p class="text-xs text-gray-400 mt-2">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    <span class="text-gray-300 mx-1">&bull;</span>
                                    {{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y h:i A') }}
                                </p>
                            </div>

                            <!-- Unread indicator & action -->
                            @if(is_null($notification->read_at))
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></span>
                                    <button @click="markRead({{ $notification->id }})" 
                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline whitespace-nowrap">
                                        Mark read
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 flex-shrink-0 whitespace-nowrap">
                                    <i class="fas fa-check"></i> Read
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-bell-slash text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No Notifications</h3>
                <p class="text-gray-500 mt-2 max-w-md mx-auto">
                    You don't have any notifications yet. You'll be notified here when payments are processed or important updates are posted.
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

<script>
    function notificationCenter() {
        return {
            unreadCount: {{ $unreadCount }},

            markRead(id) {
                fetch(`/parent/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const el = document.getElementById(`notification-${id}`);
                        if (el) {
                            el.classList.remove('border-blue-200', 'ring-1', 'ring-blue-100');
                            el.classList.add('border-gray-200');
                            // Remove the mark read button & dot, replace with "Read"
                            const actionDiv = el.querySelector('.flex-shrink-0:last-child');
                        }
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        this.updateBadge();
                        // Reload to update the UI cleanly
                        location.reload();
                    }
                })
                .catch(err => console.error('Error marking notification as read:', err));
            },

            markAllRead() {
                fetch('/parent/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.unreadCount = 0;
                        this.updateBadge();
                        location.reload();
                    }
                })
                .catch(err => console.error('Error marking all notifications as read:', err));
            },

            updateBadge() {
                const badge = document.getElementById('notification-badge');
                if (badge) {
                    if (this.unreadCount > 0) {
                        badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            }
        };
    }
</script>
@endsection
