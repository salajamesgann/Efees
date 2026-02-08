<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Change Password</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h1 class="text-2xl font-bold mb-4">Change Password</h1>
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('auth.password.update') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="new_password" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required />
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required />
                </div>
                <div class="flex items-center gap-3">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg" type="submit">Update</button>
                    <a href="{{ route('user_dashboard') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

