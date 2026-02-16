<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Reports & Analytics - Efees Admin</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
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
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-20 md:hidden" style="display: none;"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 h-screen bg-white text-slate-800 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 border-r border-slate-200 flex flex-col shrink-0 shadow-[4px_0_24px_-12px_rgba(0,0,0,0.1)]" id="sidebar">
        <div class="flex items-center justify-between gap-3 px-8 py-6 border-b border-slate-100 bg-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <div>
                    <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Efees Admin</h1>
                    <p class="text-xs text-slate-500 font-medium">Administration</p>
                </div>
            </div>
            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 transition-colors">
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

            

            <!-- Student Management -->
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
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fees.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.fees.index') }}">
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
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.sms.logs') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-comment-alt text-lg {{ request()->routeIs('admin.sms.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
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

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Mobile Header -->
        <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-blue-900">Efees</span>
            </div>
            <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-700">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-8 overflow-y-auto custom-scrollbar" x-data="{ showScheduleModal: false }">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
                <p class="text-gray-500 mt-1">Monitor fees, collections, and student financial status.</p>
            </div>
            <div class="flex gap-3">
                <button @click="showScheduleModal = true" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium flex items-center gap-2">
                    <i class="fas fa-clock"></i> Schedule Report
                </button>
                <form method="POST" action="{{ route('admin.reports.export.csv') }}">
                    @csrf
                    <!-- Pass current filters to export -->
                    <input type="hidden" name="school_year" value="{{ request('school_year') }}">
                    <input type="hidden" name="level" value="{{ request('level') }}">
                    <input type="hidden" name="section" value="{{ request('section') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium flex items-center gap-2">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex justify-between items-center">
                <p>{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900"><i class="fas fa-times"></i></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm flex justify-between items-center">
                <p>{{ session('error') }}</p>
                <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900"><i class="fas fa-times"></i></button>
            </div>
        @endif

        <!-- Dashboard Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div onclick="document.getElementById('paymentTrendsChart').scrollIntoView({behavior: 'smooth'})" class="cursor-pointer bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Fees Collected</p>
                        <h3 id="stat-total-collected" class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($totalCollected, 2) }}</h3>
                    </div>
                    <div class="p-2 bg-green-50 rounded-lg text-green-600">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending Approvals</p>
                        <h3 id="stat-pending-approvals" class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($pendingApprovals, 2) }}</h3>
                    </div>
                    <div class="p-2 bg-yellow-50 rounded-lg text-yellow-600">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
            </div>
            <div onclick="document.getElementById('paymentStatusChart').scrollIntoView({behavior: 'smooth'})" class="cursor-pointer bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Outstanding Debt</p>
                        <h3 id="stat-pending-payments" class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($pendingPayments, 2) }}</h3>
                    </div>
                    <div class="p-2 bg-orange-50 rounded-lg text-orange-600">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
            <div onclick="document.getElementById('paymentStatusChart').scrollIntoView({behavior: 'smooth'})" class="cursor-pointer bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Overdue Balances</p>
                        <h3 id="stat-overdue-balances" class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($overdueBalances, 2) }}</h3>
                    </div>
                    <div class="p-2 bg-red-50 rounded-lg text-red-600">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Reminders Sent</p>
                        <h3 id="stat-reminders-sent" class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($remindersSent) }}</h3>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                        <i class="fas fa-sms"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Payment Status Overview -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex flex-col h-[400px]">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-blue-500"></i>
                    Payment Status Overview
                </h3>
                <div class="flex-1 relative">
                    <canvas id="paymentStatusChart"></canvas>
                </div>
            </div>

            <!-- Collections by Grade/Section -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 lg:col-span-2 flex flex-col h-[400px]">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-green-500"></i>
                    Collections by Grade/Section
                </h3>
                <div class="flex-1 relative">
                    <canvas id="collectionsByGradeChart"></canvas>
                </div>
            </div>

            <!-- Payment Trends -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 lg:col-span-3 flex flex-col h-[400px]">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-orange-500"></i>
                    Payment Trends
                </h3>
                <div class="flex-1 relative">
                    <canvas id="paymentTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Filters & Detailed Reports -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Detailed Reports</h2>
                <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">School Year</label>
                        <select name="school_year" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Years</option>
                            @foreach($schoolYears as $year)
                                <option value="{{ $year }}" {{ request('school_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Grade Level</label>
                        <select name="level" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Levels</option>
                            @foreach($levels as $lvl)
                                <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Section</label>
                        <select name="section" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Sections</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec }}" {{ request('section') == $sec ? 'selected' : '' }}>{{ $sec }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Statuses</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or ID" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Level / Section</th>
                            <th class="px-6 py-3">School Year</th>
                            <th class="px-6 py-3 text-right">Tuition & Fees</th>
                            <th class="px-6 py-3 text-right">Total Paid</th>
                            <th class="px-6 py-3 text-right">Balance</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students as $student)
                            @php
                                $assignment = $student->getCurrentFeeAssignment($student->school_year);
                                $totalDue = $assignment ? $assignment->total_amount : 0;
                                $paid = $student->total_paid;
                                $balance = $student->current_balance;
                                
                                $statusClass = 'bg-gray-100 text-gray-600';
                                $statusText = 'Pending';
                                if ($balance <= 0 && $totalDue > 0) {
                                    $statusClass = 'bg-green-100 text-green-700';
                                    $statusText = 'Paid';
                                } elseif ($student->feeRecords->where('status', 'overdue')->count() > 0) {
                                    $statusClass = 'bg-red-100 text-red-700';
                                    $statusText = 'Overdue';
                                } elseif ($paid > 0) {
                                    $statusClass = 'bg-yellow-100 text-yellow-700';
                                    $statusText = 'Partial';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-900">{{ $student->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->student_id }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="text-gray-900">{{ $student->level }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->section }}</div>
                                </td>
                                <td class="px-6 py-3 text-gray-600">{{ $student->school_year }}</td>
                                <td class="px-6 py-3 text-right font-medium text-gray-900">₱{{ number_format($totalDue, 2) }}</td>
                                <td class="px-6 py-3 text-right text-green-600">₱{{ number_format($paid, 2) }}</td>
                                <td class="px-6 py-3 text-right font-bold text-red-600">₱{{ number_format($balance, 2) }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No records found matching filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200">
                {{ $students->links() }}
            </div>
        </div>

        

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Scheduled Reports -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-900">Scheduled Reports</h2>
                </div>
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium">
                        <tr>
                            <th class="px-6 py-3">Frequency</th>
                            <th class="px-6 py-3">Next Run</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($scheduledReports as $schedule)
                            <tr>
                                <td class="px-6 py-3 capitalize">{{ $schedule->frequency }}</td>
                                <td class="px-6 py-3">{{ \Carbon\Carbon::parse($schedule->next_run_at)->format('M d, Y') }}</td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>
                                </td>
                                <td class="px-6 py-3">
                                    <form action="{{ route('admin.reports.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No scheduled reports.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Generated History -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Generated History</h2>
                </div>
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium">
                        <tr>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($generatedReports as $report)
                            <tr>
                                <td class="px-6 py-3">{{ $report->type }}</td>
                                <td class="px-6 py-3">{{ $report->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-3">
                                    <a href="{{ route('admin.reports.download', $report->id) }}" class="text-blue-600 hover:underline">Download</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No history available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-gray-200">
                    {{ $generatedReports->links() }}
                </div>
            </div>
        </div>
    </main>

    <!-- Schedule Modal -->
    <div x-show="showScheduleModal" style="display: none;" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6" @click.away="showScheduleModal = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Schedule Automatic Report</h3>
                <button @click="showScheduleModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" action="{{ route('admin.reports.schedule') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                    <select name="frequency" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg text-sm text-blue-700 mb-6">
                    <i class="fas fa-info-circle mr-1"></i>
                    Report will include all student data filtered by the current parameters.
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showScheduleModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Schedule Report</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart Instances
            let statusChart, collectionsChart, trendsChart;

            const formatCurrency = (val) => '₱' + Number(val).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            const formatNumber = (val) => Number(val).toLocaleString();

            const initCharts = () => {
                // Payment Status Chart
                const statusCtx = document.getElementById('paymentStatusChart').getContext('2d');
                statusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: [],
                        datasets: [{
                            data: [],
                            backgroundColor: [],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ` ${context.label}: ${context.raw} records`;
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });

                // Collections by Grade Chart
                const collectionsCtx = document.getElementById('collectionsByGradeChart').getContext('2d');
                collectionsChart = new Chart(collectionsCtx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Collections',
                            data: [],
                            backgroundColor: '#10b981',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Collections: ₱${context.parsed.y.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#6b7280' } },
                            y: { 
                                beginAtZero: true,
                                grid: { color: '#e5e7eb' },
                                ticks: { 
                                    color: '#6b7280',
                                    callback: (val) => '₱' + formatNumber(val) 
                                }
                            }
                        }
                    }
                });

                // Payment Trends Chart
                const trendsCtx = document.getElementById('paymentTrendsChart').getContext('2d');
                trendsChart = new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Monthly Collections',
                            data: [],
                            borderColor: '#f97316',
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Collections: ₱${context.parsed.y.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { grid: { color: '#e5e7eb' }, ticks: { color: '#6b7280' } },
                            y: { 
                                beginAtZero: true,
                                grid: { color: '#e5e7eb' },
                                ticks: { 
                                    color: '#6b7280',
                                    callback: (val) => '₱' + formatNumber(val) 
                                }
                            }
                        }
                    }
                });
            };

            const updateCharts = (data) => {
                // Update Status Chart
                statusChart.data.labels = data.statusOverview.map(s => s.label);
                statusChart.data.datasets[0].data = data.statusOverview.map(s => s.count);
                statusChart.data.datasets[0].backgroundColor = data.statusOverview.map(s => s.color);
                statusChart.update();

                // Update Collections Chart
                collectionsChart.data.labels = data.collectionsByGrade.map(c => c.label);
                collectionsChart.data.datasets[0].data = data.collectionsByGrade.map(c => c.total);
                collectionsChart.update();

                // Update Trends Chart
                trendsChart.data.labels = data.paymentTrends.map(t => t.month);
                trendsChart.data.datasets[0].data = data.paymentTrends.map(t => t.total);
                trendsChart.update();
            };

            const fetchMetrics = async () => {
                try {
                    // Get current filters from the form
                    const form = document.querySelector('form[action="{{ route("admin.reports.index") }}"]');
                    const formData = new FormData(form);
                    const params = new URLSearchParams(formData);

                    const response = await fetch(`{{ route("admin.reports.metrics") }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (!response.ok) return;

                    const data = await response.json();

                    // Update stat cards
                    document.getElementById('stat-total-collected').textContent = formatCurrency(data.totalCollected);
                    const pendingApprovalsEl = document.getElementById('stat-pending-approvals');
                    if (pendingApprovalsEl) pendingApprovalsEl.textContent = formatCurrency(data.pendingApprovals);
                    document.getElementById('stat-pending-payments').textContent = formatCurrency(data.pendingPayments);
                    document.getElementById('stat-overdue-balances').textContent = formatCurrency(data.overdueBalances);
                    document.getElementById('stat-reminders-sent').textContent = formatNumber(data.remindersSent);

                    // Update charts
                    updateCharts(data);
                } catch (error) {
                    console.error('Failed to fetch real-time metrics:', error);
                }
            };

            // Initialize
            initCharts();
            fetchMetrics();

            // Poll every 10 seconds (increased from 5 to be more efficient)
            setInterval(fetchMetrics, 10000);

            // Update charts immediately when filters change (optional enhancement)
            document.querySelectorAll('select[name]').forEach(el => {
                el.addEventListener('change', fetchMetrics);
            });
        });
    </script>
</body>
</html>
