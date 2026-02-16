<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Profile - Efees Admin</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" x-cloak></div>

    <!-- Sidebar -->
    @include('layouts.admin_sidebar')

    <!-- Mobile Header & Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Mobile Header -->
        <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-blue-900">Efees</span>
            </div>
            <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Main content -->
        <main class="flex-1 p-6 lg:p-8 overflow-y-auto bg-gray-50 custom-scrollbar">
            <div class="max-w-3xl mx-auto space-y-6">
                <!-- Breadcrumbs -->
                <nav class="flex text-sm text-slate-500">
                    <a href="{{ route('admin_dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600 transition-colors">User Management</a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-900 font-medium">User Profile</span>
                </nav>

                <!-- Profile Header -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    <div class="w-24 h-24 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-3xl font-bold uppercase shrink-0">
                        {{ substr($user->email, 0, 1) }}
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <h1 class="text-2xl font-bold text-slate-900">
                            @if($user->roleable)
                                @if($user->roleable_type === 'App\Models\ParentContact')
                                    {{ $user->roleable->full_name }}
                                @else
                                    {{ $user->roleable->first_name }} {{ $user->roleable->last_name }}
                                @endif
                            @else
                                N/A
                            @endif
                        </h1>
                        <p class="text-slate-500 font-medium mt-1">{{ ucfirst($user->role->role_name) }}</p>
                        <div class="mt-4 flex flex-wrap justify-center sm:justify-start gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-green-50 text-green-700 border border-green-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Active
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                <i class="far fa-envelope text-slate-400"></i>
                                {{ $user->email }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 w-full sm:w-auto">
                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg font-medium transition-colors shadow-sm">
                            <i class="fas fa-edit"></i>
                            <span>Edit Profile</span>
                        </a>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-lg font-semibold text-slate-800">Personal Information</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($user->roleable)
                            @if($user->roleable_type === 'App\Models\ParentContact')
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Full Name</label>
                                    <div class="text-slate-900 font-medium">{{ $user->roleable->full_name }}</div>
                                </div>
                            @else
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">First Name</label>
                                    <div class="text-slate-900 font-medium">{{ $user->roleable->first_name }}</div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Last Name</label>
                                    <div class="text-slate-900 font-medium">{{ $user->roleable->last_name }}</div>
                                </div>
                                @if($user->roleable->MI)
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Middle Initial</label>
                                    <div class="text-slate-900 font-medium">{{ $user->roleable->MI }}</div>
                                </div>
                                @endif
                            @endif

                            @if(isset($user->roleable->contact_number) || isset($user->roleable->phone))
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Phone Number</label>
                                <div class="text-slate-900 font-medium">{{ $user->roleable->contact_number ?? $user->roleable->phone }}</div>
                            </div>
                            @endif

                            @if(isset($user->roleable->position))
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Position</label>
                                <div class="text-slate-900 font-medium">{{ $user->roleable->position }}</div>
                            </div>
                            @endif
                            
                            @if(isset($user->roleable->department))
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Department</label>
                                <div class="text-slate-900 font-medium">{{ $user->roleable->department }}</div>
                            </div>
                            @endif
                        @else
                            <div class="md:col-span-2 text-slate-500 italic">No detailed profile information available.</div>
                        @endif
                    </div>
                </div>

                <!-- Account Information -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-lg font-semibold text-slate-800">Account Information</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email Address</label>
                            <div class="text-slate-900 font-medium">{{ $user->email }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Role</label>
                            <div class="text-slate-900 font-medium">{{ ucfirst($user->role->role_name) }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">User ID</label>
                            <div class="text-slate-900 font-medium font-mono">#{{ $user->user_id }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Joined Date</label>
                            <div class="text-slate-900 font-medium">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>
