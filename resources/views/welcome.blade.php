<!DOCTYPE html>
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
            </div>
        </div>
    </nav>

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
