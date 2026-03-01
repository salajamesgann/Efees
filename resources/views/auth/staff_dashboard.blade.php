<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Efees Staff Dashboard</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(37, 99, 235, 0.25); }
        .badge-status { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; }
        .badge-paid { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-partial { background-color: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .badge-unpaid { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .scrollbar-thin { scrollbar-width: thin; }
        
        /* Custom Sidebar Scrollbar */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        /* Custom Main Content Scrollbar */
        .main-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .main-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .main-scrollbar::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 10px;
        }
        .main-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    <style>[x-cloak]{display:none!important}</style>
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
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 md:hidden" x-cloak></div>
    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 z-40 w-72 bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 md:translate-x-0 overflow-y-auto sidebar-scrollbar shadow-2xl md:shadow-none">
        <div class="flex items-center justify-between gap-3 px-8 py-6 border-b border-gray-200 bg-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <div>
                    <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Efees Staff</h1>
                    <p class="text-xs text-slate-500 font-medium">Staff Panel</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow pb-6">
            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff_dashboard') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff_dashboard') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('staff_dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Student Records</span>
            </a>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff.payment_processing') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff.payment_processing') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-credit-card text-lg {{ request()->routeIs('staff.payment_processing') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Payment Processing</span>
            </a>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff.sms_reminders') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff.sms_reminders') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-sms text-lg {{ request()->routeIs('staff.sms_reminders') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">SMS Reminders</span>
            </a>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff.reports') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff.reports') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-chart-line text-lg {{ request()->routeIs('staff.reports') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Reports</span>
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirmLogout()">
                @csrf
                <button class="w-full flex items-center gap-3 bg-blue-600 text-white font-bold text-sm rounded-lg h-10 justify-center cursor-pointer transition-colors duration-300 hover:bg-blue-700" type="submit">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-8 overflow-y-auto main-scrollbar">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-gray-900">Student Records</h1>
                @if(isset($activeYear))
                    <p class="text-xs text-gray-500 mt-1">
                        Active School Year: <span class="font-semibold">{{ $activeYear }}</span>
                    </p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <!-- Notification Bell -->
                <div x-data="{ open: false }" class="relative mr-4">
                    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fas fa-bell text-2xl"></i>
                        @if(isset($notifications) && count($notifications) > 0)
                            <span class="absolute top-1 right-1 h-3 w-3 bg-red-500 rounded-full ring-2 ring-white"></span>
                        @endif
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         @click.away="open = false" 
                         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden" 
                         x-cloak>
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @if(isset($notifications) && count($notifications) > 0)
                                @foreach($notifications as $notification)
                                    <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors last:border-0">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 mt-1">
                                                @if(str_contains(strtolower($notification->title), 'reject'))
                                                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                                @elseif(str_contains(strtolower($notification->title), 'approv'))
                                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                @else
                                                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $notification->title }}</p>
                                                <p class="text-xs text-gray-500 mt-1 break-words">{{ $notification->body }}</p>
                                                <p class="text-[10px] text-gray-400 mt-2">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-8 text-center text-gray-500 text-sm">
                                    <i class="fas fa-bell-slash text-2xl mb-2 text-gray-300"></i>
                                    <p>No notifications</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-sm font-semibold text-blue-600">
                        {{ optional(Auth::user()->roleable)->full_name ?? 'Staff Member' }}
                    </p>
                    <p class="text-xs text-gray-600">{{ Auth::user()->email }}</p>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">Staff</span>
                </div>
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-lg">
                    {{ optional(Auth::user()->roleable)->initials ?? 'S' }}
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 border border-green-200 text-green-800 bg-green-50 rounded-md px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 border border-red-200 text-red-800 bg-red-50 rounded-md px-4 py-3">
                {{ session('error') }}
            </div>
        @endif

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Paid -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between group hover:border-green-200 hover:shadow-md transition-all duration-300">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fully Paid</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['paid'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 font-medium mt-1 flex items-center gap-1">
                        <i class="fas fa-check text-[10px]"></i>
                        <span>Cleared</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center text-green-600 group-hover:scale-110 transition-transform duration-300 shadow-sm shadow-green-100">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>

            <!-- Partially Paid -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between group hover:border-amber-200 hover:shadow-md transition-all duration-300">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Partially Paid</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['partial'] ?? 0 }}</p>
                    <p class="text-xs text-amber-600 font-medium mt-1 flex items-center gap-1">
                        <i class="fas fa-clock text-[10px]"></i>
                        <span>Pending Balance</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform duration-300 shadow-sm shadow-amber-100">
                    <i class="fas fa-adjust text-xl"></i>
                </div>
            </div>

            <!-- Unpaid -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between group hover:border-red-200 hover:shadow-md transition-all duration-300">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Unpaid</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['unpaid'] ?? 0 }}</p>
                    <p class="text-xs text-red-600 font-medium mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle text-[10px]"></i>
                        <span>Action Needed</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center text-red-600 group-hover:scale-110 transition-transform duration-300 shadow-sm shadow-red-100">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-8 overflow-hidden" x-data="{ showFilters: {{ request()->anyFilled(['level', 'strand', 'section', 'status']) ? 'true' : 'false' }} }">
            <form method="GET" action="{{ route('staff_dashboard') }}">
                <div class="px-6 py-4 flex flex-col md:flex-row md:items-center gap-4">
                    <!-- Search Bar (Always Visible) -->
                    <div class="flex-1 relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                            <i class="fas fa-search"></i>
                        </div>
                        <input id="search" name="q" value="{{ $query ?? '' }}" placeholder="Search by student name or ID..." type="search"
                               class="block w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-11 pr-4 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"/>
                    </div>

                    <!-- Filter Toggle & Actions -->
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showFilters = !showFilters" 
                                :class="showFilters ? 'bg-blue-50 text-blue-600 ring-1 ring-blue-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">
                            <i class="fas fa-filter"></i>
                            <span>Filters</span>
                            @if(request()->anyFilled(['level', 'strand', 'section', 'status']))
                                <span class="bg-blue-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ml-1">
                                    {{ collect(request()->only(['level', 'strand', 'section', 'status']))->filter()->count() }}
                                </span>
                            @endif
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="showFilters ? 'rotate-180' : ''"></i>
                        </button>
                        
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-blue-200 transition-all">
                            Search
                        </button>
                    </div>
                </div>

                <!-- Expanded Filters -->
                <div x-show="showFilters" x-collapse x-cloak class="border-t border-slate-100 bg-slate-50/50 px-6 py-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Level -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Level</label>
                            <div class="relative">
                                <select id="level" name="level"
                                        class="block w-full rounded-xl border-slate-200 bg-white py-2 px-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm appearance-none cursor-pointer">
                                    <option value="">All Levels</option>
                                    @foreach(($levels ?? []) as $lvl)
                                        <option value="{{ $lvl }}" {{ ($level ?? '') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Strand -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Strand</label>
                            <div class="relative">
                                <select id="strand" name="strand"
                                        class="block w-full rounded-xl border-slate-200 bg-white py-2 px-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm appearance-none cursor-pointer">
                                    <option value="">All Strands</option>
                                    @foreach(($strands ?? []) as $str)
                                        <option value="{{ $str }}" {{ ($strand ?? '') === $str ? 'selected' : '' }}>{{ $str }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Section -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Section</label>
                            <div class="relative">
                                <select id="section" name="section"
                                        class="block w-full rounded-xl border-slate-200 bg-white py-2 px-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm appearance-none cursor-pointer">
                                    <option value="">All Sections</option>
                                    @foreach(($sections ?? []) as $sec)
                                        <option value="{{ $sec }}" {{ ($section ?? '') === $sec ? 'selected' : '' }}>{{ $sec }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status</label>
                            <div class="relative">
                                <select id="status" name="status"
                                        class="block w-full rounded-xl border-slate-200 bg-white py-2 px-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm appearance-none cursor-pointer">
                                    <option value="">All Statuses</option>
                                    <option value="paid" {{ ($status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="partially-paid" {{ ($status ?? '') === 'partially-paid' ? 'selected' : '' }}>Partially paid</option>
                                    <option value="unpaid" {{ ($status ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('staff_dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors">
                            Reset Filters
                        </a>
                    </div>
                </div>
            </form>
        </section>

        <!-- Student Table -->
        <section class="bg-white rounded-2xl shadow-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Student Fee Overview</h2>
                    <p class="text-sm text-gray-500">Detailed balances and payment status per student</p>
                </div>
                <span class="text-sm text-gray-500">Last updated {{ now()->format('M d, Y') }}</span>
            </div>

            <div class="overflow-x-auto scrollbar-thin">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-gray-600 uppercase text-xs tracking-wide">
                            <th class="px-6 py-3 font-semibold">
                                <a href="{{ route('staff_dashboard', array_merge(request()->query(), ['sort' => 'name', 'direction' => (request('sort') === 'name' && request('direction') !== 'desc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600">
                                    Student
                                    @if(($sort ?? '') === 'name')
                                        <i class="fas fa-sort-{{ ($direction ?? 'asc') === 'desc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold">Level</th>
                            <th class="px-6 py-3 font-semibold">
                                <a href="{{ route('staff_dashboard', array_merge(request()->query(), ['sort' => 'section', 'direction' => (request('sort') === 'section' && request('direction') !== 'desc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600">
                                    Section
                                    @if(($sort ?? '') === 'section')
                                        <i class="fas fa-sort-{{ ($direction ?? 'asc') === 'desc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold text-right">Total Fee</th>
                            <th class="px-6 py-3 font-semibold text-right">
                                <a href="{{ route('staff_dashboard', array_merge(request()->query(), ['sort' => 'paid', 'direction' => (request('sort') === 'paid' && request('direction') !== 'desc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600">
                                    Paid
                                    @if(($sort ?? '') === 'paid')
                                        <i class="fas fa-sort-{{ ($direction ?? 'asc') === 'desc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold text-right">
                                <a href="{{ route('staff_dashboard', array_merge(request()->query(), ['sort' => 'due', 'direction' => (request('sort') === 'due' && request('direction') !== 'desc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600">
                                    Outstanding
                                    @if(($sort ?? '') === 'due')
                                        <i class="fas fa-sort-{{ ($direction ?? 'asc') === 'desc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold text-center">
                                <a href="{{ route('staff_dashboard', array_merge(request()->query(), ['sort' => 'status', 'direction' => (request('sort') === 'status' && request('direction') !== 'desc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600">
                                    Status
                                    @if(($sort ?? '') === 'status')
                                        <i class="fas fa-sort-{{ ($direction ?? 'asc') === 'desc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold">
                                <a href="{{ route('staff_dashboard', array_merge(request()->query(), ['sort' => 'latest_payment', 'direction' => (request('sort') === 'latest_payment' && request('direction') !== 'desc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600">
                                    Last Payment
                                    @if(($sort ?? '') === 'latest_payment')
                                        <i class="fas fa-sort-{{ ($direction ?? 'asc') === 'desc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" id="students-table-body">
                        @forelse ($studentRecords as $record)
                            <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $record->student->full_name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $record->student->student_id }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $record->student->level }}
                                    @if(in_array($record->student->level, ['Grade 11', 'Grade 12']) && $record->student->strand)
                                        <span class="text-xs text-gray-500 block">({{ $record->student->strand }})</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600 student-section">
                                    {{ $record->student->section ?: '—' }}
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-900">₱{{ number_format($record->totalFee, 2) }}</td>
                                <td class="px-6 py-4 text-right text-green-600 font-semibold whitespace-nowrap">₱{{ number_format($record->paidAmount, 2) }}</td>
                                <td class="px-6 py-4 text-right {{ $record->dueAmount > 0 ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold' }} whitespace-nowrap">
                                    ₱{{ number_format($record->dueAmount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $badgeClass = [
                                            'paid' => 'badge-status badge-paid',
                                            'partially-paid' => 'badge-status badge-partial',
                                            'unpaid' => 'badge-status badge-unpaid'
                                        ][$record->status] ?? 'badge-status badge-unpaid';
                                    @endphp
                                    <span class="{{ $badgeClass }}">
                                        <span class="w-2 h-2 rounded-full bg-current"></span>
                                        {{ $record->statusText }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    @if($record->latestTransaction)
                                        <div class="flex flex-col gap-1">
                                            <span class="font-medium text-gray-900">{{ $record->latestTransaction->created_at->format('M d, Y') }}</span>
                                            @if($record->latestTransaction->status === 'pending')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 w-fit">Pending</span>
                                            @elseif($record->latestTransaction->status === 'approved')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">Approved</span>
                                            @elseif($record->latestTransaction->status === 'rejected')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit" title="{{ $record->latestTransaction->remarks }}">Rejected</span>
                                            @endif
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('staff.student_details', $record->student) }}" class="inline-flex items-center gap-1 text-blue-600 font-semibold hover:text-blue-700">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    @if($query || $status)
                                        No student records match your filter.
                                    @else
                                        No student records found. Please add students in the admin panel.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($studentRecords->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $studentRecords->appends(request()->query())->links() }}
                </div>
            @endif
        </section>
    </main>

    <!-- Toast container -->
    <div id="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem;"></div>

    <!-- Supabase Realtime Notifications -->
    <script>
        window.SUPABASE_URL = "{{ env('SUPABASE_URL', '') }}";
        window.SUPABASE_ANON_KEY = "{{ env('SUPABASE_ANON_KEY', '') }}";
        window.AUTH_USER_ID = {{ Auth::user()->user_id ?? 'null' }};
    </script>
    <script type="module">
        import { createClient } from 'https://esm.sh/@supabase/supabase-js@2';

        const url = window.SUPABASE_URL;
        const anon = window.SUPABASE_ANON_KEY;
        const authUserId = window.AUTH_USER_ID;

        const supabase = (url && anon) ? createClient(url, anon) : null;

        const toastContainer = document.getElementById('toast-container');

        function showToast(title, body, ts) {
            if (!toastContainer) return;
            const wrap = document.createElement('div');
            wrap.style.background = '#ffffff';
            wrap.style.border = '1px solid #e2e8f0';
            wrap.style.color = '#334155';
            wrap.style.padding = '0.75rem 1rem';
            wrap.style.borderRadius = '0.5rem';
            wrap.style.boxShadow = '0 6px 16px rgba(0,0,0,0.08)';
            wrap.style.minWidth = '260px';
            const when = ts ? new Date(ts).toLocaleString() : new Date().toLocaleString();
            wrap.innerHTML = `<div style="font-weight:700;color:#2563eb;margin-bottom:4px;">${title}</div><div>${body}</div><div style="margin-top:6px;font-size:12px;color:#64748b;">Updated at ${when}</div>`;
            toastContainer.appendChild(wrap);
            setTimeout(() => { wrap.remove(); }, 6000);
        }

        async function initRealtime() {
            if (!supabase || !authUserId) {
                console.warn('Realtime not initialized: missing client or user id');
                return;
            }
            const channel = supabase.channel(`notifications-${authUserId}`);
            channel.on(
                'postgres_changes',
                { event: 'INSERT', schema: 'public', table: 'notifications', filter: `user_id=eq.${authUserId}` },
                (payload) => {
                    const n = payload.new || {};
                    showToast(n.title || 'Notification', n.body || '', n.created_at);
                }
            );
            channel.subscribe((status) => {
                if (status === 'SUBSCRIBED') {
                    console.log('Realtime: subscribed to notifications for', authUserId);
                } else {
                    showToast('Connection Issue', 'Realtime subscription not active.', null);
                }
            });
        }

        initRealtime();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search');
            const statusSelect = document.getElementById('status');
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenuDropdown = document.getElementById('user-menu-dropdown');

            if (userMenuButton && userMenuDropdown) {
                userMenuButton.addEventListener('click', function() {
                    userMenuDropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function(event) {
                    if (!userMenuButton.contains(event.target) && !userMenuDropdown.contains(event.target)) {
                        userMenuDropdown.classList.add('hidden');
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('keyup', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        event.target.form.submit();
                    }
                });
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', () => statusSelect.form.submit());
            }

            // Real-time updates for student list
            function fetchStudentList() {
                const urlParams = new URLSearchParams(window.location.search);
                fetch('{{ route("staff_dashboard.list") }}?' + urlParams.toString())
                    .then(response => response.json())
                    .then(data => {
                        updateStudentTable(data.studentRecords.data);
                        const timestampEl = document.getElementById('last-updated-timestamp');
                        if (timestampEl) {
                            timestampEl.textContent = 'Last updated ' + new Date().toLocaleString();
                        }
                    })
                    .catch(error => console.error('Error fetching student list:', error));
            }

            function updateStudentTable(records) {
                const tbody = document.getElementById('students-table-body');
                if (!tbody) return;

                if (records.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                No student records match your filter.
                            </td>
                        </tr>`;
                    return;
                }

                tbody.innerHTML = records.map(record => {
                    const badgeClass = {
                        'paid': 'badge-status badge-paid',
                        'partially-paid': 'badge-status badge-partial',
                        'unpaid': 'badge-status badge-unpaid'
                    }[record.status] || 'badge-status badge-unpaid';

                    const latestPayment = record.latestTransaction 
                        ? (() => {
                            const date = new Date(record.latestTransaction.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
                            let badge = '';
                            if (record.latestTransaction.status === 'pending') {
                                badge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 w-fit">Pending</span>';
                            } else if (record.latestTransaction.status === 'approved') {
                                badge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">Approved</span>';
                            } else if (record.latestTransaction.status === 'rejected') {
                                badge = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit" title="${record.latestTransaction.remarks || ''}">Rejected</span>`;
                            }
                            return `<div class="flex flex-col gap-1"><span class="font-medium text-gray-900">${date}</span>${badge}</div>`;
                        })()
                        : '—';

                    const dueClass = record.dueAmount > 0 ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold';

                    return `
                        <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">${record.student.first_name} ${record.student.last_name}</p>
                                <p class="text-xs text-gray-500">ID: ${record.student.student_id}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                ${record.student.level}
                                ${['Grade 11', 'Grade 12'].includes(record.student.level) && record.student.strand ? `<span class="text-xs text-gray-500 block">(${record.student.strand})</span>` : ''}
                            </td>
                            <td class="px-6 py-4 text-gray-600">${record.student.section || '—'}</td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">₱${Number(record.totalFee).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            <td class="px-6 py-4 text-right text-green-600 font-semibold whitespace-nowrap">₱${Number(record.paidAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            <td class="px-6 py-4 text-right ${dueClass} whitespace-nowrap">
                                ₱${Number(record.dueAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="${badgeClass}">
                                    <span class="w-2 h-2 rounded-full bg-current"></span>
                                    ${record.statusText}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                ${latestPayment}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="/staff/student-details/${record.student.student_id}" class="inline-flex items-center gap-1 text-blue-600 font-semibold hover:text-blue-700">
                                    <i class="fas fa-eye"></i>
                                    View
                                </a>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            // Poll every 10 seconds
            setInterval(fetchStudentList, 10000);
        });
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <!-- Logout Confirmation Script -->
    <script>
    function confirmLogout() {
        return confirm('Are you sure you want to sign out?');
    }
    </script>
</body>
</html>
