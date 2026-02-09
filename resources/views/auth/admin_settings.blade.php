<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Settings - Efees Admin</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between bg-white border-b border-slate-200 px-4 py-3 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
                <i class="fas fa-user-shield text-lg"></i>
            </div>
            <span class="font-bold text-slate-800 text-lg">Efees Admin</span>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 md:hidden" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 z-40 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 md:translate-x-0 shadow-2xl md:shadow-none">
        <div class="flex items-center gap-3 px-8 py-6 border-b border-slate-100 bg-white/50 backdrop-blur-sm sticky top-0 z-10">
            <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <i class="fas fa-user-shield text-lg"></i>
            </div>
            <div>
                <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Efees Admin</h1>
                <p class="text-xs text-slate-500 font-medium">Administration</p>
            </div>
        </div>

        <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow overflow-y-auto pb-6 custom-scrollbar">
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
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fees.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}">
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
  <main class="flex-1 p-8">
    <h1 class="text-2xl font-semibold mb-6">System Settings</h1>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-lg border border-gray-200 p-6">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Active School Year</label>
            <input type="text" name="school_year" value="{{ old('school_year', optional($settings['school_year'] ?? null)->value) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="e.g. 2025-2026" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Semester</label>
            <input type="text" name="semester" value="{{ old('semester', optional($settings['semester'] ?? null)->value) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="e.g. First Semester" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Penalty Rate</label>
            <input type="text" name="penalty_rate" value="{{ old('penalty_rate', optional($settings['penalty_rate'] ?? null)->value) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="e.g. 5%" />
        </div>
        
        <div class="mb-6 pt-4 border-t border-gray-200 space-y-4">
            <h2 class="text-lg font-semibold mb-2">System Behavior</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Auto-generate fees on enrollment</p>
                    <p class="text-xs text-gray-500 mt-1">Automatically create fee records when a new student is enrolled.</p>
                </div>
                <div>
                    <input type="checkbox" id="auto_generate_fees_on_enrollment" name="auto_generate_fees_on_enrollment" value="1"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           {{ (optional($settings['auto_generate_fees_on_enrollment'] ?? null)->value == '1') ? 'checked' : '' }} />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Enable SMS notifications</p>
                    <p class="text-xs text-gray-500 mt-1">Global master switch for all SMS notifications and reminders.</p>
                </div>
                <div>
                    <input type="checkbox" id="notifications_enabled" name="notifications_enabled" value="1"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           {{ (optional($settings['notifications_enabled'] ?? null)->value == '1') ? 'checked' : '' }} />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Maintenance mode</p>
                    <p class="text-xs text-gray-500 mt-1">When enabled, non-admin users are limited to view-only access.</p>
                </div>
                <div>
                    <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           {{ (optional($settings['maintenance_mode'] ?? null)->value == '1') ? 'checked' : '' }} />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Allow staff to edit fee records</p>
                    <p class="text-xs text-gray-500 mt-1">When disabled, staff accounts can only view fee information and cannot approve or modify fees.</p>
                </div>
                <div>
                    <input type="checkbox" id="allow_staff_edit_fees" name="allow_staff_edit_fees" value="1"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           {{ (optional($settings['allow_staff_edit_fees'] ?? null)->value == '1') ? 'checked' : '' }} />
                </div>
            </div>
        </div>

        <div class="mb-6 pt-4 border-t border-gray-200 space-y-4">
            <h2 class="text-lg font-semibold mb-2">Security</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Login Attempts</label>
                    <input type="number" min="3" max="20" name="max_login_attempts" value="{{ old('max_login_attempts', optional($settings['max_login_attempts'] ?? null)->value) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="e.g. 5" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lockout Minutes</label>
                    <input type="number" min="1" max="1440" name="lockout_minutes" value="{{ old('lockout_minutes', optional($settings['lockout_minutes'] ?? null)->value) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="e.g. 15" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password Expiry Days</label>
                    <input type="number" min="7" max="365" name="password_expiry_days" value="{{ old('password_expiry_days', optional($settings['password_expiry_days'] ?? null)->value) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="e.g. 90" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg" type="submit">Save</button>
            <a href="{{ route('admin_dashboard') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">Back</a>
        </div>
    </form>

    <section class="mt-8 bg-white rounded-lg border border-red-200 p-6">
        <h2 class="text-lg font-semibold text-red-700 mb-2">Danger Zone</h2>
        <p class="text-sm text-gray-700 mb-4">
            This will permanently remove all students, their fee records, payments, and parent accounts.
            This action is intended for clearing demo or test data only.
        </p>
        <form method="POST" action="{{ route('admin.settings.reset-demo') }}" class="space-y-4">
            @csrf
            <div>
                <label for="confirm" class="block text-sm font-medium text-gray-700">
                    Type RESET to confirm
                </label>
                <input
                    id="confirm"
                    name="confirm"
                    type="text"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    placeholder="RESET"
                    autocomplete="off"
                />
            </div>
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold"
                onclick="return confirm('Are you absolutely sure? This will delete all students and parent accounts.');"
            >
                Reset demo data
            </button>
        </form>
    </section>
</main>
</body>
</html>
