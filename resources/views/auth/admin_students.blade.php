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
    body { font-family: 'Inter', 'Noto Sans', sans-serif; background-color: #000; min-height: 100vh; }
    .card { background-color: #1a1a1a; color: #fb923c; }
    .btn { background-color: #f97316; color: #000; }
    .btn:hover { background-color: #ea7a11; }
    .scrollbar-thin::-webkit-scrollbar { width: 6px; height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background-color: #f97316; border-radius: 3px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    /* Scrollbar for sidebar */
    #sidebar::-webkit-scrollbar { width: 6px; }
    #sidebar::-webkit-scrollbar-thumb { background-color: #f97316; border-radius: 3px; }
    #sidebar::-webkit-scrollbar-track { background: transparent; }
  </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen">
  <!-- Sidebar -->
  <aside class="flex flex-col bg-black text-[#fb923c] w-full md:w-64 min-h-screen border-r border-[#ea9e4a] overflow-y-auto" id="sidebar" style="scrollbar-width: thin; scrollbar-color: #f97316 transparent;">
    <div class="flex items-center gap-3 px-8 py-6 border-b border-[#e7f4e7]">
      <div class="w-8 h-8 flex-shrink-0 text-[#f97316]">
        <svg class="w-full h-full" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
          <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor"></path>
        </svg>
      </div>
      <h1 class="text-[#f97316] font-extrabold text-xl tracking-tight select-none" style="letter-spacing: -0.015em;">Efees Admin</h1>
    </div>
    <nav class="flex flex-col mt-6 px-4 space-y-1 flex-grow">
      <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="{{ route('admin_dashboard') }}">
        <i class="fas fa-tachometer-alt w-5"></i>
        <span class="text-sm font-semibold">Dashboard</span>
      </a>
      <a aria-current="page" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-[#f97316] text-black font-semibold transition-colors duration-300" href="{{ route('admin.students.index') }}">
        <i class="fas fa-users w-5"></i>
        <span class="text-sm font-semibold">Manage Students</span>
      </a>
      <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="#">
        <i class="fas fa-file-invoice-dollar w-5"></i>
        <span class="text-sm font-semibold">Fee Management</span>
      </a>
      <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="#">
        <i class="fas fa-chart-bar w-5"></i>
        <span class="text-sm font-semibold">Reports</span>
      </a>
      <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="#">
        <i class="fas fa-cog w-5"></i>
        <span class="text-sm font-semibold">Settings</span>
      </a>
    </nav>
    <div class="px-4 py-4 border-t border-[#e7f4e7]">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 bg-[#f97316] text-black font-bold text-sm rounded-lg h-10 justify-center cursor-pointer select-none transition-colors duration-300 hover:bg-[#ea7a11]" aria-label="Logout">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- Main content -->
  <main class="flex-1 p-8 overflow-y-auto" style="color: #fb923c; background-color: #121212;">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-extrabold select-none" style="letter-spacing: -0.015em;">Manage Students</h1>
    </div>

    @if (session('success'))
      <div class="mb-6 rounded border border-green-700 bg-green-900/40 text-green-200 px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    <!-- Students List -->
    <section class="bg-[#1a1a1a] rounded-lg shadow-lg p-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
        <h2 class="text-lg font-bold" style="color:#f97316;">Students</h2>
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex gap-2 w-full md:w-auto">
          <input type="text" name="q" value="{{ $q }}" placeholder="Search by ID, name, email, section, department" class="flex-1 md:w-80 rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" />
          <button class="btn font-semibold px-4 py-2 rounded">Search</button>
        </form>
      </div>
      <div class="overflow-x-auto scrollbar-thin">
        <table class="min-w-full text-sm text-white">
          <thead class="bg-[#121212]">
            <tr class="text-left">
              <th class="px-4 py-2">Student ID</th>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Level</th>
              <th class="px-4 py-2">Section</th>
              <th class="px-4 py-2">Department</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ea9e4a]">
            @forelse ($students as $s)
              <tr>
                <td class="px-4 py-2 whitespace-nowrap">{{ $s->student_id }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ $s->full_name }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ $s->level }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ $s->section }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ $s->department }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ optional($s->user)->email ?? '-' }}</td>
                <td class="px-4 py-2 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <a href="{{ route('admin.students.edit', $s) }}" class="inline-flex items-center px-3 py-1 rounded bg-blue-500 text-black font-semibold hover:bg-blue-400" title="Edit">
                      <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <form method="POST" action="{{ route('admin.students.destroy', $s) }}" onsubmit="return confirm('Delete this student? This action cannot be undone.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="inline-flex items-center px-3 py-1 rounded bg-red-600 text-white font-semibold hover:bg-red-500" title="Delete">
                        <i class="fas fa-trash mr-1"></i> Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-[#fdba74]">No students found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-4">
        {{ $students->links() }}
      </div>
    </section>
  </main>
</body>
</html>
