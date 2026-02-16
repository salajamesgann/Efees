<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>Student Profile - Efees</title>
  <link rel="preconnect" href="https://fonts.gstatic.com/">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <style>
    body { font-family: 'Inter', 'Noto Sans', sans-serif; background-color: #1e293b; color: #f1f5f9; }
    .gradient-bg {
      background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
    }
    .gradient-text {
      background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .btn-primary {
      background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(251, 146, 60, 0.3);
    }
    .card-hover {
      transition: all 0.3s ease;
    }
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px -5px rgba(251, 146, 60, 0.2);
    }
  </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-slate-900 text-slate-100">
  <!-- Sidebar -->
  <aside class="flex flex-col bg-slate-800 text-slate-300 w-full md:w-72 min-h-screen border-r border-slate-700 overflow-y-auto custom-scrollbar" id="sidebar" style="scrollbar-width: thin; scrollbar-color: #f97316 transparent;">
    <div class="flex items-center gap-3 px-8 py-6 border-b border-slate-700">
      <div class="w-8 h-8 flex-shrink-0 text-orange-500">
        <svg class="w-full h-full" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
          <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor">
          </path>
        </svg>
      </div>
      <h1 class="text-orange-400 font-extrabold text-xl tracking-tight select-none" style="letter-spacing: -0.015em;">
        Efees
      </h1>
    </div>
    <nav class="flex flex-col mt-6 px-4 space-y-1 flex-grow">
      <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-orange-400 transition-colors duration-200" href="{{ route('user_dashboard') }}">
        <i class="fas fa-tachometer-alt w-5"></i>
        <span class="text-sm font-semibold">
          Student Dashboard
        </span>
      </a>
      <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-orange-400 transition-colors duration-200" href="#">
        <i class="fas fa-credit-card w-5"></i>
        <span class="text-sm font-semibold">Online Payment</span>
      </a>

      <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-orange-400 transition-colors duration-200" href="#">
        <i class="fas fa-history w-5">
        </i>
        <span class="text-sm font-semibold">
          Payment History
        </span>
      </a>
      <a class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-orange-400 transition-colors duration-300" href="#">
        <div class="flex items-center gap-3">
          <i class="fas fa-sms w-5">
          </i>
          <span class="text-sm font-semibold">
            SMS Notifications
          </span>
        </div>
        <span id="notif-badge" class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-orange-500 rounded-full select-none">
          {{ $notifications->count() ?? 0 }}
        </span>
      </a>
      <a aria-current="page" class="flex items-center gap-3 px-4 py-3 rounded-lg text-orange-400 bg-slate-700 border-r-4 border-orange-500 font-semibold transition-colors duration-200" href="{{ route('student.profile.show') }}">
        <i class="fas fa-user-cog w-5"></i>
        <span class="text-sm font-semibold">Profile Management</span>
      </a>
    </nav>
    <div class="px-4 py-4 border-t border-slate-700">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="w-full flex items-center gap-3 bg-orange-500 text-white font-bold text-sm rounded-lg h-10 justify-center cursor-pointer select-none transition-colors duration-300 hover:bg-orange-600" type="submit" aria-label="Logout">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- Main content -->
  <main class="flex-1 p-8 overflow-y-auto bg-slate-900 custom-scrollbar">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-extrabold select-none text-slate-100" style="letter-spacing: -0.015em;">
        Profile Management
      </h1>
      <!-- User Profile Dropdown -->
      <div class="relative" id="user-menu">
        <button class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-800 transition-colors duration-200" id="user-menu-button" type="button" aria-expanded="false" aria-haspopup="true">
          <div class="text-right">
            <p class="text-sm font-semibold text-orange-400">{{ Auth::user()->student ? Auth::user()->student->first_name . ' ' . Auth::user()->student->last_name : 'User' }}</p>
            <p class="text-xs text-slate-400">{{ Auth::user()->email }}</p>
          </div>
          @php($photo = optional(Auth::user()->student)->profile_picture_url)
          @if(!empty($photo))
            <img src="{{ $photo }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-orange-500" />
          @else
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white font-bold text-sm">
              {{ Auth::user()->student ? strtoupper(substr(Auth::user()->student->first_name, 0, 1) . substr(Auth::user()->student->last_name, 0, 1)) : 'U' }}
            </div>
          @endif
          <i class="fas fa-chevron-down text-slate-400"></i>
        </button>
        <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-slate-800 py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden" id="user-menu-dropdown" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
          <a class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-orange-400 transition-colors duration-200" href="{{ route('user_dashboard') }}" role="menuitem" tabindex="-1" id="user-menu-item-0">Dashboard</a>
          <a class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-orange-400 transition-colors duration-200" href="{{ route('student.profile.show') }}" role="menuitem" tabindex="-1" id="user-menu-item-1">Profile</a>
          <a class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-orange-400 transition-colors duration-200" href="{{ route('logout') }}" role="menuitem" tabindex="-1" id="user-menu-item-2">Logout</a>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="mb-6 border border-green-600 text-green-300 bg-green-900/20 rounded-md px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    <div class="max-w-4xl mx-auto">
      <div class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6">
        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
          @csrf
          <div class="flex items-center gap-4">
            <div class="w-20 h-20 rounded-full overflow-hidden bg-slate-700 border border-slate-600 flex items-center justify-center">
              @if(($student->profile_picture_url ?? null))
                <img src="{{ $student->profile_picture_url }}" alt="Profile Picture" class="w-full h-full object-cover"/>
              @else
                <span class="text-xl font-bold text-white bg-gradient-to-br from-orange-500 to-orange-600 w-full h-full flex items-center justify-center">
                  {{ strtoupper(substr($student->first_name ?? 'U',0,1) . substr($student->last_name ?? 'N',0,1)) }}
                </span>
              @endif
            </div>
            <div>
              <label for="profile_picture" class="block text-sm font-medium text-slate-300">Profile Picture</label>
              <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="mt-1 block w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" />
              <p class="text-xs text-slate-400 mt-1">Accepted: JPG, PNG, WEBP. Max 2MB.</p>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-300" for="student_id">Student ID</label>
            <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 px-3" type="text" id="student_id" name="student_id" value="{{ $student->student_id }}" readonly />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-300" for="first_name">First Name</label>
              <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-300" for="middle_initial">Middle Initial</label>
              <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="middle_initial" name="middle_initial" value="{{ old('middle_initial', $student->middle_initial) }}" maxlength="1" />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-300" for="last_name">Last Name</label>
              <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-300" for="email">Email</label>
              <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-300" for="contact_number">Contact Number</label>
              <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $student->contact_number) }}" required />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-300" for="sex">Sex</label>
              <select class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" id="sex" name="sex" required>
                <option value="">Select sex</option>
                <option value="Male" {{ old('sex', $student->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('sex', $student->sex) == 'Female' ? 'selected' : '' }}>Female</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-300" for="level">Grade Level</label>
              <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="level" name="level" value="{{ old('level', $student->level) }}" required placeholder="e.g., Grade 7, 1st Year, etc." />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-300" for="section">Section</label>
              <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="section" name="section" value="{{ old('section', $student->section) }}" required />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-300" for="new_password">New Password</label>
              <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="password" id="new_password" name="new_password" placeholder="Leave blank to keep current" />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-300" for="new_password_confirmation">Confirm New Password</label>
              <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Re-enter new password" />
            </div>
          </div>

          <div class="flex items-center gap-3">
            <a href="{{ route('user_dashboard') }}" class="px-4 h-11 inline-flex items-center rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700">Cancel</a>
            <button type="submit" class="px-4 h-11 inline-flex items-center btn-primary text-white rounded-lg font-semibold shadow-lg">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <!-- Scripts -->
  <script>
    // User dropdown toggle
    document.getElementById('user-menu-button').addEventListener('click', function() {
      const dropdown = document.getElementById('user-menu-dropdown');
      dropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('user-menu-dropdown');
      const button = document.getElementById('user-menu-button');
      if (!button.contains(event.target)) {
        dropdown.classList.add('hidden');
      }
    });

    // Preview profile picture on file select
    const fileInput = document.getElementById('profile_picture');
    const avatarContainer = document.querySelector('.w-20.h-20.rounded-full.overflow-hidden');
    if (fileInput && avatarContainer) {
      fileInput.addEventListener('change', function() {
        const file = this.files && this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
          const img = avatarContainer.querySelector('img');
          if (img) {
            img.src = e.target.result;
          } else {
            avatarContainer.innerHTML = '<img alt="Profile Picture" class="w-full h-full object-cover"/>';
            avatarContainer.querySelector('img').src = e.target.result;
          }
        };
        reader.readAsDataURL(file);
      });
    }
  </script>
</body>
</html>

