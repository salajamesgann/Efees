<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Student Enrollment - Efees Admin</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    @include('layouts.admin_sidebar')

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Mobile Header -->
        <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-blue-900">Efees Admin</span>
            </div>
            <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Main Content -->
        <main class="flex-1 md:h-screen overflow-y-auto bg-gray-50 custom-scrollbar">
            <div class="p-6 md:p-8 max-w-7xl mx-auto space-y-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Student Enrollment</h1>
                        <p class="text-gray-500 text-sm mt-1">Manage student records, enrollment status, and balances.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="hidden md:flex items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                            <i class="fas fa-file-export text-gray-400"></i>
                            Export
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2 animate-fade-in-down">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Filters & Search -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <!-- Search -->
                        <div class="md:col-span-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Search</label>
                            <div class="relative">
                                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Search by name, ID, or section..." 
                                    class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400 transition-shadow">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Level Filter -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Level</label>
                            <select name="level" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="all" {{ ($level ?? 'all') == 'all' ? 'selected' : '' }}>All Levels</option>
                                <option value="Grade 11" {{ ($level ?? 'all') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                                <option value="Grade 12" {{ ($level ?? 'all') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                            </select>
                        </div>

                        <!-- Strand Filter -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Strand</label>
                            <select name="strand" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="all" {{ ($strand ?? 'all') == 'all' ? 'selected' : '' }}>All Strands</option>
                                <option value="STEM" {{ ($strand ?? 'all') == 'STEM' ? 'selected' : '' }}>STEM</option>
                                <option value="ABM" {{ ($strand ?? 'all') == 'ABM' ? 'selected' : '' }}>ABM</option>
                                <option value="HUMSS" {{ ($strand ?? 'all') == 'HUMSS' ? 'selected' : '' }}>HUMSS</option>
                                <option value="GAS" {{ ($strand ?? 'all') == 'GAS' ? 'selected' : '' }}>GAS</option>
                                <option value="TVL" {{ ($strand ?? 'all') == 'TVL' ? 'selected' : '' }}>TVL</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                            <select name="status" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="all" {{ ($status ?? 'all') == 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="Active" {{ ($status ?? 'all') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ ($status ?? 'all') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="Archived" {{ ($status ?? 'all') == 'Archived' ? 'selected' : '' }}>Archived</option>
                                <option value="Graduated" {{ ($status ?? 'all') == 'Graduated' ? 'selected' : '' }}>Graduated</option>
                                <option value="Dropped" {{ ($status ?? 'all') == 'Dropped' ? 'selected' : '' }}>Dropped</option>
                            </select>
                        </div>
                        
                        <!-- Reset Button -->
                         <div class="md:col-span-2 flex justify-end">
                            @if(($search ?? '') || ($status ?? 'all') != 'all' || ($level ?? 'all') != 'all' || ($strand ?? 'all') != 'all')
                                <a href="{{ route('admin.enrollment.index') }}" class="flex items-center justify-center gap-2 w-full text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-200 text-xs uppercase text-gray-500 font-bold tracking-wider">
                                    <th class="px-6 py-4">Student</th>
                                    <th class="px-6 py-4">Level & Section</th>
                                    <th class="px-6 py-4">Balance</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($students as $student)
                                    <tr class="hover:bg-blue-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 flex items-center justify-center font-bold text-sm shadow-sm border border-blue-200/50">
                                                    {{ substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $student->full_name }}</div>
                                                    <div class="text-xs text-gray-500 font-mono">{{ $student->student_id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $student->level }}
                                                    @if($student->strand)
                                                        <span class="text-gray-400 mx-1">•</span> {{ $student->strand }}
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Section: <span class="font-medium text-gray-700">{{ $student->section }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $svc = app(\App\Services\FeeManagementService::class);
                                                $totals = $svc->computeTotalsForStudent($student);
                                                $totalDue = (float) ($totals['totalAmount'] ?? 0.0);
                                                $paid = (float) optional($student->payments)->where('status', 'paid')->sum('amount');
                                                $balance = max($totalDue - $paid, 0.0);
                                            @endphp
                                            <div class="font-semibold text-sm {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                ₱{{ number_format($balance, 2) }}
                                            </div>
                                            <div class="text-xs text-gray-400">Outstanding</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                                {{ $student->enrollment_status === 'Active' ? 'bg-green-100 text-green-700 border border-green-200' : '' }}
                                                {{ $student->enrollment_status === 'Inactive' ? 'bg-gray-100 text-gray-700 border border-gray-200' : '' }}
                                                {{ $student->enrollment_status === 'Archived' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                                                {{ $student->enrollment_status === 'Graduated' ? 'bg-blue-100 text-blue-700 border border-blue-200' : '' }}
                                                {{ $student->enrollment_status === 'Dropped' ? 'bg-orange-100 text-orange-700 border border-orange-200' : '' }}
                                            ">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                                    {{ $student->enrollment_status === 'Active' ? 'bg-green-500' : '' }}
                                                    {{ $student->enrollment_status === 'Inactive' ? 'bg-gray-500' : '' }}
                                                    {{ $student->enrollment_status === 'Archived' ? 'bg-red-500' : '' }}
                                                    {{ $student->enrollment_status === 'Graduated' ? 'bg-blue-500' : '' }}
                                                    {{ $student->enrollment_status === 'Dropped' ? 'bg-orange-500' : '' }}
                                                "></span>
                                                {{ $student->enrollment_status ?? 'Active' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="{{ route('admin.enrollment.show', $student) }}" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="View Profile">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('admin.enrollment.destroy', $student) }}" method="POST" onsubmit="return confirm('Are you sure you want to archive this student?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Archive Student">
                                                        <i class="fas fa-archive"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <i class="fas fa-user-slash text-2xl text-gray-400"></i>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900">No students found</h3>
                                                <p class="text-gray-500 text-sm mt-1 max-w-sm">
                                                    We couldn't find any students matching your current filters. Try adjusting your search criteria.
                                                </p>
                                                <a href="{{ route('admin.enrollment.index') }}" class="mt-4 text-blue-600 hover:text-blue-700 text-sm font-medium hover:underline">
                                                    Clear all filters
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($students->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            {{ $students->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 md:hidden"
         x-cloak></div>
</body>
</html>
