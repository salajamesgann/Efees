<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>Efees - Sign Up</title>
  <link rel="preconnect" href="https://fonts.gstatic.com/">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <style>
    body { font-family: 'Inter', 'Noto Sans', sans-serif; }
  </style>
</head>
<body class="min-h-screen bg-white text-slate-700">
  <div class="flex-grow flex items-center justify-center p-4">
    <div class="w-full max-w-2xl p-6 md:p-8 bg-white rounded-2xl shadow-lg border border-slate-200">
      <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
          <div class="w-12 h-12 flex items-center justify-center">
            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-purple-600">
              <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor"/>
            </svg>
          </div>
        </div>
        <h1 class="text-2xl md:text-3xl font-semibold text-purple-700 mb-2">Create your account</h1>
        <p class="text-base text-slate-600">Join Efees to manage your student fees</p>
      </div>

      <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        
        @if ($errors->any())
          <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-md text-sm">
            @foreach ($errors->all() as $error)
              <p>{{ $error }}</p>
            @endforeach
          </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1">
            <label for="first_name" class="block text-sm font-medium text-slate-700">First Name</label>
            <input
              id="first_name"
              type="text"
              name="first_name"
              value="{{ old('first_name') }}"
              required
              autofocus
              placeholder="John"
              class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3"
            />
          </div>
          
          <div class="space-y-1">
            <label for="last_name" class="block text-sm font-medium text-slate-700">Last Name</label>
            <input
              id="last_name"
              type="text"
              name="last_name"
              value="{{ old('last_name') }}"
              required
              placeholder="Doe"
              class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1">
            <label for="middle_initial" class="block text-sm font-medium text-slate-700">Middle Initial</label>
            <input
              id="middle_initial"
              type="text"
              name="middle_initial"
              value="{{ old('middle_initial') }}"
              maxlength="1"
              placeholder="M"
              class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3"
            />
          </div>

          <div class="space-y-1">
            <label for="sex" class="block text-sm font-medium text-slate-700">Sex</label>
            <select 
              id="sex"
              name="sex" 
              required
              class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3 appearance-none"
            >
              <option value="">Select sex</option>
              <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
              <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
          </div>
        </div>

        <div class="space-y-1">
          <label for="student_id" class="block text-sm font-medium text-slate-700">Student ID</label>
          <input
            id="student_id"
            type="text"
            name="student_id"
            value="{{ old('student_id') }}"
            required
            placeholder="Enter your student ID"
            class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3"
          />
        </div>

        <div class="space-y-1">
          <label for="contact_number" class="block text-sm font-medium text-slate-700">Contact Number</label>
          <input
            id="contact_number"
            type="text"
            name="contact_number"
            value="{{ old('contact_number') }}"
            required
            placeholder="e.g. 09171234567"
            class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3"
          />
        </div>

        <div class="space-y-1">
          <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
          <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            placeholder="you@example.com"
            class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3"
          />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1">
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            <input
              id="password"
              type="password"
              name="password"
              required
              placeholder="••••••••"
              class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3"
            />
          </div>
          
          <div class="space-y-1">
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm Password</label>
            <input
              id="password_confirmation"
              type="password"
              name="password_confirmation"
              required
              placeholder="••••••••"
              class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1">
            <label for="year" class="block text-sm font-medium text-slate-700">Year</label>
            <select 
              id="year"
              name="year" 
              required
              class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3 appearance-none"
            >
              <option value="">Select your year</option>
              <option value="1st Year" {{ old('year') == '1st Year' ? 'selected' : '' }}>1st Year</option>
              <option value="2nd Year" {{ old('year') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
              <option value="3rd Year" {{ old('year') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
              <option value="4th Year" {{ old('year') == '4th Year' ? 'selected' : '' }}>4th Year</option>
            </select>
          </div>

          <div class="space-y-1">
            <label for="section" class="block text-sm font-medium text-slate-700">Section</label>
            <input
              id="section"
              type="text"
              name="section"
              value="{{ old('section') }}"
              required
              placeholder="Enter your section"
              class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3"
            />
          </div>
        </div>

        <div class="pt-2">
          <button
            type="submit"
            class="w-full h-11 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold shadow-lg"
          >
            Create Account
          </button>
        </div>
      </form>

      <div class="mt-6">
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-slate-500">Already have an account?</span>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-3">
          <a href="{{ route('login') }}" class="w-full inline-flex justify-center items-center h-11 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-medium">
            Sign in to your account
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="py-6 border-t border-slate-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <p class="text-center text-sm text-slate-500">
        &copy; {{ date('Y') }} Efees. All rights reserved.
      </p>
    </div>
  </footer>

  <!-- Scripts -->
  <script>
    // Add any client-side interactivity here
    document.addEventListener('DOMContentLoaded', function() {
      // Add smooth scrolling to all links
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
          });
        });
      });
    });
  </script>
</body>
</html>
