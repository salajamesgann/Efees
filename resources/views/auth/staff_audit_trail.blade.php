<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Activity Log - Efees Staff</title>
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
    <main class="flex-1 md:h-screen overflow-y-auto main-scrollbar p-6 md:p-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-history text-indigo-600"></i>
                    </div>
                    My Activity Log
                </h1>
                <p class="text-gray-600 mt-1">Review your recorded actions and changes in the system.</p>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <form method="GET" class="flex flex-col sm:flex-row gap-3 flex-wrap">
                    <div class="relative flex-1 min-w-[180px]">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search actions, details, student ID..."
                               class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <select name="action" class="border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Actions</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="from" value="{{ request('from') }}" class="border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500" placeholder="From">
                    <input type="date" name="to" value="{{ request('to') }}" class="border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500" placeholder="To">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'action', 'from', 'to']))
                        <a href="{{ route('staff.audit_trail') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors text-center">
                            <i class="fas fa-times mr-1"></i> Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Activity Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                @if($logs->isEmpty())
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-check text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-1">No Activity Found</h3>
                        <p class="text-sm text-gray-400">No recorded actions match your current filters.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($logs as $log)
                            <div class="p-5 hover:bg-gray-50 transition-colors" x-data="{ expanded: false }">
                                <div class="flex items-start gap-4">
                                    <!-- Action icon -->
                                    <div class="flex-shrink-0 mt-0.5">
                                        @php
                                            $actionLower = strtolower($log->action);
                                            $isCreate = str_contains($actionLower, 'create') || str_contains($actionLower, 'add') || str_contains($actionLower, 'store');
                                            $isUpdate = str_contains($actionLower, 'update') || str_contains($actionLower, 'edit') || str_contains($actionLower, 'modify');
                                            $isDelete = str_contains($actionLower, 'delete') || str_contains($actionLower, 'remove') || str_contains($actionLower, 'void');
                                            $isPayment = str_contains($actionLower, 'payment');
                                            $isSms = str_contains($actionLower, 'sms') || str_contains($actionLower, 'remind');
                                            $isLogin = str_contains($actionLower, 'login') || str_contains($actionLower, 'logout');
                                        @endphp
                                        @if($isPayment)
                                            <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-credit-card text-green-600 text-sm"></i>
                                            </div>
                                        @elseif($isCreate)
                                            <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-plus text-blue-600 text-sm"></i>
                                            </div>
                                        @elseif($isUpdate)
                                            <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-pen text-amber-600 text-sm"></i>
                                            </div>
                                        @elseif($isDelete)
                                            <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-trash text-red-600 text-sm"></i>
                                            </div>
                                        @elseif($isSms)
                                            <div class="w-9 h-9 bg-purple-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-sms text-purple-600 text-sm"></i>
                                            </div>
                                        @elseif($isLogin)
                                            <div class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-sign-in-alt text-gray-600 text-sm"></i>
                                            </div>
                                        @else
                                            <div class="w-9 h-9 bg-slate-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-cog text-slate-500 text-sm"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold
                                                    @if($isCreate) bg-blue-50 text-blue-700 border border-blue-200
                                                    @elseif($isUpdate) bg-amber-50 text-amber-700 border border-amber-200
                                                    @elseif($isDelete) bg-red-50 text-red-700 border border-red-200
                                                    @elseif($isPayment) bg-green-50 text-green-700 border border-green-200
                                                    @else bg-gray-50 text-gray-700 border border-gray-200
                                                    @endif">
                                                    {{ $log->action }}
                                                </span>
                                                @if($log->model_type)
                                                    <span class="text-xs text-gray-400 ml-2">
                                                        {{ class_basename($log->model_type) }}
                                                        @if($log->model_id)
                                                            <span class="font-mono">#{{ $log->model_id }}</span>
                                                        @endif
                                                    </span>
                                                @endif

                                                @if($log->details)
                                                    <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $log->details }}</p>
                                                @endif

                                                <p class="text-xs text-gray-400 mt-1.5">
                                                    <i class="far fa-clock mr-1"></i>
                                                    {{ $log->created_at->diffForHumans() }}
                                                    <span class="text-gray-300 mx-1">&bull;</span>
                                                    {{ $log->created_at->format('M d, Y h:i A') }}
                                                    @if($log->ip_address)
                                                        <span class="text-gray-300 mx-1">&bull;</span>
                                                        <i class="fas fa-globe mr-0.5"></i> {{ $log->ip_address }}
                                                    @endif
                                                </p>
                                            </div>

                                            <!-- Expand button if old/new values exist -->
                                            @if($log->old_values || $log->new_values)
                                                <button @click="expanded = !expanded" class="text-xs text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap flex-shrink-0">
                                                    <span x-show="!expanded"><i class="fas fa-chevron-down mr-1"></i>Details</span>
                                                    <span x-show="expanded" x-cloak><i class="fas fa-chevron-up mr-1"></i>Hide</span>
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Expandable change details -->
                                        @if($log->old_values || $log->new_values)
                                            <div x-show="expanded" x-collapse x-cloak class="mt-3 bg-gray-50 rounded-lg border border-gray-200 p-4 text-xs">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    @if($log->old_values)
                                                        <div>
                                                            <h4 class="font-bold text-gray-500 uppercase tracking-wider mb-2">Before</h4>
                                                            <div class="space-y-1">
                                                                @foreach((array) $log->old_values as $key => $val)
                                                                    <div class="flex gap-2">
                                                                        <span class="font-medium text-gray-600 min-w-[80px]">{{ $key }}:</span>
                                                                        <span class="text-red-600 bg-red-50 px-1.5 py-0.5 rounded">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($log->new_values)
                                                        <div>
                                                            <h4 class="font-bold text-gray-500 uppercase tracking-wider mb-2">After</h4>
                                                            <div class="space-y-1">
                                                                @foreach((array) $log->new_values as $key => $val)
                                                                    <div class="flex gap-2">
                                                                        <span class="font-medium text-gray-600 min-w-[80px]">{{ $key }}:</span>
                                                                        <span class="text-green-600 bg-green-50 px-1.5 py-0.5 rounded">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
