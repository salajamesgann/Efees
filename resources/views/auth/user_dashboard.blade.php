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
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.pay') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-gray-600 hover:bg-gray-50' }}" href="{{ route('parent.pay') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-credit-card text-lg {{ request()->routeIs('parent.pay') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                </div>
                <span class="text-sm font-medium">Payments</span>
            </a>

            <!-- Payment History Link -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('parent.history') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-gray-600 hover:bg-gray-50' }}" href="{{ route('parent.history') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-history text-lg {{ request()->routeIs('parent.history') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600' }}"></i>
                </div>
                <span class="text-sm font-medium">Payment History</span>
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
            <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 px-4 pb-6">
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
    <main class="flex-1 p-6 md:p-8 overflow-y-auto custom-scrollbar">
        
        @hasSection('content')
            @yield('content')
        @else
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
                <p class="text-gray-600 mt-1">Manage fees and view status for your linked students.</p>
            </div>
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

                                <!-- Upcoming Fees -->
                                <div class="mb-6">
                                    <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Upcoming / Unpaid Fees</h4>
                                    <div id="child-fees-{{ $child['student_id'] }}" class="space-y-3">
                                        @if(count($child['upcomingFees']) > 0)
                                            @foreach($child['upcomingFees'] as $fee)
                                                @php
                                                    $isOverdue = $fee->payment_date && $fee->payment_date->isPast() && ($fee->status !== 'paid');
                                                @endphp
                                                <div class="flex justify-between items-center gap-4 p-3 bg-gray-50 rounded-lg border {{ $isOverdue ? 'border-red-300 bg-red-50' : 'border-gray-100' }}">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <p class="font-medium text-gray-800 text-sm">{{ $fee['notes'] ?? 'Fee' }}</p>
                                                            @if($isOverdue)
                                                                <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wider border border-red-200">Overdue</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-gray-500">
                                                            Due: {{ $fee->payment_date ? $fee->payment_date->format('M d, Y') : 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <span class="{{ $isOverdue ? 'text-red-700' : 'text-gray-700' }} font-bold text-sm">₱{{ number_format((float) $fee->balance, 2) }}</span>
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


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const POLL_INTERVAL = 30000; // 30 seconds

            function fetchMetrics() {
                fetch('{{ route("parent.metrics") }}')
                    .then(response => response.json())
                    .then(data => {
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
                                
                                if (childFeesContainer && child.upcomingFees) {
                                    if (child.upcomingFees.length > 0) {
                                        childFeesContainer.innerHTML = child.upcomingFees.map(fee => {
                                            const overdueClass = fee.isOverdue ? 'border-red-300 bg-red-50' : 'border-gray-100';
                                            const amountClass = fee.isOverdue ? 'text-red-700' : 'text-gray-700';
                                            const badge = fee.isOverdue 
                                                ? `<span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wider border border-red-200">Overdue</span>` 
                                                : '';
                                            
                                            return `
                                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border ${overdueClass}">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <p class="font-medium text-gray-800 text-sm">${fee.notes}</p>
                                                            ${badge}
                                                        </div>
                                                        <p class="text-xs text-gray-500">
                                                            Due: ${fee.payment_date}
                                                        </p>
                                                    </div>
                                                    <span class="${amountClass} font-bold text-sm">₱${Number(fee.balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                                </div>
                                            `;
                                        }).join('');
                                    } else {
                                        childFeesContainer.innerHTML = `<p class="text-sm text-gray-500 italic">No pending fees.</p>`;
                                    }
                                }
                            });
                        }
                    })
                    .catch(err => console.error('Error fetching parent metrics:', err));
            }

            // Start Polling
            setInterval(window.fetchMetrics, POLL_INTERVAL);
        });
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Supabase Realtime -->
    @include('partials.supabase_realtime')
</body>
</html>
