<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Notifications - Efees Staff</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-scrollbar::-webkit-scrollbar { width: 5px; }
        .sidebar-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        .main-scrollbar::-webkit-scrollbar { width: 8px; }
        .main-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .main-scrollbar::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 10px; }
        .main-scrollbar::-webkit-scrollbar-thumb:hover { background: #2563eb; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    <style>[x-cloak]{display:none!important}</style>

    <!-- Mobile top bar -->
    <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-user-shield text-lg"></i>
            </div>
            <span class="font-bold text-lg text-blue-900">Efees Staff</span>
        </div>
        <button @click="sidebarOpen = true" class="text-slate-600 hover:text-slate-800">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>

    <!-- Sidebar overlay -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 md:hidden" x-cloak></div>

    <!-- Sidebar -->
    @include('layouts.staff_sidebar')

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto main-scrollbar p-4 md:p-5" x-data="notificationCenter()">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bell text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Notifications</h1>
                        <p class="text-xs text-gray-500">
                            @if($unreadCount > 0)
                                <span class="font-semibold text-blue-600" x-text="unreadCount">{{ $unreadCount }}</span> unread of {{ $totalCount }} total
                            @else
                                All caught up — {{ $totalCount }} total
                            @endif
                        </p>
                    </div>
                </div>
                @if($unreadCount > 0)
                    <button @click="markAllRead()"
                            class="inline-flex items-center gap-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-3 py-1.5 rounded-lg font-medium transition-colors text-xs">
                        <i class="fas fa-check-double text-[10px]"></i> Mark All Read
                    </button>
                @endif
            </div>

            <!-- Filters Bar -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-3">
                <form method="GET" action="{{ route('staff.notifications') }}" class="flex flex-col md:flex-row gap-2">
                    <!-- Search -->
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search notifications..."
                               class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <!-- Status Filter -->
                    <select name="status" class="text-xs border border-gray-300 rounded-md px-2 py-1.5 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                    </select>
                    <!-- Type Filter -->
                    <select name="type" class="text-xs border border-gray-300 rounded-md px-2 py-1.5 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Payment</option>
                        <option value="alert" {{ request('type') === 'alert' ? 'selected' : '' }}>Alert</option>
                        <option value="fee" {{ request('type') === 'fee' ? 'selected' : '' }}>Fee</option>
                        <option value="link" {{ request('type') === 'link' ? 'selected' : '' }}>Link Request</option>
                    </select>
                    <div class="flex gap-1.5">
                        <button type="submit" class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                            <i class="fas fa-filter text-[10px]"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'type']))
                            <a href="{{ route('staff.notifications') }}" class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
                                <i class="fas fa-times text-[10px]"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Notification List -->
            <div class="space-y-1.5">
                @forelse($notifications as $notification)
                    <div id="notification-{{ $notification->id }}"
                         class="bg-white rounded-lg shadow-sm border transition-all duration-200 overflow-hidden
                                {{ is_null($notification->read_at) ? 'border-blue-200 ring-1 ring-blue-50' : 'border-gray-100' }}">
                        <div class="px-3 py-2.5 flex items-start gap-2.5">
                            <!-- Icon -->
                            <div class="flex-shrink-0 mt-0.5">
                                @php
                                    $title = strtolower($notification->title ?? '');
                                    $isPayment = str_contains($title, 'payment');
                                    $isAlert = str_contains($title, 'alert') || str_contains($title, 'overdue');
                                    $isLink = str_contains($title, 'link');
                                    $isFee = str_contains($title, 'fee');
                                @endphp
                                @if($isPayment)
                                    <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check-circle text-green-600 text-xs"></i>
                                    </div>
                                @elseif($isAlert)
                                    <div class="w-7 h-7 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-exclamation-circle text-red-600 text-xs"></i>
                                    </div>
                                @elseif($isLink)
                                    <div class="w-7 h-7 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-link text-purple-600 text-xs"></i>
                                    </div>
                                @elseif($isFee)
                                    <div class="w-7 h-7 bg-amber-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-file-invoice-dollar text-amber-600 text-xs"></i>
                                    </div>
                                @else
                                    <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-bell text-blue-600 text-xs"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-xs {{ is_null($notification->read_at) ? 'text-gray-900' : 'text-gray-600' }} truncate">
                                            {{ $notification->title }}
                                        </h3>
                                        <p class="text-gray-500 text-[11px] mt-0.5 line-clamp-1">{{ $notification->body }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">
                                            <i class="fas fa-clock mr-0.5"></i>
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                            <span class="text-gray-300 mx-0.5">&bull;</span>
                                            {{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y h:i A') }}
                                        </p>
                                    </div>

                                    @if(is_null($notification->read_at))
                                        <div class="flex items-center gap-1.5 flex-shrink-0">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                            <button @click="markRead({{ $notification->id }})"
                                                    class="text-[10px] text-blue-600 hover:text-blue-800 font-medium hover:underline whitespace-nowrap">
                                                Mark read
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-gray-400 flex-shrink-0 whitespace-nowrap">
                                            <i class="fas fa-check"></i> Read
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 mb-2">
                            <i class="fas fa-bell-slash text-gray-400 text-sm"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">No Notifications</h3>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                            @if(request()->hasAny(['search', 'status', 'type']))
                                No notifications match your filters. <a href="{{ route('staff.notifications') }}" class="text-blue-600 hover:underline">Clear filters</a>
                            @else
                                You'll be notified here when payments are processed, system alerts occur, or admin announcements are posted.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </main>

    <script>
        function notificationCenter() {
            return {
                unreadCount: {{ $unreadCount }},

                markRead(id) {
                    fetch(`/staff/notifications/${id}/read`, {
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
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                            location.reload();
                        }
                    })
                    .catch(err => console.error('Error:', err));
                },

                markAllRead() {
                    fetch('/staff/notifications/read-all', {
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
                            location.reload();
                        }
                    })
                    .catch(err => console.error('Error:', err));
                },
            };
        }
    </script>
</body>
</html>
