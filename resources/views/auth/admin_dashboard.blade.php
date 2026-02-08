<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
<<<<<<< HEAD
  <meta name="csrf-token" content="{{ csrf_token() }}">
=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
  <title>
   Efees Admin Dashboard
  </title>
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: 'Inter', 'Noto Sans', sans-serif; }
    .gradient-bg {
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    }
<<<<<<< HEAD
=======
    .card-hover {
      transition: all 0.3s ease;
    }
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.2);
    }
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
    .chart-container {
      position: relative;
      height: 300px;
    }
  </style>
 </head>
<<<<<<< HEAD
 <body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
  <!-- Mobile Header -->
  <div class="md:hidden flex items-center justify-between bg-white border-b border-slate-200 px-4 py-3 sticky top-0 z-20">
      <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
              <i class="fas fa-user-shield text-lg"></i>
          </div>
          <span class="font-bold text-slate-800 text-lg">Efees Admin</span>
      </div>
      <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
          <i class="fas fa-bars text-xl"></i>
      </button>
  </div>

  <!-- Mobile Overlay -->
  <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 md:hidden" x-cloak></div>

  <!-- Sidebar -->
  <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 z-40 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 md:translate-x-0 shadow-2xl md:shadow-none">
      <div class="flex items-center gap-3 px-8 py-6 border-b border-slate-100 bg-white/50 backdrop-blur-sm sticky top-0 z-10">
          <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
              <i class="fas fa-user-shield text-lg"></i>
          </div>
          <div>
              <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Efees Admin</h1>
              <p class="text-xs text-slate-500 font-medium">Administration</p>
          </div>
      </div>

      <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow overflow-y-auto pb-6 custom-scrollbar">
          <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
          
          <!-- Dashboard -->
          <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin_dashboard') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin_dashboard') }}">
              <div class="w-8 flex justify-center">
                  <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('admin_dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
              </div>
              <span class="text-sm font-medium">Dashboard</span>
          </a>

          

          <!-- Student Management -->
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

            <!-- Requests -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.requests.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.requests.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-key text-lg {{ request()->routeIs('admin.requests.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Requests</span>
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
  <!-- Main content -->
  <main class="flex-1 p-6 md:p-8 overflow-y-auto">
   <div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-gray-900">
=======
 <body class="flex flex-col md:flex-row min-h-screen bg-slate-900 text-slate-100">
  <!-- Sidebar -->
  <aside class="flex flex-col bg-slate-800 text-slate-300 w-full md:w-72 min-h-screen border-r border-slate-700 overflow-y-auto" id="sidebar" style="scrollbar-width: thin; scrollbar-color: #8b5cf6 transparent;">
   <div class="flex items-center gap-3 px-8 py-6 border-b border-slate-700">
    <div class="w-8 h-8 flex-shrink-0 text-indigo-500">
     <svg class="w-full h-full" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
      <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor">
      </path>
     </svg>
    </div>
    <h1 class="text-indigo-400 font-extrabold text-xl tracking-tight select-none" style="letter-spacing: -0.015em;">
     Efees Admin
    </h1>
   </div>
   <nav class="flex flex-col mt-6 px-4 space-y-1 flex-grow">
    <a aria-current="page" class="flex items-center gap-3 px-4 py-3 rounded-lg text-indigo-400 bg-slate-700 border-r-4 border-indigo-500 font-semibold transition-colors duration-200" href="#">
     <i class="fas fa-tachometer-alt w-5">
     </i>
     <span class="text-sm font-semibold">
      Dashboard
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="{{ route('admin.students.index') }}">
     <i class="fas fa-users w-5">
     </i>
     <span class="text-sm font-semibold">
      Manage Students
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="{{ route('admin.staff.index') }}">
     <i class="fas fa-user-tie w-5">
     </i>
     <span class="text-sm font-semibold">
      Staff Management
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
     <i class="fas fa-file-invoice-dollar w-5">
     </i>
     <span class="text-sm font-semibold">
      Fee Management
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
     <i class="fas fa-chart-bar w-5">
     </i>
     <span class="text-sm font-semibold">
      Reports
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
     <i class="fas fa-cog w-5">
     </i>
     <span class="text-sm font-semibold">
      Settings
     </span>
    </a>
   </nav>
   <div class="px-4 py-4 border-t border-slate-700">
    <form method="POST" action="{{ route('logout') }}">
     @csrf
     <button class="w-full flex items-center gap-3 bg-indigo-500 text-white font-bold text-sm rounded-lg h-10 justify-center cursor-pointer select-none transition-colors duration-300 hover:bg-indigo-600" type="submit" aria-label="Logout">
      <i class="fas fa-sign-out-alt"></i>
      <span>Logout</span>
     </button>
    </form>
   </div>
  </aside>
  <!-- Main content -->
  <main class="flex-1 p-6 md:p-8 overflow-y-auto bg-slate-900">
   <div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-slate-100">
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
     Admin Dashboard
    </h1>
    <!-- Admin Profile Circle -->
    <div class="flex items-center gap-3">
     <div class="text-right">
<<<<<<< HEAD
      <p class="text-sm font-semibold text-blue-600">{{ Auth::user()->student ? Auth::user()->student->first_name . ' ' . Auth::user()->student->last_name : 'Admin' }}</p>
      <p class="text-xs text-gray-600">{{ Auth::user()->email }}</p>
      <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">Admin</span>
     </div>
     <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-lg">
=======
      <p class="text-sm font-semibold text-indigo-400">{{ Auth::user()->student ? Auth::user()->student->first_name . ' ' . Auth::user()->student->last_name : 'Admin' }}</p>
      <p class="text-xs text-slate-400">{{ Auth::user()->email }}</p>
      <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-900/50 text-indigo-300 border border-indigo-600">Admin</span>
     </div>
     <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-bold text-lg">
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
      {{ Auth::user()->student ? strtoupper(substr(Auth::user()->student->first_name, 0, 1) . substr(Auth::user()->student->last_name, 0, 1)) : 'A' }}
     </div>
    </div>
   </div>

   @if(session('success'))
<<<<<<< HEAD
     <div class="mb-6 border border-green-200 text-green-800 bg-green-50 rounded-md px-4 py-3">
=======
     <div class="mb-6 border border-green-600 text-green-300 bg-green-900/20 rounded-md px-4 py-3">
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
       {{ session('success') }}
     </div>
   @endif

<<<<<<< HEAD
   <!-- Filters -->
   <div class="bg-white rounded-xl shadow-lg border border-slate-200 p-6 mb-8 transition-all duration-300 hover:shadow-xl">
       <div class="flex items-center gap-2 mb-4 text-slate-800 border-b border-slate-100 pb-2">
           <i class="fas fa-filter text-blue-600"></i>
           <h3 class="font-bold text-sm uppercase tracking-wide">Filter Dashboard</h3>
       </div>
       
       <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
           <!-- School Year -->
            <div class="relative">
                <div id="readOnlyBadge" class="hidden absolute top-0 right-0 items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200 text-[10px] font-bold shadow-sm animate-pulse">
                    <i class="fas fa-lock mr-1"></i> Read Only
                </div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1">
                    <i class="fas fa-calendar-alt text-blue-500"></i> School Year
                </label>
                <select id="filterSchoolYear" class="form-select w-full text-sm border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 py-2.5 shadow-sm transition-all hover:border-blue-300" onchange="fetchMetrics()">
                    <option value="">All Years</option>
                    @foreach($schoolYears as $year)
                        <option value="{{ $year }}" {{ (session('admin_dashboard_filters.school_year') == $year) || (session('admin_dashboard_filters.school_year') === null && $activeSy == $year) ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

           <!-- Grade Level -->
           <div>
               <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1">
                   <i class="fas fa-layer-group text-indigo-500"></i> Grade Level
               </label>
               <select id="filterLevel" class="form-select w-full text-sm border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 py-2.5 shadow-sm transition-all hover:border-blue-300" onchange="fetchMetrics()">
                   <option value="">All Levels</option>
                   @foreach($levels as $lvl)
                       <option value="{{ $lvl }}" {{ session('admin_dashboard_filters.level') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                   @endforeach
               </select>
           </div>

           <!-- Section -->
           <div>
               <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1">
                   <i class="fas fa-users text-purple-500"></i> Section
               </label>
               <select id="filterSection" class="form-select w-full text-sm border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 py-2.5 shadow-sm transition-all hover:border-blue-300" onchange="fetchMetrics()">
                   <option value="">All Sections</option>
                   @foreach($sections as $sec)
                       <option value="{{ $sec }}" {{ session('admin_dashboard_filters.section') == $sec ? 'selected' : '' }}>{{ $sec }}</option>
                   @endforeach
               </select>
           </div>

           <!-- Date From -->
           <div>
               <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1">
                   <i class="fas fa-calendar-day text-slate-400"></i> Date From
               </label>
               <input type="date" id="filterStartDate" class="form-input w-full text-sm border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 py-2.5 shadow-sm transition-all hover:border-blue-300" onchange="fetchMetrics()" value="{{ session('admin_dashboard_filters.start_date') }}">
           </div>

           <!-- Date To -->
           <div>
               <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1">
                   <i class="fas fa-calendar-day text-slate-400"></i> Date To
               </label>
               <input type="date" id="filterEndDate" class="form-input w-full text-sm border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 py-2.5 shadow-sm transition-all hover:border-blue-300" onchange="fetchMetrics()" value="{{ session('admin_dashboard_filters.end_date') }}">
           </div>

           <!-- Actions -->
           <div class="flex gap-2">
               <button onclick="fetchMetrics()" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-bold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 group">
                   <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-500"></i> Refresh
               </button>
               <button onclick="window.resetFilters()" class="px-4 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-lg transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2" title="Reset Filters">
                   <i class="fas fa-undo"></i>
               </button>
           </div>
       </div>
   </div>

   <!-- Main Metrics Cards -->
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <!-- Total Fees Collected -->
    <div onclick="document.getElementById('paymentTrendsChart').scrollIntoView({behavior: 'smooth'})" class="cursor-pointer block transform transition-transform hover:-translate-y-1">
        <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 flex flex-col justify-center items-center text-center h-full relative overflow-hidden group">
             <div class="absolute top-0 left-0 w-full h-1 bg-green-500"></div>
             <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity text-xs text-slate-400">
                 <i class="fas fa-external-link-alt"></i>
             </div>
             <h2 class="text-base md:text-lg font-semibold mb-2 flex items-center gap-2 select-none text-green-600">
              <i class="fas fa-coins text-green-500"></i>
              Total Collected
             </h2>
             <p class="text-3xl font-extrabold mb-1 text-green-600 select-text" id="totalCollectedDisplay">
              ₱{{ number_format($totalCollected ?? 0, 2) }}
             </p>
             <div class="flex items-center gap-2 text-xs font-medium mt-2">
                @php
                    $collectedPct = $expectedCollection > 0 ? ($totalCollected / $expectedCollection) * 100 : 0;
                    $collectedTrend = $prevTotalCollected > 0 ? (($totalCollected - $prevTotalCollected) / $prevTotalCollected) * 100 : 0;
                @endphp
                <span class="text-slate-500" id="collectedPctDisplay">({{ number_format($collectedPct, 1) }}% of expected)</span>
             </div>
             <div class="flex items-center gap-1 text-xs mt-1" id="collectedTrendDisplay">
                @if($collectedTrend >= 0)
                    <span class="text-green-600 flex items-center"><i class="fas fa-arrow-up mr-1"></i> {{ number_format(abs($collectedTrend), 1) }}%</span>
                @else
                    <span class="text-red-500 flex items-center"><i class="fas fa-arrow-down mr-1"></i> {{ number_format(abs($collectedTrend), 1) }}%</span>
                @endif
                <span class="text-slate-400">vs last month</span>
             </div>
        </section>
    </div>

    <!-- Pending Approvals -->
    <a href="{{ route('admin.payment_approvals.index') }}" class="block transform transition-transform hover:-translate-y-1">
        <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 flex flex-col justify-center items-center text-center h-full relative overflow-hidden group">
             <div class="absolute top-0 left-0 w-full h-1 bg-amber-500"></div>
             <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity text-xs text-slate-400">
                 <i class="fas fa-external-link-alt"></i>
             </div>
             <h2 class="text-base md:text-lg font-semibold mb-2 flex items-center gap-2 select-none text-amber-600">
              <i class="fas fa-check-double text-amber-500"></i>
              Pending Approvals
             </h2>
             <p class="text-3xl font-extrabold mb-1 text-amber-600 select-text" id="pendingApprovalsDisplay">
              ₱{{ number_format($pendingApprovals ?? 0, 2) }}
             </p>
             <div class="flex items-center gap-2 text-xs font-medium mt-2">
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="fas fa-info-circle text-amber-500"></i> Needs Action
                </span>
             </div>
             <div class="flex items-center gap-1 text-xs mt-1">
                <span class="text-slate-400">Requires verification</span>
             </div>
        </section>
    </a>

    <!-- Pending Payments -->
    <div onclick="document.getElementById('pendingPaymentsTable').scrollIntoView({behavior: 'smooth'})" class="cursor-pointer block transform transition-transform hover:-translate-y-1">
        <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 flex flex-col justify-center items-center text-center h-full relative overflow-hidden group">
             <div class="absolute top-0 left-0 w-full h-1 bg-orange-500"></div>
             <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity text-xs text-slate-400">
                 <i class="fas fa-external-link-alt"></i>
             </div>
             <h2 class="text-base md:text-lg font-semibold mb-2 flex items-center gap-2 select-none text-orange-600">
              <i class="fas fa-exclamation-circle text-orange-500"></i>
              Outstanding Debt
            </h2>
            <p class="text-3xl font-extrabold mb-1 text-orange-600 select-text" id="pendingOutstandingDisplay">
              ₱{{ number_format($pendingOutstanding ?? 0, 2) }}
            </p>
             <div class="flex items-center gap-2 text-xs font-medium mt-2">
                @php
                    $pendingPct = $expectedCollection > 0 ? ($pendingOutstanding / $expectedCollection) * 100 : 0;
                @endphp
                <span class="text-slate-500" id="pendingPctDisplay">({{ number_format($pendingPct, 1) }}% of total)</span>
             </div>
             <div class="flex items-center gap-1 text-xs mt-1">
                <span class="text-slate-400">Outstanding amount</span>
             </div>
        </section>
    </div>

    <!-- Total Students -->
    <a href="{{ route('admin.students.index') }}" class="block transform transition-transform hover:-translate-y-1">
        <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 flex flex-col justify-center items-center text-center h-full relative overflow-hidden group">
             <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
             <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity text-xs text-slate-400">
                 <i class="fas fa-external-link-alt"></i>
             </div>
             <h2 class="text-base md:text-lg font-semibold mb-2 flex items-center gap-2 select-none text-blue-600">
              <i class="fas fa-users text-blue-500"></i>
              Total Students
             </h2>
             <p class="text-3xl font-extrabold mb-1 text-blue-600 select-text" id="studentsCountDisplay">
              {{ $studentsCount ?? 0 }}
             </p>
             <div class="flex items-center gap-1 text-xs mt-3" id="studentsTrendDisplay">
                @php
                    $studentsTrend = $prevStudentsCount > 0 ? (($studentsCount - $prevStudentsCount) / $prevStudentsCount) * 100 : 0;
                @endphp
                @if($studentsTrend >= 0)
                    <span class="text-green-600 flex items-center"><i class="fas fa-arrow-up mr-1"></i> {{ number_format(abs($studentsTrend), 1) }}%</span>
                @else
                    <span class="text-red-500 flex items-center"><i class="fas fa-arrow-down mr-1"></i> {{ number_format(abs($studentsTrend), 1) }}%</span>
                @endif
                <span class="text-slate-400">vs last month</span>
             </div>
        </section>
    </a>

    <!-- Reminders Sent -->
    <a href="{{ route('admin.sms.logs') }}" class="block transform transition-transform hover:-translate-y-1">
        <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 flex flex-col justify-center items-center text-center h-full relative overflow-hidden group">
             <div class="absolute top-0 left-0 w-full h-1 bg-purple-500"></div>
             <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity text-xs text-slate-400">
                 <i class="fas fa-external-link-alt"></i>
             </div>
             <h2 class="text-base md:text-lg font-semibold mb-2 flex items-center gap-2 select-none text-purple-600">
              <i class="fas fa-comment-dots text-purple-500"></i>
              SMS Sent (Week)
             </h2>
             <p class="text-3xl font-extrabold mb-1 text-purple-600 select-text" id="smsSentDisplay">
              {{ $smsSentThisWeek ?? 0 }}
             </p>
             <div class="flex items-center gap-1 text-xs mt-3" id="smsTrendDisplay">
                @php
                    $smsTrend = $smsSentLastWeek > 0 ? (($smsSentThisWeek - $smsSentLastWeek) / $smsSentLastWeek) * 100 : 0;
                @endphp
                @if($smsTrend >= 0)
                    <span class="text-green-600 flex items-center"><i class="fas fa-arrow-up mr-1"></i> {{ number_format(abs($smsTrend), 1) }}%</span>
                @else
                    <span class="text-red-500 flex items-center"><i class="fas fa-arrow-down mr-1"></i> {{ number_format(abs($smsTrend), 1) }}%</span>
                @endif
                <span class="text-slate-400">vs last week</span>
             </div>
        </section>
    </a>
=======
   <!-- Search bar -->
   <div class="max-w-md mb-8">
    <label class="sr-only" for="search">
     Search Students
    </label>
    <div class="relative text-slate-500 focus-within:text-indigo-400 transition-colors duration-200">
     <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <i class="fas fa-search text-slate-400">
      </i>
     </div>
     <input class="block w-full rounded-lg border border-slate-700 bg-slate-800 py-2 pl-10 pr-3 text-sm placeholder-slate-400 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" id="search" name="search" placeholder="Search students..." type="search"/>
    </div>
   </div>

   <!-- Main Metrics Cards -->
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Fees Collected -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-green-400">
      <i class="fas fa-dollar-sign text-green-500">
      </i>
      Total Collected
     </h2>
     <p class="text-4xl font-extrabold mb-2 text-green-400 select-text">
      $89,450
     </p>
     <p class="text-slate-400 select-text">
      This month
     </p>
    </section>
    <!-- Pending Payments -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-orange-400">
      <i class="fas fa-exclamation-triangle text-orange-500">
      </i>
      Pending Payments
     </h2>
     <p class="text-4xl font-extrabold mb-2 text-orange-400 select-text">
      $12,650
     </p>
     <p class="text-slate-400 select-text">
      Outstanding amount
     </p>
    </section>
    <!-- Total Students -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-indigo-400">
      <i class="fas fa-users text-indigo-500">
      </i>
      Total Students
     </h2>
     <p class="text-4xl font-extrabold mb-2 text-indigo-400 select-text">
      156
     </p>
     <p class="text-slate-400 select-text">
      Registered students
     </p>
    </section>
    <!-- Reminders Sent -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-purple-400">
      <i class="fas fa-bell text-purple-500">
      </i>
      Reminders Sent
     </h2>
     <p class="text-4xl font-extrabold mb-2 text-purple-400 select-text">
      47
     </p>
     <p class="text-slate-400 select-text">
      This week
     </p>
    </section>
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
   </div>

   <!-- Charts Section -->
   <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Payment Status Pie Chart -->
<<<<<<< HEAD
    <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 relative">
     <div class="flex justify-between items-center mb-6">
         <h2 class="text-xl font-semibold flex items-center gap-2 select-none text-gray-900">
          <i class="fas fa-chart-pie text-blue-500"></i>
          Payment Status Overview
         </h2>
         <button onclick="exportChart('paymentStatusChart')" class="text-slate-400 hover:text-blue-600 transition-colors" title="Export as Image">
             <i class="fas fa-download"></i>
         </button>
     </div>
     <div class="chart-container relative" style="min-height: 300px;">
      <div id="paymentStatusLoading" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10 hidden">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
=======
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover">
     <h2 class="text-xl font-semibold mb-6 flex items-center gap-2 select-none text-slate-100">
      <i class="fas fa-chart-pie text-indigo-500"></i>
      Payment Status Overview
     </h2>
     <div class="chart-container">
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
      <canvas id="paymentStatusChart"></canvas>
     </div>
    </section>

    <!-- Collections by Grade/Section Bar Chart -->
<<<<<<< HEAD
    <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 relative">
     <div class="flex justify-between items-center mb-6">
         <h2 class="text-xl font-semibold flex items-center gap-2 select-none text-gray-900">
          <i class="fas fa-chart-bar text-green-500"></i>
          Collections by Grade/Section
         </h2>
         <button onclick="exportChart('collectionsByGradeChart')" class="text-slate-400 hover:text-green-600 transition-colors" title="Export as Image">
             <i class="fas fa-download"></i>
         </button>
     </div>
     <div class="chart-container relative" style="min-height: 300px;">
      <div id="collectionsLoading" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10 hidden">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
      </div>
=======
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover">
     <h2 class="text-xl font-semibold mb-6 flex items-center gap-2 select-none text-slate-100">
      <i class="fas fa-chart-bar text-green-500"></i>
      Collections by Grade/Section
     </h2>
     <div class="chart-container">
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
      <canvas id="collectionsByGradeChart"></canvas>
     </div>
    </section>
   </div>

   <!-- Payment Trends Line Chart (Full Width) -->
<<<<<<< HEAD
   <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-8 relative">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold flex items-center gap-2 select-none text-gray-900">
         <i class="fas fa-chart-line text-orange-500"></i>
         Payment Trends
        </h2>
        <button onclick="exportChart('paymentTrendsChart')" class="text-slate-400 hover:text-orange-600 transition-colors" title="Export as Image">
            <i class="fas fa-download"></i>
        </button>
    </div>
    <div class="chart-container relative" style="min-height: 400px;">
     <div id="trendsLoading" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10 hidden">
         <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-600"></div>
     </div>
=======
   <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover mb-8">
    <h2 class="text-xl font-semibold mb-6 flex items-center gap-2 select-none text-slate-100">
     <i class="fas fa-chart-line text-orange-500"></i>
     Payment Trends (School Year 2024)
    </h2>
    <div class="chart-container">
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
     <canvas id="paymentTrendsChart"></canvas>
    </div>
   </section>

   <!-- Recent Activity Tables -->
   <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Pending Payments Table -->
<<<<<<< HEAD
    <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6" id="pendingPaymentsTable">
     <div class="flex justify-between items-center mb-4">
         <h2 class="text-base md:text-lg font-semibold flex items-center gap-2 select-none text-orange-600">
          <i class="fas fa-exclamation-circle text-orange-500"></i>
          Pending Payments
         </h2>
         <div class="text-xs text-slate-500">
             Top 10 oldest/highest
         </div>
     </div>
     <div class="overflow-x-auto scrollbar-thin max-h-96">
      <table class="min-w-full text-sm">
       <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
        <tr>
         <th class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap" scope="col">Student</th>
         <th class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap" scope="col">Parent</th>
         <th class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap" scope="col">Due Date</th>
         <th class="px-4 py-3 text-center font-semibold text-gray-700 whitespace-nowrap" scope="col">Overdue</th>
         <th class="px-4 py-3 text-right font-semibold text-gray-700 whitespace-nowrap" scope="col">Balance</th>
         <th class="px-4 py-3 text-center font-semibold text-gray-700 whitespace-nowrap" scope="col">Action</th>
        </tr>
       </thead>
        <tbody class="divide-y divide-gray-200" id="pendingPaymentsTableBody">
        @foreach(($pendingPayments ?? collect()) as $rec)
         <tr class="hover:bg-orange-50 transition-colors">
          <td class="px-4 py-3 whitespace-nowrap">
              <div class="font-medium text-gray-900">{{ $rec['student_name'] }}</div>
              <div class="text-xs text-gray-500">ID: {{ $rec['student_id'] }}</div>
          </td>
          <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ $rec['parent_name'] }}</td>
          <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $rec['due_date'] }}</td>
          <td class="px-4 py-3 whitespace-nowrap text-center">
              @if($rec['days_overdue'] > 0)
                  <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                      {{ $rec['days_overdue'] }} days
                  </span>
              @else
                  <span class="text-gray-400 text-xs">-</span>
              @endif
          </td>
          <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-orange-600">₱{{ number_format((float)($rec['balance'] ?? 0), 2) }}</td>
          <td class="px-4 py-3 whitespace-nowrap text-center">
             <a href="{{ route('admin.students.index', ['id' => $rec['student_id']]) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                 View Ledger
             </a>
          </td>
         </tr>
        @endforeach
=======
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-orange-400">
      <i class="fas fa-exclamation-triangle text-orange-500">
      </i>
      Pending Payments
     </h2>
     <div class="overflow-x-auto scrollbar-thin max-h-64">
      <table class="min-w-full text-sm">
       <thead class="bg-slate-700 sticky top-0 z-10">
        <tr>
         <th class="px-4 py-2 text-left font-semibold text-slate-300" scope="col">Student</th>
         <th class="px-4 py-2 text-left font-semibold text-slate-300" scope="col">Fee Type</th>
         <th class="px-4 py-2 text-right font-semibold text-slate-300" scope="col">Amount</th>
        </tr>
       </thead>
       <tbody class="divide-y divide-slate-700">
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">John Doe</td>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Tuition Fee</td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-orange-400">$1,200</td>
        </tr>
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Jane Smith</td>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Library Fee</td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-orange-400">$150</td>
        </tr>
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Mike Johnson</td>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Lab Fee</td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-orange-400">$300</td>
        </tr>
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
       </tbody>
      </table>
     </div>
    </section>

    <!-- Recent Transactions -->
<<<<<<< HEAD
    <section class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-green-600">
      <i class="fas fa-history text-green-500"></i>
      Recent Transactions
     </h2>
     <div class="overflow-x-auto scrollbar-thin max-h-96">
      <table class="min-w-full text-sm">
       <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
        <tr>
         <th class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap" scope="col">Student</th>
         <th class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap" scope="col">Date</th>
         <th class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap" scope="col">Method</th>
         <th class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap" scope="col">Ref #</th>
         <th class="px-4 py-3 text-right font-semibold text-gray-700 whitespace-nowrap" scope="col">Amount</th>
        </tr>
       </thead>
       <tbody class="divide-y divide-gray-200" id="recentTransactionsTableBody">
        @foreach(($recentTransactions ?? collect()) as $pay)
         <tr class="hover:bg-green-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('admin.students.index', ['id' => $pay['student_id']]) }}'">
             <td class="px-4 py-3 whitespace-nowrap">
                 <div class="font-medium text-gray-900">{{ $pay['student_name'] }}</div>
             </td>
          <td class="px-4 py-3 whitespace-nowrap text-gray-500">
              {{ $pay['paid_at'] }}
              @if(!empty($pay['ip_address']))
                  <div class="text-xs text-gray-400 mt-0.5" title="IP Address"><i class="fas fa-shield-alt mr-1"></i>{{ $pay['ip_address'] }}</div>
              @endif
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
              <span class="px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                  {{ $pay['method'] }}
              </span>
          </td>
          <td class="px-4 py-3 whitespace-nowrap text-xs font-mono text-gray-500">{{ $pay['reference_number'] }}</td>
          <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-green-600">₱{{ number_format((float)($pay['amount_paid'] ?? 0), 2) }}</td>
         </tr>
        @endforeach
=======
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-green-400">
      <i class="fas fa-history text-green-500">
      </i>
      Recent Transactions
     </h2>
     <div class="overflow-x-auto scrollbar-thin max-h-64">
      <table class="min-w-full text-sm">
       <thead class="bg-slate-700 sticky top-0 z-10">
        <tr>
         <th class="px-4 py-2 text-left font-semibold text-slate-300" scope="col">Student</th>
         <th class="px-4 py-2 text-right font-semibold text-slate-300" scope="col">Amount</th>
         <th class="px-4 py-2 text-left font-semibold text-slate-300" scope="col">Status</th>
        </tr>
       </thead>
       <tbody class="divide-y divide-slate-700">
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">John Doe</td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-green-400">+$1,200</td>
         <td class="px-4 py-3 whitespace-nowrap">
          <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-900/50 text-green-400 border border-green-600">Completed</span>
         </td>
        </tr>
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Jane Smith</td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-green-400">+$150</td>
         <td class="px-4 py-3 whitespace-nowrap">
          <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-900/50 text-green-400 border border-green-600">Completed</span>
         </td>
        </tr>
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
       </tbody>
      </table>
     </div>
    </section>
   </div>
  </main>

  <script>
<<<<<<< HEAD
   const ACTIVE_SCHOOL_YEAR = "{{ $activeSy }}";

   document.addEventListener('DOMContentLoaded', function() {
    let paymentStatusChart = null;
    let collectionsByGradeChart = null;
    let paymentTrendsChart = null;

    // Initialize Payment Status Pie Chart
    const paymentStatusCtx = document.getElementById('paymentStatusChart');
    if (paymentStatusCtx) {
     paymentStatusChart = new Chart(paymentStatusCtx, {
=======
   document.addEventListener('DOMContentLoaded', function() {
    // Payment Status Pie Chart
    const paymentStatusCtx = document.getElementById('paymentStatusChart');
    if (paymentStatusCtx) {
     new Chart(paymentStatusCtx, {
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
      type: 'doughnut',
      data: {
       labels: ['Paid', 'Pending'],
       datasets: [{
<<<<<<< HEAD
        data: [0, 0],
=======
        data: [89450, 12650],
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        backgroundColor: [
         '#10b981',
         '#f97316'
        ],
        borderWidth: 0,
        cutout: '70%'
       }]
      },
      options: {
       responsive: true,
       maintainAspectRatio: false,
       plugins: {
        legend: {
         position: 'bottom',
         labels: {
<<<<<<< HEAD
          color: '#374151',
=======
          color: '#e2e8f0',
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
          padding: 20,
          usePointStyle: true
         }
        },
        tooltip: {
         callbacks: {
          label: function(context) {
           const total = context.dataset.data.reduce((a, b) => a + b, 0);
<<<<<<< HEAD
           const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
           return `${context.label}: ₱${context.parsed.toLocaleString()} (${percentage}%)`;
=======
           const percentage = Math.round((context.parsed / total) * 100);
           return `${context.label}: $${context.parsed.toLocaleString()} (${percentage}%)`;
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
          }
         }
        }
       }
      }
     });
    }

<<<<<<< HEAD
    // Initialize Collections by Grade/Section Bar Chart
    const collectionsByGradeCtx = document.getElementById('collectionsByGradeChart');
    if (collectionsByGradeCtx) {
     collectionsByGradeChart = new Chart(collectionsByGradeCtx, {
      type: 'bar',
      data: {
       labels: [],
       datasets: [{
        label: 'Amount Collected (₱)',
        data: [],
        backgroundColor: [
         '#3b82f6',
         '#10b981',
         '#f59e0b',
         '#ef4444',
         '#8b5cf6',
         '#06b6d4'
=======
    // Collections by Grade/Section Bar Chart
    const collectionsByGradeCtx = document.getElementById('collectionsByGradeChart');
    if (collectionsByGradeCtx) {
     new Chart(collectionsByGradeCtx, {
      type: 'bar',
      data: {
       labels: ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
       datasets: [{
        label: 'Amount Collected ($)',
        data: [12500, 15800, 14200, 18900, 13200, 14800],
        backgroundColor: [
         '#6366f1',
         '#8b5cf6',
         '#ec4899',
         '#f59e0b',
         '#10b981',
         '#3b82f6'
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        ],
        borderRadius: 6,
        borderSkipped: false
       }]
      },
      options: {
       responsive: true,
       maintainAspectRatio: false,
       plugins: {
        legend: {
         display: false
        },
        tooltip: {
         callbacks: {
          label: function(context) {
<<<<<<< HEAD
           return `Collected: ₱${context.parsed.y.toLocaleString()}`;
=======
           return `Collected: $${context.parsed.y.toLocaleString()}`;
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
          }
         }
        }
       },
       scales: {
        x: {
          grid: {
<<<<<<< HEAD
           color: '#e5e7eb'
          },
          ticks: {
           color: '#6b7280'
          }
        },
        y: {
         beginAtZero: true,
         grid: {
          color: '#e5e7eb'
          },
         ticks: {
          color: '#6b7280',
          callback: function(value) {
           return '₱' + value.toLocaleString();
=======
           color: '#374151'
          },
          ticks: {
           color: '#9ca3af'
          }
        },
        y: {
         grid: {
          color: '#374151'
         },
         ticks: {
          color: '#9ca3af',
          callback: function(value) {
           return '$' + value.toLocaleString();
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
          }
         }
        }
       }
      }
     });
    }

<<<<<<< HEAD
    // Initialize Payment Trends Line Chart
    const paymentTrendsCtx = document.getElementById('paymentTrendsChart');
    if (paymentTrendsCtx) {
     paymentTrendsChart = new Chart(paymentTrendsCtx, {
      type: 'line',
      data: {
       labels: [],
       datasets: [{
        label: 'Monthly Collections',
        data: [],
=======
    // Payment Trends Line Chart
    const paymentTrendsCtx = document.getElementById('paymentTrendsChart');
    if (paymentTrendsCtx) {
     new Chart(paymentTrendsCtx, {
      type: 'line',
      data: {
       labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
       datasets: [{
        label: 'Monthly Collections',
        data: [6500, 7200, 8900, 12000, 15800, 14200, 18900, 16500, 13200, 14800, 11200, 13500],
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        borderColor: '#f97316',
        backgroundColor: 'rgba(249, 115, 22, 0.1)',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#f97316',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 6,
        pointHoverRadius: 8
       }]
      },
      options: {
       responsive: true,
       maintainAspectRatio: false,
       plugins: {
        legend: {
         display: false
        },
        tooltip: {
         callbacks: {
          label: function(context) {
<<<<<<< HEAD
           return `Collections: ₱${context.parsed.y.toLocaleString()}`;
=======
           return `Collections: $${context.parsed.y.toLocaleString()}`;
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
          }
         }
        }
       },
       scales: {
        x: {
         grid: {
<<<<<<< HEAD
          color: '#e5e7eb'
         },
         ticks: {
          color: '#6b7280'
         }
        },
        y: {
         beginAtZero: true,
         grid: {
          color: '#e5e7eb'
         },
         ticks: {
          color: '#6b7280',
          callback: function(value) {
           return '₱' + value.toLocaleString();
=======
          color: '#374151'
         },
         ticks: {
          color: '#9ca3af'
         }
        },
        y: {
         grid: {
          color: '#374151'
         },
         ticks: {
          color: '#9ca3af',
          callback: function(value) {
           return '$' + value.toLocaleString();
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
          }
         }
        }
       },
       interaction: {
        intersect: false,
        mode: 'index'
       }
      }
     });
    }
<<<<<<< HEAD

    // Export Chart Function
    window.exportChart = function(canvasId) {
        const canvas = document.getElementById(canvasId);
        if (canvas) {
            const link = document.createElement('a');
            link.download = canvasId + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        }
    }

    // Reset Filters Function
    window.resetFilters = function() {
        const schoolYear = document.getElementById('filterSchoolYear');
        const level = document.getElementById('filterLevel');
        const section = document.getElementById('filterSection');
        const startDate = document.getElementById('filterStartDate');
        const endDate = document.getElementById('filterEndDate');

        if (schoolYear) schoolYear.value = ACTIVE_SCHOOL_YEAR || '';
        if (level) level.value = '';
        if (section) section.value = '';
        if (startDate) startDate.value = '';
        if (endDate) endDate.value = '';

        fetchMetrics();
    }

    // Function to fetch and update metrics
    function fetchMetrics() {
        // Toggle Read Only Badge
        const schoolYearEl = document.getElementById('filterSchoolYear');
        const badgeEl = document.getElementById('readOnlyBadge');
        if (schoolYearEl && badgeEl) {
            const selectedYear = schoolYearEl.value;
            // If a year is selected AND it is NOT the active year, show badge.
            // If "All Years" is selected (value=""), we can decide. Usually "All Years" implies read-only historical view too, or mixed.
            // But strict requirement: "when i click previous year i can only view not editable".
            // So if selectedYear !== ACTIVE_SCHOOL_YEAR and selectedYear !== '', show badge.
            // Or maybe if selectedYear !== ACTIVE_SCHOOL_YEAR, show badge?
            // If I select "All Years", I can't really "edit" bulk data safely anyway.
            // Let's stick to: if selectedYear && selectedYear !== ACTIVE_SCHOOL_YEAR -> Show Badge.
            if (selectedYear && selectedYear !== ACTIVE_SCHOOL_YEAR) {
                badgeEl.classList.remove('hidden');
                badgeEl.classList.add('flex');
            } else {
                badgeEl.classList.add('hidden');
                badgeEl.classList.remove('flex');
            }
        }

        // Loading State
        const mainContent = document.querySelector('main');
        if (mainContent) mainContent.style.opacity = '0.7';
        
        // Show chart loaders
        ['paymentStatusLoading', 'collectionsLoading', 'trendsLoading'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.remove('hidden');
        });

        const schoolYear = document.getElementById('filterSchoolYear')?.value || '';
        const level = document.getElementById('filterLevel')?.value || '';
        const section = document.getElementById('filterSection')?.value || '';
        const startDate = document.getElementById('filterStartDate')?.value || '';
        const endDate = document.getElementById('filterEndDate')?.value || '';

        const params = new URLSearchParams({
            school_year: schoolYear,
            level: level,
            section: section,
            start_date: startDate,
            end_date: endDate
        });

        fetch('{{ route("admin_dashboard.metrics") }}?' + params.toString())
            .then(response => response.json())
            .then(data => {
                // Remove Loading State
                if (mainContent) mainContent.style.opacity = '1';
                
                // Hide chart loaders
                ['paymentStatusLoading', 'collectionsLoading', 'trendsLoading'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.classList.add('hidden');
                });

                // --- UPDATE KPI CARDS ---

                // 1. Total Collected
                const totalCollectedEl = document.getElementById('totalCollectedDisplay');
                if (totalCollectedEl) totalCollectedEl.innerText = '₱' + data.totalCollected.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

                // Pending Approvals
                const pendingApprovalsEl = document.getElementById('pendingApprovalsDisplay');
                if (pendingApprovalsEl && data.pendingApprovals !== undefined) {
                    pendingApprovalsEl.innerText = '₱' + data.pendingApprovals.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }

                // Collected Pct
                const collectedPctEl = document.getElementById('collectedPctDisplay');
                if (collectedPctEl) {
                    const pct = data.expectedCollection > 0 ? (data.totalCollected / data.expectedCollection) * 100 : 0;
                    collectedPctEl.innerText = `(${pct.toFixed(1)}% of expected)`;
                }

                // Collected Trend
                const collectedTrendEl = document.getElementById('collectedTrendDisplay');
                if (collectedTrendEl) {
                    const prev = data.prevTotalCollected;
                    const curr = data.totalCollected;
                    const trend = prev > 0 ? ((curr - prev) / prev) * 100 : 0;
                    const isUp = trend >= 0;
                    const colorClass = isUp ? 'text-green-600' : 'text-red-500';
                    const icon = isUp ? 'fa-arrow-up' : 'fa-arrow-down';
                    
                    collectedTrendEl.innerHTML = `
                        <span class="${colorClass} flex items-center"><i class="fas ${icon} mr-1"></i> ${Math.abs(trend).toFixed(1)}%</span>
                        <span class="text-slate-400">vs last month</span>
                    `;
                }

                // 2. Pending Payments
                const pendingOutstandingEl = document.getElementById('pendingOutstandingDisplay');
                if (pendingOutstandingEl) pendingOutstandingEl.innerText = '₱' + data.pendingOutstanding.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

                const pendingPctEl = document.getElementById('pendingPctDisplay');
                if (pendingPctEl) {
                     const pct = data.expectedCollection > 0 ? (data.pendingOutstanding / data.expectedCollection) * 100 : 0;
                     pendingPctEl.innerText = `(${pct.toFixed(1)}% of total)`;
                }

                // 3. Students Count
                const studentsCountEl = document.getElementById('studentsCountDisplay');
                if (studentsCountEl) studentsCountEl.innerText = data.studentsCount;

                const studentsTrendEl = document.getElementById('studentsTrendDisplay');
                if (studentsTrendEl) {
                    const prev = data.prevStudentsCount;
                    const curr = data.studentsCount;
                    const trend = prev > 0 ? ((curr - prev) / prev) * 100 : 0;
                    const isUp = trend >= 0;
                    const colorClass = isUp ? 'text-green-600' : 'text-red-500';
                    const icon = isUp ? 'fa-arrow-up' : 'fa-arrow-down';
                    
                    studentsTrendEl.innerHTML = `
                        <span class="${colorClass} flex items-center"><i class="fas ${icon} mr-1"></i> ${Math.abs(trend).toFixed(1)}%</span>
                        <span class="text-slate-400">vs last month</span>
                    `;
                }

                // 4. SMS Sent
                const smsSentEl = document.getElementById('smsSentDisplay');
                if (smsSentEl) smsSentEl.innerText = data.smsSentThisWeek;

                const smsTrendEl = document.getElementById('smsTrendDisplay');
                if (smsTrendEl) {
                    const prev = data.smsSentLastWeek;
                    const curr = data.smsSentThisWeek;
                    const trend = prev > 0 ? ((curr - prev) / prev) * 100 : 0;
                    const isUp = trend >= 0;
                    const colorClass = isUp ? 'text-green-600' : 'text-red-500';
                    const icon = isUp ? 'fa-arrow-up' : 'fa-arrow-down';
                    
                    smsTrendEl.innerHTML = `
                        <span class="${colorClass} flex items-center"><i class="fas ${icon} mr-1"></i> ${Math.abs(trend).toFixed(1)}%</span>
                        <span class="text-slate-400">vs last week</span>
                    `;
                }

                // --- UPDATE CHARTS ---

                // Update Payment Status Chart
                if (paymentStatusChart) {
                    paymentStatusChart.data.datasets[0].data = [data.totalCollected, data.pendingOutstanding];
                    paymentStatusChart.update();
                }

                // Update Collections by Grade Chart
        if (collectionsByGradeChart) {
            collectionsByGradeChart.data.labels = data.collectionsByGrade.map(item => item.label);
            collectionsByGradeChart.data.datasets[0].data = data.collectionsByGrade.map(item => item.total);
            // Ensure enough colors
            const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
            while (collectionsByGradeChart.data.datasets[0].backgroundColor.length < data.collectionsByGrade.length) {
                collectionsByGradeChart.data.datasets[0].backgroundColor.push(colors[collectionsByGradeChart.data.datasets[0].backgroundColor.length % colors.length]);
            }
            collectionsByGradeChart.update();
        }

                // Update Payment Trends Chart
                if (paymentTrendsChart) {
                    paymentTrendsChart.data.labels = data.paymentTrends.map(item => item.month);
                    paymentTrendsChart.data.datasets[0].data = data.paymentTrends.map(item => item.total);
                    paymentTrendsChart.update();
                }

                // --- UPDATE TABLES ---

                // Pending Payments Table
                const pendingTableBody = document.getElementById('pendingPaymentsTableBody');
                if (pendingTableBody) {
                    pendingTableBody.innerHTML = '';
                    if (data.pendingPayments && data.pendingPayments.length > 0) {
                        data.pendingPayments.forEach(rec => {
                            const overdueBadge = rec.days_overdue > 0 
                                ? `<span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">${rec.days_overdue} days</span>`
                                : `<span class="text-gray-400 text-xs">-</span>`;
                            
                            const row = `
                                <tr class="hover:bg-orange-50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">${rec.student_name}</div>
                                        <div class="text-xs text-gray-500">ID: ${rec.student_id}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-600">${rec.parent_name}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-500">${rec.due_date}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">${overdueBadge}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-orange-600">₱${rec.balance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <a href="/admin/students?id=${rec.student_id}" class="text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                                            View Ledger
                                        </a>
                                    </td>
                                </tr>
                            `;
                            pendingTableBody.insertAdjacentHTML('beforeend', row);
                        });
                    } else {
                         pendingTableBody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No pending payments found</td></tr>';
                    }
                }

                // Recent Transactions Table
                const recentTableBody = document.getElementById('recentTransactionsTableBody');
                if (recentTableBody) {
                    recentTableBody.innerHTML = '';
                    if (data.recentTransactions && data.recentTransactions.length > 0) {
                        data.recentTransactions.forEach(pay => {
                            const row = `
                                <tr class="hover:bg-green-50 transition-colors cursor-pointer" onclick="window.location.href='/admin/students?id=${pay.student_id}'">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">${pay.student_name}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                                        ${pay.paid_at}
                                        ${pay.ip_address ? `<div class="text-xs text-gray-400 mt-0.5" title="IP Address"><i class="fas fa-shield-alt mr-1"></i>${pay.ip_address}</div>` : ''}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            ${pay.method}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs font-mono text-gray-500">${pay.reference_number}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-green-600">₱${pay.amount_paid.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                </tr>
                            `;
                            recentTableBody.insertAdjacentHTML('beforeend', row);
                        });
                    } else {
                        recentTableBody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No recent transactions found</td></tr>';
                    }
                }

            })
            .catch(error => {
                console.error('Error fetching metrics:', error);
                if (mainContent) mainContent.style.opacity = '1';
                // Hide chart loaders on error
                ['paymentStatusLoading', 'collectionsLoading', 'trendsLoading'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.classList.add('hidden');
                });
            });
    }

    // Expose fetchMetrics to window so it can be called from onclick
    window.fetchMetrics = fetchMetrics;

    // Fetch immediately and then every 30 seconds (reduced frequency)
    fetchMetrics();
    setInterval(fetchMetrics, 30000);
=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
   });
  </script>
 </body>
</html>
