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
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-slate-50 font-sans text-slate-900" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" style="display: none;"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white text-slate-800 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 border-r border-slate-200 flex flex-col shrink-0 shadow-[4px_0_24px_-12px_rgba(0,0,0,0.1)]" id="sidebar">
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
            
            <!-- Student Management -->
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

            <!-- Settings -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.settings.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-cog text-lg {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Settings</span>
            </a>
        </nav>

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
        <main class="flex-1 overflow-y-auto bg-slate-50 p-6 lg:p-8">
            <div class="max-w-3xl mx-auto space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">User Profile</h1>
                        <p class="text-sm text-slate-500 mt-1">View user details</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Back to List
                        </a>
                        <a href="{{ route('admin.staff.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Edit User
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center space-x-6 mb-6">
                            <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-3xl">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                                <p class="text-slate-500">
                                    @if($user->role->role_name === 'staff' && $user->roleable)
                                        {{ $user->roleable->position }}
                                    @else
                                        {{ ucfirst($user->role->role_name) }}
                                    @endif
                                </p>
                                <div class="mt-2 flex items-center gap-2">
                                    @php
                                        $isActive = false;
                                        if ($user->role->role_name === 'staff' && $user->roleable) {
                                            $isActive = $user->roleable->is_active;
                                        } elseif ($user->role->role_name === 'parent' && $user->roleable) {
                                            $isActive = $user->roleable->account_status === 'Active';
                                        }
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $isActive ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-2">Personal Information</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-xs text-slate-500">Full Name</dt>
                                        <dd class="text-sm font-medium text-slate-900">{{ $user->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-slate-500">User ID</dt>
                                        <dd class="text-sm font-medium text-slate-900">
                                            @if($user->role->role_name === 'staff' && $user->roleable)
                                                {{ $user->roleable->staff_id }}
                                            @elseif($user->role->role_name === 'parent')
                                                P-{{ $user->user_id }}
                                            @else
                                                {{ $user->user_id }}
                                            @endif
                                        </dd>
                                    </div>
                                    @if($user->role->role_name === 'staff' && $user->roleable)
                                        <div>
                                            <dt class="text-xs text-slate-500">Date of Birth</dt>
                                            <dd class="text-sm font-medium text-slate-900">{{ $user->roleable->birth_date ? \Carbon\Carbon::parse($user->roleable->birth_date)->format('F j, Y') : 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-slate-500">Gender</dt>
                                            <dd class="text-sm font-medium text-slate-900">{{ ucfirst($user->roleable->gender ?? 'N/A') }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                            
                            <div>
                                @if($user->role->role_name === 'staff' && $user->roleable)
                                    <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-2">Employment Details</h3>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-xs text-slate-500">Department</dt>
                                            <dd class="text-sm font-medium text-slate-900">{{ $user->roleable->department }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-slate-500">Position</dt>
                                            <dd class="text-sm font-medium text-slate-900">{{ $user->roleable->position }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-slate-500">Hire Date</dt>
                                            <dd class="text-sm font-medium text-slate-900">{{ $user->roleable->hire_date ? \Carbon\Carbon::parse($user->roleable->hire_date)->format('F j, Y') : 'N/A' }}</dd>
                                        </div>
                                    </dl>
                                @elseif($user->role->role_name === 'parent' && $user->roleable)
                                    <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-2">Children / Students</h3>
                                    <dl class="space-y-3">
                                        @if($user->roleable->students->count() > 0)
                                            <div class="space-y-2">
                                                @foreach($user->roleable->students as $student)
                                                    <div class="flex items-center gap-3 p-2 rounded-lg bg-slate-50 border border-slate-100">
                                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                                            {{ substr($student->first_name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-medium text-slate-900">
                                                                <a href="{{ route('admin.students.index', ['search' => $student->student_id]) }}" class="hover:text-blue-600 hover:underline">
                                                                    {{ $student->first_name }} {{ $student->last_name }}
                                                                </a>
                                                            </div>
                                                            <div class="text-xs text-slate-500">
                                                                {{ $student->student_id }} • {{ $student->grade_level }}
                                                                @if($student->pivot->relationship)
                                                                    • {{ ucfirst($student->pivot->relationship) }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-sm text-slate-500 italic">No students associated</div>
                                        @endif
                                    </dl>
                                @endif
                            </div>

                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-2">Contact Information</h3>
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                                    <div>
                                        <dt class="text-xs text-slate-500">Email Address</dt>
                                        <dd class="text-sm font-medium text-slate-900">{{ $user->email }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-slate-500">Phone Number</dt>
                                        <dd class="text-sm font-medium text-slate-900">
                                            @if($user->role->role_name === 'staff' && $user->roleable)
                                                {{ $user->roleable->contact_number }}
                                            @elseif($user->role->role_name === 'parent' && $user->roleable)
                                                {{ $user->roleable->phone }}
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="md:col-span-2">
                                        <dt class="text-xs text-slate-500">Address</dt>
                                        <dd class="text-sm font-medium text-slate-900">
                                            @if($user->role->role_name === 'staff' && $user->roleable)
                                                {{ $user->roleable->address ?? 'N/A' }}
                                            @elseif($user->role->role_name === 'parent' && $user->roleable)
                                                {{ implode(', ', array_filter([$user->roleable->address_street, $user->roleable->address_barangay, $user->roleable->address_city, $user->roleable->address_province])) ?: 'N/A' }}
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
