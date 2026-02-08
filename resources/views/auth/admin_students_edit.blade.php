<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit Student - Efees Admin</title>
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: 'Inter', 'Noto Sans', sans-serif; }
    .card-hover {
      transition: all 0.3s ease;
    }
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.2);
    }
    [x-cloak] { display: none !important; }
    /* Custom Scrollbar for Sidebar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }
  </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 md:translate-x-0 shadow-2xl md:shadow-none" id="sidebar">
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

            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.parents.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.parents.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-user-friends text-lg {{ request()->routeIs('admin.parents.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Parent Management</span>
            </a>

            <!-- Staff Management -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.staff.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.staff.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-chalkboard-teacher text-lg {{ request()->routeIs('admin.staff.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Staff Management</span>
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
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-slate-800 tracking-tight">Efees Admin</span>
            </div>
            <button @click="sidebarOpen = true" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-50 rounded-lg transition-all">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Main content -->
        <main class="flex-1 p-8 overflow-y-auto bg-slate-50">
            <div class="flex justify-between items-center mb-8">
              <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Student</h1>
                <p class="text-sm text-slate-500 mt-1">Update student information</p>
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

            <form method="POST" action="{{ route('admin.students.update', $student) }}" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
              @csrf
              @method('PUT')

              <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                  <i class="fas fa-user-edit text-lg"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-800">Student Information</h2>
              </div>

              <!-- Personal Information Section -->
              <div class="mb-8">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">First Name <span class="text-red-500">*</span></label>
                    <input name="first_name" value="{{ old('first_name', $student->first_name) }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Middle Name</label>
                    <input name="middle_name" value="{{ old('middle_name', $student->middle_name ?? $student->middle_initial) }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Last Name <span class="text-red-500">*</span></label>
                    <input name="last_name" value="{{ old('last_name', $student->last_name) }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Suffix</label>
                    <input name="suffix" value="{{ old('suffix', $student->suffix) }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Jr., Sr., III" />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Gender <span class="text-red-500">*</span></label>
                    <select name="sex" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required>
                      <option value="">Select Gender</option>
                      <option value="Male" {{ old('sex', $student->sex) === 'Male' ? 'selected' : '' }}>Male</option>
                      <option value="Female" {{ old('sex', $student->sex) === 'Female' ? 'selected' : '' }}>Female</option>
                      <option value="Other" {{ old('sex', $student->sex) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Date of Birth</label>
                    <input id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', optional($student->date_of_birth)->format('Y-m-d')) }}" type="date" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Age</label>
                    <input id="age" type="text" class="w-full rounded-xl border-slate-200 bg-slate-100 text-slate-800 px-4 py-2.5 focus:outline-none shadow-sm" readonly disabled />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Nationality</label>
                    <input name="nationality" value="{{ old('nationality', $student->nationality) }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
                  </div>

                  <div class="md:col-span-3">
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Address</label>
                    <input name="address" value="{{ old('address', $student->address) }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Complete home address" />
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
                      @foreach(['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $grade)
                        <option value="{{ $grade }}" {{ old('level', $student->level) === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Section <span class="text-red-500">*</span></label>
                    <input name="section" value="{{ old('section', $student->section) }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">School Year</label>
                    <input name="school_year" value="{{ old('school_year', $student->school_year) }}" placeholder="e.g. 2024-2025" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
                  </div>

                  <div id="category-field" class="{{ in_array(old('level', $student->level), ['Grade 11','Grade 12']) ? '' : 'hidden' }}">
                    <label class="block text-sm font-semibold mb-2 text-slate-700">SHS Strand</label>
                    <select name="strand" id="category" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                      <option value="">Select Strand</option>
                      @foreach(['STEM','ABM','HUMSS','GAS'] as $cat)
                        <option value="{{ $cat }}" {{ old('strand', $student->strand ?? $student->department) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div id="voucher-field" class="{{ in_array(old('level', $student->level), ['Grade 11','Grade 12']) ? '' : 'hidden' }}">
                    <label class="block text-sm font-semibold mb-2 text-slate-700">SHS Funding Type</label>
                    <select name="shs_voucher_type" id="shs_voucher_type" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                      <option value="">Select Type</option>
                      <option value="regular" {{ old('shs_voucher_type', $student->is_shs_voucher ? null : 'regular') === 'regular' ? 'selected' : '' }}>Regular</option>
                      <option value="shs_voucher" {{ old('shs_voucher_type', $student->is_shs_voucher ? 'shs_voucher' : null) === 'shs_voucher' ? 'selected' : '' }}>Senior High School (SHS) Voucher</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Parent/Guardian Information Section -->
              <div class="mb-8">
                @php
                    $linkedParent = $student->parents->sortByDesc('pivot.is_primary')->first();
                    
                    $pName = $linkedParent ? $linkedParent->full_name : '';
                    $pRel = $linkedParent ? $linkedParent->pivot->relationship : '';
                    $pPhone = $linkedParent ? $linkedParent->phone : '';
                    $pEmail = $linkedParent ? $linkedParent->email : '';
                    $pAddress = $linkedParent ? $linkedParent->address_street : '';
                    $pIsPrimary = $linkedParent ? $linkedParent->pivot->is_primary : false;
                @endphp
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Parent/Guardian Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Parent/Guardian Name <span class="text-red-500">*</span></label>
                    <input name="parent_guardian_name" value="{{ old('parent_guardian_name', $pName) }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Relationship <span class="text-red-500">*</span></label>
                    <select name="relationship" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required>
                      <option value="">Select Relationship</option>
                      <option value="Father" {{ old('relationship', $pRel) === 'Father' ? 'selected' : '' }}>Father</option>
                      <option value="Mother" {{ old('relationship', $pRel) === 'Mother' ? 'selected' : '' }}>Mother</option>
                      <option value="Guardian" {{ old('relationship', $pRel) === 'Guardian' ? 'selected' : '' }}>Guardian</option>
                      <option value="Grandparent" {{ old('relationship', $pRel) === 'Grandparent' ? 'selected' : '' }}>Grandparent</option>
                      <option value="Other" {{ old('relationship', $pRel) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Contact Number <span class="text-red-500">*</span></label>
                    <input name="parent_contact_number" value="{{ old('parent_contact_number', $pPhone) }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Email Address <span class="text-slate-400 font-normal">(Optional)</span></label>
                    <input name="parent_email" value="{{ old('parent_email', $pEmail) }}" type="email" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Address <span class="text-slate-400 font-normal">(Optional)</span></label>
                    <input name="parent_address" value="{{ old('parent_address', $pAddress) }}" type="text" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Complete address" />
                  </div>

                  <div class="md:col-span-3">
                    <div class="flex items-center p-4 rounded-xl bg-slate-50 border border-slate-100">
                      <input name="is_primary_contact" id="is_primary_contact" type="checkbox" value="1" class="h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500" {{ old('is_primary_contact', $pIsPrimary) ? 'checked' : '' }}>
                      <label for="is_primary_contact" class="ml-3 text-sm font-medium text-slate-700 select-none cursor-pointer">Set as Primary Contact for Emergency</label>
                    </div>
                  </div>

                  <div class="md:col-span-3">
                    <div class="p-4 rounded-xl bg-white border border-slate-200">
                      <p class="text-sm font-semibold mb-2 text-slate-700">Linked Parents</p>
                      @if($student->parents()->exists())
                        <ul class="text-sm text-slate-700 space-y-1">
                          @foreach($student->parents as $p)
                            <li>{{ $p->full_name }} — {{ $p->pivot->relationship }}@if($p->pivot->is_primary) • primary @endif</li>
                          @endforeach
                        </ul>
                      @else
                        <p class="text-sm text-slate-500">No linked parents</p>
                      @endif
                      <div class="mt-2">
                        <a href="{{ route('admin.parents.index') }}" class="text-xs font-semibold text-blue-600">Manage Parents</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end gap-4 mt-10 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.students.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold rounded-xl transition-all duration-200 shadow-sm">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-blue-200 hover:shadow-blue-300">Update Student</button>
              </div>
            </form>
        </main>
    </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const gradeLevel = document.getElementById('grade-level');
        const categoryField = document.getElementById('category-field');
        const categoryInput = document.getElementById('category');
        const voucherField = document.getElementById('voucher-field');

        function toggleCategory() {
            if (['Grade 11', 'Grade 12'].includes(gradeLevel.value)) {
                categoryField.classList.remove('hidden');
                categoryInput.required = true;
                if (voucherField) {
                    voucherField.classList.remove('hidden');
                }
            } else {
                categoryField.classList.add('hidden');
                categoryInput.required = false;
                categoryInput.value = '';
                if (voucherField) {
                    voucherField.classList.add('hidden');
                }
            }
        }

        gradeLevel.addEventListener('change', toggleCategory);
        toggleCategory(); // Initial check

        const dobInput = document.getElementById('date_of_birth');
        const ageInput = document.getElementById('age');

        function updateAge() {
            if (!dobInput || !ageInput) return;
            if (!dobInput.value) {
                ageInput.value = '';
                return;
            }
            const dob = new Date(dobInput.value + 'T00:00:00');
            if (Number.isNaN(dob.getTime())) {
                ageInput.value = '';
                return;
            }
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
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
