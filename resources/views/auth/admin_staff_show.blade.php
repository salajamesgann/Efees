<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Account Details - Efees Admin</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.2);
        }
        .info-card {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-slate-900 text-slate-100">
    <!-- Sidebar -->
    <aside class="flex flex-col bg-slate-800 text-slate-300 w-full md:w-72 min-h-screen border-r border-slate-700 overflow-y-auto" id="sidebar" style="scrollbar-width: thin; scrollbar-color: #8b5cf6 transparent;">
        <div class="flex items-center gap-3 px-8 py-6 border-b border-slate-700">
            <div class="w-8 h-8 flex-shrink-0 text-indigo-500">
                <svg class="w-full h-full" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor"></path>
                </svg>
            </div>
            <h1 class="text-indigo-400 font-extrabold text-xl tracking-tight select-none" style="letter-spacing: -0.015em;">Efees Admin</h1>
        </div>
        <nav class="flex flex-col mt-6 px-4 space-y-1 flex-grow">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="{{ route('admin_dashboard') }}">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span class="text-sm font-semibold">Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="{{ route('admin.students.index') }}">
                <i class="fas fa-users w-5"></i>
                <span class="text-sm font-semibold">Manage Students</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-indigo-400 bg-slate-700 border-r-4 border-indigo-500 font-semibold transition-colors duration-200" href="{{ route('admin.staff.index') }}">
                <i class="fas fa-user-tie w-5"></i>
                <span class="text-sm font-semibold">Staff Management</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
                <i class="fas fa-file-invoice-dollar w-5"></i>
                <span class="text-sm font-semibold">Fee Management</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
                <i class="fas fa-chart-bar w-5"></i>
                <span class="text-sm font-semibold">Reports</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
                <i class="fas fa-cog w-5"></i>
                <span class="text-sm font-semibold">Settings</span>
            </a>
        </nav>
        <div class="px-4 py-4 border-t border-slate-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 bg-indigo-500 text-white font-bold text-sm rounded-lg h-10 justify-center cursor-pointer select-none transition-colors duration-300 hover:bg-indigo-600" aria-label="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-8 overflow-y-auto bg-slate-900">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-100">Staff Account Details</h1>
                <p class="text-slate-400 mt-1">Detailed information for {{ $staff->full_name }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.staff.edit', $staff) }}" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-edit"></i>
                    Edit Account
                </a>
                <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Back to Staff
                </a>
            </div>
        </div>

        <!-- Staff Profile Card -->
        <div class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-8 card-hover mb-8">
            <div class="flex flex-col md:flex-row items-start gap-8">
                <!-- Profile Picture -->
                <div class="flex-shrink-0">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-bold text-4xl shadow-lg">
                        {{ $staff->initials }}
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="flex-1 min-w-0">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-100 mb-2">{{ $staff->full_name }}</h2>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-id-badge text-slate-400"></i>
                                    <span class="text-slate-300">Staff ID: {{ $staff->staff_id }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-envelope text-slate-400"></i>
                                    <span class="text-slate-300">{{ $staff->user->email ?? 'No Email' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-phone text-slate-400"></i>
                                    <span class="text-slate-300">{{ $staff->contact_number }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-tag text-slate-400"></i>
                                    <span class="text-slate-300">{{ $staff->user->role->role_name ?? 'No Role' }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-3">Account Information</h3>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-envelope text-slate-400"></i>
                                    <span class="text-slate-300">{{ $staff->user->email ?? 'No Email' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-tag text-slate-400"></i>
                                    <span class="text-slate-300">{{ $staff->user->role->role_name ?? 'No Role' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-phone text-slate-400"></i>
                                    <span class="text-slate-300">{{ $staff->contact_number ?? 'No Phone' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-{{ $staff->is_active ? 'check-circle text-green-400' : 'times-circle text-red-400' }}"></i>
                                    <span class="{{ $staff->is_active ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $staff->is_active ? 'Active Account' : 'Inactive Account' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Information Cards -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Account Information -->
            <div class="info-card rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-indigo-400 flex items-center gap-2">
                    <i class="fas fa-key"></i>
                    Account Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Username:</span>
                        <span class="text-slate-300">{{ $staff->user->email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Role:</span>
                        <span class="text-slate-300">{{ $staff->user->role->role_name ?? 'No Role' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Account Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $staff->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $staff->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Created:</span>
                        <span class="text-slate-300">{{ isset($staff->created_at) ? $staff->created_at->format('M d, Y \a\t h:i A') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Last Updated:</span>
                        <span class="text-slate-300">{{ isset($staff->updated_at) ? $staff->updated_at->format('M d, Y \a\t h:i A') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="info-card rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-indigo-400 flex items-center gap-2">
                    <i class="fas fa-address-book"></i>
                    Personal Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Full Name:</span>
                        <span class="text-slate-300">{{ $staff->full_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Staff ID:</span>
                        <span class="text-slate-300">{{ $staff->staff_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Email:</span>
                        <span class="text-slate-300">{{ $staff->user->email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Phone:</span>
                        <span class="text-slate-300">{{ $staff->contact_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Position:</span>
                        <span class="text-slate-300">{{ $staff->position ?? 'Staff' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Section -->
        <div class="mt-8 bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover">
            <h3 class="text-lg font-semibold mb-4 text-indigo-400">Quick Actions</h3>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.staff.edit', $staff) }}" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-edit"></i>
                    Edit Account
                </a>

                <form method="POST" action="{{ route('admin.staff.toggle-status', $staff) }}" onsubmit="return confirm('Are you sure you want to {{ $staff->is_active ? 'deactivate' : 'activate' }} this staff account?');" class="inline-block">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-2 {{ $staff->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-{{ $staff->is_active ? 'ban' : 'check' }}"></i>
                        {{ $staff->is_active ? 'Deactivate' : 'Activate' }} Account
                    </button>
                </form>

                <button onclick="showPasswordResetModal()" class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-key"></i>
                    Reset Password
                </button>
            </div>
        </div>

        <!-- Password Reset Modal -->
        <div id="passwordResetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-slate-800 rounded-lg p-6 max-w-md w-full mx-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-100">Reset Password</h3>
                    <button onclick="hidePasswordResetModal()" class="text-slate-400 hover:text-slate-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-slate-300 mb-2">This will generate a new temporary password for {{ $staff->full_name }}.</p>
                    <p class="text-sm text-slate-400">The staff member will need to change their password on next login.</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button onclick="hidePasswordResetModal()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <form method="POST" action="{{ route('admin.staff.reset-password', $staff) }}" class="inline-block">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg transition-colors duration-200">
                            Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function showPasswordResetModal() {
            document.getElementById('passwordResetModal').classList.remove('hidden');
        }

        function hidePasswordResetModal() {
            document.getElementById('passwordResetModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('passwordResetModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hidePasswordResetModal();
            }
        });
    </script>
</body>
</html>
