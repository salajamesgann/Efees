<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Payment History & Reports - Fee Management</title>
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
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 z-40 w-72 bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 md:translate-x-0 overflow-y-auto shadow-2xl md:shadow-none">
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
<main class="flex-1 p-8">
<header class="mb-8">
<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Payment History & Reports</h1>
<p class="text-gray-600 dark:text-gray-400 mt-2">View payment history, generate reports, and analyze fee collection data</p>
</header>

<div class="bg-white dark:bg-background-dark rounded-xl shadow-sm p-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Generate Payment Report</h2>
        
        <form method="POST" action="{{ route('staff.reports.export.csv') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Date Range -->
                <div>
                    <label for="from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                    <input type="date" name="from" id="from" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                </div>
                
                <div>
                    <label for="to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                    <input type="date" name="to" id="to" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                </div>
                
                <!-- Filters -->
                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level / Grade</label>
                    <select name="level" id="level" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                        <option value="">All Levels</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl }}">{{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="section" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Section</label>
                    <select name="section" id="section" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                        <option value="">All Sections</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec }}">{{ $sec }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-file-csv"></i>
                    Export CSV
                </button>
            </div>
        </form>
    </div>

    <!-- Scheduled Reports Section -->
    <div class="bg-white dark:bg-background-dark rounded-xl shadow-sm p-8 mt-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Schedule Reports</h2>
        
        <form method="POST" action="{{ route('staff.reports.schedule') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Filters -->
                <div>
                    <label for="sched_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level / Grade</label>
                    <select name="level" id="sched_level" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                        <option value="">All Levels</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl }}">{{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="sched_section" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Section</label>
                    <select name="section" id="sched_section" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                        <option value="">All Sections</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec }}">{{ $sec }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Frequency</label>
                    <select name="frequency" id="frequency" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20" required>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-primary hover:bg-blue-600 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-clock"></i>
                        Schedule Report
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Your Scheduled Reports</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Type</th>
                                <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Frequency</th>
                                <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Next Run</th>
                                <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($scheduledReports as $report)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-3 px-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $report->report_type }}
                                        <div class="text-xs text-gray-500">
                                            @if(isset($report->parameters['level'])) Level: {{ $report->parameters['level'] }} @endif
                                            @if(isset($report->parameters['section'])) Section: {{ $report->parameters['section'] }} @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $report->frequency }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $report->next_run_at->format('M d H:i') }}</td>
                                    <td class="py-3 px-4 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $report->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm">
                                        <form action="{{ route('staff.reports.schedule.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No scheduled reports found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Generated Reports History</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Type</th>
                                <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Date Generated</th>
                                <th class="py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($generatedReports as $genReport)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-3 px-4 text-sm text-gray-900 dark:text-gray-100">{{ $genReport->type }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $genReport->created_at->format('M d, Y H:i') }}</td>
                                    <td class="py-3 px-4 text-sm">
                                        <a href="{{ route('staff.reports.download', $genReport->id) }}" class="text-primary hover:text-blue-700 text-xs font-medium flex items-center gap-1">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No generated reports yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
</div>
<script src="//unpkg.com/alpinejs" defer></script>
</body></html>
