<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Payment Processing - Fee Management</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
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
          },
        },
      };
    </script>
    <style>
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
        <header class="mb-8 flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Payment Processing</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Process student payments and manage payment records</p>
            </div>

            <!-- Notification Bell -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <i class="fas fa-bell text-2xl"></i>
                    @if(isset($notifications) && count($notifications) > 0)
                        <span class="absolute top-1 right-1 h-3 w-3 bg-red-500 rounded-full ring-2 ring-white dark:ring-gray-900"></span>
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
                     class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden" 
                     x-cloak>
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @if(isset($notifications) && count($notifications) > 0)
                            @foreach($notifications as $notification)
                                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 transition-colors last:border-0">
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
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $notification->title }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 break-words">{{ $notification->body }}</p>
                                            <p class="text-[10px] text-gray-400 mt-2">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                <i class="fas fa-bell-slash text-2xl mb-2 text-gray-300"></i>
                                <p>No notifications</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg p-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg p-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-background-dark rounded-xl shadow-sm p-8" x-data="paymentProcessing()">
            <form method="POST" action="{{ route('staff.payments.store') }}">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                    
                    <!-- LEFT COLUMN: Filters & Table -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Filters Panel -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">School Year</label>
                                    <select x-model="filters.school_year" @change="fetchStudents()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="">All Years</option>
                                        @foreach($schoolYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Year Level</label>
                                    <select x-model="filters.level" @change="fetchStudents()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="">All Levels</option>
                                        @foreach($levels as $level)
                                            <option value="{{ $level }}">{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="showStrand" x-cloak>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Strand</label>
                                    <select x-model="filters.strand" @change="fetchStudents()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="">All Strands</option>
                                        @foreach($strands as $strand)
                                            <option value="{{ $strand }}">{{ $strand }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Section</label>
                                    <select x-model="filters.section" @change="fetchStudents()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="">All Sections</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section }}">{{ $section }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-4 flex justify-end" x-show="pagination.last_page > 1" x-cloak>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <!-- Previous Page Link -->
                                    <button 
                                        @click="changePage(pagination.current_page - 1)" 
                                        :disabled="pagination.current_page === 1"
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700"
                                    >
                                        <span class="sr-only">Previous</span>
                                        <i class="fas fa-chevron-left h-5 w-5 flex items-center justify-center"></i>
                                    </button>

                                    <!-- Page Numbers -->
                                    <template x-for="(link, index) in pagination.links">
                                        <template x-if="index > 0 && index < pagination.links.length - 1">
                                            <span>
                                                <template x-if="link.url">
                                                    <button 
                                                        @click="changePage(link.label)"
                                                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                                        :class="link.active 
                                                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600 dark:bg-blue-900/30 dark:border-blue-500 dark:text-blue-200' 
                                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700'"
                                                        x-text="link.label"
                                                    ></button>
                                                </template>
                                                <template x-if="!link.url">
                                                    <span 
                                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400"
                                                        x-html="link.label"
                                                    ></span>
                                                </template>
                                            </span>
                                        </template>
                                    </template>

                                    <!-- Next Page Link -->
                                    <button 
                                        @click="changePage(pagination.current_page + 1)" 
                                        :disabled="pagination.current_page === pagination.last_page"
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700"
                                    >
                                        <span class="sr-only">Next</span>
                                        <i class="fas fa-chevron-right h-5 w-5 flex items-center justify-center"></i>
                                    </button>
                                </nav>
                            </div>
                        </div>

                        <!-- Table Panel -->
                        <div>
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Students List</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Select a student to process payment.</p>
                                </div>
                                <div class="w-full md:w-64">
                                    <input
                                        type="text"
                                        x-model="filters.search"
                                        @input.debounce.500ms="fetchStudents()"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm px-3 py-2 focus:border-primary focus:ring focus:ring-primary/20"
                                        placeholder="Search name or ID..."
                                    >
                                </div>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">Student</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">Level/Sec</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">Status</th>
                                            <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">Balance</th>
                                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">Payment Status</th>
                                            <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                        <template x-for="student in students" :key="student.student_id">
                                            <tr
                                                class="hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer transition-colors"
                                                :class="form.student_id == student.student_id ? 'bg-blue-50/70 dark:bg-blue-900/40 ring-1 ring-inset ring-blue-500/20' : ''"
                                                @click="selectStudent(student)"
                                            >
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-gray-900 dark:text-gray-100" x-text="student.last_name + ', ' + student.first_name"></div>
                                                    <div class="text-xs text-gray-500" x-text="student.student_id"></div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-gray-900 dark:text-gray-100" x-text="student.level || '-'"></div>
                                                    <div class="text-xs text-gray-500" x-text="(student.section || '') + (student.strand ? ' - ' + student.strand : '')"></div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                        :class="student.enrollment_status === 'Active'
                                                            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200'
                                                            : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'"
                                                        x-text="student.enrollment_status || 'N/A'"
                                                    ></span>
                                                </td>
                                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-gray-100" x-text="formatMoney(student.total_balance)"></td>
                                                <td class="px-4 py-3 text-center">
                                                    <template x-if="student.payments && student.payments.length > 0">
                                                        <div>
                                                            <template x-if="student.payments[0].status === 'pending'">
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200">
                                                                    Pending Approval
                                                                </span>
                                                            </template>
                                                            <template x-if="student.payments[0].status === 'rejected'">
                                                                <div class="flex flex-col items-center gap-1">
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200">
                                                                        Rejected
                                                                    </span>
                                                                    <button @click.stop="viewRejection(student.payments[0])" class="text-xs text-blue-600 hover:text-blue-800 underline">View Message</button>
                                                                </div>
                                                            </template>
                                                             <template x-if="student.payments[0].status === 'approved'">
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200">
                                                                    Approved
                                                                </span>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template x-if="!student.payments || student.payments.length === 0">
                                                        <span class="text-xs text-gray-400">-</span>
                                                    </template>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 shadow-sm transition-colors"
                                                        :class="form.student_id == student.student_id ? 'bg-blue-600 border-blue-600 text-white hover:bg-blue-700' : ''"
                                                    >
                                                        <span x-text="form.student_id == student.student_id ? 'Selected' : 'Select'"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="students.length === 0">
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <i class="fas fa-search text-3xl mb-3 text-gray-300"></i>
                                                    <p>No students found matching your filters.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Payment Form -->
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 sticky top-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <i class="fas fa-cash-register text-primary"></i>
                                Payment Details
                            </h3>

                            <!-- Selected Student Card -->
                            <div class="mb-6">
                                <input type="hidden" name="student_id" x-model="form.student_id" required>
                                <template x-if="selectedStudentInfo">
                                    <div class="rounded-lg bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-800 shadow-sm overflow-hidden">
                                        <div class="bg-blue-50/50 dark:bg-blue-900/20 px-4 py-3 border-b border-blue-100 dark:border-blue-800/50">
                                            <div class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wider">Paying For</div>
                                            <div class="font-bold text-gray-900 dark:text-white text-lg" x-text="selectedStudentInfo.last_name + ', ' + selectedStudentInfo.first_name"></div>
                                            <div class="text-xs text-gray-500" x-text="selectedStudentInfo.student_id"></div>
                                        </div>
                                        <div class="px-4 py-3 grid grid-cols-2 gap-2 text-xs">
                                             <div>
                                                <span class="text-gray-500">Level:</span>
                                                <span class="font-medium text-gray-900 dark:text-gray-100" x-text="selectedStudentInfo.level"></span>
                                             </div>
                                             <div>
                                                <span class="text-gray-500">Section:</span>
                                                <span class="font-medium text-gray-900 dark:text-gray-100" x-text="selectedStudentInfo.section"></span>
                                             </div>
                                             <div class="col-span-2 pt-2 border-t border-gray-100 dark:border-gray-700 mt-1 flex justify-between items-center">
                                                <span class="text-gray-500">Total Balance:</span>
                                                <span class="font-bold text-red-600 dark:text-red-400 text-sm" x-text="formatMoney(selectedStudentInfo.total_balance)"></span>
                                             </div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!selectedStudentInfo">
                                    <div class="rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 p-6 text-center">
                                        <i class="fas fa-user-graduate text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Select a student from the list to proceed with payment.</p>
                                    </div>
                                </template>
                            </div>

                            <!-- Form Fields -->
                            <div class="space-y-4" :class="!form.student_id ? 'opacity-50 pointer-events-none' : ''">
                                
                                <div>
                                    <label for="fee_record_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Fee</label>
                                    <select name="fee_record_id" id="fee_record_id" x-model="form.fee_record_id" @change="updateAmount()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                                        <option value="">-- General Payment --</option>
                                        <template x-for="fee in fees" :key="fee.id">
                                            <option :value="fee.id" x-text="fee.name + ' - Bal: ' + formatMoney(fee.balance)"></option>
                                        </template>
                                    </select>
                                    <p x-show="fees.length === 0 && form.student_id" class="text-xs text-green-600 mt-1" x-cloak>No outstanding fees found.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount Payable</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500 text-sm">₱</span>
                                        <input type="text" :value="parseFloat(amountPayable).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-500 cursor-not-allowed shadow-sm pl-7 text-sm" readonly>
                                    </div>
                                </div>

                                <div>
                                    <label for="amount_paid" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount Paid</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500 text-sm">₱</span>
                                        <input type="number" name="amount_paid" id="amount_paid" step="0.01" min="0.01" x-model="form.amount_paid" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20 pl-7 text-sm font-semibold" required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Method</label>
                                        <select name="method" id="method" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary/20" required>
                                            <option value="Cash" selected>Cash</option>
                                        </select>
                                    </div>
                                    <div>
                                         <label for="paid_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                        <input type="date" name="paid_at" id="paid_at" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary/20" value="{{ old('paid_at', now()->format('Y-m-d')) }}">
                                    </div>
                                </div>

                                <div>
                                    <label for="reference_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference No.</label>
                                    <input type="text" name="reference_number" id="reference_number" x-model="form.reference_number" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="OR#, Ref#, etc.">
                                </div>

                                <div>
                                    <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                                    <textarea name="remarks" id="remarks" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary/20" placeholder="Optional notes...">{{ old('remarks') }}</textarea>
                                </div>

                                <button type="submit" class="w-full bg-primary hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-colors duration-200 flex items-center justify-center gap-2 mt-4">
                                    <i class="fas fa-check-circle"></i>
                                    Process Payment
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
            <!-- Rejection Reason Modal -->
        <div x-show="showRejectionModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showRejectionModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeRejectionModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showRejectionModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Payment Rejected
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-300 whitespace-pre-wrap" x-text="rejectionMessage"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" @click="closeRejectionModal()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>
</div>
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    function paymentProcessing() {
        return {
            filters: {
                search: '',
                school_year: '{{ $activeYear ?? "" }}',
                level: '',
                strand: '',
                section: ''
            },
            form: {
                student_id: '{{ old('student_id') }}',
                fee_record_id: '',
                amount_paid: '{{ old('amount_paid') }}',
                reference_number: '{{ old('reference_number') }}'
            },
            students: [],
            pagination: {
                current_page: 1,
                last_page: 1,
                links: []
            },
            fees: [],
            amountPayable: 0,
            
            rejectionMessage: '',
            showRejectionModal: false,
            
            viewRejection(payment) {
                this.rejectionMessage = payment.remarks || 'No reason provided.';
                this.showRejectionModal = true;
            },
            
            closeRejectionModal() {
                this.showRejectionModal = false;
                this.rejectionMessage = '';
            },
            
            init() {
                this.fetchStudents();
                if (!this.form.reference_number) {
                    this.fetchReference();
                }
            },
            
            async fetchStudents(page = 1) {
                const params = new URLSearchParams({
                    action: 'fetch_students',
                    page: page,
                    ...this.filters
                });
                
                try {
                    const response = await fetch(`{{ route('staff.payment_processing') }}?${params}`);
                    const data = await response.json();
                    this.students = data.data;
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        links: data.links
                    };
                } catch (e) {
                    console.error('Error fetching students:', e);
                }
            },

            changePage(page) {
                if (page >= 1 && page <= this.pagination.last_page) {
                    this.fetchStudents(page);
                }
            },
            
            async fetchFees() {
                if (!this.form.student_id) {
                    this.fees = [];
                    this.amountPayable = 0;
                    return;
                }
                
                const params = new URLSearchParams({
                    action: 'fetch_fees',
                    student_id: this.form.student_id
                });
                
                try {
                    const response = await fetch(`{{ route('staff.payment_processing') }}?${params}`);
                    this.fees = await response.json();
                    
                    // Auto-calculate total payable (sum of balances) if no specific fee selected
                    this.updateAmount();
                } catch (e) {
                    console.error('Error fetching fees:', e);
                }
            },
            
            async fetchReference() {
                try {
                    const response = await fetch(`{{ route('staff.payment_processing') }}?action=fetch_reference`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    this.form.reference_number = data.reference_number;
                } catch (e) {
                    console.error('Error fetching reference:', e);
                }
            },
            
            updateAmount() {
                if (this.form.fee_record_id) {
                    const fee = this.fees.find(f => f.id == this.form.fee_record_id);
                    if (fee) {
                        this.amountPayable = fee.balance;
                        this.form.amount_paid = fee.balance; // Auto-fill amount paid
                    }
                } else {
                    // Sum of all unpaid fees, but handle discounts as negative if they are stored as positive balances
                    // or just sum them up if they already reduce the total.
                    // Usually, discounts should be subtracted if record_type is 'discount'.
                    this.amountPayable = this.fees.reduce((sum, fee) => {
                        return sum + parseFloat(fee.balance);
                    }, 0);
                    
                    // Ensure amountPayable is not negative
                    if (this.amountPayable < 0) this.amountPayable = 0;
                }
            },
            
            get selectedStudentInfo() {
                if (!this.form.student_id || !this.students.length) return null;
                return this.students.find(s => s.student_id == this.form.student_id);
            },

            get showStrand() {
                 return this.filters.level && (this.filters.level.includes('11') || this.filters.level.includes('12'));
            },
            
            formatMoney(amount) {
                 return '₱' + parseFloat(amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            },
            
            selectStudent(student) {
                this.form.student_id = student.student_id;
                this.fetchFees();
            }
        }
    }
</script>
</body>
</html>
