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

@if(session('warning'))
    <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl flex items-center gap-2">
        <i class="fas fa-triangle-exclamation"></i>
        {{ session('warning') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
        <p class="font-semibold mb-1">Upload failed:</p>
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Batch Promotion -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col">
        <div class="flex-1">
            <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                <i class="fas fa-graduation-cap text-blue-600"></i>
                Batch Promotion
            </h2>
            <form action="{{ route('super_admin.bulk.promote') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">From Level</label>
                        <select name="from_level" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Level</option>
                            @foreach($levels as $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">To Level</label>
                        <input type="text" name="to_level" required placeholder="To Level" 
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">From SY</label>
                        <select name="school_year" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Year</option>
                            @foreach($schoolYears as $sy)
                                <option value="{{ $sy }}" {{ $sy == $activeSY ? 'selected' : '' }}>{{ $sy }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">To SY</label>
                        <input type="text" name="target_school_year" required placeholder="Next SY" 
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div class="flex items-center gap-2 px-1">
                    <input type="checkbox" name="clear_sections" id="clear_sections" value="1" checked class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                    <label for="clear_sections" class="text-[10px] font-bold text-slate-500 uppercase tracking-wide cursor-pointer">Clear Section Assignments</label>
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                    Promote Students
                </button>
            </form>
        </div>
        <p class="text-[10px] text-slate-400 italic text-center mt-4">
            Moves enrolled students from the selected level & SY to the target level & SY.
        </p>
    </div>

    <!-- Bulk Status Update -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col">
        <div class="flex-1">
            <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                <i class="fas fa-user-tag text-purple-600"></i>
                Status Update
            </h2>
            <form action="{{ route('super_admin.bulk.statusUpdate') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Target Level</label>
                    <select name="level" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                        <option value="">Select Level</option>
                        @foreach($levels as $level)
                            <option value="{{ $level }}">{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">School Year</label>
                    <select name="school_year" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                        <option value="">Select Year</option>
                        @foreach($schoolYears as $sy)
                            <option value="{{ $sy }}" {{ $sy == $activeSY ? 'selected' : '' }}>{{ $sy }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">New Enrollment Status</label>
                    <select name="new_status" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                        <option value="Graduated">Graduated</option>
                        <option value="Withdrawn">Withdrawn</option>
                        <option value="Dropped">Dropped</option>
                        <option value="Enrolled">Enrolled</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition-all shadow-lg shadow-purple-100">
                    Update All Students
                </button>
            </form>
        </div>
        <p class="text-[10px] text-slate-400 italic text-center mt-4">
            Changes the status of all students matching the level and school year.
        </p>
    </div>

    <!-- Bulk Archiving -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col">
        <div class="flex-1">
            <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                <i class="fas fa-archive text-amber-600"></i>
                Bulk Archiving
            </h2>

            <!-- Archiving Stats -->
            <div class="grid grid-cols-3 gap-2 mb-6">
                <div class="p-2 bg-blue-50 rounded-xl border border-blue-100 text-center">
                    <div class="text-[8px] font-bold text-blue-600 uppercase mb-1">Graduated</div>
                    <div class="text-base font-black text-blue-800">{{ number_format($stats['graduated_students']) }}</div>
                </div>
                <div class="p-2 bg-orange-50 rounded-xl border border-orange-100 text-center">
                    <div class="text-[8px] font-bold text-orange-600 uppercase mb-1">Withdrawn</div>
                    <div class="text-base font-black text-orange-800">{{ number_format($stats['withdrawn_students']) }}</div>
                </div>
                <div class="p-2 bg-red-50 rounded-xl border border-red-100 text-center">
                    <div class="text-[8px] font-bold text-red-600 uppercase mb-1">Dropped</div>
                    <div class="text-base font-black text-red-800">{{ number_format($stats['dropped_students']) }}</div>
                </div>
            </div>

            <form action="{{ route('super_admin.bulk.archive') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">School Year</label>
                    <select name="school_year" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                        <option value="">Select Year</option>
                        @foreach($schoolYears as $sy)
                            <option value="{{ $sy }}" {{ $sy == $activeSY ? 'selected' : '' }}>{{ $sy }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Level (Optional)</label>
                    <select name="level" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                        <option value="">All Levels</option>
                        @foreach($levels as $level)
                            <option value="{{ $level }}">{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status (Optional)</label>
                    <select name="status" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                        <option value="">All Statuses</option>
                        <option value="Enrolled">Enrolled</option>
                        <option value="Withdrawn">Withdrawn</option>
                        <option value="Graduated">Graduated</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2.5 bg-amber-600 text-white text-sm font-bold rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-100" 
                        onclick="return confirm('Are you sure you want to archive these students?');">
                    Archive Students
                </button>
            </form>
        </div>
        <p class="text-[10px] text-slate-400 italic text-center mt-4">
            Soft-deletes students based on filters.
        </p>
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
            <form id="student-import-form" action="{{ route('super_admin.students.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-4">
                @csrf
                <div class="flex-1 relative">
                    <input type="file" name="csv_file" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <button id="student-import-submit" type="submit" class="px-8 py-2.5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-100 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                    <i class="fas fa-upload"></i>
                    <span id="student-import-submit-label">Upload & Import</span>
                </button>
            </form>
            <p id="student-import-status" class="text-[10px] text-slate-400 mt-4">
                <i class="fas fa-info-circle mr-1"></i>
                Our enhanced importer validates LRN formats, grade levels, and existing student IDs before processing.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('student-import-form');
    var submitBtn = document.getElementById('student-import-submit');
    var submitLabel = document.getElementById('student-import-submit-label');
    var statusText = document.getElementById('student-import-status');

    if (!form || !submitBtn || !submitLabel || !statusText) {
        return;
    }

    form.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitLabel.textContent = 'Uploading...';
        statusText.classList.remove('text-slate-400');
        statusText.classList.add('text-green-700');
        statusText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Import in progress. Please wait for confirmation.';
    });
});
</script>
@endsection
