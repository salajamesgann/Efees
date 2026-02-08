<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-Fees Portal - Reset Password</title>
    
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; background-color: #ffffff; color: #334155; }
        .gradient-bg { background: linear-gradient(135deg, #475569 0%, #3b82f6 100%); }
        .gradient-text { background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); transition: all 0.3s ease; }
        .btn-primary:hover { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3); }
        .form-input { transition: all 0.3s ease; border: 1px solid #e2e8f0; }
        .form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/" class="flex items-center hover:opacity-80 transition-opacity">
                            <i class="fas fa-graduation-cap text-3xl gradient-text mr-3"></i>
                            <span class="text-xl font-bold text-slate-800">E-Fees Portal</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="gradient-bg flex-grow flex items-center justify-center p-6 py-20">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
                        <i class="fas fa-lock-open text-3xl text-blue-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800">Reset Password</h1>
                    <p class="text-slate-500 mt-2">Create a new password for your account</p>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm flex items-start gap-3">
                            <i class="fas fa-exclamation-circle mt-0.5"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-slate-400"></i>
                            </div>
                            <input id="email" type="email" name="email" required autofocus value="{{ $email ?? old('email') }}"
                                class="form-input block w-full pl-10 pr-3 py-2.5 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="name@example.com">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-400"></i>
                            </div>
                            <input id="password" type="password" name="password" required
                                class="form-input block w-full pl-10 pr-3 py-2.5 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div>
                        <label for="password-confirm" class="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-400"></i>
                            </div>
                            <input id="password-confirm" type="password" name="password_confirmation" required
                                class="form-input block w-full pl-10 pr-3 py-2.5 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="w-full btn-primary text-white font-bold py-3 px-4 rounded-lg shadow-lg flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Reset Password</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
