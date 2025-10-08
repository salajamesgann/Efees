<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>Efees - Login</title>

  <!-- Fonts & Tailwind -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc; /* soft white */
      color: #1e293b; /* neutral text */
    }
    .btn-primary {
      background-color: #2563eb; /* blue */
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background-color: purple;
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }
    .gradient-text {
      background: linear-gradient(135deg, #2563eb 0%,rgb(37, 22, 249) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
  </style>
</head>

<body class="min-h-screen flex flex-col">

  <!-- Navigation -->
  <nav class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16 items-center">
      <div class="flex items-center">
        <i class="fas fa-graduation-cap text-2xl text-blue-600 mr-2"></i>
        <span class="text-lg font-semibold gradient-text">E-Fees Portal</span>
      </div>
      <div>
        @if (Route::has('signup'))
          <a href="{{ route('signup') }}" 
             class="bg-blue-500 hover:bg-purple-600 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Sign Up
          </a>
        @endif
      </div>
    </div>
  </nav>

  <!-- Login Section -->
  <div class="flex-grow flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg border border-slate-200 p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-semibold text-slate-800 mb-1">Welcome back 👋</h1>
        <p class="text-slate-500 text-sm">Sign in to your account</p>
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

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com"
                 class="w-full h-11 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 bg-white">
        </div>

        <!-- Password -->
        <div>
          <div class="flex justify-between items-center mb-1">
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                Forgot password?
              </a>
            @endif
          </div>
          <input id="password" type="password" name="password" required placeholder="•••••••"
                 class="w-full h-11 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 bg-white">
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
          <input id="remember" name="remember" type="checkbox"
                 class="h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
          <label for="remember" class="ml-2 text-sm text-slate-600">Remember me</label>
        </div>

        <!-- Login Button -->
        <button type="submit" class="w-full h-11 btn-primary text-white rounded-lg font-medium">
          Sign In
        </button>
      </form>

      <!-- Divider -->
      <div class="mt-6 relative">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-slate-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-2 bg-white text-slate-500">Or</span>
        </div>
      </div>

      <!-- Sign up link -->
      <div class="mt-6 text-center">
        <a href="{{ route('signup') }}" class="text-blue-600 hover:text-purple-500 text-sm font-medium">
          Create an account
        </a>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="py-6 bg-white border-t border-slate-200 text-center text-slate-500 text-sm">
    &copy; {{ date('Y') }} Efees. All rights reserved.
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
</body>
</html>
