<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Create Student - Efees Admin</title>
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
<body class="bg-slate-50 font-sans text-slate-900 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
  <!-- Mobile Sidebar Overlay -->
  <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" x-cloak></div>

  <!-- Sidebar -->
  <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white text-slate-800 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 border-r border-slate-200 flex flex-col shrink-0 shadow-[4px_0_24px_-12px_rgba(0,0,0,0.1)]" id="sidebar">
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

      <!-- Main content -->
      <main class="flex-1 p-8 overflow-y-auto bg-slate-50 custom-scrollbar">
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Create Student</h1>
            <p class="text-sm text-slate-500 mt-1">Add a new student to the system</p>
          </div>
          <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold px-4 py-2 rounded-xl transition-all duration-200 shadow-sm hover:text-blue-600 hover:shadow-md">
            <i class="fas fa-arrow-left"></i>
            Back to Students
          </a>
        </div>

        @if ($errors->any())
          <div class="mb-6 border border-red-200 text-red-800 bg-red-50 rounded-xl px-4 py-3 shadow-sm">
            <ul class="list-disc list-inside text-sm">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.students.store') }}" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
          @csrf

          <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
              <i class="fas fa-user text-lg"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-800">Student Information</h2>
          </div>

          <!-- Personal Information Section -->
          <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">First Name <span class="text-red-500">*</span></label>
                <input name="first_name" value="{{ old('first_name') }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required />
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Middle Name</label>
                <input name="middle_name" value="{{ old('middle_name') }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Last Name <span class="text-red-500">*</span></label>
                <input name="last_name" value="{{ old('last_name') }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required />
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Suffix</label>
                <input name="suffix" value="{{ old('suffix') }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Jr., Sr., III" />
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Gender <span class="text-red-500">*</span></label>
                <select name="sex" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required>
                  <option value="">Select Gender</option>
                  <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                  <option value="Other" {{ old('sex') === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Date of Birth</label>
                <input id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" type="date" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Age</label>
                <input id="age" type="text" class="w-full rounded-xl border-slate-200 bg-slate-100 text-slate-800 px-4 py-2.5 focus:outline-none shadow-sm" readonly disabled />
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Nationality</label>
                <input name="nationality" value="{{ old('nationality') }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
              </div>

              <div class="md:col-span-3">
                <label class="block text-sm font-semibold mb-2 text-slate-700">Address</label>
                <input name="address" value="{{ old('address') }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Complete home address" />
              </div>
            </div>
          </div>

          <!-- Academic Information Section -->
          <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Academic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Grade Level <span class="text-red-500">*</span></label>
                <select name="level" id="grade-level" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required>
                  <option value="">Select Grade</option>
                  @foreach(['Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6','Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $grade)
                    <option value="{{ $grade }}" {{ old('level') === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                  @endforeach
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Section <span class="text-red-500">*</span></label>
                <input name="section" value="{{ old('section') }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required />
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">School Year</label>
                <input name="school_year" value="{{ old('school_year') }}" placeholder="e.g. 2024-2025" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
              </div>

              <div id="category-field" class="hidden">
                <label class="block text-sm font-semibold mb-2 text-slate-700">SHS Strand</label>
                <select name="strand" id="category" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                  <option value="">Select Strand</option>
                  @foreach(['STEM','ABM','HUMSS','GAS'] as $cat)
                    <option value="{{ $cat }}" {{ old('strand') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                  @endforeach
                </select>
              </div>
              <div id="voucher-field" class="hidden">
                <label class="block text-sm font-semibold mb-2 text-slate-700">SHS Funding Type</label>
                <select name="shs_voucher_type" id="shs_voucher_type" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                  <option value="">Select Type</option>
                  <option value="regular" {{ old('shs_voucher_type') === 'regular' ? 'selected' : '' }}>Regular</option>
                  <option value="shs_voucher" {{ old('shs_voucher_type') === 'shs_voucher' ? 'selected' : '' }}>Senior High School (SHS) Voucher</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Parent/Guardian Information Section -->
          <div class="mb-8" x-data="{ 
              parentMode: '{{ old('parent_mode', 'new') }}',
              search: '',
              parents: {{ json_encode($existingParents ?? []) }},
              showDropdown: false,
              get filteredParents() {
                  if (this.search === '') return []; 
                  return this.parents.filter(p => p.label.toLowerCase().includes(this.search.toLowerCase())).slice(0, 10);
              },
              selectParent(p) {
                  this.search = p.label;
                  $refs.parentIdInput.value = p.id;
                  this.showDropdown = false;
              }
          }">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Parent/Guardian Information</h3>
            
            <!-- Parent Mode Selection -->
            <div class="mb-6">
                <label class="text-sm font-semibold text-slate-700 block mb-2">Parent Account Mode</label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm hover:bg-slate-50 transition-colors">
                        <input type="radio" name="parent_mode" value="new" x-model="parentMode" class="text-blue-600 focus:ring-blue-500" />
                        <span class="text-sm text-slate-700 font-medium">Create New Parent Account</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm hover:bg-slate-50 transition-colors">
                        <input type="radio" name="parent_mode" value="existing" x-model="parentMode" class="text-blue-600 focus:ring-blue-500" />
                        <span class="text-sm text-slate-700 font-medium">Link Existing Parent</span>
                    </label>
                </div>
            </div>

            <!-- New Parent Fields -->
            <div x-show="parentMode === 'new'" class="grid grid-cols-1 md:grid-cols-3 gap-5">
              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Parent Name <span class="text-red-500">*</span></label>
                <input name="parent_guardian_name" value="{{ old('parent_guardian_name') }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Full Name" />
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Phone Number (11 digits) <span class="text-red-500">*</span></label>
                <input name="parent_contact_number" value="{{ old('parent_contact_number') }}" type="text" maxlength="11" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="09xxxxxxxxx" />
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Email Address <span class="text-slate-400 font-normal">(Optional)</span></label>
                <input name="parent_email" value="{{ old('parent_email') }}" type="email" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
              </div>

              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Password <span class="text-red-500">*</span></label>
                <input name="parent_password" type="password" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Create password for parent" />
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-2 text-slate-700">Address <span class="text-slate-400 font-normal">(Optional)</span></label>
                <input name="parent_address" value="{{ old('parent_address') }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Complete address" />
              </div>
            </div>

            <!-- Existing Parent Search -->
            <div x-show="parentMode === 'existing'" class="grid grid-cols-1 md:grid-cols-2 gap-5" x-cloak>
                 <div class="relative">
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Search Existing Parent <span class="text-red-500">*</span></label>
                    <input type="text" x-model="search" @focus="showDropdown = true" @click.away="showDropdown = false"
                           placeholder="Search by name or phone..." 
                           class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
                    <input type="hidden" name="existing_parent_id" x-ref="parentIdInput">
                    
                    <div x-show="showDropdown && filteredParents.length > 0" class="absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg border border-slate-100 max-h-60 overflow-y-auto">
                        <ul class="py-1">
                            <template x-for="p in filteredParents" :key="p.id">
                                <li @click="selectParent(p)" class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm text-slate-700 transition-colors">
                                    <span x-text="p.label"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Start typing to search existing parents.</p>
                </div>
            </div>

            <!-- Relationship Field (Always Visible) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-5">
              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Relationship <span class="text-red-500">*</span></label>
                <select name="relationship" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required>
                  <option value="">Select Relationship</option>
                  <option value="Father" {{ old('relationship') === 'Father' ? 'selected' : '' }}>Father</option>
                  <option value="Mother" {{ old('relationship') === 'Mother' ? 'selected' : '' }}>Mother</option>
                  <option value="Guardian" {{ old('relationship') === 'Guardian' ? 'selected' : '' }}>Guardian</option>
                  <option value="Grandparent" {{ old('relationship') === 'Grandparent' ? 'selected' : '' }}>Grandparent</option>
                  <option value="Other" {{ old('relationship') === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>
              
               <div class="md:col-span-2 flex items-center">
                   <!-- Placeholder for alignment or other fields -->
                   <div class="flex items-center p-4 rounded-xl bg-slate-50 border border-slate-100 w-full">
                      <input name="send_sms" id="send_sms" type="checkbox" value="1" class="h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500" {{ old('send_sms') ? 'checked' : '' }}>
                      <label for="send_sms" class="ml-3 text-sm font-medium text-slate-700 select-none cursor-pointer">Send SMS Notification to Parent</label>
                    </div>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-4 mt-10 pt-6 border-t border-slate-100">
            <a href="{{ route('admin.students.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold rounded-xl transition-all duration-200 shadow-sm">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-blue-200 hover:shadow-blue-300">Create Student</button>
          </div>
        </form>
      </main>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var gradeSelect = document.getElementById('grade-level');
      var categoryField = document.getElementById('category-field');
      var voucherField = document.getElementById('voucher-field');
      function updateCategoryVisibility() {
        var g = gradeSelect.value || '';
        var show = (g === 'Grade 11' || g === 'Grade 12');
        categoryField.classList.toggle('hidden', !show);
        if (voucherField) {
          voucherField.classList.toggle('hidden', !show);
        }
      }
      gradeSelect.addEventListener('change', updateCategoryVisibility);
      updateCategoryVisibility();

      var dobInput = document.getElementById('date_of_birth');
      var ageInput = document.getElementById('age');

      function updateAge() {
        if (!dobInput || !ageInput) return;
        if (!dobInput.value) {
          ageInput.value = '';
          return;
        }
        var dob = new Date(dobInput.value + 'T00:00:00');
        if (Number.isNaN(dob.getTime())) {
          ageInput.value = '';
          return;
        }
        var today = new Date();
        var age = today.getFullYear() - dob.getFullYear();
        var m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
          age--;
        }
        ageInput.value = age >= 0 ? String(age) : '';
      }

      if (dobInput) {
        dobInput.addEventListener('change', updateAge);
        updateAge();
      }
    });
  </script>
</body>
</html>
