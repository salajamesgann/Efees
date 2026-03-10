<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management - Efees Admin</title>
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
<body class="flex flex-col md:flex-row h-screen overflow-hidden bg-slate-50 font-sans text-slate-900" x-data="{ sidebarOpen: false }">
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
            <div class="max-w-7xl mx-auto space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">User Management</h1>
                        <p class="text-sm text-slate-500 mt-1">Manage staff and parent accounts</p>
                    </div>
                    <a href="{{ route('super_admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors shadow-sm font-medium">
                        <i class="fas fa-plus"></i>
                        <span>Add User</span>
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
                        <form method="GET" action="{{ route('super_admin.users.index') }}" class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
                            </div>
                            <select name="role" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                                <option value="all" {{ ($role_filter ?? 'all') == 'all' ? 'selected' : '' }}>All Roles</option>
                                <option value="admin" {{ ($role_filter ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ ($role_filter ?? '') == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="parent" {{ ($role_filter ?? '') == 'parent' ? 'selected' : '' }}>Parent</option>
                            </select>
                            <select name="status" class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">User Details</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role / Position</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Contact</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($users as $user)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-slate-900">{{ $user->name }}</div>
                                                    <div class="text-xs text-slate-500">
                                                        @if($user->role->role_name === 'staff' && $user->roleable)
                                                            ID: {{ $user->roleable->staff_id }}
                                                        @elseif($user->role->role_name === 'parent')
                                                            ID: P-{{ $user->user_id }}
                                                        @else
                                                            ID: {{ $user->user_id }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-slate-900">{{ ucfirst($user->role->role_name) }}</div>
                                            <div class="text-xs text-slate-500">
                                                @if($user->role->role_name === 'staff' && $user->roleable)
                                                    {{ $user->roleable->department ?? 'N/A' }} - {{ $user->roleable->position ?? 'N/A' }}
                                                @elseif($user->role->role_name === 'parent')
                                                    Guardian Account
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-slate-900">{{ $user->email }}</div>
                                            <div class="text-xs text-slate-500">
                                                @if($user->role->role_name === 'staff' && $user->roleable)
                                                    {{ $user->roleable->contact_number }}
                                                @elseif($user->role->role_name === 'parent' && $user->roleable)
                                                    {{ $user->roleable->phone }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                // Handle different status fields for different user types
                                                if ($user->role->role_name === 'parent' && $user->roleable) {
                                                    $isActive = $user->roleable->account_status === 'Active';
                                                    $statusText = $user->roleable->account_status;
                                                } else {
                                                    $isActive = $user->is_active;
                                                    $statusText = $isActive ? 'Active' : 'Inactive';
                                                }
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <!-- View Action -->
                                                <a href="{{ route('super_admin.users.show', $user) }}" 
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                                                   title="View User">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </a>
                                                
                                                <!-- Edit Action -->
                                                <a href="{{ route('super_admin.users.edit', $user) }}" 
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 transition-colors duration-200"
                                                   title="Edit User">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </a>
                                                
                                                <!-- Toggle Status Action -->
                                                <form action="{{ route('super_admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    @if($user->role->role_name === 'parent' && $user->roleable)
                                                        @if($user->roleable->account_status === 'Active')
                                                            <button type="submit" 
                                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition-colors duration-200"
                                                                    title="Archive Parent">
                                                                <i class="fas fa-archive text-sm"></i>
                                                            </button>
                                                        @else
                                                            <button type="submit" 
                                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-700 transition-colors duration-200"
                                                                    title="Activate Parent">
                                                                <i class="fas fa-check text-sm"></i>
                                                            </button>
                                                        @endif
                                                    @else
                                                        @if($isActive)
                                                            <button type="submit" 
                                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition-colors duration-200"
                                                                    title="Deactivate User">
                                                                <i class="fas fa-ban text-sm"></i>
                                                            </button>
                                                        @else
                                                            <button type="submit" 
                                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-700 transition-colors duration-200"
                                                                    title="Activate User">
                                                                <i class="fas fa-check text-sm"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </form>
                                                
                                                <!-- Delete Action (only for inactive users) -->
                                                @if($user->role)
                                                    @if($user->role->role_name === 'parent' && $user->roleable)
                                                        @if($user->roleable->account_status === 'Archived')
                                                            <form action="{{ route('super_admin.users.destroy', $user) }}" method="POST" class="inline" 
                                                                  onsubmit="return confirm('Are you sure you want to delete this user account? This action cannot be undone.');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition-colors duration-200"
                                                                        title="Delete User">
                                                                    <i class="fas fa-trash text-sm"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @elseif(!$isActive)
                                                        <form action="{{ route('super_admin.users.destroy', $user) }}" method="POST" class="inline" 
                                                              onsubmit="return confirm('Are you sure you want to delete this user account? This action cannot be undone.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition-colors duration-200"
                                                                    title="Delete User">
                                                                <i class="fas fa-trash text-sm"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-slate-500">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-200">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
