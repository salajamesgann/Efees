<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-Fees Portal - Forgot Password</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        fadeInUp: 'fadeInUp 0.8s ease-out forwards',
                        fadeIn: 'fadeIn 1s ease-out forwards',
                        blob: 'blob 7s infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .text-gradient {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glass-nav {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen flex flex-col">
    <nav class="glass-nav fixed w-full z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white shadow-lg shadow-brand-500/40">
                        <i class="fas fa-graduation-cap text-sm"></i>
                    </div>
                    <span class="text-lg font-bold tracking-tight">E-Fees<span class="text-brand-400">Portal</span></span>
                </a>
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-4 py-2 rounded-full bg-white text-slate-900 text-sm font-semibold hover:bg-slate-100 transition-all hover:shadow-lg hover:-translate-y-0.5">
                    <i class="fas fa-arrow-left mr-2 text-xs"></i>
                    Back to Login
                </a>
            </div>
        </div>
    </nav>

    <div class="relative flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 pt-24 pb-12">
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute top-[-6rem] left-[-4rem] w-80 h-80 bg-brand-500 rounded-full mix-blend-screen filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute bottom-[-6rem] right-[-4rem] w-96 h-96 bg-purple-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-blob" style="animation-delay: 2s"></div>
        </div>

        <div class="w-full max-w-lg">
            <div class="bg-slate-900/80 border border-slate-800 shadow-2xl rounded-3xl px-6 sm:px-8 py-8 sm:py-10 backdrop-blur-xl animate-fadeInUp">
                <div class="flex flex-col items-center text-center mb-8">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand-500/10 border border-brand-500/40 mb-4">
                        <i class="fas fa-key text-xl text-brand-400"></i>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold mb-2">
                        Forgot your <span class="text-gradient">password</span>?
                    </h1>
                    <p class="text-sm sm:text-base text-slate-400 max-w-md">
                        Enter the email associated with your account and we will send you a secure link to reset your password.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    @if (session('success'))
                        <div class="bg-emerald-500/10 border border-emerald-500/40 text-emerald-200 px-4 py-3 rounded-2xl text-sm flex items-start gap-3">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-500/10 border border-red-500/40 text-red-200 px-4 py-3 rounded-2xl text-sm flex items-start gap-3">
                            <i class="fas fa-exclamation-circle mt-0.5"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold text-slate-200 text-left">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-slate-500 text-sm"></i>
                            </div>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                required
                                autofocus
                                value="{{ old('email') }}"
                                class="block w-full pl-10 pr-3 py-2.5 rounded-xl text-sm bg-slate-950/70 border border-slate-700 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all"
                                placeholder="name@example.com"
                            >
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-brand-600 text-white font-semibold text-sm tracking-wide shadow-lg shadow-brand-600/30 hover:bg-brand-500 hover:-translate-y-0.5 active:translate-y-0 transition-all"
                    >
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Send Reset Link</span>
                    </button>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 border-t border-slate-800 mt-4">
                        <div class="text-xs sm:text-sm text-slate-500">
                            Remembered your password?
                        </div>
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center text-xs sm:text-sm font-semibold text-brand-300 hover:text-brand-100 transition-colors"
                        >
                            <i class="fas fa-arrow-left mr-2 text-[10px]"></i>
                            Back to login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
