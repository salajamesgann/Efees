<!DOCTYPE html>
<html lang="en" x-data="darkMode()" x-bind:class="{ 'dark': isDark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard - Efees</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        /* Sidebar scrollbar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        .sidebar-scroll { scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent; }
        .dark .sidebar-scroll::-webkit-scrollbar-thumb { background: #374151; }
        .dark .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #4b5563; }
        .dark .sidebar-scroll { scrollbar-color: #374151 transparent; }
        @media print {
            .no-print, aside, .mobile-bottom-nav { display: none !important; }
            body { background: white; }
            main { padding: 0; margin: 0; width: 100%; padding-bottom: 0 !important; }
            .fab-pay, .refresh-bar { display: none !important; }
        }
        /* Mobile bottom nav */
        .mobile-bottom-nav {
            padding-bottom: env(safe-area-inset-bottom, 0);
        }
        .mobile-bottom-nav .nav-item {
            -webkit-tap-highlight-color: transparent;
            transition: all 0.2s ease;
        }
        .mobile-bottom-nav .nav-item.active {
            color: #2563eb;
        }
        .mobile-bottom-nav .nav-item.active .nav-icon {
            transform: translateY(-2px) scale(1.1);
        }
        .mobile-bottom-nav .nav-item .nav-icon {
            transition: transform 0.2s ease;
        }
        /* Safe area for bottom nav content spacing */
        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            .mobile-bottom-nav { padding-bottom: env(safe-area-inset-bottom); }
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
        /* Summary hero card effects */
        .summary-hero {
            animation: heroEntrance 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }
        @keyframes heroEntrance {
            from { opacity: 0; transform: translateY(12px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .summary-shimmer {
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.03) 45%, rgba(255,255,255,0.06) 50%, rgba(255,255,255,0.03) 55%, transparent 60%);
            background-size: 200% 100%;
            animation: shimmerSweep 4s ease-in-out infinite;
        }
        @keyframes shimmerSweep {
            0%, 100% { background-position: 200% center; }
            50% { background-position: -200% center; }
        }
        .gauge-entrance {
            animation: gaugeIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both;
        }
        @keyframes gaugeIn {
            from { opacity: 0; transform: scale(0.6) rotate(-20deg); }
            to { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        .gauge-fill {
            transition: stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1) 0.5s;
        }
        .progress-bar-glow {
            box-shadow: 0 0 8px rgba(52, 211, 153, 0.3), 0 0 16px rgba(34, 211, 238, 0.15);
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1) 0.3s;
        }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-200" x-data="{ sidebarOpen: false }">

    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-2.5 sticky top-0 z-20">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-md">
                <i class="fas fa-graduation-cap text-sm"></i>
            </div>
            <div>
                <span class="font-bold text-gray-800 dark:text-gray-100 text-base leading-tight block">Parent Portal</span>
                <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium leading-none">{{ Auth::user()->name ?? 'Parent' }}</span>
            </div>
        </div>
        <div class="flex items-center gap-1">
            <!-- Dark Mode Toggle (Mobile) -->
            <button @click="toggleDark()" class="p-2.5 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors" title="Toggle dark mode">
                <i class="fas text-lg" :class="isDark ? 'fa-sun text-yellow-400' : 'fa-moon'"></i>
            </button>
            <a href="{{ route('parent.notifications') }}" class="relative p-2.5 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                <i class="fas fa-bell text-lg"></i>
                <span id="mobile-notification-badge" class="absolute top-1 right-1 bg-red-500 text-white text-[8px] font-bold rounded-full w-4 h-4 flex items-center justify-center hidden">0</span>
            </a>
            <button @click="sidebarOpen = !sidebarOpen" class="p-2.5 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>
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
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:sticky md:top-0 inset-y-0 left-0 z-40 w-72 h-screen bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col transition-transform duration-300 md:translate-x-0 shadow-2xl md:shadow-none overflow-y-auto sidebar-scroll">
        <div class="flex items-center gap-3 px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 sticky top-0 z-10">
            <div class="w-10 h-10 flex-shrink-0 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30">
                <i class="fas fa-users-cog text-lg"></i>
            </div>
            <div>
                <h1 class="text-blue-900 dark:text-blue-100 font-extrabold text-xl tracking-tight select-none">Parent Portal</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Efees System</p>
            </div>
        </div>

        <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow overflow-y-auto pb-6">
            <p class="px-4 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Menu</p>
            
            <!-- Dashboard Link (Overview) -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.dashboard') && (!isset($selectedChild) || !$selectedChild) ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-bold shadow-sm ring-1 ring-blue-100 dark:ring-blue-800' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}" href="{{ route('parent.dashboard') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('parent.dashboard') && (!isset($selectedChild) || !$selectedChild) ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}"></i>
                </div>
                <span class="text-sm font-medium">Overview</span>
            </a>

            @if(isset($isParent) && $isParent)
            <!-- Payments Link -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.pay') && !request()->routeIs('parent.pay.multi') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-bold shadow-sm ring-1 ring-blue-100 dark:ring-blue-800' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}" href="{{ route('parent.pay') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-credit-card text-lg {{ request()->routeIs('parent.pay') && !request()->routeIs('parent.pay.multi') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}"></i>
                </div>
                <span class="text-sm font-medium">Payments</span>
            </a>

            <!-- Multi-Child Payment Link -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.pay.multi') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-bold shadow-sm ring-1 ring-blue-100 dark:ring-blue-800' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}" href="{{ route('parent.pay.multi') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-users text-lg {{ request()->routeIs('parent.pay.multi') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}"></i>
                </div>
                <span class="text-sm font-medium">Multi-Child Pay</span>
            </a>

            <!-- Payment History Link -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.history') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-bold shadow-sm ring-1 ring-blue-100 dark:ring-blue-800' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}" href="{{ route('parent.history') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-history text-lg {{ request()->routeIs('parent.history') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}"></i>
                </div>
                <span class="text-sm font-medium">Payment History</span>
            </a>

            <!-- Notifications Link -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.notifications') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-bold shadow-sm ring-1 ring-blue-100 dark:ring-blue-800' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}" href="{{ route('parent.notifications') }}">
                <div class="w-8 flex justify-center relative">
                    <i class="fas fa-bell text-lg {{ request()->routeIs('parent.notifications') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}"></i>
                    <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center hidden">0</span>
                </div>
                <span class="text-sm font-medium">Notifications</span>
            </a>
            @endif

            <!-- Student Selector Section -->
            @if(isset($isParent) && $isParent && isset($myChildren) && count($myChildren) > 0)
                <div class="mt-6 px-4 mb-2 flex items-center justify-between">
                     <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">My Students</p>
                     <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 py-0.5 px-2 rounded-full text-[10px] font-bold">{{ count($myChildren) }}</span>
                </div>
                
                <div class="space-y-2 mb-6">
                @foreach($myChildren as $child)
                    <div x-data="{ expanded: {{ (isset($selectedChild) && $selectedChild && $selectedChild->student_id === $child->student_id) ? 'true' : 'false' }} }" class="rounded-xl overflow-hidden transition-all duration-200" :class="expanded ? 'bg-gray-50 dark:bg-gray-700/50' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                        <!-- Student Header (Click to toggle) -->
                        <div @click="expanded = !expanded" class="flex items-center gap-3 px-4 py-2.5 cursor-pointer select-none">
                            <div class="w-8 flex justify-center flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors"
                                     :class="expanded ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300'">
                                    {{ substr($child->first_name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex flex-col min-w-0 flex-1">
                                <span class="text-sm font-medium truncate text-gray-900 dark:text-gray-100">{{ $child->first_name }} {{ $child->last_name }}</span>
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 truncate">{{ $child->student_id }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-gray-400 dark:text-gray-500 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''"></i>
                        </div>

                        <!-- Submenu -->
                        <div x-show="expanded" class="bg-gray-50 dark:bg-gray-700/30 pb-2 space-y-0.5" x-cloak>
                            <!-- Dashboard -->
                            <a href="{{ route('parent.dashboard', ['student_id' => $child->student_id]) }}" 
                               class="flex items-center gap-3 pl-14 pr-4 py-2 text-sm font-medium hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->fullUrlIs(route('parent.dashboard', ['student_id' => $child->student_id])) ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400' }}">
                                <i class="fas fa-chart-pie w-4 text-center text-xs"></i> Overview
                            </a>
                            
                            <!-- Fee Breakdown -->
                            <a href="{{ route('parent.fees.show', $child->student_id) }}" 
                               class="flex items-center gap-3 pl-14 pr-4 py-2 text-sm font-medium hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->routeIs('parent.fees.show') && request()->route('student')->student_id == $child->student_id ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400' }}">
                                <i class="fas fa-file-invoice w-4 text-center text-xs"></i> Fee Breakdown
                            </a>
                            
                            <!-- SOA -->
                            <a href="{{ route('parent.soa', $child->student_id) }}" 
                               class="flex items-center gap-3 pl-14 pr-4 py-2 text-sm font-medium hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->routeIs('parent.soa') && request()->route('student')->student_id == $child->student_id ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400' }}">
                                <i class="fas fa-file-invoice-dollar w-4 text-center text-xs"></i> SOA
                            </a>

                            <!-- Payment Schedule -->
                            <a href="{{ route('parent.schedule', $child->student_id) }}" 
                               class="flex items-center gap-3 pl-14 pr-4 py-2 text-sm font-medium hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->routeIs('parent.schedule') && request()->route('student')->student_id == $child->student_id ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400' }}">
                                <i class="fas fa-calendar-alt w-4 text-center text-xs"></i> Payment Schedule
                            </a>
                            
                            <!-- Pay Now -->
                            <a href="{{ route('parent.pay', ['student_id' => $child->student_id]) }}" 
                               class="flex items-center gap-3 pl-14 pr-4 py-2 text-sm font-medium hover:text-blue-600 dark:hover:text-blue-400 transition-colors text-gray-500 dark:text-gray-400">
                                <i class="fas fa-credit-card w-4 text-center text-xs"></i> Pay Fees
                            </a>
                        </div>
                    </div>
                @endforeach
                </div>
            @endif

            <!-- Dark Mode Toggle (Sidebar) -->
            <div class="px-4 mb-2">
                <button @click="toggleDark()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 group">
                    <div class="w-8 flex justify-center">
                        <i class="fas text-lg text-gray-400 dark:text-gray-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" :class="isDark ? 'fa-sun' : 'fa-moon'"></i>
                    </div>
                    <span class="text-sm font-medium" x-text="isDark ? 'Light Mode' : 'Dark Mode'"></span>
                    <div class="ml-auto w-9 h-5 rounded-full transition-colors duration-200 flex items-center px-0.5" :class="isDark ? 'bg-blue-600' : 'bg-gray-300'">
                        <div class="w-4 h-4 bg-white rounded-full shadow transition-transform duration-200" :class="isDark ? 'translate-x-4' : 'translate-x-0'"></div>
                    </div>
                </button>
            </div>

            <!-- Settings -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.profile.edit') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-bold shadow-sm ring-1 ring-blue-100 dark:ring-blue-800' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}" href="{{ route('parent.profile.edit') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-cog text-lg {{ request()->routeIs('parent.profile.edit') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}"></i>
                </div>
                <span class="text-sm font-medium">Settings</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 px-4 pb-6" onsubmit="return confirmLogout()">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 group">
                    <div class="w-8 flex justify-center">
                        <i class="fas fa-sign-out-alt text-lg text-gray-400 dark:text-gray-500 group-hover:text-red-500 dark:group-hover:text-red-400 transition-colors"></i>
                    </div>
                    <span class="text-sm font-medium">Sign Out</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main id="mainContent" class="flex-1 px-4 py-5 md:p-8 md:h-screen md:overflow-y-auto custom-scrollbar relative pb-24 md:pb-8">

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
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 mb-4 sm:mb-8">
            <div>
                <h1 class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100">Dashboard Overview</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-0.5 sm:mt-1 text-sm">Manage fees and view status for your linked students.</p>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Refresh Button (always visible) -->
                <button id="refreshBtn" onclick="refreshDashboard()" class="inline-flex items-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 text-gray-600 dark:text-g
                ray-300 hover:text-blue-600 dark:hover:text-blue-400 px-3 sm:px-4 py-2 rounded-xl font-medium transition-all text-sm shadow-sm active:bg-blue-100 dark:active:bg-blue-900/50" title="Refresh balances">
                    <i id="refreshIcon" class="fas fa-sync-alt text-sm"></i>
                    <span class="hidden sm:inline">Refresh</span>
                </button>
                <div class="hidden md:flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ Auth::user()->name ?? 'Parent' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold border border-blue-200 dark:border-blue-700">
                        {{ substr(Auth::user()->email, 0, 1) }}
                    </div>
                </div>
            </div>
        </div>

        @if($isParent)
            @if((isset($selectedChild) && $selectedChild) || request('section') == 'students')
                @include('auth.student_dashboard_content')
            @else
            <!-- Financial Overview — Hero Card -->
            @php
                $totalFees = $consolidatedBalanceDue + $consolidatedTotalPaid;
                $overallPct = $totalFees > 0 ? min(round(($consolidatedTotalPaid / $totalFees) * 100), 100) : 0;
                $gaugeCirc = 2 * 3.14159 * 46;
                $gaugeOffset = $gaugeCirc - ($gaugeCirc * $overallPct / 100);
            @endphp
            <div class="summary-hero relative overflow-hidden rounded-xl sm:rounded-2xl mb-4 sm:mb-8" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 35%, #4338ca 65%, #6366f1 100%);">
                <!-- Decorative orbs -->
                <div class="absolute top-0 right-0 w-40 sm:w-72 h-40 sm:h-72 bg-white/[0.04] rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-32 sm:w-56 h-32 sm:h-56 bg-purple-400/[0.08] rounded-full translate-y-1/2 -translate-x-1/4 blur-3xl pointer-events-none"></div>

                <!-- Shimmer sweep -->
                <div class="summary-shimmer absolute inset-0 pointer-events-none"></div>

                <div class="relative z-10 p-4 sm:p-7">
                    <!-- Header row -->
                    <div class="flex items-center justify-between mb-3 sm:mb-6">
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <span class="relative flex h-1.5 w-1.5 sm:h-2 sm:w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 sm:h-2 sm:w-2 bg-emerald-400"></span>
                            </span>
                            <span class="text-indigo-200/80 text-[10px] sm:text-[11px] font-semibold uppercase tracking-[0.15em]">Financial Overview</span>
                        </div>
                        <span class="text-indigo-300/50 text-[10px] sm:text-[11px] font-medium tracking-wide">SY {{ date('Y') }}–{{ date('Y') + 1 }}</span>
                    </div>

                    <!-- Content: always horizontal row -->
                    <div class="flex items-center gap-3 sm:gap-8">
                        <!-- Gauge ring — compact on mobile -->
                        <div class="relative flex-shrink-0 gauge-entrance">
                            <svg viewBox="0 0 120 120" class="w-[72px] h-[72px] sm:w-[130px] sm:h-[130px]">
                                <circle cx="60" cy="60" r="55" fill="none" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
                                <circle cx="60" cy="60" r="46" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="7" stroke-linecap="round"/>
                                <circle cx="60" cy="60" r="46" fill="none" stroke="url(#heroGaugeGrad)" stroke-width="7"
                                    stroke-dasharray="{{ $gaugeCirc }}" stroke-dashoffset="{{ $gaugeOffset }}"
                                    stroke-linecap="round" class="gauge-fill" transform="rotate(-90 60 60)"/>
                                <defs>
                                    <linearGradient id="heroGaugeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#34d399"/>
                                        <stop offset="50%" stop-color="#22d3ee"/>
                                        <stop offset="100%" stop-color="#a78bfa"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-lg sm:text-4xl font-black text-white tracking-tight leading-none" id="overallPct">{{ $overallPct }}</span>
                                <span class="text-[7px] sm:text-[10px] text-indigo-300/80 uppercase font-bold tracking-[0.2em] mt-0.5">% paid</span>
                            </div>
                        </div>

                        <!-- Stats column — tighter on mobile -->
                        <div class="flex-1 min-w-0 space-y-1.5 sm:space-y-3">
                            <!-- Balance Due & Total Paid — inline on mobile, stacked on sm+ -->
                            <div class="grid grid-cols-2 sm:grid-cols-1 gap-1.5 sm:gap-3">
                                <div class="bg-white/[0.07] backdrop-blur-sm rounded-lg sm:rounded-xl px-2.5 py-2 sm:px-4 sm:py-3.5 border border-white/[0.08]">
                                    <p class="text-indigo-300/70 text-[8px] sm:text-[10px] font-semibold uppercase tracking-[0.12em] mb-0.5 leading-tight">Balance Due</p>
                                    <p id="consolidatedBalanceDue" class="text-sm sm:text-2xl font-black text-white truncate tracking-tight leading-snug">₱{{ number_format($consolidatedBalanceDue, 2) }}</p>
                                </div>
                                <div class="bg-white/[0.07] backdrop-blur-sm rounded-lg sm:rounded-xl px-2.5 py-2 sm:px-4 sm:py-3.5 border border-white/[0.08]">
                                    <p class="text-indigo-300/70 text-[8px] sm:text-[10px] font-semibold uppercase tracking-[0.12em] mb-0.5 leading-tight">Total Paid</p>
                                    <p id="consolidatedTotalPaid" class="text-sm sm:text-2xl font-black text-emerald-400 truncate tracking-tight leading-snug">₱{{ number_format($consolidatedTotalPaid, 2) }}</p>
                                </div>
                            </div>

                            <!-- Gradient progress bar -->
                            <div class="pt-0 sm:pt-1">
                                <div class="flex items-center justify-between text-[9px] sm:text-[11px] mb-1">
                                    <span class="text-indigo-200/60 font-medium">₱{{ number_format($consolidatedTotalPaid, 2) }} of ₱{{ number_format($totalFees, 2) }}</span>
                                    <span class="text-indigo-300/40 font-medium hidden sm:inline">total fees</span>
                                </div>
                                <div class="w-full bg-white/[0.08] rounded-full h-1 sm:h-[7px] overflow-hidden">
                                    <div class="h-full rounded-full progress-bar-glow" style="width: {{ $overallPct }}%; background: linear-gradient(90deg, #34d399, #22d3ee, #a78bfa);"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Children List -->
            <h2 id="students" class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 sm:mb-6 flex items-center gap-2 pt-2 sm:pt-4">
                <i class="fas fa-child text-blue-600 dark:text-blue-400"></i>
                Your Students
            </h2>

            @if($childrenSummaries->count() > 0)
                <div id="childrenContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8">
                    @foreach($childrenSummaries as $child)
                        <div id="child-card-{{ $child['student_id'] }}" class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Card Header -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-2.5 sm:px-6 sm:py-4 border-b border-gray-100 dark:border-gray-600 flex justify-between items-center">
                                <div class="min-w-0">
                                    <h3 class="font-bold text-sm sm:text-lg text-gray-900 dark:text-gray-100 truncate">{{ $child['full_name'] }}</h3>
                                    <p class="text-[11px] sm:text-sm text-gray-500 dark:text-gray-400">
                                        {{ $child['level'] ?? 'N/A' }} • {{ $child['section'] ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="h-8 w-8 sm:h-10 sm:w-10 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold text-xs sm:text-sm border border-gray-200 dark:border-gray-500 flex-shrink-0">
                                    {{ substr($child['first_name'], 0, 1) }}{{ substr($child['last_name'], 0, 1) }}
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-3 sm:p-6">
                                <!-- Payment Progress Ring -->
                                @php
                                    $totalAmt = $child['totalAmount'] ?? 0;
                                    $paidPct = $totalAmt > 0 ? round(($child['totalPaid'] / $totalAmt) * 100) : 0;
                                    $paidPct = min($paidPct, 100);
                                    $circumference = 2 * 3.14159 * 40;
                                    $dashOffset = $circumference - ($circumference * $paidPct / 100);
                                    $ringColor = $paidPct >= 100 ? '#16a34a' : ($paidPct >= 50 ? '#2563eb' : ($paidPct >= 25 ? '#f59e0b' : '#ef4444'));
                                @endphp
                                <div class="flex items-center gap-3 sm:gap-5 mb-3 sm:mb-5 p-2.5 sm:p-4 bg-gradient-to-r from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800 rounded-lg sm:rounded-xl border border-gray-100 dark:border-gray-600">
                                    <div class="relative flex-shrink-0" id="child-ring-{{ $child['student_id'] }}">
                                        <svg width="80" height="80" viewBox="0 0 96 96" class="transform -rotate-90 w-[56px] h-[56px] sm:w-[80px] sm:h-[80px]">
                                            <circle cx="48" cy="48" r="40" fill="none" stroke="currentColor" stroke-width="7" class="text-gray-200 dark:text-gray-600"/>
                                            <circle cx="48" cy="48" r="40" fill="none" stroke="{{ $ringColor }}" stroke-width="7"
                                                stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $dashOffset }}"
                                                stroke-linecap="round" class="transition-all duration-700"/>
                                        </svg>
                                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                                            <span class="text-xs sm:text-lg font-bold text-gray-900 dark:text-gray-100" id="child-pct-{{ $child['student_id'] }}">{{ $paidPct }}%</span>
                                            <span class="text-[8px] sm:text-[10px] text-gray-400 dark:text-gray-500 uppercase font-semibold tracking-wider">paid</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mb-0.5 sm:mb-1">Total Tuition</p>
                                        <p class="text-sm sm:text-base font-bold text-gray-900 dark:text-gray-100 mb-1.5 sm:mb-2" id="child-total-{{ $child['student_id'] }}">₱{{ number_format($totalAmt, 2) }}</p>
                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 sm:h-2">
                                            <div class="h-1.5 sm:h-2 rounded-full transition-all duration-700" style="width: {{ $paidPct }}%; background-color: {{ $ringColor }};"></div>
                                        </div>
                                        <div class="flex justify-between text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5 sm:mt-1">
                                            <span>₱{{ number_format($child['totalPaid'], 2) }} paid</span>
                                            <span>₱{{ number_format($child['balanceDue'], 2) }} left</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 sm:gap-4 mb-3 sm:mb-5">
                                    <div class="bg-red-50 dark:bg-red-900/20 p-2 sm:p-4 rounded-lg sm:rounded-xl border border-red-100 dark:border-red-800/30">
                                        <p class="text-[10px] sm:text-xs text-red-600 dark:text-red-400 uppercase font-bold tracking-wide mb-0.5">Balance</p>
                                        <p id="child-balance-{{ $child['student_id'] }}" class="text-base sm:text-xl font-bold text-red-700 dark:text-red-400">₱{{ number_format($child['balanceDue'], 2) }}</p>
                                    </div>
                                    <div class="bg-green-50 dark:bg-green-900/20 p-2 sm:p-4 rounded-lg sm:rounded-xl border border-green-100 dark:border-green-800/30">
                                        <p class="text-[10px] sm:text-xs text-green-600 dark:text-green-400 uppercase font-bold tracking-wide mb-0.5">Paid</p>
                                        <p id="child-paid-{{ $child['student_id'] }}" class="text-base sm:text-xl font-bold text-green-700 dark:text-green-400">₱{{ number_format($child['totalPaid'], 2) }}</p>
                                    </div>
                                </div>

                                <!-- Upcoming Fees -->
                                <div class="mb-3 sm:mb-6">
                                    <h4 class="text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 sm:mb-3 uppercase tracking-wide">Upcoming / Unpaid Fees</h4>
                                    <div id="child-fees-{{ $child['student_id'] }}" class="space-y-2 sm:space-y-3">
                                        @if(count($child['upcomingFees']) > 0)
                                            @foreach($child['upcomingFees'] as $fee)
                                                @php
                                                    $isOverdue = $fee->payment_date && $fee->payment_date->isPast() && ($fee->status !== 'paid');
                                                    $daysUntil = $fee->payment_date ? (int) now()->startOfDay()->diffInDays($fee->payment_date->startOfDay(), false) : null;
                                                @endphp
                                                <div class="flex justify-between items-center gap-2 sm:gap-4 p-2 sm:p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border {{ $isOverdue ? 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20' : 'border-gray-100 dark:border-gray-600' }}">
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                                            <p class="font-medium text-gray-800 dark:text-gray-200 text-xs sm:text-sm truncate">{{ $fee['notes'] ?? 'Fee' }}</p>
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
                                                        <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">
                                                            Due: {{ $fee->payment_date ? $fee->payment_date->format('M d, Y') : 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <span class="{{ $isOverdue ? 'text-red-700 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }} font-bold text-xs sm:text-sm whitespace-nowrap">₱{{ number_format((float) $fee->balance, 2) }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">No pending fees.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <a href="{{ route('parent.pay', ['student_id' => $child['student_id'], 'pay_full' => 1]) }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 sm:py-3 rounded-lg sm:rounded-xl transition-colors shadow-sm shadow-blue-200 text-sm sm:text-base">
                                    Pay Now
                                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                        <i class="fas fa-child text-gray-400 dark:text-gray-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">No Students Linked</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-md mx-auto">
                        There are no students linked to your parent account yet. Please contact the school administration to link your children.
                    </p>
                </div>
            @endif
            @endif
        @else
            <!-- Fallback for non-parent users visiting this page -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400 dark:text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
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
    <div id="fabPay" class="fab-pay fixed bottom-20 right-4 z-30 md:hidden no-print" x-data="{ expanded: false }">
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
            <a href="{{ route('parent.pay') }}" class="flex items-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 pl-4 pr-3 py-2.5 rounded-full shadow-lg hover:shadow-xl transition-all whitespace-nowrap">
                <span class="text-sm font-medium">Single Payment</span>
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center">
                    <i class="fas fa-credit-card text-blue-600 dark:text-blue-400 text-xs"></i>
                </div>
            </a>
            <!-- Multi-Child Payment -->
            <a href="{{ route('parent.pay.multi') }}" class="flex items-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 pl-4 pr-3 py-2.5 rounded-full shadow-lg hover:shadow-xl transition-all whitespace-nowrap">
                <span class="text-sm font-medium">Multi-Child Pay</span>
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-green-600 dark:text-green-400 text-xs"></i>
                </div>
            </a>
            <!-- Payment History -->
            <a href="{{ route('parent.history') }}" class="flex items-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 pl-4 pr-3 py-2.5 rounded-full shadow-lg hover:shadow-xl transition-all whitespace-nowrap">
                <span class="text-sm font-medium">History</span>
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/40 rounded-full flex items-center justify-center">
                    <i class="fas fa-history text-purple-600 dark:text-purple-400 text-xs"></i>
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

    <!-- Mobile Bottom Navigation Bar -->
    @if(isset($isParent) && $isParent)
    <nav class="mobile-bottom-nav md:hidden fixed bottom-0 left-0 right-0 z-30 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 no-print" style="box-shadow: 0 -1px 12px rgba(0,0,0,0.06);">
        <div class="flex items-center justify-around px-1 pt-1.5 pb-1">
            <!-- Dashboard -->
            <a href="{{ route('parent.dashboard') }}" class="nav-item flex flex-col items-center gap-0.5 min-w-0 px-2 py-1 rounded-lg {{ request()->routeIs('parent.dashboard') && (!isset($selectedChild) || !$selectedChild) ? 'active' : 'text-gray-400 dark:text-gray-500' }}">
                <div class="nav-icon">
                    <i class="fas fa-home text-[17px]"></i>
                </div>
                <span class="text-[10px] font-semibold leading-tight">Home</span>
            </a>

            <!-- Pay -->
            <a href="{{ route('parent.pay') }}" class="nav-item flex flex-col items-center gap-0.5 min-w-0 px-2 py-1 rounded-lg {{ request()->routeIs('parent.pay') || request()->routeIs('parent.pay.multi') ? 'active' : 'text-gray-400 dark:text-gray-500' }}">
                <div class="nav-icon">
                    <i class="fas fa-credit-card text-[17px]"></i>
                </div>
                <span class="text-[10px] font-semibold leading-tight">Pay</span>
            </a>

            <!-- History -->
            <a href="{{ route('parent.history') }}" class="nav-item flex flex-col items-center gap-0.5 min-w-0 px-2 py-1 rounded-lg {{ request()->routeIs('parent.history') ? 'active' : 'text-gray-400 dark:text-gray-500' }}">
                <div class="nav-icon">
                    <i class="fas fa-receipt text-[17px]"></i>
                </div>
                <span class="text-[10px] font-semibold leading-tight">History</span>
            </a>

            <!-- Notifications -->
            <a href="{{ route('parent.notifications') }}" class="nav-item flex flex-col items-center gap-0.5 min-w-0 px-2 py-1 rounded-lg relative {{ request()->routeIs('parent.notifications') ? 'active' : 'text-gray-400 dark:text-gray-500' }}">
                <div class="nav-icon relative">
                    <i class="fas fa-bell text-[17px]"></i>
                    <span id="bottomnav-notification-badge" class="absolute -top-1.5 -right-2 bg-red-500 text-white text-[8px] font-bold rounded-full w-3.5 h-3.5 flex items-center justify-center hidden">0</span>
                </div>
                <span class="text-[10px] font-semibold leading-tight">Alerts</span>
            </a>

            <!-- Profile / Settings -->
            <a href="{{ route('parent.profile.edit') }}" class="nav-item flex flex-col items-center gap-0.5 min-w-0 px-2 py-1 rounded-lg {{ request()->routeIs('parent.profile.edit') ? 'active' : 'text-gray-400 dark:text-gray-500' }}">
                <div class="nav-icon">
                    <i class="fas fa-user-circle text-[17px]"></i>
                </div>
                <span class="text-[10px] font-semibold leading-tight">Profile</span>
            </a>
        </div>
    </nav>
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

            const isDark = document.documentElement.classList.contains('dark');
            const toast = document.createElement('div');
            toast.id = 'refreshToast';
            toast.className = `fixed top-4 left-1/2 -translate-x-1/2 z-50 px-5 py-2.5 rounded-full shadow-lg text-sm font-medium flex items-center gap-2 transition-all duration-300 ${
                isError ? 'bg-red-600 text-white' : (isDark ? 'bg-gray-700 text-white' : 'bg-gray-900 text-white')
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
            const pctEl = document.getElementById('overallPct');
            
            if (balanceEl) balanceEl.innerText = '₱' + Number(data.consolidatedBalanceDue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            if (paidEl) paidEl.innerText = '₱' + Number(data.consolidatedTotalPaid).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            // Update hero gauge
            const totalFees = Number(data.consolidatedBalanceDue) + Number(data.consolidatedTotalPaid);
            const newPct = totalFees > 0 ? Math.min(Math.round((data.consolidatedTotalPaid / totalFees) * 100), 100) : 0;
            if (pctEl) pctEl.innerText = newPct;
            const heroGauge = document.querySelector('.gauge-fill');
            if (heroGauge) {
                const circ = 2 * Math.PI * 46;
                heroGauge.setAttribute('stroke-dashoffset', circ - (circ * newPct / 100));
            }
            const heroBar = document.querySelector('.progress-bar-glow');
            if (heroBar) heroBar.style.width = newPct + '%';

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
                        const badges = [
                            document.getElementById('notification-badge'),
                            document.getElementById('mobile-notification-badge'),
                            document.getElementById('bottomnav-notification-badge')
                        ];
                        badges.forEach(badge => {
                            if (badge) {
                                if (data.count > 0) {
                                    badge.textContent = data.count > 99 ? '99+' : data.count;
                                    badge.classList.remove('hidden');
                                } else {
                                    badge.classList.add('hidden');
                                }
                            }
                        });
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

    // Dark Mode Alpine Component
    function darkMode() {
        return {
            isDark: localStorage.getItem('efees-dark-mode') === 'true' || 
                    (!localStorage.getItem('efees-dark-mode') && window.matchMedia('(prefers-color-scheme: dark)').matches),
            toggleDark() {
                this.isDark = !this.isDark;
                localStorage.setItem('efees-dark-mode', this.isDark);
            }
        }
    }
    </script>
</body>
</html>
