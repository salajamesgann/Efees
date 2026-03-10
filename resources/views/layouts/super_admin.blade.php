<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - Efees</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
    <!-- Sidebar -->
    @include('layouts.admin_sidebar')

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Mobile Header -->
        <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-slate-800 tracking-tight">Efees Super Admin</span>
            </div>
            <button @click="sidebarOpen = true" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-50 rounded-lg transition-all">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto bg-slate-50 custom-scrollbar">
            @yield('content')
        </main>
    </div>
</body>
</html>
