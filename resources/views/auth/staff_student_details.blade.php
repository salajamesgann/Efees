<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Student Details - Fee Management</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
<script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#1173d4",
              "background-light": "#f6f7f8",
              "background-dark": "#101922",
            },
            fontFamily: {
              display: ["Inter", "sans-serif"],
            },
            borderRadius: {
              DEFAULT: "0.25rem",
              lg: "0.5rem",
              xl: "0.75rem",
              full: "9999px",
            },
          },
        },
      };
    </script>
<style>
      .paid-badge {
        background-color: #22c55e26;
        color: #16a34a;
      }
      .partially-paid-badge {
        background-color: #f59e0b26;
        color: #d97706;
      }
      .unpaid-badge {
        background-color: #ef444426;
        color: #dc2626;
      }
      .dark .paid-badge {
        background-color: #22c55e33;
        color: #4ade80;
      }
      .dark .partially-paid-badge {
        background-color: #f59e0b33;
        color: #facc15;
      }
      .dark .unpaid-badge {
        background-color: #ef444433;
        color: #f87171;
      }

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
<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200" x-data="{ sidebarOpen: false }">
<style>[x-cloak]{display:none!important}</style>
<div class="flex min-h-screen">
<div class="md:hidden w-full bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
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
<form method="POST" action="{{ route('logout') }}">
@csrf
<button class="w-full flex items-center gap-3 bg-blue-600 text-white font-bold text-sm rounded-lg h-10 justify-center cursor-pointer transition-colors duration-300 hover:bg-blue-700" type="submit">
<i class="fas fa-sign-out-alt"></i>
<span>Logout</span>
</button>
</form>
</div>
</aside>
<main class="flex-1 overflow-y-auto main-scrollbar p-8">
<header class="mb-8">
<div class="flex justify-between items-center">
<div>
<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Student Details</h1>
<p class="text-gray-600 dark:text-gray-400 mt-2">Manage fee records and payment status for {{ $student->full_name }}</p>
@if(isset($activeYear))
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
        Active School Year: <span class="font-semibold">{{ $activeYear }}</span>
        @if($isLockedYear ?? false)
            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[0.7rem] font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                <i class="fas fa-lock mr-1"></i> Locked Year (view-only)
            </span>
        @endif
    </p>
@endif
</div>
<div class="flex gap-3">
<a href="{{ route('staff_dashboard') }}" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
<i class="fas fa-arrow-left"></i>
Back to Records
</a>
</div>
</div>
</header>

@if(session('success'))
    <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg p-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg p-4">
        {{ session('error') }}
    </div>
@endif

@php
    $assignment = $student->getCurrentFeeAssignment();
    $baseTuition = (float) ($assignment?->base_tuition ?? 0);
    $chargesTotal = (float) ($assignment?->additional_charges_total ?? 0);
    $discountsTotal = (float) ($assignment?->discounts_total ?? 0);
    $totalFee = (float) ($assignment?->total_amount ?? ($baseTuition + $chargesTotal - $discountsTotal));
    $paidAmount = (float) $student->feeRecords->where('status', 'paid')->sum('amount');
    $recordBalance = (float) $student->feeRecords->sum('balance');
    $dueAmount = $recordBalance > 0 ? $recordBalance : max($totalFee - $paidAmount, 0);

    $status = 'unpaid';
    $statusText = 'Unpaid';

    if ($paidAmount > 0 && $dueAmount > 0) {
        $status = 'partially-paid';
        $statusText = 'Partially Paid';
    } elseif ($dueAmount <= 0) {
        $status = 'paid';
        $statusText = 'Paid';
    }
@endphp

<!-- Student Information Card -->
<div class="bg-white dark:bg-background-dark rounded-xl shadow-sm mb-8">
    <div class="p-6">
        <div class="flex flex-col md:flex-row items-start gap-6">
            <div class="flex-shrink-0">
                <div class="w-20 h-20 rounded-full bg-gradient-to-r from-primary to-blue-600 flex items-center justify-center text-white font-bold text-2xl shadow-md">
                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                </div>
            </div>
            <div class="flex-1 w-full">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ $student->full_name }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-y-6 gap-x-8">
                    <!-- Student ID -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Student ID</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $student->student_id }}</p>
                    </div>

                    <!-- Full Name -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Full Name</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $student->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">School Year</p>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ $student->school_year ?: 'N/A' }}
                            @if($isLockedYear ?? false)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[0.7rem] font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                    <i class="fas fa-lock mr-1"></i> Locked
                                </span>
                            @endif
                        </p>
                    </div>

                    <!-- Gender -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Gender</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $student->sex ?? 'N/A' }}</p>
                    </div>

                    <!-- Grade / Year Level -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Grade / Level</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $student->level }}</p>
                    </div>

                    <!-- Section -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Section</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $student->section ?? 'N/A' }}</p>
                    </div>

                    <!-- School Year -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">School Year</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $student->school_year ?? $assignment->school_year ?? 'N/A' }}</p>
                    </div>

                    <!-- Enrollment Status -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Enrollment Status</p>
                        @php
                            $status = strtolower($student->enrollment_status ?? 'active');
                            $statusClass = match($status) {
                                'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'inactive' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                            {{ ucfirst($student->enrollment_status ?? 'Active') }}
                        </span>
                    </div>

                    @php
                        $parentName = 'N/A';
                        $parentContact = 'N/A';
                        
                        // New relationship support
                        if ($student->parents && $student->parents->isNotEmpty()) {
                            // Try to find primary parent
                            $primaryParent = $student->parents->firstWhere('pivot.is_primary', true) ?? $student->parents->first();
                            if ($primaryParent) {
                                $parentName = $primaryParent->full_name;
                                $parentContact = $primaryParent->phone;
                            }
                        }
                    @endphp

                    <!-- Parent/Guardian Name -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Parent/Guardian</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $parentName }}</p>
                    </div>

                    <!-- Parent Contact -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Contact Number</p>
                        <p class="font-semibold text-gray-900 dark:text-white font-mono">{{ $parentContact }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        @if(in_array($student->level, ['Grade 11','Grade 12']))
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
            <form method="POST" action="{{ route('staff.student_details.update_category', $student) }}">
                @csrf
                <div class="max-w-md">
                    <label for="strand" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Senior High Strand / Category</label>
                    <div class="flex gap-3">
                        <select name="strand" id="strand" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            <option value="">Select Strand</option>
                            <option value="STEM" {{ old('strand', $student->strand) === 'STEM' ? 'selected' : '' }}>STEM</option>
                            <option value="ABM" {{ old('strand', $student->strand) === 'ABM' ? 'selected' : '' }}>ABM</option>
                            <option value="HUMSS" {{ old('strand', $student->strand) === 'HUMSS' ? 'selected' : '' }}>HUMSS</option>
                            <option value="GAS" {{ old('strand', $student->strand) === 'GAS' ? 'selected' : '' }}>GAS</option>
                            <option value="ICT" {{ old('strand', $student->strand) === 'ICT' ? 'selected' : '' }}>ICT</option>
                            <option value="HE" {{ old('strand', $student->strand) === 'HE' ? 'selected' : '' }}>Home Economics (HE)</option>
                            <option value="IA" {{ old('strand', $student->strand) === 'IA' ? 'selected' : '' }}>Industrial Arts (IA)</option>
                            <option value="Agri-Fishery" {{ old('strand', $student->strand) === 'Agri-Fishery' ? 'selected' : '' }}>Agri-Fishery</option>
                        </select>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200">
                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 mt-6">
    <div class="bg-white dark:bg-background-dark rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-600 dark:text-gray-400">Base Tuition</p>
        <p class="text-lg font-bold text-gray-900 dark:text-white">₱{{ number_format($baseTuition, 2) }}</p>
    </div>
    <div class="bg-white dark:bg-background-dark rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-600 dark:text-gray-400">Additional Charges</p>
        <p class="text-lg font-bold text-gray-900 dark:text-white">₱{{ number_format($chargesTotal, 2) }}</p>
    </div>
    <div class="bg-white dark:bg-background-dark rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-600 dark:text-gray-400">Discounts</p>
        <p class="text-lg font-bold text-red-600">-₱{{ number_format($discountsTotal, 2) }}</p>
    </div>
</div>

<!-- Fee Adjustment History -->
<div class="bg-white dark:bg-background-dark rounded-xl shadow-sm mb-6">
    <div class="p-6">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Fee Adjustment History</h3>
        
        @if(isset($assignment) && $assignment->adjustments->isNotEmpty())
            <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                @foreach($assignment->adjustments->sortByDesc('created_at') as $adj)
                    <div class="flex items-start justify-between p-3 rounded-lg border {{ $adj->type == 'discount' ? 'bg-green-50 border-green-100 dark:bg-green-900/20 dark:border-green-800' : 'bg-red-50 border-red-100 dark:bg-red-900/20 dark:border-red-800' }}">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $adj->name }}</p>
                                <span class="text-[10px] px-1.5 py-0.5 rounded uppercase font-bold {{ $adj->type == 'discount' ? 'bg-green-200 text-green-800 dark:bg-green-800 dark:text-green-200' : 'bg-red-200 text-red-800 dark:bg-red-800 dark:text-red-200' }}">
                                    {{ $adj->type }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $adj->created_at->format('M d, Y h:i A') }}</p>
                            @if($adj->remarks)
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 italic">"{{ $adj->remarks }}"</p>
                            @endif
                        </div>
                        <span class="font-bold text-sm {{ $adj->type == 'discount' ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                            {{ $adj->type == 'discount' ? '-' : '+' }}₱{{ number_format($adj->amount, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-dashed border-gray-300 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">No manual adjustments applied yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Fee Records Table -->
<div class="bg-white dark:bg-background-dark rounded-xl shadow-sm">
<div class="p-6">
<h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Fee Records</h3>

<!-- Summary Card -->
<div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 mb-6">
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
<div>
<p class="text-sm text-gray-600 dark:text-gray-400">Total Fee</p>
<p class="text-lg font-bold text-gray-900 dark:text-white">₱{{ number_format($totalFee, 2) }}</p>
</div>
<div>
<p class="text-sm text-gray-600 dark:text-gray-400">Paid Amount</p>
<p class="text-lg font-bold text-green-600">₱{{ number_format($paidAmount, 2) }}</p>
</div>
<div>
<p class="text-sm text-gray-600 dark:text-gray-400">Due Amount</p>
<p class="text-lg font-bold {{ $dueAmount > 0 ? 'text-red-600' : 'text-green-600' }}">₱{{ number_format($dueAmount, 2) }}</p>
</div>
<div>
<p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
<p class="text-sm font-semibold">
<span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full {{ $status }}-badge">{{ $statusText }}</span>
</p>
</div>
</div>
</div>

<div class="overflow-x-auto">
<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
<thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/30 dark:text-gray-400">
<tr>
<th class="px-6 py-4 font-medium" scope="col">Reference No</th>
<th class="px-6 py-4 font-medium" scope="col">Fee Name</th>
<th class="px-6 py-4 font-medium" scope="col">Amount</th>
<th class="px-6 py-4 font-medium" scope="col">Balance</th>
<th class="px-6 py-4 font-medium" scope="col">Status</th>
<th class="px-6 py-4 font-medium" scope="col">Due/Paid Date</th>
<th class="px-6 py-4 font-medium text-center" scope="col">Actions</th>
</tr>
</thead>
<tbody>
@forelse ($student->feeRecords as $record)
    <tr class="bg-white dark:bg-background-dark border-b dark:border-gray-700/50">
        <td class="px-6 py-4 whitespace-nowrap">{{ $record->reference_number ?? $record->fee_id }}</td>
<td class="px-6 py-4 whitespace-nowrap">
    <div class="flex flex-col">
        <span class="font-medium">{{ ucwords(str_replace('_', ' ', $record->record_type)) }}</span>
        @if($record->notes)
            <span class="text-xs text-gray-500">{{ Str::limit($record->notes, 30) }}</span>
        @endif
    </div>
</td>
<td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($record->amount, 2) }}</td>
        <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($record->balance, 2) }}</td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full {{ $record->status === 'paid' ? 'paid-badge' : 'unpaid-badge' }}">
                {{ $record->status ?? 'Unpaid' }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            @if($record->payment_date)
                {{ $record->payment_date->format('M d, Y') }}
                @if($record->status !== 'paid' && $record->payment_date->isPast())
                    <span class="text-xs text-red-500 block">(Overdue)</span>
                @endif
            @else
                <span class="text-gray-400">N/A</span>
            @endif
        </td>
        @php
            $isLockedYearView = ($isLockedYear ?? false);
        @endphp
        <td class="px-6 py-4 text-center">
            @if($canEditFees && ! $isLockedYearView)
            <form method="POST" action="{{ route('staff.fee_records.update', $record) }}" class="inline-block">
                @csrf
                <div class="flex items-center gap-2">
                    <input type="number" name="amount" step="0.01" min="0" value="{{ $record->amount }}" class="w-24 rounded border border-gray-300 px-2 py-1 text-sm {{ $record->status === 'paid' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" {{ $record->status === 'paid' ? 'disabled' : '' }}>
                    <input type="number" name="balance" step="0.01" min="0" value="{{ $record->balance }}" class="w-24 rounded border border-gray-300 px-2 py-1 text-sm {{ $record->status === 'paid' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" {{ $record->status === 'paid' ? 'disabled' : '' }}>
                    <select name="status" class="rounded border border-gray-300 px-2 py-1 text-sm {{ $record->status === 'paid' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" {{ $record->status === 'paid' ? 'disabled' : '' }}>
                        <option value="pending" {{ $record->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $record->status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ $record->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ $record->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <input type="date" name="payment_date" value="{{ optional($record->payment_date)?->format('Y-m-d') }}" class="rounded border border-gray-300 px-2 py-1 text-sm {{ $record->status === 'paid' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" {{ $record->status === 'paid' ? 'disabled' : '' }}>
                    <input type="text" name="notes" value="{{ $record->notes }}" placeholder="Notes" class="w-40 rounded border border-gray-300 px-2 py-1 text-sm {{ $record->status === 'paid' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" {{ $record->status === 'paid' ? 'disabled' : '' }}>
                    <button type="submit" class="px-3 py-1 {{ $record->status === 'paid' ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600 text-white' }} text-xs font-semibold rounded transition-colors duration-200" {{ $record->status === 'paid' ? 'disabled' : '' }}>Save</button>
                </div>
            </form>
            @if($record->status !== 'paid' && ! $isLockedYearView)
            <form method="POST" action="{{ route('staff.approve', $student) }}" class="inline-block ml-2" onsubmit="return confirm('Mark this fee record as paid?')">
                @csrf
                <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded transition-colors duration-200">
                    Mark Paid
                </button>
            </form>
            @endif
            @else
                <span class="text-sm text-gray-500 italic">
                    @if($isLockedYearView)
                        Editing disabled for locked school year
                    @else
                        Editing Disabled
                    @endif
                </span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
            No fee records found for this student.
        </td>
    </tr>
@endforelse
</tbody>
</table>
</div>
</div>

<!-- Payment History Table -->
<div class="bg-white dark:bg-background-dark rounded-xl shadow-sm mt-8">
    <div class="p-6">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Payment History</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/30 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-medium" scope="col">Date</th>
                        <th class="px-6 py-4 font-medium" scope="col">Reference No</th>
                        <th class="px-6 py-4 font-medium" scope="col">Amount</th>
                        <th class="px-6 py-4 font-medium" scope="col">Method</th>
                        <th class="px-6 py-4 font-medium" scope="col">Status</th>
                        <th class="px-6 py-4 font-medium text-center" scope="col">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($student->payments->sortByDesc('paid_at') as $payment)
                        <tr class="bg-white dark:bg-background-dark border-b dark:border-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $payment->reference_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                ₱{{ number_format($payment->amount_paid, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $payment->method ?? 'Manual' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                    ];
                                    $statusColor = $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full {{ $statusColor }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('staff.payments.receipt', $payment) }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                    <i class="fas fa-receipt"></i>
                                    <span class="text-xs font-medium">View</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No payment history found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


</main>
</div>
<script src="//unpkg.com/alpinejs" defer></script>
</body></html>
