<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Student - Efees Admin</title>
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <style>
    body { font-family: 'Inter', 'Noto Sans', sans-serif; background-color: #000; min-height: 100vh; }
    .card { background-color: #1a1a1a; color: #fb923c; }
    .btn { background-color: #f97316; color: #000; }
    .btn:hover { background-color: #ea7a11; }
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
    <div class="flex items-center justify-between mb-8">
      <h1 class="text-3xl font-extrabold select-none" style="letter-spacing: -0.015em;">Edit Student</h1>
      <a href="{{ route('admin.students.index') }}" class="btn font-semibold px-4 py-2 rounded inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to list
      </a>
    </div>

    @if ($errors->any())
      <div class="mb-6 rounded border border-red-700 bg-red-900/40 text-red-200 px-4 py-3">
        <div class="font-bold mb-1">Please fix the following errors:</div>
        <ul class="list-disc list-inside text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <section class="card rounded-lg shadow-lg p-6">
      <form method="POST" action="{{ route('admin.students.update', $student) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm mb-1">Student ID</label>
            <input type="text" class="w-full rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" value="{{ $student->student_id }}" disabled />
          </div>
          <div>
            <label class="block text-sm mb-1">First Name</label>
            <input name="first_name" value="{{ old('first_name', $student->first_name) }}" type="text" class="w-full rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" required />
          </div>
          <div>
            <label class="block text-sm mb-1">Middle Initial</label>
            <input name="middle_initial" value="{{ old('middle_initial', $student->middle_initial) }}" type="text" maxlength="1" class="w-full rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm mb-1">Last Name</label>
            <input name="last_name" value="{{ old('last_name', $student->last_name) }}" type="text" class="w-full rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" required />
          </div>
          <div>
            <label class="block text-sm mb-1">Contact Number</label>
            <input name="contact_number" value="{{ old('contact_number', $student->contact_number) }}" type="number" class="w-full rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" required />
          </div>
          <div>
            <label class="block text-sm mb-1">Level</label>
            <select name="level" class="w-full rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" required>
              @php($levels = ['1st Year','2nd Year','3rd Year','4th Year'])
              @foreach($levels as $lvl)
                <option value="{{ $lvl }}" {{ old('level', $student->level) === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Section</label>
            <input name="section" value="{{ old('section', $student->section) }}" type="text" class="w-full rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" required />
          </div>
          <div>
            <label class="block text-sm mb-1">Department</label>
            <input name="department" value="{{ old('department', $student->department) }}" type="text" class="w-full rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" required />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm mb-1">Email</label>
            <input name="email" value="{{ old('email', optional($student->user)->email) }}" type="email" class="w-full rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" required />
          </div>
          <div>
            <label class="block text-sm mb-1">New Password (optional)</label>
            <input name="password" type="password" class="w-full rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" placeholder="Leave blank to keep current" />
          </div>
          <div>
            <label class="block text-sm mb-1">Confirm New Password</label>
            <input name="password_confirmation" type="password" class="w-full rounded border border-[#ea9e4a] bg-[#0f0f0f] text-[#fb923c] px-3 py-2" />
          </div>
        </div>

        <div class="flex items-center gap-3">
          <button type="submit" class="btn font-bold px-6 py-2 rounded">Save Changes</button>
          <a href="{{ route('admin.students.index') }}" class="inline-flex items-center px-4 py-2 rounded bg-[#1f2937] text-[#fb923c] hover:bg-[#374151]">Cancel</a>
        </div>
      </form>
    </section>
  </main>
</body>
</html>
