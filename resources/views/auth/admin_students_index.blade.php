<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Student Management - Efees Admin</title>
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    body { font-family: 'Inter', 'Noto Sans', sans-serif; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Toggle Switch */
    .toggle-checkbox:checked {
        right: 0;
        border-color: #2563EB;
    }
    .toggle-checkbox {
        right: 0;
        transition: all 0.3s;
    }
    .toggle-label {
        width: 3rem;
        height: 1.5rem;
    }
  </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white text-slate-800 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 border-r border-slate-200 flex flex-col shrink-0 shadow-[4px_0_24px_-12px_rgba(0,0,0,0.1)]" id="sidebar">
        <div class="flex items-center justify-between gap-3 px-8 py-6 border-b border-slate-100 bg-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <div>
                    <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Efees Admin</h1>
                    <p class="text-xs text-slate-500 font-medium">Administration</p>
                </div>
            </div>
            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow pb-6 overflow-y-auto custom-scrollbar">
            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
            
            <!-- Dashboard -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin_dashboard') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin_dashboard') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('admin_dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <!-- Student Management -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.students.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.students.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-users text-lg {{ request()->routeIs('admin.students.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Student Management</span>
            </a>

            <!-- User Management -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.staff.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.staff.index') }}">
                <div class="w-8 flex justify-center">
                  <i class="fas fa-chalkboard-teacher text-lg {{ request()->routeIs('admin.staff.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">User Management</span>
            </a>

            <!-- Fee Management -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fees.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.fees.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-file-invoice-dollar text-lg {{ request()->routeIs('admin.fees.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Fee Management</span>
            </a>

            <!-- Payment Approvals -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.payment_approvals.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.payment_approvals.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-check-double text-lg {{ request()->routeIs('admin.payment_approvals.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Payment Approvals</span>
            </a>

            <!-- Reports & Analytics -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.reports.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-chart-bar text-lg {{ request()->routeIs('admin.reports.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Reports & Analytics</span>
            </a>

            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-6">System</p>

            <!-- Audit Logs -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.audit-logs.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-shield-alt text-lg {{ request()->routeIs('admin.audit-logs.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Audit Logs</span>
            </a>

            <!-- SMS Control -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.sms.logs') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-comment-alt text-lg {{ request()->routeIs('admin.sms.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">SMS Control</span>
            </a>

            <!-- Requests -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.requests.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.requests.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-key text-lg {{ request()->routeIs('admin.requests.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Requests</span>
            </a>

            <!-- Settings -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.settings.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-cog text-lg {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Settings</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 px-4 pb-6">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 hover:shadow-sm transition-all duration-200 group border border-red-100">
                    <div class="w-8 flex justify-center">
                        <i class="fas fa-sign-out-alt text-lg group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="text-sm font-bold">Logout</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-white" x-data="{ 
        activeTab: 'personal',
        isCreating: {{ json_encode($isCreating ?? false) }},
        gradeLevel: '{{ old('level', $selectedStudent?->level ?? request('level') ?? '') }}',
        strandName: '{{ old('strand', $selectedStudent?->strand ?? request('strand') ?? '') }}',
        sections: [],
        loadingSections: false,
        mobileShowDetail: false,
        sectionModalOpen: false,
        strandModalOpen: false,
        async fetchSections() {
            if (!this.gradeLevel) { 
                this.sections = []; 
                return; 
            }
            this.loadingSections = true;
            try {
                const params = new URLSearchParams();
                params.set('level', this.gradeLevel);
                if (['Grade 11','Grade 12'].includes(this.gradeLevel) && this.strandName) {
                    params.set('strand', this.strandName);
                }
                const res = await fetch('{{ route('admin.students.sections.list') }}' + '?' + params.toString());
                const data = await res.json();
                this.sections = Array.isArray(data) ? data : [];
            } catch (e) {
                console.error(e);
                this.sections = [];
            } finally {
                this.loadingSections = false;
            }
        },
        init() {
             const urlParams = new URLSearchParams(window.location.search);
             if(urlParams.get('create')) {
                 this.isCreating = true;
             }
             
             // Initial fetch if data exists
             if (this.gradeLevel) {
                 this.fetchSections();
             }
             
             // Watchers
             this.$watch('gradeLevel', () => this.fetchSections());
             this.$watch('strandName', () => this.fetchSections());
        }
    }">
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

        <!-- Master-Detail Body -->
        <div class="flex-1 flex overflow-hidden">
            <!-- LEFT PANEL: Student List -->
            <div class="w-full md:w-1/3 min-w-[320px] max-w-md border-r border-slate-200 bg-white flex flex-col z-0"
                 :class="mobileShowDetail ? 'hidden md:flex' : 'flex'">
                <!-- Header: Title & Add Button -->
                <div class="p-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2">
                        @if($viewState !== 'levels')
                            @php
                                $backParams = [];
                                if ($viewState === 'sections') {
                                    // Back to Levels or Strands
                                    $backParams['level'] = $currentLevel;
                                    if(isset($currentStrand)) {
                                        // If strand is set, clicking back should go to strands list (which means level is set but strand is null)
                                        // But currently admin.students.index with just level will show strands if SHS.
                                        // So passing just 'level' is correct.
                                        // Wait, if I am in sections and I have a strand, I want to go back to strand selection.
                                        // AdminStudentController: if level is 11/12 and !strand, show strands.
                                        // So calling route with just 'level' will show strands. Correct.
                                    } else {
                                        // If no strand (Junior High), back goes to levels (no params)
                                        $backParams = [];
                                    }
                                } elseif ($viewState === 'strands') {
                                    // Back to Levels
                                    $backParams = [];
                                } elseif ($viewState === 'students') {
                                    // Back to Sections
                                    $backParams['level'] = $currentLevel;
                                    if(isset($currentStrand)) {
                                        $backParams['strand'] = $currentStrand;
                                    }
                                    $backParams['section'] = $currentSection; // Wait, back to sections means section param should be removed.
                                    // Actually, if I want to go back to sections list, I should pass level (and strand if applicable).
                                    unset($backParams['section']);
                                }

                                if(isset($currentSchoolYear)) {
                                    $backParams['school_year'] = $currentSchoolYear;
                                }
                            @endphp
                            <a href="{{ route('admin.students.index', $backParams) }}" class="text-slate-500 hover:text-blue-600 transition-colors">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        @endif
                        <h2 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                            @if($viewState === 'levels')
                                Select Grade Level
                            @elseif($viewState === 'strands')
                                {{ $currentLevel }} (Strand)
                            @elseif($viewState === 'sections')
                                {{ $currentLevel }} @if(isset($currentStrand)) - {{ $currentStrand }} @endif
                            @elseif($viewState === 'students' && $q)
                                Search Results
                            @elseif($viewState === 'students' && !($currentLevel && $currentSection))
                                All Students
                            @else
                                {{ $currentSection }}
                            @endif

                            @if(isset($activeSy) && $currentSchoolYear !== $activeSy)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                    <i class="fas fa-lock mr-1"></i> Read Only
                                </span>
                            @endif
                        </h2>
                    </div>
                    
                    @if(isset($activeSy) && $currentSchoolYear === $activeSy)
                        @if($viewState === 'students' && !$q)
                        <a href="{{ route('admin.students.index', ['create' => true, 'level' => $currentLevel, 'section' => $currentSection, 'strand' => $currentStrand ?? null, 'school_year' => $currentSchoolYear]) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition-colors shadow-sm flex items-center gap-2">
                            <i class="fas fa-plus"></i> Add Student
                        </a>
                        @elseif($viewState === 'sections')
                        <button @click="sectionModalOpen = true" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition-colors shadow-sm flex items-center gap-2">
                            <i class="fas fa-plus"></i> Add Section
                        </button>
                        @elseif($viewState === 'strands')
                        <button @click="strandModalOpen = true" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition-colors shadow-sm flex items-center gap-2">
                            <i class="fas fa-plus"></i> Add Strand
                        </button>
                        @endif
                    @endif

                    @if($viewState === 'levels')
                    <form method="GET" action="{{ route('admin.students.index') }}" class="flex items-center">
                        <select name="school_year" onchange="this.form.submit()" class="text-sm border-none bg-slate-50 rounded-lg text-slate-600 font-medium focus:ring-0 cursor-pointer hover:text-slate-900 py-1 pl-3 pr-8">
                            @foreach($schoolYears as $sy)
                                <option value="{{ $sy }}" {{ $currentSchoolYear == $sy ? 'selected' : '' }}>{{ $sy }}</option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                </div>

                <!-- Search (Available in all views) -->
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 space-y-3 shrink-0">
                    <form method="GET" action="{{ route('admin.students.index') }}" class="relative"
                          x-data="{
                              query: @json($q ?? ''),
                              suggestions: [],
                              showSuggestions: false,
                              async fetchSuggestions() {
                                  if (this.query.length < 1) {
                                      this.suggestions = [];
                                      this.showSuggestions = false;
                                      return;
                                  }
                                  try {
                                      const response = await fetch('{{ route('admin.students.search') }}?q=' + encodeURIComponent(this.query));
                                      this.suggestions = await response.json();
                                      this.showSuggestions = true;
                                  } catch (e) {
                                      console.error('Search failed', e);
                                  }
                              }
                          }"
                          @click.away="showSuggestions = false">
                        @if($currentLevel)
                        <input type="hidden" name="level" value="{{ $currentLevel }}">
                        @endif
                        @if($currentSection)
                        <input type="hidden" name="section" value="{{ $currentSection }}">
                        @endif
                        @if(isset($currentStrand))
                        <input type="hidden" name="strand" value="{{ $currentStrand }}">
                        @endif
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" name="q" x-model="query" @input.debounce.300ms="fetchSuggestions()" 
                                    placeholder="Search students..." 
                                    autocomplete="off"
                                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400" />
                                
                                <!-- Suggestions Dropdown -->
                                <div x-show="showSuggestions && suggestions.length > 0" 
                                    class="absolute z-50 w-full mt-1 bg-white rounded-xl shadow-lg border border-slate-100 max-h-60 overflow-y-auto"
                                    style="display: none;"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95">
                                    <template x-for="student in suggestions" :key="student.student_id">
                                        <a :href="'{{ route('admin.students.index') }}?id=' + student.student_id" 
                                        class="block px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="font-medium text-slate-700" x-text="student.first_name + ' ' + student.last_name"></div>
                                                    <div class="text-xs text-slate-500">
                                                        <span x-text="student.student_id"></span> • 
                                                        <span x-text="student.level"></span> - <span x-text="student.section"></span>
                                                    </div>
                                                </div>
                                                <i class="fas fa-chevron-right text-xs text-slate-300"></i>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </div>
                            <a href="{{ route('admin.students.index', ['view_all' => 1]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors flex items-center justify-center" title="View All Students">
                                <i class="fas fa-list"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- List -->
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <ul class="divide-y divide-slate-100">
                        @if($viewState === 'levels')
                            @forelse($levels as $lvl)
                            <li>
                                <a href="{{ route('admin.students.index', array_merge(request()->query(), ['level' => $lvl])) }}" 
                                   class="block p-4 hover:bg-slate-50 transition-colors group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                                <i class="fas fa-layer-group"></i>
                                            </div>
                                            <span class="font-semibold text-slate-700 group-hover:text-blue-700 transition-colors">{{ $lvl }}</span>
                                        </div>
                                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-blue-400"></i>
                                    </div>
                                </a>
                            </li>
                            @empty
                            <li class="p-8 text-center text-slate-500 text-sm">No grade levels found.</li>
                            @endforelse
                        
                        @elseif($viewState === 'strands')
                            @forelse($strands as $strand)
                            <li>
                                <a href="{{ route('admin.students.index', array_merge(request()->query(), ['level' => $currentLevel, 'strand' => $strand->name])) }}" 
                                   class="block p-4 hover:bg-slate-50 transition-colors group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-100 transition-colors">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                            <span class="font-semibold text-slate-700 group-hover:text-teal-700 transition-colors">{{ $strand->name }}</span>
                                        </div>
                                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-teal-400"></i>
                                    </div>
                                </a>
                            </li>
                            @empty
                            <li class="p-8 text-center text-slate-500 text-sm">No strands found for {{ $currentLevel }}.</li>
                            @endforelse

                        @elseif($viewState === 'sections')
                            @forelse($sections as $sec)
                            <li>
                                <div class="flex items-center justify-between p-4 hover:bg-slate-50 transition-colors group relative">
                                    <a href="{{ route('admin.students.index', array_merge(request()->query(), ['level' => $currentLevel, 'section' => $sec->name])) }}" 
                                       class="flex-1 flex items-center justify-between pr-10">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <span class="font-semibold text-slate-700 group-hover:text-indigo-700 transition-colors">{{ $sec->name }}</span>
                                        </div>
                                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-indigo-400"></i>
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    @unless($isReadOnly)
                                    <form action="{{ route('admin.students.sections.destroy', $sec->id) }}" method="POST" 
                                          class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity"
                                          onsubmit="return confirm('Are you sure you want to delete section {{ $sec->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        @if(request()->query('strand'))
                                        <input type="hidden" name="strand" value="{{ request()->query('strand') }}">
                                        @endif
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete Section">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endunless
                                </div>
                            </li>
                            @empty
                            <li class="p-8 text-center text-slate-500 text-sm">No sections found for {{ $currentLevel }}.</li>
                            @endforelse

                        @elseif($viewState === 'students')
                            @forelse($students as $student)
                            <li>
                                <a href="{{ route('admin.students.index', array_merge(request()->except(['page']), ['id' => $student->student_id, 'create' => null])) }}" 
                                   class="block p-4 hover:bg-slate-50 transition-colors border-l-4 {{ isset($selectedStudent) && $selectedStudent->student_id === $student->student_id ? 'bg-blue-50/50 border-blue-600' : 'border-transparent' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-slate-900 truncate">{{ $student->full_name }}</div>
                                            <div class="text-xs text-slate-500 font-mono mb-1">{{ $student->student_id }}</div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">
                                                    {{ $student->level }} - {{ $student->section }}
                                                </span>
                                                @if($student->is_shs_voucher)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200" title="SHS Voucher Holder">
                                                    <i class="fas fa-ticket-alt mr-1"></i> Voucher
                                                </span>
                                                @endif
                                                @if(strtolower($student->enrollment_status) === 'archived')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 uppercase">Archived</span>
                                                @endif
                                            </div>
                                            
                                            <!-- Parent Info in List -->
                                            @php
                                                $listParent = $student->parents->first();
                                                $listParentName = $listParent ? $listParent->full_name : '';
                                            @endphp
                                            @if($listParentName)
                                            <div class="text-xs text-slate-500 truncate mt-1 flex items-center gap-1">
                                                <i class="fas fa-user-friends text-[10px] text-slate-400"></i>
                                                <span>{{ $listParentName }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @empty
                            <li class="p-8 text-center text-slate-500 text-sm">
                                No students found.
                            </li>
                            @endforelse
                        @endif
                    </ul>
                </div>

                <!-- Pagination -->
                @if($viewState === 'students')
                <div class="px-4 pt-4 pb-2 border-t border-slate-100 bg-slate-50/50 text-xs shrink-0">
                    {{ $students->appends(request()->query())->links('pagination::simple-tailwind') }}
                </div>
                @endif
                
                <!-- Export (Always Visible) -->
                <div class="p-4 border-t border-slate-100 bg-white shrink-0">
                    <a href="{{ route('admin.students.export', array_merge(request()->query(), ['school_year' => $currentSchoolYear])) }}" target="_blank" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm group">
                        <i class="fas fa-file-download text-slate-400 group-hover:text-teal-600 transition-colors"></i>
                        <span>Download Master List</span>
                    </a>
                </div>
            </div>

            <!-- RIGHT PANEL: Detail / Form -->
            <div :class="mobileShowDetail ? 'flex' : 'hidden md:flex'" class="flex-1 flex-col overflow-hidden bg-slate-50 relative w-full">
                <!-- Mobile Back Button -->
                <div class="md:hidden p-3 bg-white border-b border-slate-200 flex items-center gap-2 sticky top-0 z-20">
                    <button @click="mobileShowDetail = false" class="flex items-center gap-2 text-slate-500 hover:text-slate-700 px-2 py-1 rounded-lg hover:bg-slate-50">
                        <i class="fas fa-arrow-left"></i>
                        <span class="font-bold text-sm">Back to List</span>
                    </button>
                </div>
                
                @if (session('success'))
                <div class="absolute top-4 right-4 z-50 bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-200 shadow-sm flex items-center gap-3 animate-fade-in-down" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-2 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if($isCreating)
                    <!-- CREATE MODE -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-10">
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 max-w-4xl mx-auto">
                            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                    <i class="fas fa-user-plus text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-slate-800">Enroll New Student</h2>
                                    <p class="text-sm text-slate-500">Enter student details below</p>
                                </div>
                            </div>
                            
                            @include('auth.partials.student_form', ['mode' => 'create', 'student' => null, 'isReadOnly' => $isReadOnly])
                        </div>
                    </div>

                @elseif($selectedStudent)
                    <!-- VIEW/EDIT MODE -->
                    <!-- Header -->
                    <div class="bg-white border-b border-slate-200 px-8 py-6 shrink-0">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-2xl font-bold shadow-lg shadow-blue-200">
                                    {{ substr($selectedStudent->first_name, 0, 1) }}
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-slate-900">{{ $selectedStudent->full_name }}</h1>
                                    <div class="flex items-center gap-3 text-sm text-slate-500 mt-1 flex-wrap">
                                        <span class="font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-600">{{ $selectedStudent->student_id }}</span>
                                        <span>•</span>
                                        <span>{{ $selectedStudent->level }}</span>

                                        @if($selectedStudent->is_shs_voucher)
                                        <span>•</span>
                                        <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 px-2.5 py-0.5 rounded-full text-xs font-bold border border-amber-200">
                                            <i class="fas fa-ticket-alt"></i> SHS Voucher
                                        </span>
                                        @endif
                                        
                                        @php
                                            $primaryParent = $selectedStudent->parents->where('pivot.is_primary', true)->first() ?? $selectedStudent->parents->first();
                                        @endphp
                                        @if($primaryParent)
                                            <span>•</span>
                                            <span class="flex items-center gap-1 text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full text-xs font-medium" title="Primary Parent">
                                                <i class="fas fa-user-friends text-[10px]"></i> {{ $primaryParent->full_name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(strtolower($selectedStudent->enrollment_status) === 'archived')
                                <form action="{{ route('admin.students.unarchive', $selectedStudent) }}" method="POST" onsubmit="return confirm('Are you sure you want to unarchive this student?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-slate-400 hover:text-green-600 hover:bg-green-50 transition-colors p-2 rounded-lg" title="Unarchive Student">
                                        <i class="fas fa-box-open"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.students.archive', $selectedStudent) }}" method="POST" onsubmit="return confirm('Are you sure you want to archive this student?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors p-2 rounded-lg" title="Archive Student">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>

                        <!-- Tabs -->
                        <div class="flex items-center gap-8 border-b border-transparent -mb-px">
                            <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'" class="pb-3 text-sm font-bold border-b-2 transition-all">Personal</button>
                            <button @click="activeTab = 'academic'" :class="activeTab === 'academic' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'" class="pb-3 text-sm font-bold border-b-2 transition-all">Academic</button>
                            <button @click="activeTab = 'parent'" :class="activeTab === 'parent' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'" class="pb-3 text-sm font-bold border-b-2 transition-all">Parent / Guardian</button>
                            <button @click="activeTab = 'fees'" :class="activeTab === 'fees' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'" class="pb-3 text-sm font-bold border-b-2 transition-all">Fees & Discounts</button>
                            <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'" class="pb-3 text-sm font-bold border-b-2 transition-all">Recent Activity</button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar p-8">
                        <div class="max-w-4xl mx-auto">
                            <!-- Form Partial for Edit -->
                            <div x-show="['personal', 'academic', 'parent'].includes(activeTab)">
                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                                    @include('auth.partials.student_form', ['mode' => 'edit', 'student' => $selectedStudent, 'isReadOnly' => $isReadOnly])
                                </div>
                            </div>

                            <!-- Fees & Discounts Tab -->
                            <div x-show="activeTab === 'fees'" class="space-y-6" x-cloak>
                                <!-- Current Fee Assignment Summary -->
                                @if(isset($feeAssignment))
                                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                                        <div class="flex items-center justify-between mb-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                                    <i class="fas fa-file-invoice-dollar text-lg"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-bold text-slate-800">Fee Assignment</h3>
                                                    <p class="text-sm text-slate-500">Current financial status</p>
                                                </div>
                                            </div>
                                            <a href="{{ route('admin.enrollment.show', $selectedStudent) }}" class="text-sm text-blue-600 font-semibold hover:text-blue-700 hover:underline">
                                                View Full Details <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Fees</p>
                                                <p class="text-xl font-bold text-slate-800">₱{{ number_format($feeAssignment->total_amount, 2) }}</p>
                                            </div>
                                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Discounts</p>
                                                <p class="text-xl font-bold text-emerald-600">-₱{{ number_format($feeAssignment->discounts_total, 2) }}</p>
                                            </div>
                                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Net Payable</p>
                                                <p class="text-xl font-bold text-blue-600">₱{{ number_format($feeAssignment->total_amount, 2) }}</p>
                                            </div>
                                        </div>

                                        <!-- Applied Discounts List -->
                                        <h4 class="text-sm font-bold text-slate-700 mb-3">Applied Discounts</h4>
                                        @php
                                            $hasAppliedDiscounts = $feeAssignment->discounts->count() > 0;
                                            $showSiblingExclusion = $selectedStudent->is_shs_voucher;
                                            $siblingDiscountApplied = $feeAssignment->discounts->contains(function($d) {
                                                return str_contains(strtolower($d->discount_name), 'sibling') || 
                                                       collect($d->eligibility_rules)->contains('field', 'sibling_rank');
                                            });
                                        @endphp

                                        @if($hasAppliedDiscounts || $showSiblingExclusion)
                                            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                                                <table class="min-w-full divide-y divide-slate-100">
                                                    <thead class="bg-slate-50">
                                                        <tr>
                                                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Discount</th>
                                                            <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                                                            <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-100">
                                                        @foreach($feeAssignment->discounts as $discount)
                                                            <tr>
                                                                <td class="px-4 py-3 text-sm text-slate-700 font-medium">
                                                                    {{ $discount->discount_name }}
                                                                    <span class="text-slate-400 text-xs font-normal ml-1">({{ $discount->type === 'percentage' ? $discount->value.'%' : 'Fixed' }})</span>
                                                                </td>
                                                                <td class="px-4 py-3 text-sm text-emerald-600 font-bold text-right">
                                                                    -₱{{ number_format($discount->pivot->applied_amount ?? 0, 2) }}
                                                                </td>
                                                                <td class="px-4 py-3 text-right">
                                                                    @unless($isReadOnly)
                                                                    <form action="{{ route('admin.enrollment.discounts.destroy', ['student' => $selectedStudent, 'discount' => $discount->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this discount?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </form>
                                                                    @endunless
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        
                                                        @if($showSiblingExclusion && !$siblingDiscountApplied)
                                                            <tr>
                                                                <td class="px-4 py-3 text-sm text-slate-500 italic">
                                                                    Sibling Discount
                                                                </td>
                                                                <td class="px-4 py-3 text-sm text-slate-400 italic text-right">
                                                                    Not Applicable (SHS Voucher)
                                                                </td>
                                                                <td class="px-4 py-3 text-right">
                                                                    <span class="text-slate-300" title="Excluded due to SHS Voucher"><i class="fas fa-ban"></i></span>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-sm text-slate-500 italic">No discounts currently applied.</p>
                                        @endif
                                    </div>
                                @else
                                    <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 flex items-start gap-3">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                                        <div>
                                            <h4 class="text-sm font-bold text-yellow-800">No Fee Assignment Found</h4>
                                            <p class="text-xs text-yellow-700 mt-1">This student has not been assigned a fee structure yet. Please enroll the student properly to enable fee management.</p>
                                            <a href="{{ route('admin.enrollment.edit', $selectedStudent) }}" class="inline-block mt-2 text-xs font-bold text-yellow-800 underline hover:text-yellow-900">Manage Enrollment</a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Activity Tab -->
                            <div x-show="activeTab === 'activity'" class="space-y-4" x-cloak>
                                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Recent Activity</h3>
                                @if(isset($recentActivity) && count($recentActivity) > 0)
                                    <div class="flow-root">
                                        <ul role="list" class="-mb-8">
                                            @foreach($recentActivity as $log)
                                            <li>
                                                <div class="relative pb-8">
                                                    @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                                    @endif
                                                    <div class="relative flex space-x-3">
                                                        <div>
                                                            <span class="h-8 w-8 rounded-full bg-blue-50 flex items-center justify-center ring-8 ring-white">
                                                                <i class="fas fa-history text-blue-600 text-xs"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                            <div>
                                                                <p class="text-sm text-slate-500">{{ $log->action }} <span class="font-medium text-slate-900">{{ $log->details }}</span></p>
                                                            </div>
                                                            <div class="whitespace-nowrap text-right text-sm text-slate-500">
                                                                <time datetime="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</time>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <div class="text-center py-12 bg-white rounded-2xl border border-slate-200 border-dashed">
                                        <div class="text-slate-400 mb-2"><i class="fas fa-history text-2xl"></i></div>
                                        <p class="text-slate-500 text-sm">No recent activity recorded.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="flex-1 flex flex-col items-center justify-center text-slate-400">
                        <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-6 text-slate-300">
                            <i class="fas fa-user-graduate text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-700">Select a Student</h3>
                        <p class="text-sm text-slate-500 max-w-xs text-center mt-2">Choose a student from the list to view their details, manage enrollment, or view activity logs.</p>
                        <a href="{{ route('admin.students.index', ['create' => true]) }}" class="mt-6 px-6 py-2.5 bg-blue-600 text-white rounded-xl font-semibold shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all">
                            Enroll New Student
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @if($viewState === 'sections')
        <!-- Add Section Modal -->
        <template x-teleport="body">
            <div x-show="sectionModalOpen" class="fixed inset-0 z-[99] overflow-y-auto" style="display: none;" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="sectionModalOpen" @click="sectionModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="sectionModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form action="{{ route('admin.students.storeSection') }}" method="POST">
                            @csrf
                            <input type="hidden" name="level" value="{{ $currentLevel }}">
                            @if(isset($currentStrand))
                                <input type="hidden" name="strand" value="{{ $currentStrand }}">
                            @endif
                            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-indigo-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                        <i class="fas fa-layer-group text-indigo-600"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Add New Section</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">Create a new section for {{ $currentLevel }}@if(isset($currentStrand)) ({{ $currentStrand }})@endif.</p>
                                            <div class="mt-4">
                                                <label for="section_name" class="block text-sm font-medium text-gray-700">Section Name</label>
                                                <input type="text" name="name" id="section_name" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="e.g. Einstein">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Add Section
                                </button>
                                <button type="button" @click="sectionModalOpen = false" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
        @endif

        @if($viewState === 'strands')
        <!-- Add Strand Modal -->
        <template x-teleport="body">
            <div x-show="strandModalOpen" class="fixed inset-0 z-[99] overflow-y-auto" style="display: none;" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="strandModalOpen" @click="strandModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="strandModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form action="{{ route('admin.students.storeStrand') }}" method="POST">
                            @csrf
                            <input type="hidden" name="level" value="{{ $currentLevel }}">
                            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-teal-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                        <i class="fas fa-graduation-cap text-teal-600"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Add New Strand</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">Create a new strand for {{ $currentLevel }}.</p>
                                            <div class="mt-4">
                                                <label for="strand_name" class="block text-sm font-medium text-gray-700">Strand Name</label>
                                                <input type="text" name="name" id="strand_name" required class="mt-1 focus:ring-teal-500 focus:border-teal-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="e.g. STEM">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-teal-600 border border-transparent rounded-md shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Add Strand
                                </button>
                                <button type="button" @click="strandModalOpen = false" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
        @endif
    </div>
</body>
</html>
