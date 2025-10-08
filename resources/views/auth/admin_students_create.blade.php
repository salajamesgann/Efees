<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Student - Efees Admin</title>
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
  <main class="flex-1 p-8 overflow-y-auto bg-slate-900">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-semibold tracking-tight text-slate-100">Create Student</h1>
      <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold px-4 py-2 rounded-lg transition-colors duration-200">
        <i class="fas fa-arrow-left"></i>
        Back to Students
      </a>
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

    <form method="POST" action="{{ route('admin.students.store') }}" class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover">
      @csrf

      <h2 class="text-xl font-semibold mb-6 text-indigo-400">Student Information</h2>

      <!-- Personal Information Section -->
      <div class="mb-8">
        <h3 class="text-lg font-medium mb-4 text-slate-200">Personal Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium mb-2 text-slate-300">First Name</label>
            <input name="first_name" value="{{ old('first_name') }}" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2 text-slate-300">Middle Initial</label>
            <input name="middle_initial" value="{{ old('middle_initial') }}" type="text" maxlength="1" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2 text-slate-300">Last Name</label>
            <input name="last_name" value="{{ old('last_name') }}" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2 text-slate-300">Contact Number</label>
            <input name="contact_number" value="{{ old('contact_number') }}" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2 text-slate-300">Sex</label>
            <select name="sex" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
              <option value="">Select Sex</option>
              <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
              <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Academic Information Section -->
      <div class="mb-8">
        <h3 class="text-lg font-medium mb-4 text-slate-200">Academic Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium mb-2 text-slate-300">Grade Level</label>
            <input name="level" value="{{ old('level') }}" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required placeholder="e.g., Grade 7, 1st Year, etc." />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2 text-slate-300">Section</label>
            <input name="section" value="{{ old('section') }}" type="text" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
          </div>
        </div>
      </div>

      <!-- Account Information Section -->
      <div class="mb-8">
        <h3 class="text-lg font-medium mb-4 text-slate-200">Account Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium mb-2 text-slate-300">Email</label>
            <input name="email" value="{{ old('email') }}" type="email" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2 text-slate-300">Password</label>
            <input name="password" type="password" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2 text-slate-300">Confirm Password</label>
            <input name="password_confirmation" type="password" class="w-full rounded-lg border border-slate-600 bg-slate-700 text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-4 mt-8">
        <a href="{{ route('admin.students.index') }}" class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold rounded-lg transition-colors duration-200">Cancel</a>
        <button type="submit" class="px-6 py-2 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-lg transition-colors duration-200">Create Student</button>
      </div>
    </form>
  </main>
</body>
</html>
