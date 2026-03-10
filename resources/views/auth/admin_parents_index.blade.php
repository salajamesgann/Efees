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
  @include('layouts.admin_sidebar')

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

    <main class="flex-1 overflow-y-auto bg-slate-50 p-6 lg:p-8 custom-scrollbar">
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
