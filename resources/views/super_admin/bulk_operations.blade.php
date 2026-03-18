@extends('layouts.super_admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Bulk Operation Suite</h1>
    <p class="text-sm text-slate-500 mt-1">Manage platform-wide enrollment transitions, archiving, and bulk data imports</p>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Batch Promotion -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fas fa-graduation-cap text-blue-600"></i>
            Batch Promotion
        </h2>
        <form action="{{ route('super_admin.bulk.promote') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">From Level</label>
                <select name="from_level" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Select Level</option>
                    @foreach($levels as $level)
                        <option value="{{ $level }}">{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">To Level</label>
                <input type="text" name="to_level" required placeholder="e.g., Grade 11" 
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                Execute Batch Promotion
            </button>
            <p class="text-[10px] text-slate-400 italic text-center">
                This will move all enrolled students from the selected level to the target level.
            </p>
        </form>
    </div>

    <!-- Bulk Archiving -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fas fa-archive text-amber-600"></i>
            Bulk Archiving
        </h2>

        <!-- Archiving Stats -->
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="p-3 bg-blue-50 rounded-xl border border-blue-100 text-center">
                <div class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">Graduated</div>
                <div class="text-xl font-black text-blue-800">{{ number_format($stats['graduated_students']) }}</div>
            </div>
            <div class="p-3 bg-orange-50 rounded-xl border border-orange-100 text-center">
                <div class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">Withdrawn</div>
                <div class="text-xl font-black text-orange-800">{{ number_format($stats['withdrawn_students']) }}</div>
            </div>
            <div class="p-3 bg-red-50 rounded-xl border border-red-100 text-center">
                <div class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">Dropped</div>
                <div class="text-xl font-black text-red-800">{{ number_format($stats['dropped_students']) }}</div>
            </div>
        </div>

        <form action="{{ route('super_admin.bulk.archive') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Filter by Level (Optional)</label>
                <select name="level" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                    <option value="">All Levels</option>
                    @foreach($levels as $level)
                        <option value="{{ $level }}">{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Filter by Enrollment Status</label>
                <select name="status" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                    <option value="">All Statuses</option>
                    <option value="Enrolled">Enrolled</option>
                    <option value="Withdrawn">Withdrawn</option>
                    <option value="Graduated">Graduated</option>
                </select>
            </div>
            <button type="submit" class="w-full py-3 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-100" 
                    onclick="return confirm('Are you sure you want to archive these students? This action is reversible via database but will remove them from active views.');">
                Archive Filtered Students
            </button>
            <p class="text-[10px] text-slate-400 italic text-center">
                This will soft-delete students based on your filters. Use with caution during graduation periods.
            </p>
        </form>
    </div>
</div>

<!-- Enhanced Importer Section -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
    <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
        <i class="fas fa-file-import text-green-600"></i>
        Enhanced Student Importer
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
        <div class="md:col-span-1 space-y-4">
            <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                <h4 class="text-sm font-bold text-green-800 mb-1">Step 1: Template</h4>
                <p class="text-xs text-green-700 leading-relaxed">Download our standardized CSV template with real-time validation rules.</p>
                <a href="{{ route('super_admin.students.importTemplate') }}" class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-green-800 hover:underline">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </div>
        </div>
        <div class="md:col-span-2">
            <form action="{{ route('super_admin.students.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-4">
                @csrf
                <div class="flex-1 relative">
                    <input type="file" name="csv_file" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <button type="submit" class="px-8 py-2.5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-100 flex items-center gap-2">
                    <i class="fas fa-upload"></i> Upload & Import
                </button>
            </form>
            <p class="text-[10px] text-slate-400 mt-4">
                <i class="fas fa-info-circle mr-1"></i>
                Our enhanced importer validates LRN formats, grade levels, and existing student IDs before processing.
            </p>
        </div>
    </div>
</div>
@endsection
