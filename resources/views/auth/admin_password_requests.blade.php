<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Password Requests - Efees Admin</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
<body class="flex flex-col md:flex-row h-screen overflow-hidden bg-slate-50 font-sans text-slate-900" x-data="{ sidebarOpen: false }">
    @include('layouts.admin_sidebar')

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Mobile Header -->
        <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-blue-900">Efees</span>
            </div>
            <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-700">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-6 lg:p-8 custom-scrollbar">
            <div class="max-w-7xl mx-auto space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Password Requests</h1>
                        <p class="text-sm text-slate-500 mt-1">Manage password reset requests from parents</p>
                    </div>
                </div>

                <!-- Search and Filter Section -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <form method="GET" action="{{ route('admin.requests.index') }}" class="flex flex-col sm:flex-row gap-4">
                        <!-- Search Bar -->
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-slate-400"></i>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request()->input('search') }}" 
                                       placeholder="Search by email..." 
                                       class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="sm:w-48">
                            <select name="status" 
                                    class="block w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="pending" {{ request()->input('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request()->input('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request()->input('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        
                        <!-- Search Button -->
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm font-medium transition-colors">
                                <i class="fas fa-search mr-2"></i>Search
                            </button>
                            <a href="{{ route('admin.requests.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 text-sm font-medium transition-colors">
                                <i class="fas fa-times mr-2"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date Requested</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($requests as $request)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $request->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $request->status == 'approved' ? 'bg-green-100 text-green-800' : ($request->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $request->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($request->status == 'pending')
                                                <form action="{{ route('admin.requests.approve', $request) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900 hover:underline mr-3">Approve</button>
                                                </form>
                                                <form action="{{ route('admin.requests.reject', $request) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900 hover:underline">Reject</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.requests.destroy', $request) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this log?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-400 hover:text-slate-600 hover:underline">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 text-center">No requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-200">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Alpine.js for Mobile Sidebar -->
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
