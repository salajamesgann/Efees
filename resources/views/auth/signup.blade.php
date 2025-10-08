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
    body { 
      font-family: 'Inter', 'Noto Sans', sans-serif; 
      background-color: #f8fafc; 
      color: #1e293b; 
    }
    .gradient-bg {
      background: linear-gradient(135deg,rgb(7, 40, 250) 0%,rgb(131, 4, 250) 100%);
    }
    .gradient-text {
      background: linear-gradient(135deg,rgb(7, 40, 250) 0%,rgb(131, 4, 250) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .btn-primary {
      background: linear-gradient(135deg,rgb(7, 40, 250) 0%,rgb(131, 4, 250) 100%);
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(8, 99, 227, 0.3);
    }
    .card-hover {
      transition: all 0.3s ease;
    }
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1);
    }
    input, select {
      background-color: #ffffff !important;
      border: 1px solid #d1d5db !important;
      color: #1e293b !important;
    }
    input::placeholder {
      color: #94a3b8 !important;
    }
  </style>
</head>
<body class="min-h-screen bg-gray-50 text-slate-800">
  <div class="flex-grow flex items-center justify-center p-4">
    <div class="w-full max-w-2xl p-6 md:p-8 bg-white rounded-2xl shadow-lg border border-gray-200 card-hover">
      <div class="text-center mb-8">
        <h1 class="text-2xl md:text-3xl font-semibold text-slate-800 mb-2 gradient-text">Create your account</h1>
        <p class="text-base text-slate-500">Join Efees to manage your student fees</p>
      </div>

      <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        
        @if ($errors->any())
          <div class="bg-red-100 border border-red-300 text-red-600 px-4 py-3 rounded-md text-sm">
            @foreach ($errors->all() as $error)
              <p>{{ $error }}</p>
            @endforeach
          </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1">
            <label for="first_name" class="block text-sm font-medium text-slate-700">First Name</label>
            <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus placeholder="John"
              class="w-full h-11 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-orange-500" />
          </div>
          <div class="space-y-1">
            <label for="last_name" class="block text-sm font-medium text-slate-700">Last Name</label>
            <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Doe"
              class="w-full h-11 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-orange-500" />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1">
            <label for="middle_initial" class="block text-sm font-medium text-slate-700">Middle Initial</label>
            <input id="middle_initial" type="text" name="middle_initial" value="{{ old('middle_initial') }}" maxlength="1" placeholder="M"
              class="w-full h-11 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-orange-500" />
          </div>
          <div class="space-y-1">
            <label for="sex" class="block text-sm font-medium text-slate-700">Sex</label>
            <select id="sex" name="sex" required
              class="w-full h-11 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-orange-500 appearance-none">
              <option value="">Select sex</option>
              <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
              <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
          </div>
        </div>

        <div class="space-y-1">
          <label for="contact_number" class="block text-sm font-medium text-slate-700">Contact Number</label>
          <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number') }}" required placeholder="e.g. 09171234567"
            class="w-full h-11 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1">
            <label for="level" class="block text-sm font-medium text-slate-700">Grade</label>
            <input id="level" type="text" name="level" value="{{ old('level') }}" required placeholder="e.g., Grade 7, 1st Year, etc."
              class="w-full h-11 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
          </div>
          <div class="space-y-1">
            <label for="section" class="block text-sm font-medium text-slate-700">Section</label>
            <input id="section" type="text" name="section" value="{{ old('section') }}" required placeholder="Enter your section"
              class="w-full h-11 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
          </div>
        </div>

        <div class="space-y-1">
          <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com"
            class="w-full h-11 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1">
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            <input id="password" type="password" name="password" placeholder="••••••••"
              class="w-full h-11 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
          </div>
          <div class="space-y-1">
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="••••••••"
              class="w-full h-11 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
          </div>
        </div>

        <div class="pt-2">
          <button type="submit" class="w-full h-11 btn-primary text-white rounded-lg font-semibold">
            Create Account
          </button>
        </div>
      </form>

      <div class="mt-6">
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-slate-500">Already have an account?</span>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-3">
          <a href="{{ route('login') }}" class="w-full inline-flex justify-center items-center h-11 rounded-lg border border-gray-300 text-slate-700 hover:bg-gray-100 text-sm font-medium transition">
            Sign in to your account
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="py-6 border-t border-gray-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <p class="text-center text-sm text-slate-500">
        &copy; {{ date('Y') }} Efees. All rights reserved.
      </p>
    </div>
  </footer>
</body>
</html>
