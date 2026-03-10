<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SMS Control - {{ $template->exists ? 'Edit Template' : 'Create Template' }}</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        
        /* Custom Scrollbar */
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
<body class="flex flex-col md:flex-row min-h-screen bg-slate-50 font-sans text-slate-900" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" style="display: none;"></div>

    <!-- Sidebar -->
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
        <main class="flex-1 md:h-screen overflow-y-auto bg-slate-50 p-6 lg:p-8 custom-scrollbar">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-2xl font-bold text-slate-900 mb-6">{{ $template->exists ? 'Edit Template' : 'Create Template' }}</h1>
                
                <form method="POST" action="{{ $template->exists ? route('admin.sms.templates.update', $template) : route('admin.sms.templates.store') }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    @csrf
                    @if($template->exists)
                        @method('PUT')
                    @endif
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Template Name</label>
                            <input type="text" name="name" value="{{ old('name', $template->name) }}" 
                                   class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                                   placeholder="e.g. Payment Reminder"
                                   required />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Message Content</label>
                            <textarea name="content" rows="6" 
                                      class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                                      required>{{ old('content', $template->content) }}</textarea>
                            <div class="mt-2 p-3 bg-slate-50 rounded-lg border border-slate-100">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Available Placeholders</span>
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white border border-slate-200 text-slate-600">[Student Name]</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white border border-slate-200 text-slate-600">[Guardian Name]</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white border border-slate-200 text-slate-600">[Amount]</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white border border-slate-200 text-slate-600">[Due Date]</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white border border-slate-200 text-slate-600">[School Name]</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white border border-slate-200 text-slate-600">[Date]</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-sm" type="submit">
                                {{ $template->exists ? 'Update Template' : 'Create Template' }}
                            </button>
                            <a href="{{ route('admin.sms.templates') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors font-medium">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Alpine.js for Mobile Sidebar -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Supabase Realtime -->
    @include('partials.supabase_realtime')
</body>
</html>
