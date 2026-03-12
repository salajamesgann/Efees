<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Change Password - Efees</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="flex flex-col md:flex-row h-screen overflow-hidden bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    @include('layouts.admin_sidebar')
    <main class="flex-1 p-8 overflow-y-auto custom-scrollbar">
        <div class="max-w-xl mx-auto">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-slate-800">Change Password</div>
                        <div class="text-xs text-slate-500">Use a strong, unique password</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('auth.password.update') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">New Password</label>
                        <input type="password" name="new_password" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2" required />
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ auth()->user()->hasRole('super_admin') ? route('super_admin.settings.index') : route('admin.settings.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700">Cancel</a>
                        <button class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold" type="submit">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
</body>
</html>
