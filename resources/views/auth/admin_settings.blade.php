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
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">School Settings</h1>
        <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-100">
            <i class="fas fa-info-circle text-sm"></i>
            <span class="text-xs font-bold uppercase tracking-wider">Global settings managed by Super Admin</span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-lg border border-gray-200 p-6">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Semester</label>
            <input type="text" name="semester" value="{{ old('semester', optional($settings['semester'] ?? null)->value) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="e.g. First Semester" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Student ID Format</label>
            <input type="text" name="student_id_format"
                   value="{{ old('student_id_format', optional($settings['student_id_format'] ?? null)->value ?? 'STU-{SY}-{####}') }}"
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 font-mono"
                   placeholder="e.g. STU-{SY}-{####}" />
            <p class="mt-1.5 text-xs text-gray-500 space-x-2">
                Available tokens:
                <code class="bg-gray-100 px-1 rounded">{SY}</code> start year of active SY (e.g. 2025) &nbsp;
                <code class="bg-gray-100 px-1 rounded">{YYYY}</code> current 4-digit year &nbsp;
                <code class="bg-gray-100 px-1 rounded">{YY}</code> 2-digit year &nbsp;
                <code class="bg-gray-100 px-1 rounded">{####}</code> auto-incrementing number (number of # sets zero-pad width)
            </p>
            <p class="mt-1 text-xs text-amber-600">&#9888; Changing this format only affects <strong>new</strong> students. Existing IDs are not renamed.</p>
        </div>
        
        <div class="mb-6 pt-4 border-t border-gray-200 space-y-4">
            <h2 class="text-lg font-semibold mb-2">System Behavior</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Auto-generate fees on enrollment</p>
                    <p class="text-xs text-gray-500 mt-1">Automatically create fee records when a new student is enrolled.</p>
                </div>
                <div>
                    <input type="checkbox" id="auto_generate_fees_on_enrollment" name="auto_generate_fees_on_enrollment" value="1"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           {{ (optional($settings['auto_generate_fees_on_enrollment'] ?? null)->value == '1') ? 'checked' : '' }} />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Enable SMS notifications</p>
                    <p class="text-xs text-gray-500 mt-1">Global master switch for all SMS notifications and reminders.</p>
                </div>
                <div>
                    <input type="checkbox" id="notifications_enabled" name="notifications_enabled" value="1"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           {{ (optional($settings['notifications_enabled'] ?? null)->value == '1') ? 'checked' : '' }} />
                </div>
            </div>
        </div>

        <div class="mb-6 pt-4 border-t border-gray-200 space-y-4">
            <h2 class="text-lg font-semibold mb-2">Security</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Login Attempts</label>
                    <input type="number" min="3" max="20" name="max_login_attempts" value="{{ old('max_login_attempts', optional($settings['max_login_attempts'] ?? null)->value) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="e.g. 5" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lockout Minutes</label>
                    <input type="number" min="1" max="1440" name="lockout_minutes" value="{{ old('lockout_minutes', optional($settings['lockout_minutes'] ?? null)->value) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="e.g. 15" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg" type="submit">Save</button>
            <a href="{{ auth()->user()->hasRole('super_admin') ? route('super_admin.dashboard') : route('admin_dashboard') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">Back</a>
        </div>
    </form>

    <section class="mt-8 bg-white rounded-lg border border-red-200 p-6">
        <h2 class="text-lg font-semibold text-red-700 mb-2">Danger Zone</h2>
        <p class="text-sm text-gray-700 mb-4">
            This will permanently remove all students, their fee records, payments, and parent accounts.
            This action is intended for clearing demo or test data only.
        </p>
        <form method="POST" action="{{ route('admin.settings.reset-demo') }}" class="space-y-4">
            @csrf
            <div>
                <label for="confirm" class="block text-sm font-medium text-gray-700">
                    Type RESET to confirm
                </label>
                <input
                    id="confirm"
                    name="confirm"
                    type="text"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    placeholder="RESET"
                    autocomplete="off"
                />
            </div>
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold"
                onclick="return confirm('Are you absolutely sure? This will delete all students and parent accounts.');"
            >
                Reset demo data
            </button>
        </form>
    </section>

    <!-- Maintenance tools removed per request -->
</main>
</body>
</html>
