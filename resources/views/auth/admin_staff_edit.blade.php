<!DOCTYPE html>
<html lang="en">
<head>
<<<<<<< HEAD
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit Staff - Efees Admin</title>
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
  </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
  <!-- Mobile Sidebar Overlay -->
  <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" x-cloak></div>

  <!-- Sidebar -->
  <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 z-40 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 md:translate-x-0 shadow-2xl md:shadow-none" id="sidebar">
      <!-- Header -->
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
          <button @click="sidebarOpen = false" class="md:hidden p-2 text-slate-400 hover:text-red-500 transition-colors rounded-lg hover:bg-slate-50">
              <i class="fas fa-times text-xl"></i>
          </button>
      </div>

      <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow pb-6 overflow-y-auto custom-scrollbar">
          <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
          
          <!-- Dashboard -->
          <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin_dashboard') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin_dashboard') }}">
              <div class="w-8 flex justify-center">
                  <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('admin_dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
              </div>
              <span class="text-sm font-medium">Dashboard</span>
          </a>


          <!-- Student Management (Users) -->
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
      <main class="flex-1 p-8 overflow-y-auto bg-gray-50">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Edit Staff Account</h1>
      <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
        <i class="fas fa-arrow-left"></i>
        Back to Staff List
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

    <form method="POST" action="{{ route('admin.staff.update', $staff) }}" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 card-hover">
      @csrf
      @method('PUT')

      <h2 class="text-xl font-semibold mb-6 text-blue-600">Staff Information</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">First Name</label>
          <input name="first_name" value="{{ old('first_name', $staff->first_name) }}" type="text" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Middle Initial</label>
          <input name="middle_initial" value="{{ old('middle_initial', $staff->MI) }}" type="text" maxlength="1" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Last Name</label>
          <input name="last_name" value="{{ old('last_name', $staff->last_name) }}" type="text" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Phone Number</label>
          <input name="phone_number" value="{{ old('phone_number', $staff->contact_number) }}" type="text" placeholder="e.g. 09123456789" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>
        
        <div>
            <!-- Role update might not be allowed in simple edit, but let's keep it if needed or display as read-only if not editable -->
            <label class="block text-sm font-medium mb-2 text-gray-700">Position</label>
            <input type="text" value="{{ $staff->position }}" disabled class="w-full rounded-lg border border-gray-200 bg-gray-100 text-gray-500 px-3 py-2 cursor-not-allowed" />
            <p class="text-xs text-gray-500 mt-1">Position cannot be changed directly.</p>
        </div>
      </div>

      <h2 class="text-xl font-semibold mb-6 text-blue-600 pt-6 border-t border-gray-100">Account Credentials</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium mb-2 text-gray-700">Email Address</label>
          <input name="email" value="{{ old('email', $staff->user->email ?? '') }}" type="email" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
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
        <a href="{{ route('admin.staff.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-colors duration-200">
          Cancel
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all duration-200 transform hover:-translate-y-0.5">
          Update Account
        </button>
      </div>
    </form>
  </main>
  </div>
=======
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff Account - Efees Admin</title>
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
                <h1 class="text-3xl font-semibold tracking-tight text-slate-100">Edit Staff Account</h1>
                <p class="text-slate-400 mt-1">Update {{ $staff->full_name }}'s account information and settings</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.staff.show', $staff) }}" class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-eye"></i>
                    View Details
                </a>
                <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Back to Staff
                </a>
            </div>
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

        <form method="POST" action="{{ route('admin.staff.update', $staff) }}" class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover">
            @csrf
            @method('PUT')

            <!-- Staff Info Header -->
            <div class="bg-slate-700 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        {{ $staff->initials }}
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-slate-100">{{ $staff->full_name }}</h2>
                        <p class="text-slate-400">{{ $staff->position }} @if($staff->department) • {{ $staff->department }} @endif</p>
                        <p class="text-sm text-slate-500">Staff ID: {{ $staff->staff_id }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $staff->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $staff->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="form-section rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-6 text-indigo-400 flex items-center gap-2">
                    <i class="fas fa-user"></i>
                    Personal Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-slate-300">First Name *</label>
                        <input name="first_name" value="{{ old('first_name', $staff->first_name) }}" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-slate-300">Middle Initial</label>
                        <input name="middle_initial" value="{{ old('middle_initial', $staff->MI) }}" type="text" maxlength="1" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" placeholder="M.I." />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-slate-300">Last Name *</label>
                        <input name="last_name" value="{{ old('last_name', $staff->last_name) }}" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-slate-300">Phone Number</label>
                        <input name="phone_number" value="{{ old('phone_number', $staff->contact_number) }}" type="tel" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" placeholder="+1234567890" />
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
                        <input name="email" value="{{ old('email', $staff->user->email ?? '') }}" type="email" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" required />
                        <p class="text-xs text-slate-400 mt-1">Must be unique; used for login, notifications, password reset</p>
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-medium mb-2 text-slate-300">Password *</label>
                        <input name="password" type="password" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" />
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePasswordVisibility('password')">
                            <i class="fas fa-eye text-slate-400 hover:text-slate-300"></i>
                        </button>
                        <p class="text-xs text-slate-400 mt-1">Leave blank to keep current password</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-slate-300">Confirm New Password</label>
                        <input name="password_confirmation" type="password" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" />
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
                            The staff member will be able to access the system based on their assigned role.
                            Ensure you have proper authorization before updating accounts.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.staff.index') }}" class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold rounded-lg transition-colors duration-200">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-lg transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Update Staff Account
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
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
</body>
</html>
