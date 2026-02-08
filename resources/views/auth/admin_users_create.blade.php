<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create User - Efees Admin</title>
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
        <main class="flex-1 overflow-y-auto bg-slate-50 p-6 lg:p-8">
            <div class="max-w-4xl mx-auto space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Create New User</h1>
                        <p class="text-sm text-slate-500 mt-1">Add a new Admin, Staff, or Parent account</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg transition-colors shadow-sm font-medium">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to List</span>
                    </a>
                </div>

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-md">
                  <i class="fas fa-info-circle mr-2"></i>
                  Note: Student accounts are created automatically via the Student Enrollment process.
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ role: '{{ old('role_name', request('role', '')) }}' }">
                    <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Personal Information</h3>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="role_name" class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
                                <select name="role_name" id="role_name" x-model="role" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->role_name }}" {{ old('role_name', request('role')) == $role->role_name ? 'selected' : '' }}>{{ ucfirst($role->role_name) }}</option>
                                    @endforeach
                                </select>
                                @error('role_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Staff/Admin Fields -->
                            <div x-show="role !== 'parent'" class="contents">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1">First Name <span class="text-red-500" x-show="role !== 'parent'">*</span></label>
                                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" :required="role !== 'parent'">
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="middle_initial" class="block text-sm font-medium text-slate-700 mb-1">Middle Initial</label>
                                    <input type="text" name="middle_initial" id="middle_initial" value="{{ old('middle_initial') }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" maxlength="1">
                                    @error('middle_initial')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">Last Name <span class="text-red-500" x-show="role !== 'parent'">*</span></label>
                                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" :required="role !== 'parent'">
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Parent Fields -->
                            <div x-show="role === 'parent'" x-cloak class="md:col-span-2">
                                <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500" x-show="role === 'parent'">*</span></label>
                                <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" :required="role === 'parent'">
                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Account Information -->
                            <div class="md:col-span-2 mt-4">
                                <h3 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Account Credentials</h3>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password" id="password" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <p class="text-xs text-slate-500 mt-1">Must contain 8+ chars, uppercase, lowercase, number, special char.</p>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100">
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 rounded-lg font-medium transition-colors">Cancel</a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-sm">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
