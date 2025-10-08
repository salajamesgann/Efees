<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>Student Settings - Efees</title>
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
    .settings-card {
      background: #1e293b;
      border: 1px solid #334155;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }
    .settings-card:hover {
      border-color: #f97316;
      box-shadow: 0 0 20px rgba(251, 146, 60, 0.1);
    }
  </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-slate-900 text-slate-100">
  <!-- Sidebar -->
  <aside class="flex flex-col bg-slate-800 text-slate-300 w-full md:w-72 min-h-screen border-r border-slate-700 overflow-y-auto" id="sidebar" style="scrollbar-width: thin; scrollbar-color: #f97316 transparent;">
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
      <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-orange-400 transition-colors duration-200" href="{{ route('student.profile.show') }}">
        <i class="fas fa-user-cog w-5"></i>
        <span class="text-sm font-semibold">Profile Management</span>
      </a>
      <a aria-current="page" class="flex items-center gap-3 px-4 py-3 rounded-lg text-orange-400 bg-slate-700 border-r-4 border-orange-500 font-semibold transition-colors duration-200" href="{{ route('student.settings') }}">
        <i class="fas fa-cog w-5"></i>
        <span class="text-sm font-semibold">Settings</span>
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
  <main class="flex-1 p-8 overflow-y-auto bg-slate-900">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-extrabold select-none text-slate-100" style="letter-spacing: -0.015em;">
        Student Settings
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
          <a class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-orange-400 transition-colors duration-200" href="{{ route('student.settings') }}" role="menuitem" tabindex="-1" id="user-menu-item-2">Settings</a>
          <a class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-orange-400 transition-colors duration-200" href="{{ route('logout') }}" role="menuitem" tabindex="-1" id="user-menu-item-3">Logout</a>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="mb-6 border border-green-600 text-green-300 bg-green-900/20 rounded-md px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    <div class="max-w-6xl mx-auto space-y-8">
      <!-- Personal Information Section -->
      <div class="settings-card p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-orange-400 flex items-center gap-3">
            <i class="fas fa-user-circle"></i>
            Personal Information
          </h2>
          <button type="button" onclick="toggleSection('personal-info')" class="text-slate-400 hover:text-orange-400 transition-colors">
            <i class="fas fa-chevron-down"></i>
          </button>
        </div>
        <div id="personal-info" class="space-y-6">
          <form method="POST" action="{{ route('student.settings.personal') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="first_name">First Name</label>
                <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="middle_initial">Middle Initial</label>
                <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="middle_initial" name="middle_initial" value="{{ old('middle_initial', $student->middle_initial) }}" maxlength="1" />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="last_name">Last Name</label>
                <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="email">Email Address</label>
                <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="contact_number">Contact Number</label>
                <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $student->contact_number) }}" required />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-300 mb-2" for="address">Address</label>
              <textarea class="w-full rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3 py-2" id="address" name="address" rows="3" placeholder="Enter your complete address">{{ old('address', $student->address) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="sex">Sex</label>
                <select class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" id="sex" name="sex" required>
                  <option value="">Select sex</option>
                  <option value="Male" {{ old('sex', $student->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ old('sex', $student->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="year">Year Level</label>
                <select class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" id="year" name="year" required>
                  <option value="">Select year level</option>
                  <option value="1st Year" {{ old('year', $student->year) == '1st Year' ? 'selected' : '' }}>1st Year</option>
                  <option value="2nd Year" {{ old('year', $student->year) == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                  <option value="3rd Year" {{ old('year', $student->year) == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                  <option value="4th Year" {{ old('year', $student->year) == '4th Year' ? 'selected' : '' }}>4th Year</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="section">Section</label>
                <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="section" name="section" value="{{ old('section', $student->section) }}" required />
              </div>
            </div>

            <div class="flex justify-end">
              <button type="submit" class="px-6 h-11 inline-flex items-center btn-primary text-white rounded-lg font-semibold shadow-lg">
                <i class="fas fa-save mr-2"></i>
                Update Personal Information
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Account Settings Section -->
      <div class="settings-card p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-orange-400 flex items-center gap-3">
            <i class="fas fa-shield-alt"></i>
            Account Settings
          </h2>
          <button type="button" onclick="toggleSection('account-settings')" class="text-slate-400 hover:text-orange-400 transition-colors">
            <i class="fas fa-chevron-down"></i>
          </button>
        </div>
        <div id="account-settings" class="space-y-6">
          <form method="POST" action="{{ route('student.settings.account') }}" class="space-y-6">
            @csrf
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-2" for="username">Username (Email)</label>
              <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="email" id="username" name="username" value="{{ old('username', $user->email) }}" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="current_password">Current Password</label>
                <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="password" id="current_password" name="current_password" required />
              </div>
              <div class="text-sm text-slate-400 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Required to change password
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="new_password">New Password</label>
                <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="password" id="new_password" name="new_password" placeholder="Leave blank to keep current" />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-300 mb-2" for="new_password_confirmation">Confirm New Password</label>
                <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Re-enter new password" />
              </div>
            </div>

            <div class="flex justify-end">
              <button type="submit" class="px-6 h-11 inline-flex items-center btn-primary text-white rounded-lg font-semibold shadow-lg">
                <i class="fas fa-key mr-2"></i>
                Update Account Settings
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Communication Preferences Section -->
      <div class="settings-card p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-orange-400 flex items-center gap-3">
            <i class="fas fa-bell"></i>
            Communication Preferences
          </h2>
          <button type="button" onclick="toggleSection('communication-prefs')" class="text-slate-400 hover:text-orange-400 transition-colors">
            <i class="fas fa-chevron-down"></i>
          </button>
        </div>
        <div id="communication-prefs" class="space-y-6">
          <form method="POST" action="{{ route('student.settings.communication') }}" class="space-y-6">
            @csrf
            <div class="space-y-4">
              <div class="flex items-center justify-between p-4 bg-slate-800 rounded-lg">
                <div class="flex items-center gap-3">
                  <i class="fas fa-sms text-blue-400 text-xl"></i>
                  <div>
                    <h3 class="font-semibold text-slate-100">SMS Reminders</h3>
                    <p class="text-sm text-slate-400">Receive payment reminders via SMS</p>
                  </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" name="sms_reminders" value="1" {{ (old('sms_reminders', $preferences->sms_reminders ?? false)) ? 'checked' : '' }} class="sr-only peer">
                  <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                </label>
              </div>

              <div class="flex items-center justify-between p-4 bg-slate-800 rounded-lg">
                <div class="flex items-center gap-3">
                  <i class="fas fa-envelope text-green-400 text-xl"></i>
                  <div>
                    <h3 class="font-semibold text-slate-100">Email Notifications</h3>
                    <p class="text-sm text-slate-400">Receive notifications via email</p>
                  </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" name="email_notifications" value="1" {{ (old('email_notifications', $preferences->email_notifications ?? false)) ? 'checked' : '' }} class="sr-only peer">
                  <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                </label>
              </div>

              <div class="flex items-center justify-between p-4 bg-slate-800 rounded-lg">
                <div class="flex items-center gap-3">
                  <i class="fas fa-credit-card text-purple-400 text-xl"></i>
                  <div>
                    <h3 class="font-semibold text-slate-100">Payment Reminders</h3>
                    <p class="text-sm text-slate-400">Get notified about upcoming payments</p>
                  </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" name="payment_reminders" value="1" {{ (old('payment_reminders', $preferences->payment_reminders ?? false)) ? 'checked' : '' }} class="sr-only peer">
                  <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                </label>
              </div>

              <div class="flex items-center justify-between p-4 bg-slate-800 rounded-lg">
                <div class="flex items-center gap-3">
                  <i class="fas fa-cog text-orange-400 text-xl"></i>
                  <div>
                    <h3 class="font-semibold text-slate-100">System Updates</h3>
                    <p class="text-sm text-slate-400">Receive notifications about system updates</p>
                  </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" name="system_updates" value="1" {{ (old('system_updates', $preferences->system_updates ?? false)) ? 'checked' : '' }} class="sr-only peer">
                  <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                </label>
              </div>
            </div>

            <div class="flex justify-end">
              <button type="submit" class="px-6 h-11 inline-flex items-center btn-primary text-white rounded-lg font-semibold shadow-lg">
                <i class="fas fa-save mr-2"></i>
                Update Preferences
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Payment Methods Section -->
      <div class="settings-card p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-orange-400 flex items-center gap-3">
            <i class="fas fa-credit-card"></i>
            Payment Methods
          </h2>
          <button type="button" onclick="toggleSection('payment-methods')" class="text-slate-400 hover:text-orange-400 transition-colors">
            <i class="fas fa-chevron-down"></i>
          </button>
        </div>
        <div id="payment-methods" class="space-y-6">
          <!-- Existing Payment Methods -->
          @if($paymentMethods->count() > 0)
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-slate-100">Linked Payment Methods</h3>
              @foreach($paymentMethods as $method)
                <div class="flex items-center justify-between p-4 bg-slate-800 rounded-lg">
                  <div class="flex items-center gap-3">
                    <i class="fas fa-credit-card text-green-400 text-xl"></i>
                    <div>
                      <p class="font-semibold text-slate-100">{{ ucfirst($method->payment_type) }}</p>
                      <p class="text-sm text-slate-400">{{ $method->account_holder_name }}</p>
                    </div>
                  </div>
                  <form method="POST" action="{{ route('student.settings.payment.remove', $method->id) }}" onsubmit="return confirm('Are you sure you want to remove this payment method?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition-colors">
                      <i class="fas fa-trash mr-1"></i>
                      Remove
                    </button>
                  </form>
                </div>
              @endforeach
            </div>
          @endif

          <!-- Add New Payment Method -->
          <div class="border-t border-slate-700 pt-6">
            <h3 class="text-lg font-semibold text-slate-100 mb-4">Add New Payment Method</h3>
            <form method="POST" action="{{ route('student.settings.payment.add') }}" class="space-y-4">
              @csrf
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-2" for="payment_type">Payment Type</label>
                  <select class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" id="payment_type" name="payment_type" required onchange="togglePaymentFields()">
                    <option value="">Select payment type</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="debit_card">Debit Card</option>
                    <option value="bank_account">Bank Account</option>
                    <option value="paypal">PayPal</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-2" for="account_holder_name">Account Holder Name</label>
                  <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="account_holder_name" name="account_holder_name" required />
                </div>
              </div>

              <div id="card-fields" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-2" for="card_number">Card Number</label>
                  <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-2" for="expiry_month">Expiry Month</label>
                  <select class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" id="expiry_month" name="expiry_month">
                    <option value="">Month</option>
                    @for($i = 1; $i <= 12; $i++)
                      <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-2" for="expiry_year">Expiry Year</label>
                  <select class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" id="expiry_year" name="expiry_year">
                    <option value="">Year</option>
                    @for($i = date('Y'); $i <= date('Y') + 20; $i++)
                      <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-2" for="cvv">CVV</label>
                  <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" />
                </div>
              </div>

              <div id="bank-fields" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-2" for="bank_name">Bank Name</label>
                  <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="bank_name" name="bank_name" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-2" for="account_number">Account Number</label>
                  <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="account_number" name="account_number" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-300 mb-2" for="routing_number">Routing Number</label>
                  <input class="w-full h-11 rounded-lg bg-slate-700 border border-slate-600 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="routing_number" name="routing_number" />
                </div>
              </div>

              <div class="flex justify-end">
                <button type="submit" class="px-6 h-11 inline-flex items-center btn-primary text-white rounded-lg font-semibold shadow-lg">
                  <i class="fas fa-plus mr-2"></i>
                  Add Payment Method
                </button>
              </div>
            </form>
          </div>
        </div>
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

    // Section toggles
    function toggleSection(sectionId) {
      const section = document.getElementById(sectionId);
      const button = event.target.closest('button');
      const icon = button.querySelector('i');

      if (section.classList.contains('hidden')) {
        section.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
      } else {
        section.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
      }
    }

    // Payment method field toggles
    function togglePaymentFields() {
      const paymentType = document.getElementById('payment_type').value;
      const cardFields = document.getElementById('card-fields');
      const bankFields = document.getElementById('bank-fields');

      cardFields.classList.add('hidden');
      bankFields.classList.add('hidden');

      if (paymentType === 'credit_card' || paymentType === 'debit_card') {
        cardFields.classList.remove('hidden');
      } else if (paymentType === 'bank_account') {
        bankFields.classList.remove('hidden');
      }
    }

    // Format card number input
    document.getElementById('card_number')?.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\s/g, '');
      if (value.length > 0) {
        value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
      }
      e.target.value = value;
    });
  </script>
</body>
</html>
