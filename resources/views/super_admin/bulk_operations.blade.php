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
                        <select name="to_level" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Level</option>
                            @foreach($levels as $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </select>
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
                               pattern="^\d{4}-\d{4}$" title="Use format YYYY-YYYY (example: 2026-2027)"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div class="flex items-center gap-2 px-1">
                    <input type="checkbox" name="clear_sections" id="clear_sections" value="1" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                    <label for="clear_sections" class="text-[10px] font-bold text-slate-500 uppercase tracking-wide cursor-pointer">Clear Section Assignments (Optional)</label>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button type="submit" name="preview" value="1" class="w-full py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-all">
                        Preview Count
                    </button>
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                        Promote Students
                    </button>
                </div>
            </form>
        </div>
        <p class="text-[10px] text-slate-400 italic text-center mt-4">
            Moves promotable students (Active, Irregular, Enrolled) from the selected level & SY to the target level & SY. Target SY must already have active tuition setup.
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
                <div class="grid grid-cols-2 gap-3">
                    <button type="submit" name="preview" value="1" class="w-full py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-all">
                        Preview Count
                    </button>
                    <button type="submit" class="w-full py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition-all shadow-lg shadow-purple-100">
                        Update All Students
                    </button>
                </div>
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

            <form id="archive-form" action="{{ route('super_admin.bulk.archive') }}" method="POST" class="space-y-3">
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
                        <option value="Active">Active</option>
                        <option value="Enrolled">Enrolled</option>
                        <option value="Irregular">Irregular</option>
                        <option value="Withdrawn">Withdrawn</option>
                        <option value="Graduated">Graduated</option>
                        <option value="Dropped">Dropped</option>
                    </select>
                </div>
                <div class="p-2 bg-amber-50 border border-amber-200 rounded-lg">
                    <label class="flex items-start gap-2 text-[10px] text-amber-800 font-semibold leading-snug cursor-pointer">
                        <input type="checkbox" id="confirm_archive_all" name="confirm_archive_all" value="1" class="mt-0.5 w-3.5 h-3.5 text-amber-600 border-amber-300 rounded focus:ring-amber-500">
                        I understand that if Level and Status are blank, this archives all students in the selected school year.
                    </label>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="submit" name="preview" value="1" class="w-full py-2.5 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition-all">
                        Preview Count
                    </button>
                    <button type="submit" class="w-full py-2.5 bg-amber-600 text-white text-sm font-bold rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-100">
                        Archive Students
                    </button>
                </div>
            </form>
        </div>
        <p class="text-[10px] text-slate-400 italic text-center mt-4">
            Marks students as Archived based on filters.
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
            <form id="student-import-form" action="{{ route('super_admin.students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-2 relative">
                        <input type="file" name="csv_file" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <input
                            type="text"
                            name="school_year"
                            value="{{ old('school_year', $activeSY) }}"
                            placeholder="School Year (YYYY-YYYY)"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none"
                        >
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <p class="text-[10px] text-slate-500">
                        The School Year field overrides CSV values for every row. Format: YYYY-YYYY.
                    </p>
                    <button id="student-import-submit" type="submit" class="px-8 py-2.5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-100 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                        <i class="fas fa-upload"></i>
                        <span id="student-import-submit-label">Upload & Import</span>
                    </button>
                </div>
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
        // Continue to archive safety handler below when importer controls are absent.
    }

    if (form && submitBtn && submitLabel && statusText) {
        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitLabel.textContent = 'Uploading...';
            statusText.classList.remove('text-slate-400');
            statusText.classList.add('text-green-700');
            statusText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Import in progress. Please wait for confirmation.';
        });
    }

    var archiveForm = document.getElementById('archive-form');
    if (archiveForm) {
        archiveForm.addEventListener('submit', function (event) {
            var submitter = event.submitter;
            if (!submitter) {
                return;
            }

            if (submitter.name === 'preview') {
                return;
            }

            var levelInput = archiveForm.querySelector('select[name="level"]');
            var statusInput = archiveForm.querySelector('select[name="status"]');
            var syInput = archiveForm.querySelector('select[name="school_year"]');
            var isArchiveAllForYear = levelInput && statusInput && !levelInput.value && !statusInput.value;
            var sy = syInput ? syInput.value : '';

            var message = isArchiveAllForYear
                ? 'This will archive all students in School Year ' + sy + '. Continue?'
                : 'Are you sure you want to archive students matching the selected filters?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    }
});
</script>
@endsection
