<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard - Efees</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        @media print {
            .no-print, aside { display: none !important; }
            body { background: white; }
            main { padding: 0; margin: 0; width: 100%; }
            .fab-pay, .refresh-bar { display: none !important; }
        }
        /* Pull-to-refresh indicator */
        .ptr-indicator {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .ptr-indicator .ptr-spinner {
            animation: ptr-spin 0.8s linear infinite;
        }
        @keyframes ptr-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        /* FAB animation */
        .fab-pay {
            animation: fab-entrance 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        @keyframes fab-entrance {
            from { transform: scale(0) translateY(20px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }
        .fab-pay:active {
            transform: scale(0.92);
        }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-800" x-data="{ sidebarOpen: false }">

    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between bg-white border-b border-gray-200 px-4 py-3 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-md">
                <i class="fas fa-users-cog text-sm"></i>
            </div>
            <span class="font-bold text-gray-800 text-lg">Parent Portal</span>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-30 md:hidden" 
         x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 z-40 w-72 bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 md:translate-x-0 shadow-2xl md:shadow-none">
        <div class="flex items-center gap-3 px-8 py-6 border-b border-gray-100 bg-white sticky top-0 z-10">
            <div class="w-10 h-10 flex-shrink-0 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <i class="fas fa-users-cog text-lg"></i>
            </div>
            <div>
                <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Parent Portal</h1>
                <p class="text-xs text-gray-500 font-medium">Efees System</p>
            </div>
        </div>

        <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow overflow-y-auto pb-6">
            <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Menu</p>
            
            <!-- Dashboard Link (Overview) -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.dashboard') && (!isset($selectedChild) || !$selectedChild) ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-gray-600 hover:bg-gray-50' }}" href="{{ route('parent.dashboard') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('parent.dashboard') && (!isset($selectedChild) || !$selectedChild) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                </div>
                <span class="text-sm font-medium">Overview</span>
            </a>

            @if(isset($isParent) && $isParent)
            <!-- Payments Link -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.pay') && !request()->routeIs('parent.pay.multi') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-gray-600 hover:bg-gray-50' }}" href="{{ route('parent.pay') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-credit-card text-lg {{ request()->routeIs('parent.pay') && !request()->routeIs('parent.pay.multi') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                </div>
                <span class="text-sm font-medium">Payments</span>
            </a>

            <!-- Multi-Child Payment Link -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.pay.multi') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-gray-600 hover:bg-gray-50' }}" href="{{ route('parent.pay.multi') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-users text-lg {{ request()->routeIs('parent.pay.multi') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                </div>
                <span class="text-sm font-medium">Multi-Child Pay</span>
            </a>

            <!-- Payment History Link -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.history') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-gray-600 hover:bg-gray-50' }}" href="{{ route('parent.history') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-history text-lg {{ request()->routeIs('parent.history') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                </div>
                <span class="text-sm font-medium">Payment History</span>
            </a>

            <!-- Notifications Link -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.notifications') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-gray-600 hover:bg-gray-50' }}" href="{{ route('parent.notifications') }}">
                <div class="w-8 flex justify-center relative">
                    <i class="fas fa-bell text-lg {{ request()->routeIs('parent.notifications') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                    <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center hidden">0</span>
                </div>
                <span class="text-sm font-medium">Notifications</span>
            </a>
            @endif

            <!-- Student Selector Section -->
            @if(isset($isParent) && $isParent && isset($myChildren) && count($myChildren) > 0)
                <div class="mt-6 px-4 mb-2 flex items-center justify-between">
                     <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">My Students</p>
                     <span class="bg-blue-100 text-blue-700 py-0.5 px-2 rounded-full text-[10px] font-bold">{{ count($myChildren) }}</span>
                </div>
                
                <div class="space-y-2 mb-6">
                @foreach($myChildren as $child)
                    <div x-data="{ expanded: {{ (isset($selectedChild) && $selectedChild && $selectedChild->student_id === $child->student_id) ? 'true' : 'false' }} }" class="rounded-xl overflow-hidden transition-all duration-200" :class="expanded ? 'bg-gray-50' : 'hover:bg-gray-50'">
                        <!-- Student Header (Click to toggle) -->
                        <div @click="expanded = !expanded" class="flex items-center gap-3 px-4 py-2.5 cursor-pointer select-none">
                            <div class="w-8 flex justify-center flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors"
                                     :class="expanded ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'">
                                    {{ substr($child->first_name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex flex-col min-w-0 flex-1">
                                <span class="text-sm font-medium truncate text-gray-900">{{ $child->first_name }} {{ $child->last_name }}</span>
                                <span class="text-[10px] text-gray-400 truncate">{{ $child->student_id }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''"></i>
                        </div>

                        <!-- Submenu -->
                        <div x-show="expanded" class="bg-gray-50 pb-2 space-y-0.5" x-cloak>
                            <!-- Dashboard -->
                            <a href="{{ route('parent.dashboard', ['student_id' => $child->student_id]) }}" 
                               class="flex items-center gap-3 pl-14 pr-4 py-2 text-sm font-medium hover:text-blue-600 transition-colors {{ request()->fullUrlIs(route('parent.dashboard', ['student_id' => $child->student_id])) ? 'text-blue-600 bg-blue-50/50' : 'text-gray-500' }}">
                                <i class="fas fa-chart-pie w-4 text-center text-xs"></i> Overview
                            </a>
                            
                            <!-- Fee Breakdown -->
                            <a href="{{ route('parent.fees.show', $child->student_id) }}" 
                               class="flex items-center gap-3 pl-14 pr-4 py-2 text-sm font-medium hover:text-blue-600 transition-colors {{ request()->routeIs('parent.fees.show') && request()->route('student')->student_id == $child->student_id ? 'text-blue-600 bg-blue-50/50' : 'text-gray-500' }}">
                                <i class="fas fa-file-invoice w-4 text-center text-xs"></i> Fee Breakdown
                            </a>
                            
                            <!-- SOA -->
                            <a href="{{ route('parent.soa', $child->student_id) }}" 
                               class="flex items-center gap-3 pl-14 pr-4 py-2 text-sm font-medium hover:text-blue-600 transition-colors {{ request()->routeIs('parent.soa') && request()->route('student')->student_id == $child->student_id ? 'text-blue-600 bg-blue-50/50' : 'text-gray-500' }}">
                                <i class="fas fa-file-invoice-dollar w-4 text-center text-xs"></i> SOA
                            </a>

                            <!-- Payment Schedule -->
                            <a href="{{ route('parent.schedule', $child->student_id) }}" 
                               class="flex items-center gap-3 pl-14 pr-4 py-2 text-sm font-medium hover:text-blue-600 transition-colors {{ request()->routeIs('parent.schedule') && request()->route('student')->student_id == $child->student_id ? 'text-blue-600 bg-blue-50/50' : 'text-gray-500' }}">
                                <i class="fas fa-calendar-alt w-4 text-center text-xs"></i> Payment Schedule
                            </a>
                            
                            <!-- Pay Now -->
                            <a href="{{ route('parent.pay', ['student_id' => $child->student_id]) }}" 
                               class="flex items-center gap-3 pl-14 pr-4 py-2 text-sm font-medium hover:text-blue-600 transition-colors text-gray-500">
                                <i class="fas fa-credit-card w-4 text-center text-xs"></i> Pay Fees
                            </a>
                        </div>
                    </div>
                @endforeach
                </div>
            @endif

            <!-- Settings -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.profile.edit') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-gray-600 hover:bg-gray-50' }}" href="{{ route('parent.profile.edit') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-cog text-lg {{ request()->routeIs('parent.profile.edit') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                </div>
                <span class="text-sm font-medium">Settings</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 px-4 pb-6" onsubmit="return confirmLogout()">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500 hover:text-red-600 hover:bg-red-50 transition-all duration-200 group">
                    <div class="w-8 flex justify-center">
                        <i class="fas fa-sign-out-alt text-lg text-gray-400 group-hover:text-red-500 transition-colors"></i>
                    </div>
                    <span class="text-sm font-medium">Sign Out</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main id="mainContent" class="flex-1 p-6 md:p-8 overflow-y-auto custom-scrollbar relative">

        <!-- Pull-to-Refresh Indicator (hidden by default, shown on pull) -->
        <div id="ptrIndicator" class="ptr-indicator flex items-center justify-center overflow-hidden" style="height: 0; opacity: 0;">
            <div class="flex items-center gap-2 py-3">
                <i id="ptrIcon" class="fas fa-arrow-down text-blue-500 text-sm transition-transform"></i>
                <span id="ptrText" class="text-sm font-medium text-gray-500">Pull to refresh</span>
            </div>
        </div>

        @hasSection('content')
            @yield('content')
        @else
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
                <p class="text-gray-600 mt-1">Manage fees and view status for your linked students.</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Refresh Button (always visible) -->
                <button id="refreshBtn" onclick="refreshDashboard()" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:border-blue-300 hover:bg-blue-50 text-gray-600 hover:text-blue-600 px-4 py-2 rounded-xl font-medium transition-all text-sm shadow-sm" title="Refresh balances">
                    <i id="refreshIcon" class="fas fa-sync-alt text-sm"></i>
                    <span class="hidden sm:inline">Refresh</span>
                </button>
                <div class="hidden md:flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name ?? 'Parent' }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold border border-blue-200">
                        {{ substr(Auth::user()->email, 0, 1) }}
                    </div>
                </div>
            </div>
        </div>

        @if($isParent)
            @if((isset($selectedChild) && $selectedChild) || request('section') == 'students')
                @include('auth.student_dashboard_content')
            @else
            <!-- Consolidated Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <!-- Total Balance Card -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-lg p-6 text-white transform transition-all hover:scale-[1.01]">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-red-100 font-medium text-sm uppercase tracking-wider">Total Balance Due</p>
                            <h2 id="consolidatedBalanceDue" class="text-4xl font-bold mt-2">₱{{ number_format($consolidatedBalanceDue, 2) }}</h2>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl backdrop-blur-sm">
                            <i class="fas fa-exclamation-circle text-2xl text-white"></i>
                        </div>
                    </div>
                    <p class="mt-4 text-red-100 text-sm">
                        Total outstanding across all students
                    </p>
                </div>

                <!-- Total Paid Card -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white transform transition-all hover:scale-[1.01]">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-green-100 font-medium text-sm uppercase tracking-wider">Total Amount Paid</p>
                            <h2 id="consolidatedTotalPaid" class="text-4xl font-bold mt-2">₱{{ number_format($consolidatedTotalPaid, 2) }}</h2>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl backdrop-blur-sm">
                            <i class="fas fa-check-circle text-2xl text-white"></i>
                        </div>
                    </div>
                    <p class="mt-4 text-green-100 text-sm">
                        Total payments made this school year
                    </p>
                </div>
            </div>

            <!-- Children List -->
            <h2 id="students" class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2 pt-4">
                <i class="fas fa-child text-blue-600"></i>
                Your Students
            </h2>

            @if($childrenSummaries->count() > 0)
                <div id="childrenContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @foreach($childrenSummaries as $child)
                        <div id="child-card-{{ $child['student_id'] }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Card Header -->
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-900">{{ $child['full_name'] }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $child['full_name'] }} • {{ $child['level'] ?? 'N/A' }} • {{ $child['section'] ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-bold text-sm border border-gray-200">
                                    {{ substr($child['first_name'], 0, 1) }}{{ substr($child['last_name'], 0, 1) }}
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-6">
                                <!-- Payment Progress Ring -->
                                @php
                                    $totalAmt = $child['totalAmount'] ?? 0;
                                    $paidPct = $totalAmt > 0 ? round(($child['totalPaid'] / $totalAmt) * 100) : 0;
                                    $paidPct = min($paidPct, 100);
                                    $circumference = 2 * 3.14159 * 40;
                                    $dashOffset = $circumference - ($circumference * $paidPct / 100);
                                    $ringColor = $paidPct >= 100 ? '#16a34a' : ($paidPct >= 50 ? '#2563eb' : ($paidPct >= 25 ? '#f59e0b' : '#ef4444'));
                                @endphp
                                <div class="flex items-center gap-5 mb-6 p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-100">
                                    <div class="relative flex-shrink-0" id="child-ring-{{ $child['student_id'] }}">
                                        <svg width="96" height="96" viewBox="0 0 96 96" class="transform -rotate-90">
                                            <circle cx="48" cy="48" r="40" fill="none" stroke="#e5e7eb" stroke-width="7"/>
                                            <circle cx="48" cy="48" r="40" fill="none" stroke="{{ $ringColor }}" stroke-width="7"
                                                stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $dashOffset }}"
                                                stroke-linecap="round" class="transition-all duration-700"/>
                                        </svg>
                                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                                            <span class="text-lg font-bold text-gray-900" id="child-pct-{{ $child['student_id'] }}">{{ $paidPct }}%</span>
                                            <span class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">paid</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 mb-1">Total Tuition</p>
                                        <p class="text-base font-bold text-gray-900 mb-2" id="child-total-{{ $child['student_id'] }}">₱{{ number_format($totalAmt, 2) }}</p>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-700" style="width: {{ $paidPct }}%; background-color: {{ $ringColor }};"></div>
                                        </div>
                                        <div class="flex justify-between text-[11px] text-gray-500 mt-1">
                                            <span>₱{{ number_format($child['totalPaid'], 2) }} paid</span>
                                            <span>₱{{ number_format($child['balanceDue'], 2) }} left</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                                        <p class="text-xs text-red-600 uppercase font-bold tracking-wide mb-1">Balance</p>
                                        <p id="child-balance-{{ $child['student_id'] }}" class="text-xl font-bold text-red-700">₱{{ number_format($child['balanceDue'], 2) }}</p>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                                        <p class="text-xs text-green-600 uppercase font-bold tracking-wide mb-1">Paid</p>
                                        <p id="child-paid-{{ $child['student_id'] }}" class="text-xl font-bold text-green-700">₱{{ number_format($child['totalPaid'], 2) }}</p>
                                    </div>
                                </div>

                                <!-- Year-over-Year Fee Comparison -->
                                @php $cmp = $child['feeComparison'] ?? []; @endphp
                                @if(!empty($cmp['previousBaseFee']) && !empty($cmp['currentBaseFee']))
                                <div id="child-yoy-{{ $child['student_id'] }}" class="mb-6 p-4 rounded-xl border border-gray-100 bg-gradient-to-r from-indigo-50/60 to-purple-50/60">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wide flex items-center gap-1.5">
                                            <i class="fas fa-chart-line text-indigo-500"></i>
                                            Fee Comparison
                                        </h4>
                                        @if($cmp['changePercent'] !== null)
                                            @if($cmp['changePercent'] > 0)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                                    <i class="fas fa-arrow-up"></i> +{{ $cmp['changePercent'] }}%
                                                </span>
                                            @elseif($cmp['changePercent'] < 0)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                                    <i class="fas fa-arrow-down"></i> {{ $cmp['changePercent'] }}%
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                    <i class="fas fa-equals"></i> No change
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="text-center p-2.5 bg-white/70 rounded-lg border border-gray-100">
                                            <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1">{{ $cmp['previousYear'] ?? 'Last Year' }}</p>
                                            <p class="text-base font-bold text-gray-700">₱{{ number_format($cmp['previousBaseFee'], 2) }}</p>
                                        </div>
                                        <div class="text-center p-2.5 bg-white/70 rounded-lg border border-indigo-100">
                                            <p class="text-[10px] text-indigo-600 font-semibold uppercase tracking-wider mb-1">{{ $cmp['currentYear'] ?? 'This Year' }}</p>
                                            <p class="text-base font-bold text-indigo-700">₱{{ number_format($cmp['currentBaseFee'], 2) }}</p>
                                        </div>
                                    </div>
                                    @if($cmp['changeAmount'] !== null && $cmp['changeAmount'] != 0)
                                        <p class="text-center text-[11px] text-gray-500 mt-2.5">
                                            Base tuition for {{ $child['level'] ?? 'this level' }}
                                            @if($cmp['changeAmount'] > 0)
                                                increased by <span class="font-bold text-red-600">₱{{ number_format(abs($cmp['changeAmount']), 2) }}</span>
                                            @else
                                                decreased by <span class="font-bold text-green-600">₱{{ number_format(abs($cmp['changeAmount']), 2) }}</span>
                                            @endif
                                            year-over-year.
                                        </p>
                                    @else
                                        <p class="text-center text-[11px] text-gray-500 mt-2.5">
                                            Base tuition for {{ $child['level'] ?? 'this level' }} is unchanged from last year.
                                        </p>
                                    @endif
                                </div>
                                @endif

                                <!-- Upcoming Fees -->
                                <div class="mb-6">
                                    <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Upcoming / Unpaid Fees</h4>
                                    <div id="child-fees-{{ $child['student_id'] }}" class="space-y-3">
                                        @if(count($child['upcomingFees']) > 0)
                                            @foreach($child['upcomingFees'] as $fee)
                                                @php
                                                    $isOverdue = $fee->payment_date && $fee->payment_date->isPast() && ($fee->status !== 'paid');
                                                    $daysUntil = $fee->payment_date ? (int) now()->startOfDay()->diffInDays($fee->payment_date->startOfDay(), false) : null;
                                                @endphp
                                                <div class="flex justify-between items-center gap-4 p-3 bg-gray-50 rounded-lg border {{ $isOverdue ? 'border-red-300 bg-red-50' : 'border-gray-100' }}">
                                                    <div>
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <p class="font-medium text-gray-800 text-sm">{{ $fee['notes'] ?? 'Fee' }}</p>
                                                            @if($daysUntil !== null)
                                                                @if($daysUntil < 0)
                                                                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wider border border-red-200">
                                                                        <i class="fas fa-exclamation-triangle mr-0.5"></i> {{ abs($daysUntil) }}d overdue
                                                                    </span>
                                                                @elseif($daysUntil === 0)
                                                                    <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold uppercase tracking-wider border border-orange-200">
                                                                        <i class="fas fa-bell mr-0.5"></i> Due today
                                                                    </span>
                                                                @elseif($daysUntil <= 3)
                                                                    <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-[10px] font-bold uppercase tracking-wider border border-yellow-200">
                                                                        <i class="fas fa-clock mr-0.5"></i> {{ $daysUntil }}d left
                                                                    </span>
                                                                @elseif($daysUntil <= 7)
                                                                    <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold uppercase tracking-wider border border-blue-200">
                                                                        <i class="fas fa-calendar-day mr-0.5"></i> {{ $daysUntil }}d left
                                                                    </span>
                                                                @else
                                                                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider border border-gray-200">
                                                                        {{ $daysUntil }}d left
                                                                    </span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-gray-500">
                                                            Due: {{ $fee->payment_date ? $fee->payment_date->format('M d, Y') : 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <span class="{{ $isOverdue ? 'text-red-700' : 'text-gray-700' }} font-bold text-sm whitespace-nowrap">₱{{ number_format((float) $fee->balance, 2) }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-sm text-gray-500 italic">No pending fees.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <a href="{{ route('parent.pay', ['student_id' => $child['student_id'], 'pay_full' => 1]) }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-xl transition-colors shadow-sm shadow-blue-200">
                                    Pay Now
                                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-child text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No Students Linked</h3>
                    <p class="text-gray-500 mt-2 max-w-md mx-auto">
                        There are no students linked to your parent account yet. Please contact the school administration to link your children.
                    </p>
                </div>
            @endif
            @endif
        @else
            <!-- Fallback for non-parent users visiting this page -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            This dashboard is intended for parent accounts.
                        </p>
                    </div>
                </div>
            </div>
        @endif
        @endif
    </main>

    @if(isset($isParent) && $isParent)
    <!-- Floating Pay Now FAB (Mobile Only) -->
    <div id="fabPay" class="fab-pay fixed bottom-6 right-5 z-30 md:hidden no-print" x-data="{ expanded: false }">
        <!-- Backdrop when open -->
        <div x-show="expanded" x-cloak @click="expanded = false"
             class="fixed inset-0 bg-black/20 backdrop-blur-[2px] -z-10"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        <!-- Expandable Quick Actions -->
        <div x-show="expanded" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="absolute bottom-16 right-0 mb-2 flex flex-col items-end gap-2">
            <!-- Single Payment -->
            <a href="{{ route('parent.pay') }}" class="flex items-center gap-2 bg-white border border-gray-200 text-gray-700 pl-4 pr-3 py-2.5 rounded-full shadow-lg hover:shadow-xl transition-all whitespace-nowrap">
                <span class="text-sm font-medium">Single Payment</span>
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-credit-card text-blue-600 text-xs"></i>
                </div>
            </a>
            <!-- Multi-Child Payment -->
            <a href="{{ route('parent.pay.multi') }}" class="flex items-center gap-2 bg-white border border-gray-200 text-gray-700 pl-4 pr-3 py-2.5 rounded-full shadow-lg hover:shadow-xl transition-all whitespace-nowrap">
                <span class="text-sm font-medium">Multi-Child Pay</span>
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-green-600 text-xs"></i>
                </div>
            </a>
            <!-- Payment History -->
            <a href="{{ route('parent.history') }}" class="flex items-center gap-2 bg-white border border-gray-200 text-gray-700 pl-4 pr-3 py-2.5 rounded-full shadow-lg hover:shadow-xl transition-all whitespace-nowrap">
                <span class="text-sm font-medium">History</span>
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-history text-purple-600 text-xs"></i>
                </div>
            </a>
        </div>

        <!-- Main FAB Button -->
        <button @click="expanded = !expanded"
                class="relative w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-xl shadow-blue-300/50 flex items-center justify-center transition-all duration-300"
                :class="expanded ? 'rotate-45 bg-gray-700 hover:bg-gray-800 shadow-gray-300/50' : ''">
            <i class="fas text-xl" :class="expanded ? 'fa-plus' : 'fa-credit-card'"></i>
        </button>
    </div>
    @endif


    <script>
        // ============ Pull-to-Refresh & Refresh Logic ============
        let isRefreshing = false;
        let ptrStartY = 0;
        let ptrCurrentY = 0;
        let ptrTriggered = false;
        const PTR_THRESHOLD = 70;

        function refreshDashboard() {
            if (isRefreshing) return;
            isRefreshing = true;

            // Animate the refresh button
            const refreshBtn = document.getElementById('refreshBtn');
            const refreshIcon = document.getElementById('refreshIcon');
            if (refreshBtn) {
                refreshBtn.classList.add('pointer-events-none');
                refreshBtn.classList.replace('border-gray-200', 'border-blue-300');
                refreshBtn.classList.replace('text-gray-600', 'text-blue-600');
            }
            if (refreshIcon) refreshIcon.classList.add('animate-spin');

            // Fetch fresh data
            fetch('{{ route("parent.metrics") }}')
                .then(r => r.json())
                .then(data => {
                    updateDashboardData(data);
                    showRefreshToast('Balances updated!');
                })
                .catch(() => showRefreshToast('Refresh failed. Try again.', true))
                .finally(() => {
                    isRefreshing = false;
                    if (refreshBtn) {
                        refreshBtn.classList.remove('pointer-events-none');
                        refreshBtn.classList.replace('border-blue-300', 'border-gray-200');
                        refreshBtn.classList.replace('text-blue-600', 'text-gray-600');
                    }
                    if (refreshIcon) refreshIcon.classList.remove('animate-spin');
                    resetPTR();
                });
        }

        function showRefreshToast(message, isError = false) {
            const existing = document.getElementById('refreshToast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.id = 'refreshToast';
            toast.className = `fixed top-4 left-1/2 -translate-x-1/2 z-50 px-5 py-2.5 rounded-full shadow-lg text-sm font-medium flex items-center gap-2 transition-all duration-300 ${
                isError ? 'bg-red-600 text-white' : 'bg-gray-900 text-white'
            }`;
            toast.innerHTML = `<i class="fas ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${message}`;
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(-10px)';
            document.body.appendChild(toast);

            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(-50%) translateY(0)';
            });

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(-50%) translateY(-10px)';
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }

        function resetPTR() {
            const indicator = document.getElementById('ptrIndicator');
            const icon = document.getElementById('ptrIcon');
            const text = document.getElementById('ptrText');
            if (indicator) {
                indicator.style.height = '0';
                indicator.style.opacity = '0';
            }
            if (icon) {
                icon.classList.remove('ptr-spinner', 'fa-spinner');
                icon.classList.add('fa-arrow-down');
                icon.style.transform = '';
            }
            if (text) text.textContent = 'Pull to refresh';
            ptrTriggered = false;
        }

        // Touch-based pull-to-refresh
        document.addEventListener('DOMContentLoaded', () => {
            const main = document.getElementById('mainContent');
            if (!main) return;

            main.addEventListener('touchstart', (e) => {
                if (main.scrollTop <= 0 && !isRefreshing) {
                    ptrStartY = e.touches[0].clientY;
                }
            }, { passive: true });

            main.addEventListener('touchmove', (e) => {
                if (ptrStartY === 0 || isRefreshing) return;
                ptrCurrentY = e.touches[0].clientY;
                const pullDistance = Math.max(0, ptrCurrentY - ptrStartY);
                
                if (pullDistance > 0 && main.scrollTop <= 0) {
                    const progress = Math.min(pullDistance / PTR_THRESHOLD, 1);
                    const height = Math.min(pullDistance * 0.5, 60);
                    const indicator = document.getElementById('ptrIndicator');
                    const icon = document.getElementById('ptrIcon');
                    const text = document.getElementById('ptrText');

                    if (indicator) {
                        indicator.style.height = height + 'px';
                        indicator.style.opacity = String(progress);
                    }
                    if (icon) icon.style.transform = `rotate(${progress * 180}deg)`;
                    if (text) text.textContent = progress >= 1 ? 'Release to refresh' : 'Pull to refresh';
                    ptrTriggered = progress >= 1;
                }
            }, { passive: true });

            main.addEventListener('touchend', () => {
                if (ptrTriggered && !isRefreshing) {
                    const icon = document.getElementById('ptrIcon');
                    const text = document.getElementById('ptrText');
                    if (icon) {
                        icon.classList.remove('fa-arrow-down');
                        icon.classList.add('fa-spinner', 'ptr-spinner');
                        icon.style.transform = '';
                    }
                    if (text) text.textContent = 'Refreshing...';
                    refreshDashboard();
                } else {
                    resetPTR();
                }
                ptrStartY = 0;
                ptrCurrentY = 0;
            }, { passive: true });
        });

        // ============ Original Dashboard Logic ============
        function updateDashboardData(data) {
            // Update Consolidated Totals
            const balanceEl = document.getElementById('consolidatedBalanceDue');
            const paidEl = document.getElementById('consolidatedTotalPaid');
            
            if (balanceEl) balanceEl.innerText = '₱' + Number(data.consolidatedBalanceDue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (paidEl) paidEl.innerText = '₱' + Number(data.consolidatedTotalPaid).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            // Update Children Cards
            if (data.children && Array.isArray(data.children)) {
                data.children.forEach(child => {
                    const childBalanceEl = document.getElementById(`child-balance-${child.student_id}`);
                    const childPaidEl = document.getElementById(`child-paid-${child.student_id}`);
                    const childFeesContainer = document.getElementById(`child-fees-${child.student_id}`);

                    if (childBalanceEl) childBalanceEl.innerText = '₱' + Number(child.balanceDue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    if (childPaidEl) childPaidEl.innerText = '₱' + Number(child.totalPaid).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                    // Update Progress Ring
                    const totalAmt = child.totalAmount || 0;
                    const paidPct = totalAmt > 0 ? Math.min(Math.round((child.totalPaid / totalAmt) * 100), 100) : 0;
                    const circumference = 2 * Math.PI * 40;
                    const dashOffset = circumference - (circumference * paidPct / 100);
                    const ringColor = paidPct >= 100 ? '#16a34a' : (paidPct >= 50 ? '#2563eb' : (paidPct >= 25 ? '#f59e0b' : '#ef4444'));

                    const pctEl = document.getElementById(`child-pct-${child.student_id}`);
                    const totalEl = document.getElementById(`child-total-${child.student_id}`);
                    const ringContainer = document.getElementById(`child-ring-${child.student_id}`);

                    if (pctEl) pctEl.innerText = paidPct + '%';
                    if (totalEl) totalEl.innerText = '₱' + Number(totalAmt).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                    if (ringContainer) {
                        const progressCircle = ringContainer.querySelector('circle:nth-child(2)');
                        const progressBar = ringContainer.closest('.p-4')?.querySelector('.h-2 > div, .bg-gray-200 .h-2');
                        if (progressCircle) {
                            progressCircle.setAttribute('stroke', ringColor);
                            progressCircle.setAttribute('stroke-dashoffset', dashOffset);
                        }
                        // Update the horizontal bar
                        const barContainer = ringContainer.closest('.flex.items-center.gap-5');
                        if (barContainer) {
                            const bar = barContainer.querySelector('.h-2.rounded-full[style]');
                            if (bar) {
                                bar.style.width = paidPct + '%';
                                bar.style.backgroundColor = ringColor;
                            }
                        }
                    }

                    if (childFeesContainer && child.upcomingFees) {
                        if (child.upcomingFees.length > 0) {
                            childFeesContainer.innerHTML = child.upcomingFees.map(fee => {
                                const overdueClass = fee.isOverdue ? 'border-red-300 bg-red-50' : 'border-gray-100';
                                const amountClass = fee.isOverdue ? 'text-red-700' : 'text-gray-700';

                                // Compute countdown badge
                                let badge = '';
                                if (fee.daysUntil !== undefined && fee.daysUntil !== null) {
                                    const d = fee.daysUntil;
                                    if (d < 0) {
                                        badge = `<span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wider border border-red-200">
                                            <i class="fas fa-exclamation-triangle mr-0.5"></i> ${Math.abs(d)}d overdue</span>`;
                                    } else if (d === 0) {
                                        badge = `<span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold uppercase tracking-wider border border-orange-200">
                                            <i class="fas fa-bell mr-0.5"></i> Due today</span>`;
                                    } else if (d <= 3) {
                                        badge = `<span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-[10px] font-bold uppercase tracking-wider border border-yellow-200">
                                            <i class="fas fa-clock mr-0.5"></i> ${d}d left</span>`;
                                    } else if (d <= 7) {
                                        badge = `<span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold uppercase tracking-wider border border-blue-200">
                                            <i class="fas fa-calendar-day mr-0.5"></i> ${d}d left</span>`;
                                    } else {
                                        badge = `<span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider border border-gray-200">${d}d left</span>`;
                                    }
                                }
                                
                                return `
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border ${overdueClass}">
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-medium text-gray-800 text-sm">${fee.notes}</p>
                                                ${badge}
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                Due: ${fee.payment_date}
                                            </p>
                                        </div>
                                        <span class="${amountClass} font-bold text-sm whitespace-nowrap">₱${Number(fee.balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    </div>
                                `;
                            }).join('');
                        } else {
                            childFeesContainer.innerHTML = `<p class="text-sm text-gray-500 italic">No pending fees.</p>`;
                        }
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const POLL_INTERVAL = 30000; // 30 seconds

            function fetchMetrics() {
                fetch('{{ route("parent.metrics") }}')
                    .then(response => response.json())
                    .then(data => updateDashboardData(data))
                    .catch(err => console.error('Error fetching parent metrics:', err));
            }

            // Start Polling
            setInterval(window.fetchMetrics, POLL_INTERVAL);

            // Notification Badge Polling
            function fetchNotificationCount() {
                fetch('/parent/notifications/unread-count')
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.getElementById('notification-badge');
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count > 99 ? '99+' : data.count;
                                badge.classList.remove('hidden');
                            } else {
                                badge.classList.add('hidden');
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching notification count:', err));
            }

            // Initial fetch and polling for notifications
            fetchNotificationCount();
            setInterval(fetchNotificationCount, POLL_INTERVAL);
        });
    </script>
    <!-- Supabase Realtime -->
    @include('partials.supabase_realtime')
    
    <!-- Logout Confirmation Script -->
    <script>
    function confirmLogout() {
        return confirm('Are you sure you want to sign out?');
    }
    </script>
</body>
</html>
