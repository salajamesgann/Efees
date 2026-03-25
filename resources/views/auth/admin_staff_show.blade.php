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
<body class="flex flex-col md:flex-row min-h-screen bg-slate-50 font-sans text-slate-900" x-data="{ sidebarOpen: false }">
    @include('layouts.admin_sidebar')

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
        <main class="flex-1 md:h-screen overflow-y-auto bg-slate-50 p-6 lg:p-8 custom-scrollbar">
            <div class="max-w-3xl mx-auto space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">User Profile</h1>
                        <p class="text-sm text-slate-500 mt-1">View user details</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('super_admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Back to List
                        </a>
                        <a href="{{ route('super_admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Edit User
                        </a>
                        @if($user->role && $user->role->role_name !== 'admin')
                            <form action="{{ route('super_admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user account? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Delete User
                                </button>
                            </form>
                        @endif
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
                                        if ($user->role->role_name === 'admin') {
                                            $isActive = true; // Admin accounts are always active
                                        } elseif ($user->role->role_name === 'staff' && $user->roleable) {
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
