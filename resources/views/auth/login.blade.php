<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>Efees - Login</title>
  <link rel="preconnect" href="https://fonts.gstatic.com/">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <style>
    body { font-family: 'Inter', 'Noto Sans', sans-serif; }
    .gradient-text { background: linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
  </style>
</head>
<body class="min-h-screen bg-white text-slate-700 flex flex-col">
  <!-- Navigation (matches welcome.blade header style) -->
  <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <div class="flex-shrink-0 flex items-center">
            <i class="fas fa-graduation-cap text-3xl gradient-text mr-3"></i>
            <span class="text-xl font-bold text-slate-900">E-Fees Portal</span>
          </div>
        </div>
        <div class="flex items-center space-x-4">
          @auth
            <a href="{{ route('user_dashboard') }}" 
               class="bg-slate-100 hover:bg-slate-200 text-purple-700 px-4 py-2 rounded-lg font-medium transition-colors">
              <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
          @else
            @if (Route::has('signup'))
              <a href="{{ route('signup') }}" 
                 class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold shadow">
                Sign Up
              </a>
            @endif
          @endauth
        </div>
      </div>
    </div>
  </nav>

  <div class="flex-grow flex items-center justify-center p-4">
    <div class="w-full max-w-md p-6 md:p-8 bg-white rounded-2xl shadow-lg border border-slate-200">
      <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
          <div class="w-12 h-12 flex items-center justify-center">
            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-purple-600">
              <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor"/>
            </svg>
          </div>
        </div>
        <h1 class="text-2xl md:text-3xl font-semibold text-purple-700 mb-2">Welcome back</h1>
        <p class="text-slate-600">Sign in to your Efees account</p>
      </div>

      <form method="POST" action="{{ route('authenticate') }}" class="space-y-6">
        @csrf
        
        @if ($errors->any())
          <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-md text-sm">
            @foreach ($errors->all() as $error)
              <p>{{ $error }}</p>
            @endforeach
          </div>
        @endif

        <div class="space-y-1">
          <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
          <div class="relative">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com" class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3" />
          </div>
        </div>

        <div class="space-y-1">
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="text-sm text-purple-600 hover:text-purple-500">
                Forgot password?
              </a>
            @endif
          </div>
          <div class="relative">
            <input id="password" type="password" name="password" required placeholder="••••••••" class="w-full h-11 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3" />
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-purple-600 focus:ring-purple-500 bg-white">
            <label for="remember" class="ml-2 block text-sm text-slate-700">Remember me</label>
          </div>
        </div>

        <div>
          <button type="submit" class="w-full h-11 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold shadow-lg">Sign in</button>
        </div>
      </form>

      <div class="mt-6">
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-slate-500">Or continue with</span>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-3">
          <a href="{{ route('signup') }}" class="w-full inline-flex justify-center items-center h-11 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-medium">
            Create an account
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