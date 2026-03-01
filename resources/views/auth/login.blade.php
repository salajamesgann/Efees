<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-Fees Portal - Login</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        }
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'fade-in': 'fadeIn 1s ease-out forwards',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .glass-nav {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .text-gradient {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .form-input {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            background: rgba(30, 41, 59, 0.8);
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            outline: none;
        }
        .form-input::placeholder {
            color: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-950 text-white selection:bg-brand-500 selection:text-white min-h-screen flex flex-col overflow-x-hidden">

    <!-- Background Blobs -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-brand-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-pink-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 group cursor-pointer hover:opacity-90 transition-opacity">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white shadow-lg shadow-brand-500/30 transition-transform group-hover:scale-105 duration-300">
                        <i class="fas fa-graduation-cap text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight">E-Fees<span class="text-brand-400">Portal</span></span>
                </a>

                <!-- Right Side -->
                <a href="/" class="text-sm font-semibold text-slate-300 hover:text-white transition-colors flex items-center gap-2">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-grow flex items-center justify-center px-4 py-20 md:py-32 relative">
        <div class="w-full max-w-md animate-fade-in-up">
            
            <!-- Login Card -->
            <div class="glass-card rounded-2xl shadow-2xl p-8 sm:p-10">
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-500/20 to-brand-700/20 border border-brand-500/20 mb-6 shadow-lg shadow-brand-500/10">
                        <i class="fas fa-user-circle text-3xl text-brand-400"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-white tracking-tight mb-2">Welcome Back</h1>
                    <p class="text-slate-400">Please sign in to your account</p>
                </div>

                <form method="POST" action="{{ route('authenticate') }}" class="space-y-6">
                    @csrf

                    @if (session('success'))
                        <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl text-sm flex items-start gap-3 backdrop-blur-sm">
                            <i class="fas fa-check-circle mt-0.5 shrink-0"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl text-sm flex items-start gap-3 backdrop-blur-sm">
                            <i class="fas fa-exclamation-circle mt-0.5 shrink-0"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium text-slate-300">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-slate-500"></i>
                            </div>
                            <input id="email" type="email" name="email" required autofocus
                                class="form-input block w-full pl-10 pr-3 py-3 rounded-xl text-sm"
                                placeholder="name@example.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium text-slate-300">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-500"></i>
                            </div>
                            <input id="password" type="password" name="password" required
                                class="form-input block w-full pl-10 pr-12 py-3 rounded-xl text-sm"
                                placeholder="•••••••">
                            <button type="button" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors"
                                    onclick="togglePassword()">
                                <i id="password-toggle-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember" class="inline-flex items-center group cursor-pointer">
                            <input id="remember" type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-slate-600 text-brand-500 focus:ring-brand-500 focus:ring-offset-slate-900 bg-slate-800/50">
                            <span class="ml-2 text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Remember me</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a class="text-sm text-brand-400 hover:text-brand-300 hover:underline transition-colors font-medium" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 text-white font-bold shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:from-brand-500 hover:to-brand-400 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign In</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('password-toggle-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        }
    }
    </script>

</body>
</html>
