@extends('auth.user_dashboard')

@section('content')
<div class="max-w-4xl mx-auto pb-16 md:pb-0" x-data="notificationCenter()">
    <!-- Header -->
    <div class="mb-5 md:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2.5 sm:gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 bg-blue-100 dark:bg-blue-900/40 rounded-xl flex items-center justify-center">
                    <i class="fas fa-bell text-blue-600 dark:text-blue-400 text-sm sm:text-base"></i>
                </div>
                Notifications
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-0.5 sm:mt-1 text-sm">
                @if($unreadCount > 0)
                    You have <span class="font-bold text-blue-600" x-text="unreadCount">{{ $unreadCount }}</span> unread notification{{ $unreadCount > 1 ? 's' : '' }}.
                @else
                    All caught up!
                @endif
            </p>
        </div>
        @if($unreadCount > 0)
            <button @click="markAllRead()" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 px-5 py-2.5 rounded-xl font-medium transition-colors text-sm active:bg-gray-100 dark:active:bg-gray-600">
                <i class="fas fa-check-double"></i> Mark All as Read
            </button>
        @endif
    </div>

    <!-- Notification List -->
    <div class="space-y-2.5 sm:space-y-3">
        @forelse($notifications as $notification)
            <div id="notification-{{ $notification->id }}" 
                 class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border transition-all duration-300 overflow-hidden
                        {{ is_null($notification->read_at) ? 'border-blue-200 dark:border-blue-800 ring-1 ring-blue-100 dark:ring-blue-900/50' : 'border-gray-200 dark:border-gray-700' }}">
                <div class="p-4 sm:p-5 flex items-start gap-3 sm:gap-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0 mt-0.5">
                        @php
                            $isPayment = str_contains(strtolower($notification->title ?? ''), 'payment');
                            $isAlert = str_contains(strtolower($notification->title ?? ''), 'alert') || str_contains(strtolower($notification->title ?? ''), 'overdue');
                        @endphp
                        @if($isPayment)
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                            </div>
                        @elseif($isAlert)
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                            </div>
                        @else
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center">
                                <i class="fas fa-bell text-blue-600 dark:text-blue-400"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm {{ is_null($notification->read_at) ? '' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $notification->title }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1 leading-relaxed">{{ $notification->body }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    <span class="text-gray-300 dark:text-gray-600 mx-1">&bull;</span>
                                    {{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y h:i A') }}
                                </p>
                            </div>

                            <!-- Unread indicator & action -->
                            @if(is_null($notification->read_at))
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></span>
                                    <button @click="markRead({{ $notification->id }})" 
                                            class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium hover:underline whitespace-nowrap">
                                        Mark read
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0 whitespace-nowrap">
                                    <i class="fas fa-check"></i> Read
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                    <i class="fas fa-bell-slash text-gray-400 dark:text-gray-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">No Notifications</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-md mx-auto">
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const el = document.getElementById(`notification-${id}`);
                        if (el) {
                            // Remove unread styling
                            el.classList.remove('border-blue-200', 'ring-1', 'ring-blue-100', 'dark:border-blue-800', 'dark:ring-blue-900/50');
                            el.classList.add('border-gray-200', 'dark:border-gray-700');

                            // Replace the "Mark read" button + dot with a "Read" label
                            const actionArea = el.querySelector('.flex-shrink-0.flex');
                            if (actionArea) {
                                actionArea.outerHTML = `<span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0 whitespace-nowrap"><i class="fas fa-check"></i> Read</span>`;
                            }
                        }
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        this.updateBadge();
                        this.updateSubtitle();
                    }
                })
                .catch(err => console.error('Error marking notification as read:', err));
            },

            markAllRead() {
                fetch('/parent/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update all unread notifications in the DOM
                        document.querySelectorAll('[id^="notification-"].border-blue-200').forEach(el => {
                            el.classList.remove('border-blue-200', 'ring-1', 'ring-blue-100', 'dark:border-blue-800', 'dark:ring-blue-900/50');
                            el.classList.add('border-gray-200', 'dark:border-gray-700');
                            const actionArea = el.querySelector('.flex-shrink-0.flex');
                            if (actionArea) {
                                actionArea.outerHTML = `<span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0 whitespace-nowrap"><i class="fas fa-check"></i> Read</span>`;
                            }
                        });
                        this.unreadCount = 0;
                        this.updateBadge();
                        this.updateSubtitle();

                        // Hide the "Mark All as Read" button
                        const markAllBtn = document.querySelector('[\\@click="markAllRead()"]');
                        if (markAllBtn) markAllBtn.style.display = 'none';
                    }
                })
                .catch(err => console.error('Error marking all notifications as read:', err));
            },

            updateSubtitle() {
                const subtitle = this.$el.querySelector('p.text-gray-600');
                if (subtitle) {
                    if (this.unreadCount > 0) {
                        subtitle.innerHTML = `You have <span class="font-bold text-blue-600">${this.unreadCount}</span> unread notification${this.unreadCount > 1 ? 's' : ''}.`;
                    } else {
                        subtitle.textContent = 'All caught up!';
                    }
                }
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
