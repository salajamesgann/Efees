<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff Account - Efees Admin</title>
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
        .form-section {
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
                <h1 class="text-3xl font-semibold tracking-tight text-slate-100">Add New Staff Account</h1>
                <p class="text-slate-400 mt-1">Fill out the form below to register a new staff account with proper login credentials and role assignment.</p>
            </div>
            <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-left"></i>
                Back to Staff
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 border border-red-600 text-red-300 bg-red-900/20 rounded-md px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.staff.store') }}" class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover">
            @csrf

            <!-- Personal Information Section -->
            <div class="form-section rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-6 text-indigo-400 flex items-center gap-2">
                    <i class="fas fa-user"></i>
                    Personal Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-slate-300">First Name *</label>
                        <input name="first_name" value="{{ old('first_name') }}" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-slate-300">Middle Initial</label>
                        <input name="middle_initial" value="{{ old('middle_initial') }}" type="text" maxlength="1" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" placeholder="M.I." />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-slate-300">Last Name *</label>
                        <input name="last_name" value="{{ old('last_name') }}" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-slate-300">Phone Number</label>
                        <input name="phone_number" value="{{ old('phone_number') }}" type="tel" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" placeholder="+1234567890" />
                        <p class="text-xs text-slate-400 mt-1">Optional, useful for SMS notifications</p>
                    </div>
                </div>
            </div>

            <!-- Account Details Section -->
            <div class="form-section rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-6 text-indigo-400 flex items-center gap-2">
                    <i class="fas fa-key"></i>
                    Account Details
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-slate-300">Email Address *</label>
                        <input name="email" value="{{ old('email') }}" type="email" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" required />
                        <p class="text-xs text-slate-400 mt-1">Must be unique; used for login, notifications, password reset</p>
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-medium mb-2 text-slate-300">Password *</label>
                        <input name="password" type="password" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" required />
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePasswordVisibility('password')">
                            <i class="fas fa-eye text-slate-400 hover:text-slate-300"></i>
                        </button>
                        <p class="text-xs text-slate-400 mt-1">Must be at least 8 characters with uppercase, lowercase, number, and special character</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-slate-300">Confirm Password *</label>
                        <input name="password_confirmation" type="password" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" required />
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="bg-yellow-900/20 border border-yellow-600 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                    <div>
                        <h4 class="text-sm font-medium text-yellow-300 mb-1">Security Notice</h4>
                        <p class="text-xs text-yellow-200">
                            The staff member will receive login credentials and be able to access the system based on their assigned role.
                            Ensure you have obtained proper consent before creating accounts.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.staff.index') }}" class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold rounded-lg transition-colors duration-200">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-lg transition-colors duration-200">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Staff Account
                </button>
            </div>
        </form>
    </main>

    <script>
        // Password visibility toggle
        function togglePasswordVisibility(fieldName) {
            const field = document.querySelector(`input[name="${fieldName}"]`);
            const icon = field.nextElementSibling.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength indicator
        document.querySelector('input[name="password"]').addEventListener('input', function() {
            const password = this.value;
            const requirements = [
                { regex: /.{8,}/, element: 'length' },
                { regex: /[A-Z]/, element: 'uppercase' },
                { regex: /[a-z]/, element: 'lowercase' },
                { regex: /\d/, element: 'number' },
                { regex: /[@$!%*?&]/, element: 'special' }
            ];

            requirements.forEach(req => {
                const indicator = document.getElementById(`req-${req.element}`);
                if (indicator) {
                    indicator.classList.toggle('text-green-400', req.regex.test(password));
                    indicator.classList.toggle('text-slate-500', !req.regex.test(password));
                }
            });
        });
    </script>
</body>
</html>
