<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fee Periods - E-Fees Portal</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" style="display: none;" x-cloak></div>

    <!-- Sidebar -->
    @include('layouts.admin_sidebar')

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Mobile Header -->
        <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-blue-900">Efees</span>
            </div>
            <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-700">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-6 lg:p-8">
            <header class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Fee Periods</h1>
                    <p class="text-gray-500 mt-1">Manage School Years and Semesters.</p>
                </div>
                <a href="{{ route('admin.fees.periods.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm font-medium">
                    <i class="fas fa-plus"></i>
                    <span>New Period</span>
                </a>
            </header>

            <!-- Tab Navigation -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <nav class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-medium transition-colors duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Tuition Fees</span>
                        </a>
                        <a href="{{ route('admin.fees.index', ['tab' => 'charges']) }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-medium transition-colors duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200">
                            <i class="fas fa-list"></i>
                            <span>Additional Charges</span>
                        </a>
                        <a href="{{ route('admin.fees.index', ['tab' => 'discounts']) }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-medium transition-colors duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200">
                            <i class="fas fa-percent"></i>
                            <span>Discounts</span>
                        </a>
                         <a href="{{ route('admin.fees.periods.index') }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-medium transition-colors duration-200 bg-blue-50 text-blue-700">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Fee Periods</span>
                        </a>
                    </nav>
                </div>
                <div class="border-b border-gray-200"></div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($periodsTableMissing)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                The 'fee_periods' table does not exist in the database. Please run migrations.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- School Years -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-800">School Years</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse($schoolYears as $year)
                                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                            {{ $year->sort_order }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $year->label }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $year->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $year->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.fees.periods.edit', $year) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.fees.periods.destroy', $year) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-8 text-center text-gray-500">
                                    No school years found.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Semesters -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-800">Semesters</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse($semesters as $semester)
                                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-xs font-bold">
                                            {{ $semester->sort_order }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $semester->label }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $semester->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $semester->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.fees.periods.edit', $semester) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.fees.periods.destroy', $semester) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-8 text-center text-gray-500">
                                    No semesters found.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </main>
    </div>
</body>
</html>