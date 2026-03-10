<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tuition Details - E-Fees Portal</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        /* Custom Scrollbar */
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
<body class="bg-slate-50 font-sans text-slate-900 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" style="display: none;" x-cloak></div>

    <!-- Sidebar -->
    @include('layouts.admin_sidebar')

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Mobile Header -->
        <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-slate-800 tracking-tight">Efees Admin</span>
            </div>
            <button @click="sidebarOpen = true" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-50 rounded-lg transition-all">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto bg-slate-50 custom-scrollbar">
            <!-- Settings -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.settings.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-cog text-lg {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Settings</span>
            </a>
        </nav>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 px-4 pb-6 border-t border-slate-100">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 hover:shadow-sm transition-all duration-200 group border border-red-100">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-sign-out-alt text-lg group-hover:scale-110 transition-transform"></i>
                </div>
                <span class="text-sm font-bold">Logout</span>
            </button>
        </form>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Mobile Header -->
        <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-slate-800 tracking-tight">Efees Admin</span>
            </div>
            <button @click="sidebarOpen = true" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-50 rounded-lg transition-all">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto bg-slate-50 custom-scrollbar">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">Tuition Configuration</h1>
                <a href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            <div class="grid gap-6 md:grid-cols-2 max-w-6xl mx-auto">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    @php
                        $notes = (string) ($tuitionFee->notes ?? '');
                        $feeName = $notes;
                        $pos = mb_strpos($notes, ' — ');
                        if ($pos !== false) $feeName = mb_substr($notes, 0, $pos);
                        if (!$feeName) $feeName = $tuitionFee->grade_level.' Tuition – SY '.($tuitionFee->school_year ?? 'N/A');
                    @endphp
                    <h2 class="text-lg font-semibold mb-4">Details</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex items-center justify-between"><dt>Fee Name</dt><dd class="font-semibold">{{ $feeName }}</dd></div>
                        <div class="flex items-center justify-between"><dt>Academic Year</dt><dd class="font-semibold">{{ $tuitionFee->school_year ?? 'N/A' }}</dd></div>
                        <div class="flex items-center justify-between"><dt>Grade Level</dt><dd class="font-semibold">{{ $tuitionFee->grade_level }}</dd></div>
                        <div class="flex items-center justify-between"><dt>Track</dt><dd class="font-semibold">{{ in_array($tuitionFee->grade_level,['Grade 11','Grade 12']) ? ($tuitionFee->track ?? '—') : '—' }}</dd></div>
                        <div class="flex items-center justify-between"><dt>Strand</dt><dd class="font-semibold">{{ in_array($tuitionFee->grade_level,['Grade 11','Grade 12']) ? ($tuitionFee->strand ?? '—') : '—' }}</dd></div>
                        <div class="flex items-center justify-between"><dt>Status</dt><dd class="font-semibold">{{ $tuitionFee->is_active ? 'Active' : 'Inactive' }}</dd></div>
                        <div class="flex items-center justify-between"><dt>Base Tuition</dt><dd class="font-semibold">₱{{ number_format((float) $tuitionFee->amount, 2) }}</dd></div>
                        <div class="border-t border-dashed pt-3 flex items-center justify-between"><dt>Gross Total</dt><dd class="text-blue-600 font-bold">₱{{ number_format((float) $grossTotal, 2) }}</dd></div>
                    </dl>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <a href="{{ route('admin.fees.edit-tuition', $tuitionFee) }}" class="inline-flex items-center gap-2 h-10 px-4 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-sm hover:shadow-md hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/20">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span>Edit</span>
                        </a>
                        <form method="POST" action="{{ route('admin.fees.toggle-tuition', $tuitionFee) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="active" value="{{ $tuitionFee->is_active ? '0' : '1' }}">
                            <button type="submit" class="inline-flex items-center gap-2 h-10 px-4 rounded-full bg-gray-800 text-white font-semibold shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/10">
                                    <i class="fas {{ $tuitionFee->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                </span>
                                <span>{{ $tuitionFee->is_active ? 'Deactivate' : 'Activate' }}</span>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold mb-4">Fee Summary</h2>
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-center justify-between"><dt>Base tuition</dt><dd class="font-semibold">₱{{ number_format((float) $tuitionFee->amount, 2) }}</dd></div>
                        <div class="flex items-center justify-between"><dt>Additional charges</dt><dd class="font-semibold">₱{{ number_format((float) $chargesTotal, 2) }}</dd></div>
                        <div class="border-t border-dashed border-gray-200 pt-3 flex items-center justify-between"><dt>Gross total</dt><dd class="text-blue-600 font-bold">₱{{ number_format((float) $grossTotal, 2) }}</dd></div>
                    </dl>
                </div>
            </div>
            <div class="mt-6 grid gap-6 md:grid-cols-2 max-w-6xl mx-auto">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold mb-4">Attached Additional Charges</h2>
                    @if(collect($charges)->count() > 0)
                        <ul class="divide-y divide-gray-200 text-sm">
                            @foreach($charges as $charge)
                                <li class="py-2 flex items-center justify-between">
                                    <span>{{ is_array($charge) ? ($charge['name'] ?? '—') : ($charge->name ?? '—') }}</span>
                                    <span class="font-semibold">₱{{ number_format((float) (is_array($charge) ? ($charge['amount'] ?? 0) : ($charge->amount ?? 0)), 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-600">No charges found for this configuration.</p>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
