<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Parent Management - Efees Admin</title>
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    body { font-family: 'Inter', 'Noto Sans', sans-serif; }
    [x-cloak] { display: none !important; }
  </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
  <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" x-cloak></div>

  <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white text-slate-800 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 border-r border-slate-200 flex flex-col shrink-0 shadow-[4px_0_24px_-12px_rgba(0,0,0,0.1)]" id="sidebar">
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
      <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 transition-colors">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow pb-6 overflow-y-auto custom-scrollbar">
      <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
      <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin_dashboard') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin_dashboard') }}">
        <div class="w-8 flex justify-center">
          <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('admin_dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
        </div>
        <span class="text-sm font-medium">Dashboard</span>
      </a>
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
      <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.staff.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.staff.index') }}">
        <div class="w-8 flex justify-center">
          <i class="fas fa-chalkboard-teacher text-lg {{ request()->routeIs('admin.staff.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
        </div>
        <span class="text-sm font-medium">Staff Management</span>
      </a>
      <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fees.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.fees.index') }}">
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
      <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.reports.index') }}">
        <div class="w-8 flex justify-center">
          <i class="fas fa-chart-bar text-lg {{ request()->routeIs('admin.reports.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
        </div>
        <span class="text-sm font-medium">Reports & Analytics</span>
      </a>
      <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-6">System</p>
      <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.audit-logs.index') }}">
        <div class="w-8 flex justify-center">
          <i class="fas fa-shield-alt text-lg {{ request()->routeIs('admin.audit-logs.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
        </div>
        <span class="text-sm font-medium">Audit Logs</span>
      </a>
      <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.sms.logs') }}">
        <div class="w-8 flex justify-center">
          <i class="fas fa-comment-alt text-lg {{ request()->routeIs('admin.sms.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
        </div>
        <span class="text-sm font-medium">SMS Control</span>
      </a>
      <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.settings.index') }}">
        <div class="w-8 flex justify-center">
          <i class="fas fa-cog text-lg {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
        </div>
        <span class="text-sm font-medium">Settings</span>
      </a>
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 px-4 pb-6">
      @csrf
      <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 hover:shadow-sm transition-all duration-200 group border border-red-100">
        <div class="w-8 flex justify-center">
          <i class="fas fa-sign-out-alt text-lg group-hover:scale-110 transition-transform"></i>
        </div>
        <span class="text-sm font-bold">Logout</span>
      </button>
    </form>
  </aside>

  <div class="flex-1 flex flex-col h-screen overflow-hidden">
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

    <main class="flex-1 overflow-y-auto bg-slate-50 p-6 lg:p-8">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-gray-900">Parent / Guardian Management</h1>
        <a href="{{ route('admin.parents.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
          <i class="fas fa-plus"></i>
          Add Parent
        </a>
      </div>

      <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 bg-white p-4 rounded-xl border">
        <div class="md:col-span-6">
          <label class="text-xs font-semibold text-slate-500">Search</label>
          <input type="text" name="q" value="{{ $q }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Name, email, phone"/>
        </div>
        <div class="md:col-span-3">
          <label class="text-xs font-semibold text-slate-500">Status</label>
          <select name="status" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
            <option value="archived" {{ $status === 'archived' ? 'selected' : '' }}>Archived</option>
          </select>
        </div>
        <div class="md:col-span-3 flex items-end">
          <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all shadow-sm w-full">Filter</button>
        </div>
      </form>

      <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mt-6">
        <table class="w-full text-left border-collapse">
          <thead class="bg-slate-50/50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
            <tr>
              <th class="px-6 py-4">Name</th>
              <th class="px-6 py-4">Contact</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4">Links</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($parents as $p)
            <tr>
              <td class="px-6 py-4 font-semibold">{{ $p->full_name }}</td>
              <td class="px-6 py-4">
                <div>{{ $p->phone ?? '—' }}</div>
                <div class="text-slate-500">{{ $p->email ?? '—' }}</div>
              </td>
              <td class="px-6 py-4">{{ $p->account_status }}</td>
              <td class="px-6 py-4">
                @if($p->students()->exists())
                  <ul class="text-xs text-slate-600">
                    @foreach($p->students as $s)
                      <li>{{ $s->full_name }} ({{ $s->student_id }}) — {{ $s->pivot->relationship }}@if($s->pivot->is_primary) • primary @endif</li>
                    @endforeach
                  </ul>
                @else
                  <span class="text-slate-400">No links</span>
                @endif
              </td>
              <td class="px-6 py-4 text-right">
                <a class="text-blue-600 font-semibold" href="{{ route('admin.parents.edit', $p) }}">Edit</a>
                @if($p->account_status === 'Active')
                  <form method="POST" action="{{ route('admin.parents.archive', $p) }}" class="inline">
                    @csrf @method('patch')
                    <button class="text-orange-600 font-semibold">Archive</button>
                  </form>
                @else
                  <form method="POST" action="{{ route('admin.parents.unarchive', $p) }}" class="inline">
                    @csrf @method('patch')
                    <button class="text-green-600 font-semibold">Unarchive</button>
                  </form>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="px-6 py-10 text-center text-slate-500">No parents found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
        <div class="px-6 py-4 border-t border-slate-200">
          {{ $parents->links() }}
        </div>
      </div>
    </main>
  </div>
</body>
</html>
