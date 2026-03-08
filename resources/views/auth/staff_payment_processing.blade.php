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
    @include('layouts.staff_sidebar')
    <main class="flex-1 md:h-screen overflow-y-auto main-scrollbar p-8">
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
            <!-- Mode Toggle -->
            <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Mode:</span>
                <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="switchMode('single')" :class="mode === 'single' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'" class="px-4 py-2 text-sm font-semibold transition-colors">
                        <i class="fas fa-user mr-1"></i> Single Payment
                    </button>
                    <button type="button" @click="switchMode('bulk')" :class="mode === 'bulk' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'" class="px-4 py-2 text-sm font-semibold transition-colors">
                        <i class="fas fa-users mr-1"></i> Bulk Payment
                    </button>
                </div>
                <template x-if="mode === 'bulk' && bulkSelected.length > 0">
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                        <span x-text="bulkSelected.length"></span>&nbsp;selected
                    </span>
                </template>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- LEFT COLUMN: Filters & Table (always visible) -->
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
                                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="mode === 'single' ? 'Select a student to process payment.' : 'Check students to include in bulk payment.'"></p>
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
                                            <th x-show="mode === 'bulk'" class="px-4 py-3 text-center w-10">
                                                <input type="checkbox" @change="toggleAllStudents($event)" :checked="bulkSelected.length === students.filter(s => s.total_balance > 0).length && students.filter(s => s.total_balance > 0).length > 0" class="rounded border-gray-300 text-primary focus:ring-primary">
                                            </th>
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
                                                :class="{
                                                    'bg-blue-50/70 dark:bg-blue-900/40 ring-1 ring-inset ring-blue-500/20': mode === 'single' ? form.student_id == student.student_id : bulkSelected.includes(student.student_id)
                                                }"
                                                @click="mode === 'single' ? selectStudent(student) : toggleBulkStudent(student)"
                                            >
                                                <td x-show="mode === 'bulk'" class="px-4 py-3 text-center" @click.stop>
                                                    <input type="checkbox" :value="student.student_id" :checked="bulkSelected.includes(student.student_id)" @change="toggleBulkStudent(student)" :disabled="student.total_balance <= 0" class="rounded border-gray-300 text-primary focus:ring-primary disabled:opacity-40">
                                                </td>
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
                                                <td class="px-4 py-3 text-right" x-show="mode === 'single'">
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 shadow-sm transition-colors"
                                                        :class="form.student_id == student.student_id ? 'bg-blue-600 border-blue-600 text-white hover:bg-blue-700' : ''"
                                                    >
                                                        <span x-text="form.student_id == student.student_id ? 'Selected' : 'Select'"></span>
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3 text-right" x-show="mode === 'bulk'">
                                                    <span x-show="bulkSelected.includes(student.student_id)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                                                        <i class="fas fa-check mr-1"></i> Included
                                                    </span>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="students.length === 0">
                                            <td :colspan="mode === 'bulk' ? 7 : 6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
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

                <!-- RIGHT COLUMN -->
                <div class="lg:col-span-1">

                    <!-- Single Payment Form -->
                    <form method="POST" action="{{ route('staff.payments.store') }}" x-show="mode === 'single'">
                    @csrf
                    <div x-show="mode === 'single'">
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
                    </form>

                    <!-- Bulk Payment Form -->
                    <form method="POST" action="{{ route('staff.payments.bulk_store') }}" @submit.prevent="submitBulkPayment($event)" x-show="mode === 'bulk'">
                    @csrf
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-layer-group text-primary"></i>
                            Bulk Payment Summary
                        </h3>

                        <!-- Empty State -->
                        <template x-if="bulkSelected.length === 0">
                            <div class="rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 p-6 text-center">
                                <i class="fas fa-users text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Select students from the table above to create a bulk payment.</p>
                            </div>
                        </template>

                        <!-- Bulk Payment Options -->
                        <template x-if="bulkSelected.length > 0">
                            <div class="space-y-4">
                                <!-- Payment Mode Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Type</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" x-model="bulkPaymentType" value="full_balance" class="text-primary focus:ring-primary" @change="recalcBulkAmounts()">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Pay Full Balance</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" x-model="bulkPaymentType" value="fixed_amount" class="text-primary focus:ring-primary" @change="recalcBulkAmounts()">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Fixed Amount Per Student</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" x-model="bulkPaymentType" value="custom" class="text-primary focus:ring-primary" @change="recalcBulkAmounts()">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Custom Amount Per Student</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Fixed Amount Input -->
                                <div x-show="bulkPaymentType === 'fixed_amount'" x-cloak>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount Per Student</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500 text-sm">₱</span>
                                        <input type="number" step="0.01" min="0.01" x-model="bulkFixedAmount" @input="recalcBulkAmounts()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary/20 pl-7 text-sm font-semibold">
                                    </div>
                                </div>

                                <!-- Common Fields -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Method</label>
                                        <select name="method" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm shadow-sm focus:border-primary focus:ring-primary/20" required>
                                            <option value="Cash" selected>Cash</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                        <input type="date" name="paid_at" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm shadow-sm focus:border-primary focus:ring-primary/20" value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                                    <textarea name="remarks" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm shadow-sm focus:border-primary focus:ring-primary/20" placeholder="Optional notes for all payments..."></textarea>
                                </div>

                                <!-- Review Table -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Review Payments</label>
                                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 max-h-64 overflow-y-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                            <thead class="bg-gray-100 dark:bg-gray-900/40 sticky top-0">
                                                <tr>
                                                    <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Student</th>
                                                    <th class="px-3 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Balance</th>
                                                    <th class="px-3 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Amount</th>
                                                    <th class="px-3 py-2 text-center font-medium text-gray-600 dark:text-gray-300 w-8"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                                <template x-for="(entry, idx) in bulkEntries" :key="entry.student_id">
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                        <td class="px-3 py-2">
                                                            <div class="font-medium text-gray-900 dark:text-gray-100 text-xs" x-text="entry.student_name"></div>
                                                            <div class="text-[10px] text-gray-500" x-text="entry.student_id"></div>
                                                        </td>
                                                        <td class="px-3 py-2 text-right text-xs text-gray-600 dark:text-gray-400" x-text="formatMoney(entry.balance)"></td>
                                                        <td class="px-3 py-2 text-right">
                                                            <template x-if="bulkPaymentType === 'custom'">
                                                                <div class="relative">
                                                                    <span class="absolute left-2 top-1.5 text-gray-400 text-xs">₱</span>
                                                                    <input type="number" step="0.01" min="0.01" :max="entry.balance" x-model="entry.amount" class="w-24 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs pl-5 py-1 text-right focus:border-primary focus:ring-primary/20">
                                                                </div>
                                                            </template>
                                                            <template x-if="bulkPaymentType !== 'custom'">
                                                                <span class="text-xs font-medium text-gray-900 dark:text-gray-100" x-text="formatMoney(entry.amount)"></span>
                                                            </template>
                                                        </td>
                                                        <td class="px-3 py-2 text-center">
                                                            <button type="button" @click="removeBulkStudent(entry.student_id)" class="text-red-400 hover:text-red-600 transition-colors">
                                                                <i class="fas fa-times text-xs"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Grand Total -->
                                <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Students:</span>
                                        <span class="font-bold text-gray-900 dark:text-white ml-1" x-text="bulkEntries.length"></span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Grand Total:</span>
                                        <span class="font-bold text-lg text-primary ml-1" x-text="formatMoney(bulkGrandTotal)"></span>
                                    </div>
                                </div>

                                <!-- Hidden inputs for form submission -->
                                <template x-for="(entry, idx) in bulkEntries" :key="'hidden-' + entry.student_id">
                                    <div>
                                        <input type="hidden" :name="'payments[' + idx + '][student_id]'" :value="entry.student_id">
                                        <input type="hidden" :name="'payments[' + idx + '][amount_paid]'" :value="entry.amount">
                                    </div>
                                </template>

                                <button type="submit" :disabled="bulkEntries.length === 0 || bulkSubmitting" class="w-full bg-primary hover:bg-blue-600 disabled:bg-gray-400 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-colors duration-200 flex items-center justify-center gap-2 mt-2">
                                    <i class="fas fa-check-double" x-show="!bulkSubmitting"></i>
                                    <i class="fas fa-spinner fa-spin" x-show="bulkSubmitting"></i>
                                    <span x-text="bulkSubmitting ? 'Submitting...' : 'Submit Bulk Payment (' + bulkEntries.length + ' students)'"></span>
                                </button>
                            </div>
                        </template>
                    </div>
                    </form>

                </div>
            </div>

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
            mode: 'single', // 'single' or 'bulk'
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
            
            // Bulk payment state
            bulkSelected: [],
            bulkEntries: [],
            bulkPaymentType: 'full_balance',
            bulkFixedAmount: '',
            bulkSubmitting: false,
            
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
            
            switchMode(newMode) {
                this.mode = newMode;
                if (newMode === 'single') {
                    this.bulkSelected = [];
                    this.bulkEntries = [];
                } else {
                    this.form.student_id = '';
                    this.fees = [];
                    this.amountPayable = 0;
                }
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
                        this.form.amount_paid = fee.balance;
                    }
                } else {
                    this.amountPayable = this.fees.reduce((sum, fee) => {
                        return sum + parseFloat(fee.balance);
                    }, 0);
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
            },
            
            // Bulk payment methods
            toggleBulkStudent(student) {
                if (student.total_balance <= 0) return;
                
                const idx = this.bulkSelected.indexOf(student.student_id);
                if (idx > -1) {
                    this.bulkSelected.splice(idx, 1);
                    this.bulkEntries = this.bulkEntries.filter(e => e.student_id !== student.student_id);
                } else {
                    this.bulkSelected.push(student.student_id);
                    const amount = this.bulkPaymentType === 'fixed_amount' && this.bulkFixedAmount
                        ? Math.min(parseFloat(this.bulkFixedAmount), student.total_balance)
                        : student.total_balance;
                    this.bulkEntries.push({
                        student_id: student.student_id,
                        student_name: student.last_name + ', ' + student.first_name,
                        balance: student.total_balance,
                        amount: amount
                    });
                }
            },
            
            toggleAllStudents(event) {
                if (event.target.checked) {
                    this.students.filter(s => s.total_balance > 0).forEach(s => {
                        if (!this.bulkSelected.includes(s.student_id)) {
                            this.bulkSelected.push(s.student_id);
                            const amount = this.bulkPaymentType === 'fixed_amount' && this.bulkFixedAmount
                                ? Math.min(parseFloat(this.bulkFixedAmount), s.total_balance)
                                : s.total_balance;
                            this.bulkEntries.push({
                                student_id: s.student_id,
                                student_name: s.last_name + ', ' + s.first_name,
                                balance: s.total_balance,
                                amount: amount
                            });
                        }
                    });
                } else {
                    const currentPageIds = this.students.map(s => s.student_id);
                    this.bulkSelected = this.bulkSelected.filter(id => !currentPageIds.includes(id));
                    this.bulkEntries = this.bulkEntries.filter(e => !currentPageIds.includes(e.student_id));
                }
            },
            
            removeBulkStudent(studentId) {
                this.bulkSelected = this.bulkSelected.filter(id => id !== studentId);
                this.bulkEntries = this.bulkEntries.filter(e => e.student_id !== studentId);
            },
            
            recalcBulkAmounts() {
                this.bulkEntries.forEach(entry => {
                    if (this.bulkPaymentType === 'full_balance') {
                        entry.amount = entry.balance;
                    } else if (this.bulkPaymentType === 'fixed_amount') {
                        const fixed = parseFloat(this.bulkFixedAmount) || 0;
                        entry.amount = Math.min(fixed, entry.balance);
                    }
                    // For 'custom', keep existing amounts
                });
            },
            
            get bulkGrandTotal() {
                return this.bulkEntries.reduce((sum, e) => sum + parseFloat(e.amount || 0), 0);
            },
            
            submitBulkPayment(event) {
                if (this.bulkEntries.length === 0) return;
                
                // Validate all amounts
                for (const entry of this.bulkEntries) {
                    const amt = parseFloat(entry.amount);
                    if (!amt || amt <= 0) {
                        alert('Please enter a valid amount for ' + entry.student_name);
                        return;
                    }
                    if (amt > parseFloat(entry.balance)) {
                        alert('Amount for ' + entry.student_name + ' exceeds their balance of ' + this.formatMoney(entry.balance));
                        return;
                    }
                }
                
                this.bulkSubmitting = true;
                event.target.submit();
            }
        }
    }
</script>
</body>
</html>
