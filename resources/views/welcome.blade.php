<!DOCTYPE html>
<<<<<<< HEAD
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Fees Portal - Modern School Fee Management</title>
    
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
                        'float': 'float 6s ease-in-out infinite',
                        'slide-up': 'slideUp 0.5s ease-out forwards',
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
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
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
        .text-gradient {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-gradient-brand {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        }
        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .perspective-1000 {
            perspective: 1000px;
        }
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-slate-950 text-white selection:bg-brand-500 selection:text-white overflow-x-hidden">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex items-center gap-3 group cursor-pointer">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white shadow-lg shadow-brand-500/30 transition-transform group-hover:scale-105 duration-300">
                        <i class="fas fa-graduation-cap text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight">E-Fees<span class="text-brand-400">Portal</span></span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-semibold text-slate-300 hover:text-white transition-colors">Features</a>
                    <a href="#how-it-works" class="text-sm font-semibold text-slate-300 hover:text-white transition-colors">How it Works</a>
                    
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-full bg-white text-slate-900 text-sm font-semibold hover:bg-slate-100 transition-all hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                            Log In
                        </a>
                    @endif
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-slate-400 hover:bg-slate-800 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
=======
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Fees Portal - Smart School Fee Management System</title>
    
    <!-- External Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #121212;
            color: #e5e5e5;
        }
        
        /* Orange gradients */
        .gradient-bg {
            background: linear-gradient(135deg, #ff7a18 0%, #af002d 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #ff7a18 0%, #ff3c00 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-hover {
            transition: all 0.3s ease;
            background-color: #1e1e1e;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(255, 122, 24, 0.4);
        }
        
        .feature-icon {
            background: linear-gradient(135deg, #ff7a18 0%, #ff3c00 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ff7a18 0%, #ff3c00 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 122, 24, 0.5);
        }
        
        .testimonial-card {
            background: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(10px);
        }
        
        footer {
            background-color: #1a1a1a;
        }
    </style>
</head>
<body class="bg-black">
    <!-- Navigation -->
    <nav class="bg-gray-900 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-graduation-cap text-3xl gradient-text mr-3"></i>
                        <span class="text-xl font-bold text-white">E-Fees Portal</span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" 
                           class="text-gray-300 hover:text-orange-400 px-3 py-2 font-medium transition-colors">
                            Log In
                        </a>
                        @if (Route::has('signup'))
                        <a href="{{ route('signup') }}" 
                           class="btn-primary text-white px-6 py-2 rounded-lg font-medium">
                            Sign Up
                        </a>
                </div>
                @endif
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            </div>
        </div>
    </nav>

<<<<<<< HEAD
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-slate-900/95 backdrop-blur-xl border-b border-slate-800 absolute w-full left-0 top-20 shadow-2xl transition-all duration-300 transform origin-top">
            <div class="px-4 py-6 space-y-4">
                <a href="#features" class="block text-slate-300 hover:text-white font-medium hover:pl-2 transition-all">Features</a>
                <a href="#how-it-works" class="block text-slate-300 hover:text-white font-medium hover:pl-2 transition-all">How it Works</a>
                <hr class="border-slate-800 my-2">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="block w-full text-center py-3 rounded-xl bg-brand-600 text-white font-bold shadow-lg shadow-brand-500/20 hover:bg-brand-500 transition-all">
                        Log In
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Blobs -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-brand-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-pink-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-900/50 border border-slate-700 text-brand-300 text-sm font-semibold mb-8 animate-fade-in-up shadow-sm backdrop-blur-sm">
                    <span class="w-2 h-2 rounded-full bg-brand-400 animate-pulse"></span>
                    New Academic Year Ready
                </div>
                
                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white tracking-tight mb-8 leading-tight animate-fade-in-up" style="animation-delay: 0.1s">
                    School Fee Management <br>
                    <span class="text-gradient">System</span>
                </h1>
                
                <p class="text-xl text-slate-400 mb-10 max-w-2xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay: 0.2s">
                    Streamline fee collection, automate SMS reminders, and gain real-time financial insights. The trusted platform for modern educational institutions.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.3s">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-full bg-brand-600 text-white font-bold text-lg hover:bg-brand-500 transition-all hover:shadow-xl hover:shadow-brand-500/20 hover:-translate-y-1">
                        Get Started Now
                        <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-8 py-4 rounded-full bg-slate-900 text-slate-300 font-bold text-lg border border-slate-800 hover:border-slate-700 hover:text-white transition-all hover:shadow-lg hover:-translate-y-1">
                        View Features
                    </a>
                </div>
            </div>

            <!-- Hero Dashboard Mockup -->
            <div class="mt-20 relative animate-fade-in-up perspective-1000" style="animation-delay: 0.5s">
                <div class="relative mx-auto max-w-6xl rounded-2xl shadow-2xl bg-slate-900 border border-slate-800 overflow-hidden transform rotate-x-2 transition-transform duration-500 hover:rotate-x-0">
                    <div class="absolute top-0 w-full h-10 bg-slate-950 border-b border-slate-800 flex items-center px-4 gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                        <!-- Fake URL bar -->
                        <div class="hidden md:flex ml-4 bg-slate-900 border border-slate-700 rounded-md px-3 py-1 text-xs text-slate-500 w-64 items-center">
                            <i class="fas fa-lock mr-2 text-[10px]"></i> efees-portal.com/dashboard
                        </div>
                    </div>
                    <div class="pt-10 bg-slate-950/50 aspect-[16/9] md:aspect-[21/9] flex items-stretch text-slate-700">
                        <!-- Sidebar -->
                        <div class="hidden md:flex w-64 flex-col bg-slate-900 border-r border-slate-800 p-4 space-y-6">
                            <div class="flex items-center gap-3 px-2">
                                <div class="w-8 h-8 rounded-lg bg-brand-600"></div>
                                <div class="h-4 w-24 bg-slate-800 rounded"></div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-10 w-full bg-brand-900/30 text-brand-400 rounded-lg flex items-center px-3 gap-3">
                                    <div class="w-4 h-4 bg-brand-500/50 rounded"></div>
                                    <div class="w-20 h-3 bg-brand-500/50 rounded"></div>
                                </div>
                                <div class="h-10 w-full bg-transparent rounded-lg flex items-center px-3 gap-3">
                                    <div class="w-4 h-4 bg-slate-800 rounded"></div>
                                    <div class="w-24 h-3 bg-slate-800 rounded"></div>
                                </div>
                                <div class="h-10 w-full bg-transparent rounded-lg flex items-center px-3 gap-3">
                                    <div class="w-4 h-4 bg-slate-800 rounded"></div>
                                    <div class="w-20 h-3 bg-slate-800 rounded"></div>
                                </div>
                                <div class="h-10 w-full bg-transparent rounded-lg flex items-center px-3 gap-3">
                                    <div class="w-4 h-4 bg-slate-800 rounded"></div>
                                    <div class="w-16 h-3 bg-slate-800 rounded"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Main Content -->
                        <div class="flex-1 p-6 space-y-6 overflow-hidden">
                            <!-- Header -->
                            <div class="flex justify-between items-center mb-8">
                                <div>
                                    <div class="h-6 w-32 bg-slate-800 rounded mb-2"></div>
                                    <div class="h-4 w-48 bg-slate-800/50 rounded"></div>
                                </div>
                                <div class="h-10 w-10 rounded-full bg-slate-800"></div>
                            </div>
                            
                            <!-- Stats Cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                                <div class="h-24 sm:h-28 bg-slate-900 rounded-xl border border-slate-800 shadow-sm p-4 flex flex-row sm:flex-col justify-between items-center sm:items-stretch gap-4 sm:gap-0">
                                    <div class="flex justify-between items-start w-full sm:w-auto">
                                        <div class="w-8 h-8 rounded-lg bg-green-900/30"></div>
                                        <div class="h-4 w-12 bg-green-900/30 text-green-400 rounded text-xs flex items-center justify-center font-bold">+12%</div>
                                    </div>
                                    <div class="w-full sm:w-auto">
                                        <div class="h-6 w-24 bg-slate-800 rounded sm:mt-2"></div>
                                        <div class="h-3 w-16 bg-slate-800/50 rounded mt-1 sm:mt-0 hidden sm:block"></div>
                                    </div>
                                </div>
                                <div class="h-24 sm:h-28 bg-slate-900 rounded-xl border border-slate-800 shadow-sm p-4 flex flex-row sm:flex-col justify-between items-center sm:items-stretch gap-4 sm:gap-0">
                                    <div class="flex justify-between items-start w-full sm:w-auto">
                                        <div class="w-8 h-8 rounded-lg bg-blue-900/30"></div>
                                        <div class="h-4 w-12 bg-blue-900/30 text-blue-400 rounded text-xs flex items-center justify-center font-bold">+5%</div>
                                    </div>
                                    <div class="w-full sm:w-auto">
                                        <div class="h-6 w-24 bg-slate-800 rounded sm:mt-2"></div>
                                        <div class="h-3 w-16 bg-slate-800/50 rounded mt-1 sm:mt-0 hidden sm:block"></div>
                                    </div>
                                </div>
                                <div class="h-24 sm:h-28 bg-slate-900 rounded-xl border border-slate-800 shadow-sm p-4 flex flex-row sm:flex-col justify-between items-center sm:items-stretch gap-4 sm:gap-0">
                                    <div class="flex justify-between items-start w-full sm:w-auto">
                                        <div class="w-8 h-8 rounded-lg bg-purple-900/30"></div>
                                        <div class="h-4 w-12 bg-purple-900/30 text-purple-400 rounded text-xs flex items-center justify-center font-bold">+8%</div>
                                    </div>
                                    <div class="w-full sm:w-auto">
                                        <div class="h-6 w-24 bg-slate-800 rounded sm:mt-2"></div>
                                        <div class="h-3 w-16 bg-slate-800/50 rounded mt-1 sm:mt-0 hidden sm:block"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Chart Area -->
                            <div class="h-64 bg-slate-900 rounded-xl border border-slate-800 shadow-sm p-6 flex items-end gap-4">
                                <div class="w-full bg-slate-950 h-full rounded-lg relative overflow-hidden flex items-end justify-around px-4 pb-4">
                                    <!-- Bars -->
                                    <div class="w-8 bg-brand-900/50 rounded-t h-[40%]"></div>
                                    <div class="w-8 bg-brand-800/50 rounded-t h-[60%]"></div>
                                    <div class="w-8 bg-brand-700/50 rounded-t h-[45%]"></div>
                                    <div class="w-8 bg-brand-600 rounded-t h-[75%]"></div>
                                    <div class="w-8 bg-brand-500 rounded-t h-[90%] shadow-lg shadow-brand-500/30"></div>
                                    <div class="w-8 bg-brand-700/50 rounded-t h-[65%]"></div>
                                    <div class="w-8 bg-brand-800/50 rounded-t h-[50%]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Floating Elements -->
                <div class="hidden lg:block absolute -right-6 top-32 bg-slate-900 p-4 rounded-xl shadow-xl border border-slate-800 animate-float">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-900/30 flex items-center justify-center text-green-400">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 font-semibold">Payment Received</div>
                            <div class="text-sm font-bold text-white">+ ₱5,400.00</div>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:block absolute -left-6 bottom-32 bg-slate-900 p-4 rounded-xl shadow-xl border border-slate-800 animate-float" style="animation-delay: 2s;">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-900/30 flex items-center justify-center text-blue-400">
                            <i class="fas fa-sms"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 font-semibold">SMS Sent</div>
                            <div class="text-sm font-bold text-white">98% Delivery Rate</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-24 bg-slate-950 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-brand-400 font-bold tracking-wide uppercase text-sm mb-3">Simple Process</h2>
                <h3 class="text-3xl md:text-4xl font-extrabold text-white mb-4">How E-Fees Works</h3>
                <p class="text-slate-400 text-lg">A seamless experience for both administrators and parents.</p>
            </div>

            <div class="relative">
                <!-- Connecting Line (Desktop) -->
                <div class="hidden md:block absolute top-1/2 left-0 w-full h-0.5 bg-slate-800 -translate-y-1/2 z-0"></div>

                <div class="grid md:grid-cols-3 gap-12 relative z-10">
                    <!-- Step 1 -->
                    <div class="bg-slate-900 p-8 rounded-2xl shadow-lg border border-slate-800 text-center transform transition-all hover:-translate-y-2 hover:shadow-xl duration-300">
                        <div class="w-16 h-16 mx-auto bg-brand-600 rounded-2xl text-white flex items-center justify-center text-2xl font-bold mb-6 shadow-lg shadow-brand-500/30">1</div>
                        <h4 class="text-xl font-bold text-white mb-3">Setup & Invoice</h4>
                        <p class="text-slate-400">Admin sets up fee structures and generates digital invoices for all students in seconds.</p>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-slate-900 p-8 rounded-2xl shadow-lg border border-slate-800 text-center transform transition-all hover:-translate-y-2 hover:shadow-xl duration-300">
                        <div class="w-16 h-16 mx-auto bg-brand-600 rounded-2xl text-white flex items-center justify-center text-2xl font-bold mb-6 shadow-lg shadow-brand-500/30">2</div>
                        <h4 class="text-xl font-bold text-white mb-3">Notify Parents</h4>
                        <p class="text-slate-400">System automatically sends SMS and Email reminders with secure payment links.</p>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-slate-900 p-8 rounded-2xl shadow-lg border border-slate-800 text-center transform transition-all hover:-translate-y-2 hover:shadow-xl duration-300">
                        <div class="w-16 h-16 mx-auto bg-brand-600 rounded-2xl text-white flex items-center justify-center text-2xl font-bold mb-6 shadow-lg shadow-brand-500/30">3</div>
                        <h4 class="text-xl font-bold text-white mb-3">Collect & Track</h4>
                        <p class="text-slate-400">Parents pay online, and the system automatically updates records and generates receipts.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-slate-950 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-brand-400 font-bold tracking-wide uppercase text-sm mb-3">Powerful Features</h2>
                <h3 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Everything you need to manage fees</h3>
                <p class="text-slate-400 text-lg">Comprehensive tools designed specifically for educational institutions to ensure smooth financial operations.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group p-8 rounded-2xl bg-slate-900 border border-slate-800 hover:bg-slate-800 hover:border-brand-900/50 hover:shadow-2xl hover:shadow-brand-500/10 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-blue-900/20 text-blue-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-sms"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-3">SMS Reminders</h4>
                    <p class="text-slate-400 leading-relaxed">Automated payment reminders sent directly to parents' phones, significantly reducing late payments.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-8 rounded-2xl bg-slate-900 border border-slate-800 hover:bg-slate-800 hover:border-brand-900/50 hover:shadow-2xl hover:shadow-brand-500/10 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-indigo-900/20 text-indigo-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-3">Real-time Analytics</h4>
                    <p class="text-slate-400 leading-relaxed">Visualize collection trends, outstanding balances, and revenue growth with intuitive dashboards.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-8 rounded-2xl bg-slate-900 border border-slate-800 hover:bg-slate-800 hover:border-brand-900/50 hover:shadow-2xl hover:shadow-brand-500/10 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-emerald-900/20 text-emerald-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-3">Digital Invoicing</h4>
                    <p class="text-slate-400 leading-relaxed">Generate and track digital Statement of Accounts (SOA) accessible by parents anytime, anywhere.</p>
                </div>

                <!-- Feature 4 -->
                <div class="group p-8 rounded-2xl bg-slate-900 border border-slate-800 hover:bg-slate-800 hover:border-brand-900/50 hover:shadow-2xl hover:shadow-brand-500/10 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-amber-900/20 text-amber-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-3">Parent Portal</h4>
                    <p class="text-slate-400 leading-relaxed">Dedicated portal for parents to view history, download receipts, and manage multiple students.</p>
                </div>

                <!-- Feature 5 -->
                <div class="group p-8 rounded-2xl bg-slate-900 border border-slate-800 hover:bg-slate-800 hover:border-brand-900/50 hover:shadow-2xl hover:shadow-brand-500/10 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-rose-900/20 text-rose-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-3">Secure Records</h4>
                    <p class="text-slate-400 leading-relaxed">Enterprise-grade security ensuring student data and financial records are protected and backed up.</p>
                </div>

                <!-- Feature 6 -->
                <div class="group p-8 rounded-2xl bg-slate-900 border border-slate-800 hover:bg-slate-800 hover:border-brand-900/50 hover:shadow-2xl hover:shadow-brand-500/10 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-cyan-900/20 text-cyan-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-history"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-3">Audit Trails</h4>
                    <p class="text-slate-400 leading-relaxed">Complete transparency with detailed logs of every transaction and system modification.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-slate-900 relative overflow-hidden">
        <!-- Background Glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-brand-600/20 rounded-full blur-[100px]"></div>
        
        <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6 leading-tight">Ready to modernize your school's financial operations?</h2>
            <p class="text-xl text-slate-300 mb-10">Join forward-thinking institutions that have switched to E-Fees Portal.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-full bg-brand-600 text-white font-bold text-lg hover:bg-brand-500 transition-all hover:shadow-xl hover:shadow-brand-500/20 hover:-translate-y-1">
                    Get Started Now
                </a>
                <a href="#features" class="w-full sm:w-auto px-8 py-4 rounded-full bg-transparent text-white font-bold text-lg border border-slate-700 hover:bg-slate-800 transition-all hover:-translate-y-1">
                    Contact Sales
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-300 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center text-white">
                            <i class="fas fa-graduation-cap text-sm"></i>
                        </div>
                        <span class="text-lg font-bold text-white">E-Fees Portal</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Simplifying school fee management for forward-thinking educational institutions.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-4">Product</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Security</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-4">Company</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-brand-400 transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-4">Contact</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-envelope text-brand-500"></i>
                            support@efeesportal.com
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-phone text-brand-500"></i>
                            +63 993 269 7592
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-500">© {{ date('Y') }} E-Fees Portal. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Select elements to animate that don't already have animation classes
            // Exclude the hero section (first section) as it has its own animations
            const elements = document.querySelectorAll('section:not(:first-of-type) h2, section:not(:first-of-type) h3, section:not(:first-of-type) p, section:not(:first-of-type) .grid > div, section:not(:first-of-type) .flex > div, section:not(:first-of-type) img');
            
            elements.forEach(el => {
                // Check if element is not already animated and is not inside an animated container
                if (!el.classList.contains('animate-fade-in-up') && 
                    !el.classList.contains('animate-blob') &&
                    !el.closest('.animate-fade-in-up')) {
                    
                    el.classList.add('reveal');
                    observer.observe(el);
                }
            });
        });
    </script>
    <!-- Mobile Menu Script -->
    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
=======
    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl font-bold leading-tight mb-6">
                        Smart School Fee Management Made 
                        <span class="text-orange-300">Simple</span>
                    </h1>
                    <p class="text-xl mb-8 text-orange-100">
                        Streamline your educational institution's fee collection with automated SMS reminders, 
                        real-time tracking, and comprehensive reporting tools.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('signup') }}" 
                           class="bg-black text-orange-400 px-8 py-4 rounded-lg font-semibold hover:bg-gray-800 transition-all transform hover:scale-105">
                            <i class="fas fa-rocket mr-2"></i>Get Started Free
                        </a>
                        <a href="{{ route('login') }}" 
                           class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-orange-500 hover:text-black transition-all">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </a>
                    </div>
                </div>
                <div class="text-center">
                    <div class="animate-float">
                        <i class="fas fa-university text-9xl text-white opacity-20"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Rest of your sections remain same but with dark bg + orange accents -->
    
    <!-- Footer -->
    <footer class="text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="col-span-2">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-graduation-cap text-2xl mr-3"></i>
                        <span class="text-xl font-bold">E-Fees Portal</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Simplifying fee management for educational institutions with smart automation and real-time tracking.
                    </p>
                    <div class="flex space-x-4">
                        <i class="fab fa-facebook text-2xl text-orange-400 hover:text-white cursor-pointer"></i>
                        <i class="fab fa-twitter text-2xl text-orange-400 hover:text-white cursor-pointer"></i>
                        <i class="fab fa-linkedin text-2xl text-orange-400 hover:text-white cursor-pointer"></i>
                        <i class="fas fa-envelope text-2xl text-orange-400 hover:text-white cursor-pointer"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-orange-400">Quick Links</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('login') }}" class="hover:text-orange-300">Log In</a></li>
                        <li><a href="{{ route('signup') }}" class="hover:text-orange-300">Sign Up</a></li>
                        <li><a href="#" class="hover:text-orange-300">Features</a></li>
                        <li><a href="#" class="hover:text-orange-300">Pricing</a></li>
                        <li><a href="#" class="hover:text-orange-300">Support</a></li>
                        <li><a href="#" class="hover:text-orange-300">Documentation</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-orange-400">Contact Info</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-phone mr-2"></i>+1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope mr-2"></i>info@efeesportal.com</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i>123 Education Street</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 E-Fees Portal. All rights reserved. | Privacy Policy | Terms of Service</p>
            </div>
        </div>
    </footer>
</body>
</html>
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
