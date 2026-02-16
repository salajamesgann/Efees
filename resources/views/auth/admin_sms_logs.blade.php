<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SMS Control - Efees Admin</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="flex flex-col md:flex-row h-screen overflow-hidden bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 z-40 w-72 h-screen bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 md:translate-x-0 shadow-2xl md:shadow-none" id="sidebar">
        <!-- Header -->
        <div class="flex items-center justify-between gap-3 px-8 py-6 border-b border-slate-100 bg-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <div>
                    <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Efees Admin</h1>
                    <p class="text-xs text-slate-500 font-medium">Administration</p>
                </div>
            </div>
            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="md:hidden p-2 text-slate-400 hover:text-red-500 transition-colors rounded-lg hover:bg-slate-50">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow pb-6 overflow-y-auto custom-scrollbar">
            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
            
            <!-- Dashboard -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin_dashboard') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin_dashboard') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('admin_dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Dashboard</span>
            </a>


            <!-- Student Management (Users) -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.students.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.students.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-users text-lg {{ request()->routeIs('admin.students.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
            <span class="text-sm font-medium">Student Management</span>
            </a>
            <!-- Parent Management removed -->

            <!-- User Management -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.staff.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.staff.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-chalkboard-teacher text-lg {{ request()->routeIs('admin.staff.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">User Management</span>
            </a>

            <!-- Fee Management -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fees.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-file-invoice-dollar text-lg {{ request()->routeIs('admin.fees.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Fee Management</span>
            </a>

            <!-- Payment Approvals -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.payment_approvals.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.payment_approvals.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-check-double text-lg {{ request()->routeIs('admin.payment_approvals.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Payment Approvals</span>
            </a>

            <!-- Reports & Analytics -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.reports.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-chart-bar text-lg {{ request()->routeIs('admin.reports.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Reports & Analytics</span>
            </a>

            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-6">System</p>

            <!-- Audit Logs -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.audit-logs.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-shield-alt text-lg {{ request()->routeIs('admin.audit-logs.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Audit Logs</span>
            </a>

            <!-- SMS Control -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ (request()->routeIs('admin.sms.logs') || request()->routeIs('admin.sms.templates*')) ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.sms.logs') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-sms text-lg {{ (request()->routeIs('admin.sms.logs') || request()->routeIs('admin.sms.templates*')) ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">SMS Control</span>
            </a>

            <!-- Requests -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.requests.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.requests.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-key text-lg {{ request()->routeIs('admin.requests.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Requests</span>
            </a>

            <!-- Settings -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.settings.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-cog text-lg {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Settings</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 px-4 pb-6">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 hover:shadow-sm transition-all duration-200 group border border-red-100">
                    <div class="w-8 flex justify-center">
                        <i class="fas fa-sign-out-alt text-lg group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="text-sm font-bold">Logout</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto h-screen custom-scrollbar" x-data="{ showDetailModal: false, selectedLog: null, activeTab: 'logs' }">
        <!-- Header & Stats -->
        <div class="p-6 md:p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">SMS Control</h1>
                    <p class="text-gray-500 text-sm mt-1">Logs and Templates management.</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500">Last updated: {{ now()->format('H:i A') }}</span>
                    <button onclick="window.location.reload()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-full hover:bg-gray-100">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex items-center gap-2">
                <button @click="activeTab = 'logs'"
                        :class="activeTab === 'logs' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-gray-200">
                    Logs
                </button>
                <button @click="activeTab = 'templates'"
                        :class="activeTab === 'templates' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-gray-200">
                    Templates
                </button>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" x-show="activeTab === 'logs'" x-cloak>
                <!-- Total Sent -->
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Messages</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>

                <!-- Delivered/Sent -->
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sent / Delivered</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['sent']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                <!-- Failed -->
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Failed</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['failed']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>

                <!-- Today -->
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sent Today</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($stats['today']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>

            <!-- Filters & Table Container -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col" x-show="activeTab === 'logs'" x-cloak>
                <!-- Filters Header -->
                <div class="p-4 border-b border-gray-100 bg-gray-50/50 rounded-t-xl">
                    <form method="GET" class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by recipient or number..." 
                                class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div class="flex flex-wrap gap-2 lg:gap-4">
                            <select name="status" class="rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 min-w-[120px]">
                                <option value="">All Statuses</option>
                                <option value="queued" {{ ($status ?? '') === 'queued' ? 'selected' : '' }}>Queued</option>
                                <option value="sent" {{ ($status ?? '') === 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="failed" {{ ($status ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>

                            <select name="type" class="rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 min-w-[140px]">
                                <option value="">All Types</option>
                                <option value="reminder" {{ ($type ?? '') === 'reminder' ? 'selected' : '' }}>Reminder</option>
                                <option value="confirmation" {{ ($type ?? '') === 'confirmation' ? 'selected' : '' }}>Confirmation</option>
                                <option value="overdue alert" {{ ($type ?? '') === 'overdue alert' ? 'selected' : '' }}>Overdue Alert</option>
                            </select>

                            <div class="flex items-center gap-2">
                                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 w-32">
                                <span class="text-gray-400">-</span>
                                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 w-32">
                            </div>

                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm transition-colors shadow-sm whitespace-nowrap">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            
                            @if(request()->hasAny(['search', 'status', 'type', 'start_date', 'end_date']))
                                <a href="{{ route('admin.sms.logs') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg text-sm transition-colors" title="Clear Filters">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="px-4 pt-4">
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex justify-between items-center text-sm">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                @endif

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left">ID</th>
                                <th class="px-6 py-4 w-64">Recipient</th>
                                <th class="px-6 py-4 w-32">Type</th>
                                <th class="px-6 py-4">Message</th>
                                <th class="px-6 py-4 w-40">Sent At</th>
                                <th class="px-6 py-4 w-32">Status</th>
                                <th class="px-6 py-4 w-24 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" x-data="smsLogsTable({{ json_encode($logs->pluck('id')) }})">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50/80 transition-colors group" data-log-id="{{ $log->id }}">
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">
                                        &lt;{{ $log->id }}&gt;
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $recipientName = 'Unknown Recipient';
                                        $initials = 'UN';
                                        
                                        if ($log->student) {
                                            $recipientName = $log->student->first_name . ' ' . $log->student->last_name;
                                            $initials = substr($log->student->first_name, 0, 1) . substr($log->student->last_name, 0, 1);
                                        } elseif ($log->user) {
                                            if ($log->user->roleable) {
                                                $roleable = $log->user->roleable;
                                                if (isset($roleable->first_name)) {
                                                    $recipientName = $roleable->first_name . ' ' . ($roleable->last_name ?? '');
                                                    $initials = substr($roleable->first_name, 0, 1) . substr($roleable->last_name ?? '', 0, 1);
                                                } elseif (isset($roleable->name)) {
                                                    $recipientName = $roleable->name;
                                                    $parts = explode(' ', $recipientName);
                                                    $initials = substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1);
                                                } elseif (isset($roleable->parent_guardian_name)) {
                                                    $recipientName = $roleable->parent_guardian_name;
                                                    $parts = explode(' ', $recipientName);
                                                    $initials = substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1);
                                                }
                                            } else {
                                                $recipientName = $log->user->email ?? ('User #' . $log->user->user_id);
                                                $initials = substr($recipientName, 0, 2);
                                            }
                                        }
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-xs font-bold uppercase">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $recipientName }}</div>
                                            <div class="text-xs text-gray-500">{{ $log->mobile_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $typeClasses = [
                                            'reminder' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'confirmation' => 'bg-green-50 text-green-700 border-green-100',
                                            'overdue alert' => 'bg-red-50 text-red-700 border-red-100',
                                        ];
                                        $class = $typeClasses[$log->message_type] ?? 'bg-gray-50 text-gray-700 border-gray-100';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium border {{ $class }} capitalize inline-flex items-center gap-1.5">
                                        {{ $log->message_type ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-md">
                                        <p class="text-gray-600 truncate text-xs leading-relaxed" title="{{ $log->message }}">
                                            {{ Str::limit($log->message, 80) }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 text-xs">{{ $log->sent_at ? $log->sent_at->format('M d, Y') : '-' }}</div>
                                    <div class="text-gray-400 text-[10px]">{{ $log->sent_at ? $log->sent_at->format('h:i A') : '' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = [
                                            'sent' => ['icon' => 'fa-check', 'color' => 'text-green-600', 'bg' => 'bg-green-50'],
                                            'delivered' => ['icon' => 'fa-check-double', 'color' => 'text-green-600', 'bg' => 'bg-green-50'],
                                            'failed' => ['icon' => 'fa-times', 'color' => 'text-red-600', 'bg' => 'bg-red-50'],
                                            'queued' => ['icon' => 'fa-clock', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50'],
                                        ];
                                        $config = $statusConfig[$log->status] ?? ['icon' => 'fa-circle', 'color' => 'text-gray-600', 'bg' => 'bg-gray-50'];
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="status-dot w-2 h-2 rounded-full {{ str_replace('text-', 'bg-', $config['color']) }}"></span>
                                        <span class="status-text text-xs font-medium {{ $config['color'] }} capitalize">{{ $log->status }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="selectedLog = {{ json_encode($log->load('student', 'user')) }}; selectedLog.recipient_name = {{ json_encode($recipientName) }}; showDetailModal = true" 
                                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="View Details">
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>
                                        
                                        @if($log->status === 'failed')
                                            <form action="{{ route('admin.sms.logs.resend', $log->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to resend this message?');">
                                                @csrf
                                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition-all" title="Resend Message">
                                                    <i class="fas fa-redo text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if(app()->environment(['local','development','staging']))
                                            <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition-all" title="Mark Delivered"
                                                @click="
                                                    fetch('{{ route('admin.sms.logs.simulate', $log->id) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ status: 'delivered' }) })
                                                      .then(r => r.json()).then(j => { if (j && j.status) { const row = document.querySelector('tr[data-log-id={{ $log->id }}]'); if (row) { const t = row.querySelector('.status-text'); const d = row.querySelector('.status-dot'); if (t) t.textContent = j.status; if (d) { d.classList.remove('bg-green-600','bg-red-600','bg-yellow-600','bg-gray-600'); d.classList.add('bg-green-600'); } } } });
                                                ">
                                                <i class="fas fa-check text-xs"></i>
                                            </button>
                                            <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all" title="Mark Failed"
                                                @click="
                                                    fetch('{{ route('admin.sms.logs.simulate', $log->id) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ status: 'failed' }) })
                                                      .then(r => r.json()).then(j => { if (j && j.status) { const row = document.querySelector('tr[data-log-id={{ $log->id }}]'); if (row) { const t = row.querySelector('.status-text'); const d = row.querySelector('.status-dot'); if (t) t.textContent = j.status; if (d) { d.classList.remove('bg-green-600','bg-red-600','bg-yellow-600','bg-gray-600'); d.classList.add('bg-red-600'); } } } });
                                                ">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-inbox text-gray-300 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-900 font-medium">No SMS records found</p>
                                        <p class="text-gray-500 text-sm mt-1">Try adjusting your search or filters.</p>
                                        <a href="{{ route('admin.sms.logs') }}" class="mt-4 text-blue-600 hover:text-blue-700 text-sm font-medium">Clear all filters</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                <div class="p-4 border-t border-gray-200 bg-gray-50/50 rounded-b-xl">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>
            
            <!-- Templates Tab Content -->
            <div class="space-y-4" x-show="activeTab === 'templates'" x-cloak>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Templates</h2>
                        <p class="text-sm text-gray-500 mt-1">Manage your SMS message templates.</p>
                    </div>
                    <a href="{{ route('admin.sms.templates.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors shadow-sm font-medium">
                        <i class="fas fa-plus"></i>
                        <span>Create Template</span>
                    </a>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Content</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($templates as $t)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $t->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xl truncate">{{ Str::limit($t->content, 80) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.sms.templates.edit', $t) }}" class="text-blue-600 hover:text-blue-900 hover:underline">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $templates->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div x-cloak x-show="showDetailModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showDetailModal" 
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showDetailModal" @click.away="showDetailModal = false"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        
                        <!-- Modal Header -->
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Message Details</h3>
                            <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="px-4 py-5 sm:p-6 space-y-4">
                            <!-- Recipient Info -->
                            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900" x-text="selectedLog?.student?.first_name ? selectedLog.student.first_name + ' ' + selectedLog.student.last_name : 'Unknown'"></h4>
                                    <p class="text-xs text-gray-500" x-text="selectedLog?.mobile_number"></p>
                                </div>
                            </div>

                            <!-- Meta Grid -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Status</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium capitalize"
                                          :class="{
                                              'bg-green-50 text-green-700': selectedLog?.status === 'sent' || selectedLog?.status === 'delivered',
                                              'bg-red-50 text-red-700': selectedLog?.status === 'failed',
                                              'bg-yellow-50 text-yellow-700': selectedLog?.status === 'queued'
                                          }"
                                          x-text="selectedLog?.status"></span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Type</p>
                                    <span class="text-sm font-medium text-gray-900 capitalize" x-text="selectedLog?.message_type"></span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Sent At</p>
                                    <span class="text-sm text-gray-900" x-text="selectedLog?.sent_at ? new Date(selectedLog.sent_at).toLocaleString() : '-'"></span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Recipient</p>
                                    <span class="text-sm text-gray-900" x-text="selectedLog?.recipient_name"></span>
                                </div>
                            </div>

                            <!-- Message Content -->
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1 font-medium">Message Content</p>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed" x-text="selectedLog?.message"></p>
                            </div>

                            <!-- Provider Response (if error) -->
                            <template x-if="selectedLog?.provider_response">
                                <div class="bg-red-50 rounded-lg p-3 border border-red-100">
                                    <p class="text-xs text-red-600 mb-1 font-medium">Provider Response</p>
                                    <p class="text-xs text-red-700 font-mono break-all" x-text="selectedLog?.provider_response"></p>
                                </div>
                            </template>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                            <template x-if="selectedLog?.status === 'failed'">
                                <form :action="'/admin/sms/logs/' + selectedLog?.id + '/resend'" method="POST" class="inline-block sm:ml-3">
                                    @csrf
                                    <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:w-auto">
                                        <i class="fas fa-redo mr-2 mt-0.5"></i> Resend Message
                                    </button>
                                </form>
                            </template>
                            <button type="button" @click="showDetailModal = false" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('smsLogsTable', (initialIds) => ({
                ids: initialIds,
                polling: null,
                init() {
                    this.poll();
                    this.polling = setInterval(() => this.poll(), 5000);
                    // Listen for Supabase Realtime updates
                    window.addEventListener('sms-log-updated', () => this.poll());
                },
                destroy() {
                    if (this.polling) clearInterval(this.polling);
                },
                poll() {
                    if (!this.ids || this.ids.length === 0) return;
                    const params = new URLSearchParams();
                    this.ids.forEach(id => params.append('ids[]', id));
                    
                    fetch('{{ route('admin.sms.logs.statuses') }}?' + params.toString())
                        .then(r => r.json())
                        .then(data => {
                            (data || []).forEach(x => this.updateRow(x.id, x.status));
                        });
                },
                updateRow(id, status) {
                    const row = document.querySelector(`tr[data-log-id="${id}"]`);
                    if (!row) return;
                    const textEl = row.querySelector('.status-text');
                    const dotEl = row.querySelector('.status-dot');
                    if (textEl) textEl.textContent = status;
                    if (dotEl) {
                        dotEl.classList.remove('bg-green-600', 'bg-red-600', 'bg-yellow-600', 'bg-gray-600');
                        if (status === 'sent' || status === 'delivered') dotEl.classList.add('bg-green-600');
                        else if (status === 'failed') dotEl.classList.add('bg-red-600');
                        else if (status === 'queued') dotEl.classList.add('bg-yellow-600');
                        else dotEl.classList.add('bg-gray-600');
                    }
                },
                simulate(id, status) {
                    const token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
                    fetch(`/admin/sms/logs/${id}/simulate`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({ status })
                    })
                    .then(r => r.json())
                    .then(j => {
                        if (j && j.status) {
                            this.updateRow(id, j.status);
                        }
                    });
                }
            }));
        });
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Supabase Realtime -->
    @include('partials.supabase_realtime')
</body>
</html>
