<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Students - Efees Admin</title>
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
      <a aria-current="page" class="flex items-center gap-3 px-4 py-3 rounded-lg text-indigo-400 bg-slate-700 border-r-4 border-indigo-500 font-semibold transition-colors duration-200" href="{{ route('admin.students.index') }}">
        <i class="fas fa-users w-5"></i>
        <span class="text-sm font-semibold">Manage Students</span>
      </a>
      <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="{{ route('admin.staff.index') }}">
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
  <main class="flex-1 p-6 md:p-8 overflow-y-auto bg-slate-900">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-slate-100">Manage Students</h1>
      <a href="{{ route('admin.students.create') }}" class="inline-flex items-center gap-2 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
        <i class="fas fa-plus"></i>
        Add Student
      </a>
    </div>

    @if (session('success'))
      <div class="mb-6 border border-green-600 text-green-300 bg-green-900/20 rounded-md px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="mb-6 border border-red-600 text-red-300 bg-red-900/20 rounded-md px-4 py-3">
        {{ session('error') }}
      </div>
    @endif

    <!-- Students List -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h2 class="text-xl font-semibold text-indigo-400">Students</h2>
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex gap-2 w-full md:w-auto">
          <input type="text" name="q" value="{{ $q }}" placeholder="Search by ID, name, email, section, year level" class="flex-1 md:w-80 h-10 rounded-lg border border-slate-600 bg-slate-700 text-slate-100 placeholder-slate-400 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" />
          <button class="px-4 h-10 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-lg shadow-lg transition-colors duration-200">Search</button>
        </form>
      </div>
      <div class="overflow-x-auto scrollbar-thin">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-700">
            <tr class="text-left text-slate-300">
              <th class="px-4 py-3 font-semibold">Student ID</th>
              <th class="px-4 py-3 font-semibold">Name</th>
              <th class="px-4 py-3 font-semibold">Year Level</th>
              <th class="px-4 py-3 font-semibold">Section</th>
              <th class="px-4 py-3 font-semibold">Sex</th>
              <th class="px-4 py-3 font-semibold">Contact</th>
              <th class="px-4 py-3 font-semibold">Email</th>
              <th class="px-4 py-3 font-semibold">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700">
            @forelse ($students as $s)
              <tr class="hover:bg-slate-700/50 transition-colors duration-200">
                <td class="px-4 py-3 whitespace-nowrap text-slate-300 font-medium">{{ $s->student_id }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-slate-300 font-medium">{{ $s->full_name }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-slate-400">{{ $s->level }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-slate-400">{{ $s->section }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-slate-400">{{ $s->sex }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-slate-400">{{ $s->contact_number }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-slate-400">{{ optional($s->user)->email ?? '-' }}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <a href="{{ route('admin.students.edit', $s) }}" class="inline-flex items-center h-9 px-3 rounded-lg bg-blue-500 text-white font-semibold hover:bg-blue-600 transition-colors duration-200" title="Edit">
                      <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <form method="POST" action="{{ route('admin.students.destroy', $s) }}" onsubmit="return confirm('Delete this student? This action cannot be undone.');" class="inline-block">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="inline-flex items-center h-9 px-3 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors duration-200" title="Delete">
                        <i class="fas fa-trash mr-1"></i> Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-4 py-6 text-center text-slate-400">No students found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-6">
        {{ $students->links() }}
      </div>
    </section>
  </main>
</body>
</html>

