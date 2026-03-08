<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Create Staff - Efees Admin</title>
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
      body { font-family: 'Inter', 'Noto Sans', sans-serif; }
      [x-cloak] { display: none !important; }
      .card-hover {
          transition: all 0.3s ease;
      }
      .card-hover:hover {
          transform: translateY(-5px);
          box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.2);
      }
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
    @include('layouts.admin_sidebar')

  <!-- Main content -->
  <main class="flex-1 p-8 md:h-screen overflow-y-auto custom-scrollbar">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Create User Account</h1>
      <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
        <i class="fas fa-arrow-left"></i>
        Back to User List
      </a>
    </div>

    @if ($errors->any())
      <div class="mb-6 border border-red-200 text-red-800 bg-red-50 rounded-lg px-4 py-3">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.staff.store') }}" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 card-hover" novalidate x-data="{ submitting: false }" @submit="submitting = true">
      @csrf

      <h2 class="text-xl font-semibold mb-6 text-blue-600">User Information</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">First Name</label>
          <input name="first_name" value="{{ old('first_name') }}" type="text" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Middle Initial</label>
          <input name="middle_initial" value="{{ old('middle_initial') }}" type="text" maxlength="1" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Last Name</label>
          <input name="last_name" value="{{ old('last_name') }}" type="text" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Phone Number</label>
          <input name="phone_number" value="{{ old('phone_number') }}" type="text" placeholder="e.g. 09123456789" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>

        <div>
            <label class="block text-sm font-medium mb-2 text-gray-700">Role</label>
          <select name="role_name" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @foreach($roles as $role)
                <option value="{{ $role->role_name }}" {{ old('role_name') === $role->role_name ? 'selected' : '' }}>{{ ucfirst($role->role_name) }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <h2 class="text-xl font-semibold mb-6 text-blue-600 pt-6 border-t border-gray-100">Account Credentials</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Email Address</label>
          <input name="email" value="{{ old('email') }}" type="email" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Password</label>
          <div x-data="{ show: false }" class="relative">
            <input name="password" :type="show ? 'text' : 'password'" 
                   :required="document.querySelector('select[name=role_name]').value !== 'parent'"
                   class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
              <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
          <p x-show="document.querySelector('select[name=role_name]').value === 'parent'" class="text-xs text-blue-600 mt-1">
            <i class="fas fa-info-circle"></i> Leave empty for Gmail addresses to send password setup email
          </p>
          <p x-show="document.querySelector('select[name=role_name]').value !== 'parent'" class="text-xs text-gray-500 mt-1">
            Must contain at least 8 characters, one uppercase, one lowercase, one number, and one special character.
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Confirm Password</label>
          <div x-data="{ show: false }" class="relative">
            <input name="password_confirmation" :type="show ? 'text' : 'password'" 
                   :required="document.querySelector('select[name=role_name]').value !== 'parent'"
                   class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
              <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
          <p x-show="document.querySelector('select[name=role_name]').value === 'parent'" class="text-xs text-blue-600 mt-1">
            <i class="fas fa-info-circle"></i> Optional for parent accounts with Gmail
          </p>
        </div>
      </div>

      <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-100">
        <a href="{{ route('admin.staff.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-colors duration-200">
          Cancel
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all duration-200 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="submitting">
          <span x-show="!submitting">Create Account</span>
          <span x-show="submitting" class="inline-flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Creating...
          </span>
        </button>
      </div>
    </form>
  </main>
  </div>
</body>
</html>
