<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Settings - Efees Admin</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="flex flex-col md:flex-row h-screen overflow-hidden bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    @include('layouts.admin_sidebar')
  <main class="flex-1 p-8 overflow-y-auto custom-scrollbar">
    <div class="max-w-4xl mx-auto space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-slate-800">{{ $user->name }}</div>
                        <div class="text-xs text-slate-500">{{ $user->email }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <form method="POST" action="{{ route('admin.settings.account.profile') }}" enctype="multipart/form-data" class="md:col-span-2 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">First Name</label>
                            <input name="first_name" type="text" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2" value="{{ old('first_name', $user->roleable->first_name ?? '') }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Middle Initial</label>
                            <input name="middle_initial" type="text" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2" value="{{ old('middle_initial', $user->roleable->MI ?? '') }}" maxlength="2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Last Name</label>
                            <input name="last_name" type="text" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2" value="{{ old('last_name', $user->roleable->last_name ?? '') }}" required>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700">Phone</label>
                            <input name="phone" type="text" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2" value="{{ old('phone', $user->roleable->contact_number ?? '') }}">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">Save Profile</button>
                    </div>
                </form>
                <div class="space-y-4"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Notifications</h2>
            <form method="POST" action="{{ route('admin.settings.account.notifications') }}" class="space-y-4">
                @csrf
                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <input type="checkbox" name="approvals" value="1" class="mt-1 h-4 w-4 text-blue-600 rounded border-slate-300" {{ ($prefs['notifications']['approvals'] ?? true) ? 'checked' : '' }}>
                    <div>
                        <div class="text-sm font-semibold text-slate-800">Notify me about payment approvals</div>
                        <div class="text-[11px] text-slate-500">New pending approvals and status changes.</div>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <input type="checkbox" name="online_confirmations" value="1" class="mt-1 h-4 w-4 text-blue-600 rounded border-slate-300" {{ ($prefs['notifications']['online_confirmations'] ?? true) ? 'checked' : '' }}>
                    <div>
                        <div class="text-sm font-semibold text-slate-800">Notify me about online payment confirmations</div>
                        <div class="text-[11px] text-slate-500">Online payment events from gateways.</div>
                    </div>
                </label>
                <div class="flex justify-end">
                    <button class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">Save Notifications</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Security</h2>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-slate-800">Password</div>
                    <div class="text-[11px] text-slate-500">Keep your account secure by using a strong password.</div>
                </div>
                <a href="{{ route('auth.password.change') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold">Change Password</a>
            </div>
        </div>

    </div>
  </main>
</body>
</html>
