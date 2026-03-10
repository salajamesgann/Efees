<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit User - Efees Admin</title>
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
      <main class="flex-1 p-8 md:h-screen overflow-y-auto bg-gray-50 custom-scrollbar">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Edit User Account</h1>
      <a href="{{ route('super_admin.users.index') }}" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
        <i class="fas fa-arrow-left"></i>
        Back to List
      </a>
    </div>

    @if ($errors->any())
      <div class="mb-6 border border-red-200 text-red-800 bg-red-50 rounded-md px-4 py-3">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @php
        $firstName = '';
        $middleInitial = '';
        $lastName = '';

        if ($user->role->role_name === 'staff' && $user->roleable) {
            $firstName = $user->roleable->first_name;
            $middleInitial = $user->roleable->MI;
            $lastName = $user->roleable->last_name;
        } else {
            // Fallback for parent or others using user->name
            $parts = explode(' ', $user->name);
            $firstName = $parts[0] ?? '';
            $lastName = isset($parts[1]) ? implode(' ', array_slice($parts, 1)) : '';
        }
        
        $phone = '';
        if ($user->role->role_name === 'staff' && $user->roleable) {
            $phone = $user->roleable->contact_number;
        } elseif ($user->role->role_name === 'parent' && $user->roleable) {
            $phone = $user->roleable->phone;
        }
    @endphp

    <form method="POST" action="{{ route('super_admin.users.update', $user) }}" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 card-hover" x-data="{ submitting: false }" @submit="submitting = true">
      @csrf
      @method('PUT')

      <h2 class="text-xl font-semibold mb-6 text-blue-600">User Information</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">First Name</label>
          <input name="first_name" value="{{ old('first_name', $firstName) }}" type="text" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Middle Initial</label>
          <input name="middle_initial" value="{{ old('middle_initial', $middleInitial) }}" type="text" maxlength="1" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Last Name</label>
          <input name="last_name" value="{{ old('last_name', $lastName) }}" type="text" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Phone Number</label>
          <input name="phone_number" value="{{ old('phone_number', $phone) }}" type="text" placeholder="e.g. 09123456789" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>
        
        <div>
            <!-- Role update might not be allowed in simple edit, but let's keep it if needed or display as read-only if not editable -->
            <label class="block text-sm font-medium mb-2 text-gray-700">Role / Position</label>
            <input type="text" value="{{ $user->role->role_name === 'staff' && $user->roleable ? $user->roleable->position : ucfirst($user->role->role_name) }}" disabled class="w-full rounded-lg border border-gray-200 bg-gray-100 text-gray-500 px-3 py-2 cursor-not-allowed" />
            <p class="text-xs text-gray-500 mt-1">Role/Position cannot be changed directly.</p>
        </div>
      </div>

      <h2 class="text-xl font-semibold mb-6 text-blue-600 pt-6 border-t border-gray-100">Account Credentials</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Email Address</label>
          <input name="email" value="{{ old('email', $user->email) }}" type="email" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">New Password (Optional)</label>
          <input name="password" type="password" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
          <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password.</p>
        </div>

        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Confirm New Password</label>
          <input name="password_confirmation" type="password" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>
      </div>

      <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-100">
        <a href="{{ route('super_admin.users.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-colors duration-200">
          Cancel
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all duration-200 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="submitting">
          <span x-show="!submitting">Update Account</span>
          <span x-show="submitting" class="inline-flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Updating...
          </span>
        </button>
      </div>
    </form>
  </main>
  </div>
</body>
</html>
