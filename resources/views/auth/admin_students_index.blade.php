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
        border-radius: 10px;
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
    @include('layouts.admin_sidebar')

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
        promoteModalOpen: false,
        promoteTargetSection: '',
        promoteStudentCount: 0,
        promoteSkipCount: 0,
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
                const res = await fetch('{{ route('super_admin.students.sections.list') }}' + '?' + params.toString());
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
                                        // But currently super_admin.students.index with just level will show strands if SHS.
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
                            <a href="{{ route('super_admin.students.index', $backParams) }}" class="text-slate-500 hover:text-blue-600 transition-colors">
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
                        <a href="{{ route('super_admin.students.index', ['create' => true, 'level' => $currentLevel, 'section' => $currentSection, 'strand' => $currentStrand ?? null, 'school_year' => $currentSchoolYear]) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition-colors shadow-sm flex items-center gap-2">
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
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded-full whitespace-nowrap">
                            {{ number_format($totalStudents) }} {{ Str::plural('student', $totalStudents) }}
                        </span>
                        <form method="GET" action="{{ route('super_admin.students.index') }}" class="flex items-center">
                            <select name="school_year" onchange="this.form.submit()" class="text-sm border-none bg-slate-50 rounded-lg text-slate-600 font-medium focus:ring-0 cursor-pointer hover:text-slate-900 py-1 pl-3 pr-8">
                                @foreach($schoolYears as $sy)
                                    <option value="{{ $sy }}" {{ $currentSchoolYear == $sy ? 'selected' : '' }}>{{ $sy }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    @endif
                </div>

                <!-- Search (Available in all views) -->
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 space-y-3 shrink-0">
                    <form method="GET" action="{{ route('super_admin.students.index') }}" class="relative"
                          x-data="{
                              query: @js($q ?? ''),
                              suggestions: [],
                              showSuggestions: false,
                              async fetchSuggestions() {
                                  if (this.query.length < 1) {
                                      this.suggestions = [];
                                      this.showSuggestions = false;
                                      return;
                                  }
                                  try {
                                      const response = await fetch('{{ route('super_admin.students.search') }}?q=' + encodeURIComponent(this.query));
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
                        @if(!empty($statusFilter))
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
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
                                        <a :href="'{{ route('super_admin.students.index') }}?id=' + student.student_id" 
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
                            <a href="{{ route('super_admin.students.index', ['view_all' => 1]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors flex items-center justify-center" title="View All Students">
                                <i class="fas fa-list"></i>
                            </a>
                        </div>
                    </form>

                    @if($viewState === 'students')
                    @php
                        $statusOptions = [
                            ''           => ['label' => 'All',       'color' => 'bg-slate-100 text-slate-600 hover:bg-slate-200'],
                            'active'     => ['label' => 'Active',    'color' => 'bg-green-100 text-green-700 hover:bg-green-200'],
                            'irregular'  => ['label' => 'Irregular', 'color' => 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'],
                            'withdrawn'  => ['label' => 'Withdrawn', 'color' => 'bg-orange-100 text-orange-700 hover:bg-orange-200'],
                            'archived'   => ['label' => 'Archived',  'color' => 'bg-red-100 text-red-600 hover:bg-red-200'],
                        ];
                        $baseStatusParams = array_merge(request()->except(['status', 'page']), []);
                    @endphp
                    <div class="flex flex-wrap gap-1.5 px-1 pb-1">
                        @foreach($statusOptions as $val => $opt)
                            @php
                                $isActive = strtolower($statusFilter ?? '') === $val;
                                $params   = $val === '' ? $baseStatusParams : array_merge($baseStatusParams, ['status' => $val]);
                            @endphp
                            <a href="{{ route('super_admin.students.index', $params) }}"
                               class="px-2.5 py-1 rounded-full text-xs font-semibold transition-colors {{ $opt['color'] }} {{ $isActive ? 'ring-2 ring-offset-1 ring-current' : '' }}">
                                {{ $opt['label'] }}
                            </a>
                        @endforeach
                    </div>
                    @endif

                </div>

                <!-- List -->
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <ul class="divide-y divide-slate-100">
                        @if($viewState === 'levels')
                            @forelse($levels as $lvl)
                            @php $lvlCount = $levelStudentCounts[$lvl] ?? 0; @endphp
                            <li>
                                <a href="{{ route('super_admin.students.index', array_merge(request()->query(), ['level' => $lvl])) }}" 
                                   class="block p-4 hover:bg-slate-50 transition-colors group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                                <i class="fas fa-layer-group"></i>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-slate-700 group-hover:text-blue-700 transition-colors">{{ $lvl }}</span>
                                                @if($lvlCount > 0)
                                                <div class="text-xs text-slate-400 mt-0.5">{{ number_format($lvlCount) }} {{ Str::plural('student', $lvlCount) }} enrolled</div>
                                                @else
                                                <div class="text-xs text-slate-300 mt-0.5">No enrollments this SY</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($lvlCount > 0)
                                            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full group-hover:bg-blue-100 transition-colors">{{ $lvlCount }}</span>
                                            @endif
                                            <i class="fas fa-chevron-right text-slate-300 group-hover:text-blue-400"></i>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @empty
                            <li class="p-8 text-center text-slate-500 text-sm">No grade levels found.</li>
                            @endforelse
                        
                        @elseif($viewState === 'strands')
                            @forelse($strands as $strand)
                            <li>
                                <a href="{{ route('super_admin.students.index', array_merge(request()->query(), ['level' => $currentLevel, 'strand' => $strand->name])) }}" 
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
                            @php
                                $secCount      = $sectionStudentCounts[$sec->name] ?? 0;
                                $secPromotable = $sectionPromotableCounts[$sec->name] ?? 0;
                                $secSkipped    = $secCount - $secPromotable;
                            @endphp
                            <li>
                                <div class="flex items-center justify-between p-4 hover:bg-slate-50 transition-colors group relative">
                                    <a href="{{ route('super_admin.students.index', array_merge(request()->query(), ['level' => $currentLevel, 'section' => $sec->name])) }}" 
                                       class="flex-1 flex items-center justify-between pr-24">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-slate-700 group-hover:text-indigo-700 transition-colors">{{ $sec->name }}</span>
                                                <div class="text-xs text-slate-400 mt-0.5">
                                                    {{ $secCount }} {{ Str::plural('student', $secCount) }}
                                                </div>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-indigo-400"></i>
                                    </a>
                                    
                                    <!-- Action Buttons -->
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @unless($isReadOnly)
                                        @if($nextLevel)
                                        <!-- Promote Button -->
                                        <button type="button"
                                            @click="promoteTargetSection = '{{ $sec->name }}'; promoteStudentCount = {{ $secPromotable }}; promoteSkipCount = {{ $secSkipped }}; promoteModalOpen = true"
                                            class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all"
                                            title="Promote to {{ $nextLevel }}">
                                            <i class="fas fa-graduation-cap"></i>
                                        </button>
                                        @endif
                                        <!-- Delete Button -->
                                        <form action="{{ route('super_admin.students.destroySection', $sec->id) }}" method="POST"
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
                            @empty
                            <li class="p-8 text-center text-slate-500 text-sm">No sections found for {{ $currentLevel }}.</li>
                            @endforelse

                        @elseif($viewState === 'students')
                            @forelse($students as $student)
                            <li>
                                <a href="{{ route('super_admin.students.index', array_merge(request()->except(['page']), ['id' => $student->student_id, 'create' => null])) }}" 
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
                                                @php $es = strtolower($student->enrollment_status ?? ''); @endphp
                                                @if($es === 'archived')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 uppercase">Archived</span>
                                                @elseif($es === 'irregular')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-700 uppercase">Irregular</span>
                                                @elseif($es === 'withdrawn')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 uppercase">Withdrawn</span>
                                                @endif
                                                @php $sibCount = $student->active_sibling_count; @endphp
                                                @if($sibCount > 0)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700" title="{{ $sibCount }} linked sibling{{ $sibCount > 1 ? 's' : '' }}">
                                                    <i class="fas fa-user-friends mr-0.5 text-[8px]"></i> {{ $sibCount }}
                                                </span>
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
                    <a href="{{ route('super_admin.students.export', array_merge(request()->query(), ['school_year' => $currentSchoolYear])) }}" target="_blank" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm group">
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

                                        @php $headerSibCount = $selectedStudent->active_sibling_count; @endphp
                                        @if($headerSibCount > 0)
                                            <span>•</span>
                                            <span class="flex items-center gap-1 text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full text-xs font-bold border border-purple-200" title="{{ $headerSibCount }} linked sibling{{ $headerSibCount > 1 ? 's' : '' }}">
                                                <i class="fas fa-users text-[10px]"></i> {{ $headerSibCount }} Sibling{{ $headerSibCount > 1 ? 's' : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('super_admin.students.changeStatus', $selectedStudent) }}" method="POST"
                                      x-data="{ status: @js($selectedStudent->enrollment_status ?? 'Active'), original: @js($selectedStudent->enrollment_status ?? 'Active') }"
                                      @submit.prevent="if(status !== original && confirm('Change enrollment status to ' + status + '?')) $el.submit()">
                                    @csrf
                                    @method('PATCH')
                                    <div class="flex items-center gap-1.5">
                                        <label class="text-xs text-slate-500 font-medium whitespace-nowrap">Status:</label>
                                        <select name="enrollment_status" x-model="status"
                                                :class="{
                                                    'bg-green-50 text-green-700 border-green-200':   status === 'Active',
                                                    'bg-yellow-50 text-yellow-700 border-yellow-200': status === 'Irregular',
                                                    'bg-orange-50 text-orange-700 border-orange-200': status === 'Withdrawn',
                                                    'bg-red-50 text-red-600 border-red-200':         status === 'Archived',
                                                }"
                                                class="text-xs font-semibold border rounded-lg px-2 py-1.5 pr-6 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors appearance-none">
                                            <option value="Active">Active</option>
                                            <option value="Irregular">Irregular</option>
                                            <option value="Withdrawn">Withdrawn</option>
                                            <option value="Archived">Archived</option>
                                        </select>
                                        <button type="submit" x-show="status !== original"
                                                class="text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1.5 rounded-lg transition-colors"
                                                x-cloak>
                                            Apply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <div class="flex items-center gap-8 border-b border-transparent -mb-px overflow-x-auto">
                            <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'" class="pb-3 text-sm font-bold border-b-2 transition-all whitespace-nowrap">Personal</button>
                            <button @click="activeTab = 'academic'" :class="activeTab === 'academic' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'" class="pb-3 text-sm font-bold border-b-2 transition-all whitespace-nowrap">Academic</button>
                            <button @click="activeTab = 'parent'" :class="activeTab === 'parent' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'" class="pb-3 text-sm font-bold border-b-2 transition-all whitespace-nowrap">Parent / Guardian</button>
                            <button @click="activeTab = 'siblings'" :class="activeTab === 'siblings' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'" class="pb-3 text-sm font-bold border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5">
                                Siblings
                                @if($selectedStudent && $selectedStudent->active_sibling_count > 0)
                                <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full bg-purple-100 text-purple-700">{{ $selectedStudent->active_sibling_count }}</span>
                                @endif
                            </button>
                            <button @click="activeTab = 'fees'" :class="activeTab === 'fees' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'" class="pb-3 text-sm font-bold border-b-2 transition-all whitespace-nowrap">Fees & Discounts</button>
                            <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700'" class="pb-3 text-sm font-bold border-b-2 transition-all whitespace-nowrap">Recent Activity</button>
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
                            <div x-show="activeTab === 'fees'" class="space-y-6" x-cloak
                                 x-data="{ showAddDiscount: false, showAddCharge: false, showAdjustment: false }">
                                @if(isset($feeAssignment))
                                    {{-- ── Summary Cards ── --}}
                                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                                        <div class="flex items-center justify-between mb-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                                    <i class="fas fa-file-invoice-dollar text-lg"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-bold text-slate-800">Fee Assignment</h3>
                                                    <p class="text-sm text-slate-500">{{ $feeAssignment->school_year }} &middot; {{ $feeAssignment->semester }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @unless($isReadOnly)
                                                <form action="{{ route('super_admin.students.recalculateFees', $selectedStudent) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors border border-blue-200">
                                                        <i class="fas fa-sync-alt"></i> Recalculate
                                                    </button>
                                                </form>
                                                @endunless
                                                <a href="{{ route('admin.enrollment.show', $selectedStudent) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-600 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors border border-slate-200">
                                                    View Full Details <i class="fas fa-arrow-right ml-1"></i>
                                                </a>
                                            </div>
                                        </div>

                                        @php
                                            $remainingBalance = max(0, (float)$feeAssignment->total_amount - (float)($paidAmount ?? 0));
                                        @endphp
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Base Tuition</p>
                                                <p class="text-lg font-bold text-slate-800">₱{{ number_format($feeAssignment->base_tuition, 2) }}</p>
                                            </div>
                                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">+ Charges</p>
                                                <p class="text-lg font-bold text-amber-600">₱{{ number_format($feeAssignment->additional_charges_total, 2) }}</p>
                                            </div>
                                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">− Discounts</p>
                                                <p class="text-lg font-bold text-emerald-600">-₱{{ number_format($feeAssignment->discounts_total, 2) }}</p>
                                            </div>
                                            <div class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                                                <p class="text-[10px] font-bold text-blue-400 uppercase tracking-wider mb-0.5">Net Payable</p>
                                                <p class="text-lg font-bold text-blue-700">₱{{ number_format($feeAssignment->total_amount, 2) }}</p>
                                            </div>
                                        </div>
                                        @if(isset($paidAmount) && $paidAmount > 0)
                                        <div class="grid grid-cols-2 gap-3 mt-3">
                                            <div class="bg-green-50 p-3 rounded-xl border border-green-100">
                                                <p class="text-[10px] font-bold text-green-400 uppercase tracking-wider mb-0.5">Total Paid</p>
                                                <p class="text-lg font-bold text-green-700">₱{{ number_format($paidAmount, 2) }}</p>
                                            </div>
                                            <div class="{{ $remainingBalance > 0 ? 'bg-red-50 border-red-100' : 'bg-green-50 border-green-100' }} p-3 rounded-xl border">
                                                <p class="text-[10px] font-bold {{ $remainingBalance > 0 ? 'text-red-400' : 'text-green-400' }} uppercase tracking-wider mb-0.5">Remaining Balance</p>
                                                <p class="text-lg font-bold {{ $remainingBalance > 0 ? 'text-red-700' : 'text-green-700' }}">₱{{ number_format($remainingBalance, 2) }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    {{-- ── Fee Breakdown ── --}}
                                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                                        <h4 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                                            <i class="fas fa-list text-slate-400"></i> Fee Breakdown
                                        </h4>
                                        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                                            <table class="min-w-full divide-y divide-slate-100">
                                                <thead class="bg-slate-50">
                                                    <tr>
                                                        <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Item</th>
                                                        <th class="px-4 py-2.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-50">
                                                    {{-- Base Tuition --}}
                                                    <tr>
                                                        <td class="px-4 py-2.5 text-sm text-slate-700 font-medium">
                                                            <i class="fas fa-graduation-cap text-blue-400 mr-1.5"></i>
                                                            {{ $feeAssignment->tuitionFee->fee_name ?? ($feeAssignment->tuitionFee->notes ?? 'Base Tuition') }}
                                                            @if($feeAssignment->tuitionFee)
                                                                <span class="text-slate-400 text-xs ml-1">({{ $feeAssignment->tuitionFee->grade_level }})</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2.5 text-sm font-bold text-slate-800 text-right">₱{{ number_format($feeAssignment->base_tuition, 2) }}</td>
                                                    </tr>
                                                    {{-- Additional Charges --}}
                                                    @forelse($feeAssignment->additionalCharges as $charge)
                                                        <tr class="group hover:bg-slate-50 transition-colors">
                                                            <td class="px-4 py-2.5 text-sm text-slate-700">
                                                                <i class="fas fa-plus-circle text-amber-400 mr-1.5"></i>
                                                                {{ $charge->charge_name }}
                                                                <span class="text-slate-400 text-xs ml-1">
                                                                    @if($charge->applies_to === 'all') (All Levels) @else ({{ implode(', ', $charge->applicable_grades ?? []) }}) @endif
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-2.5 text-sm font-bold text-amber-600 text-right flex items-center justify-end gap-2">
                                                                +₱{{ number_format($charge->amount, 2) }}
                                                                @unless($isReadOnly)
                                                                <form action="{{ route('super_admin.students.charges.remove', ['student' => $selectedStudent, 'charge' => $charge->id]) }}" method="POST" class="inline opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Remove this charge?');">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="text-slate-400 hover:text-red-500 text-xs"><i class="fas fa-times"></i></button>
                                                                </form>
                                                                @endunless
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="2" class="px-4 py-2 text-xs text-slate-400 italic">No additional charges</td>
                                                        </tr>
                                                    @endforelse
                                                    {{-- Adjustments (charge type) --}}
                                                    @if(isset($feeAdjustments))
                                                        @foreach($feeAdjustments->where('type', 'charge') as $adj)
                                                        <tr>
                                                            <td class="px-4 py-2.5 text-sm text-slate-700">
                                                                <i class="fas fa-receipt text-orange-400 mr-1.5"></i>
                                                                {{ $adj->name }}
                                                                <span class="text-slate-400 text-xs ml-1">(Adjustment)</span>
                                                            </td>
                                                            <td class="px-4 py-2.5 text-sm font-bold text-amber-600 text-right">+₱{{ number_format($adj->amount, 2) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    @endif
                                                    {{-- Discounts --}}
                                                    @forelse($feeAssignment->discounts as $discount)
                                                        <tr class="group hover:bg-slate-50 transition-colors">
                                                            <td class="px-4 py-2.5 text-sm text-slate-700">
                                                                <i class="fas fa-tag text-emerald-400 mr-1.5"></i>
                                                                {{ $discount->discount_name }}
                                                                <span class="text-slate-400 text-xs ml-1">({{ $discount->type === 'percentage' ? $discount->value.'%' : 'Fixed' }})</span>
                                                            </td>
                                                            <td class="px-4 py-2.5 text-sm font-bold text-emerald-600 text-right flex items-center justify-end gap-2">
                                                                @php
                                                                    $appliedAmt = $discount->pivot->applied_amount;
                                                                    $displayAmt = ($appliedAmt !== null && (float)$appliedAmt > 0)
                                                                        ? (float)$appliedAmt
                                                                        : $discount->calculateDiscountAmount($feeAssignment->tuitionFee ? $feeAssignment->tuitionFee->amount : $feeAssignment->base_tuition);
                                                                @endphp
                                                                -₱{{ number_format($displayAmt, 2) }}
                                                                @unless($isReadOnly)
                                                                <form action="{{ route('admin.enrollment.discounts.destroy', ['student' => $selectedStudent, 'discount' => $discount->id]) }}" method="POST" class="inline opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Remove this discount?');">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="text-slate-400 hover:text-red-500 text-xs"><i class="fas fa-times"></i></button>
                                                                </form>
                                                                @endunless
                                                            </td>
                                                        </tr>
                                                    @empty
                                                    @endforelse
                                                    {{-- Adjustments (discount type) --}}
                                                    @if(isset($feeAdjustments))
                                                        @foreach($feeAdjustments->where('type', 'discount') as $adj)
                                                        <tr>
                                                            <td class="px-4 py-2.5 text-sm text-slate-700">
                                                                <i class="fas fa-percentage text-green-400 mr-1.5"></i>
                                                                {{ $adj->name }}
                                                                <span class="text-slate-400 text-xs ml-1">(Adjustment)</span>
                                                            </td>
                                                            <td class="px-4 py-2.5 text-sm font-bold text-emerald-600 text-right">-₱{{ number_format($adj->amount, 2) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    @endif
                                                    {{-- SHS Voucher exclusion indicator --}}
                                                    @php
                                                        $showSiblingExclusion = $selectedStudent->is_shs_voucher;
                                                        $siblingDiscountApplied = $feeAssignment->discounts->contains(function($d) {
                                                            return str_contains(strtolower($d->discount_name), 'sibling');
                                                        });
                                                    @endphp
                                                    @if($showSiblingExclusion && !$siblingDiscountApplied)
                                                        <tr>
                                                            <td class="px-4 py-2 text-sm text-slate-400 italic"><i class="fas fa-ban text-slate-300 mr-1.5"></i>Sibling Discount</td>
                                                            <td class="px-4 py-2 text-sm text-slate-400 italic text-right">Not Applicable (SHS Voucher)</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                                <tfoot class="bg-blue-50 border-t-2 border-blue-100">
                                                    <tr>
                                                        <td class="px-4 py-3 text-sm font-extrabold text-blue-800 uppercase tracking-wide">Total Payable</td>
                                                        <td class="px-4 py-3 text-base font-extrabold text-blue-800 text-right">₱{{ number_format($feeAssignment->total_amount, 2) }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- ── Quick Actions (Add Discount / Add Charge / Fee Adjustment) ── --}}
                                    @unless($isReadOnly)
                                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                                        <h4 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                                            <i class="fas fa-bolt text-amber-400"></i> Quick Actions
                                        </h4>
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <button @click="showAddDiscount = !showAddDiscount; showAddCharge = false; showAdjustment = false"
                                                    :class="showAddDiscount ? 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-300' : 'bg-slate-100 text-slate-700 hover:bg-emerald-50 hover:text-emerald-700'"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg transition-all">
                                                <i class="fas fa-tag"></i> Add Discount
                                            </button>
                                            <button @click="showAddCharge = !showAddCharge; showAddDiscount = false; showAdjustment = false"
                                                    :class="showAddCharge ? 'bg-amber-100 text-amber-800 ring-1 ring-amber-300' : 'bg-slate-100 text-slate-700 hover:bg-amber-50 hover:text-amber-700'"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg transition-all">
                                                <i class="fas fa-plus-circle"></i> Add Charge
                                            </button>
                                            <button @click="showAdjustment = !showAdjustment; showAddDiscount = false; showAddCharge = false"
                                                    :class="showAdjustment ? 'bg-purple-100 text-purple-800 ring-1 ring-purple-300' : 'bg-slate-100 text-slate-700 hover:bg-purple-50 hover:text-purple-700'"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg transition-all">
                                                <i class="fas fa-sliders-h"></i> Manual Adjustment
                                            </button>
                                        </div>

                                        {{-- ── Add Discount Panel ── --}}
                                        <div x-show="showAddDiscount" x-cloak x-transition class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-3">
                                            <h5 class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-3"><i class="fas fa-tag mr-1"></i> Apply a Discount</h5>
                                            @if($availableDiscounts->count() > 0)
                                                <form action="{{ route('admin.enrollment.discounts.store', $selectedStudent) }}" method="POST" class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
                                                    @csrf
                                                    <div class="flex-1 w-full">
                                                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Select Discount</label>
                                                        <select name="discount_id" required class="w-full bg-white border border-emerald-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                                                            <option value="">— Choose a discount —</option>
                                                            @foreach($availableDiscounts as $discount)
                                                                <option value="{{ $discount->id }}">
                                                                    {{ $discount->discount_name }}
                                                                    ({{ $discount->type === 'percentage' ? $discount->value.'%' : '₱'.number_format($discount->value, 2) }})
                                                                    {{ $discount->is_automatic ? '· Auto' : '· Manual' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                                                        <i class="fas fa-check"></i> Apply Discount
                                                    </button>
                                                </form>
                                            @else
                                                <p class="text-xs text-emerald-700 italic">No eligible discounts available for this student's grade level, or all have already been applied.</p>
                                            @endif
                                        </div>

                                        {{-- ── Add Charge Panel ── --}}
                                        <div x-show="showAddCharge" x-cloak x-transition class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-3">
                                            <h5 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-3"><i class="fas fa-plus-circle mr-1"></i> Attach Additional Charge</h5>
                                            @if($availableCharges->count() > 0)
                                                <form action="{{ route('super_admin.students.charges.add', $selectedStudent) }}" method="POST" class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
                                                    @csrf
                                                    <div class="flex-1 w-full">
                                                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Select Charge</label>
                                                        <select name="charge_id" required class="w-full bg-white border border-amber-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300">
                                                            <option value="">— Choose a charge —</option>
                                                            @foreach($availableCharges as $charge)
                                                                <option value="{{ $charge->id }}">
                                                                    {{ $charge->charge_name }} — ₱{{ number_format($charge->amount, 2) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 text-white text-xs font-bold rounded-lg hover:bg-amber-700 transition-colors shadow-sm">
                                                        <i class="fas fa-plus"></i> Attach Charge
                                                    </button>
                                                </form>
                                            @else
                                                <p class="text-xs text-amber-700 italic">All applicable charges are already attached, or no charges exist for this grade level.</p>
                                            @endif
                                        </div>

                                        {{-- ── Manual Adjustment Panel ── --}}
                                        <div x-show="showAdjustment" x-cloak x-transition class="bg-purple-50 border border-purple-200 rounded-xl p-4 mb-3">
                                            <h5 class="text-xs font-bold text-purple-800 uppercase tracking-wider mb-3"><i class="fas fa-sliders-h mr-1"></i> One-Off Fee Adjustment</h5>
                                            <p class="text-xs text-purple-600 mb-3">Add a manual charge (e.g., late fee, lost-ID penalty) or a manual discount (e.g., scholarship override). This is a one-time adjustment to the student's account.</p>
                                            <form action="{{ route('super_admin.students.adjustments.store', $selectedStudent) }}" method="POST" class="space-y-3">
                                                @csrf
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Type</label>
                                                        <select name="type" required class="w-full bg-white border border-purple-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                                                            <option value="charge">Charge (+)</option>
                                                            <option value="discount">Discount (−)</option>
                                                        </select>
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Description</label>
                                                        <input type="text" name="name" required placeholder="e.g., Late Enrollment Penalty" class="w-full bg-white border border-purple-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Amount (₱)</label>
                                                        <input type="number" name="amount" required min="0.01" step="0.01" placeholder="500.00" class="w-full bg-white border border-purple-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                                                    </div>
                                                    <div>
                                                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Remarks <span class="text-slate-400">(optional)</span></label>
                                                        <input type="text" name="remarks" placeholder="Reason or notes" class="w-full bg-white border border-purple-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                                                    </div>
                                                </div>
                                                <div class="flex justify-end">
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 text-white text-xs font-bold rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                                                        <i class="fas fa-check"></i> Apply Adjustment
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endunless

                                    {{-- ── Adjustments History ── --}}
                                    @if(isset($feeAdjustments) && $feeAdjustments->count() > 0)
                                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                                        <h4 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                                            <i class="fas fa-history text-slate-400"></i> Fee Adjustments
                                            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ $feeAdjustments->count() }}</span>
                                        </h4>
                                        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                                            <table class="min-w-full divide-y divide-slate-100">
                                                <thead class="bg-slate-50">
                                                    <tr>
                                                        <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Adjustment</th>
                                                        <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Type</th>
                                                        <th class="px-4 py-2.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                                                        <th class="px-4 py-2.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-50">
                                                    @foreach($feeAdjustments as $adj)
                                                    <tr>
                                                        <td class="px-4 py-2.5 text-sm text-slate-700">
                                                            {{ $adj->name }}
                                                            @if($adj->remarks)
                                                                <span class="block text-xs text-slate-400 mt-0.5">{{ $adj->remarks }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2.5">
                                                            @if($adj->type === 'charge')
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-amber-100 text-amber-700"><i class="fas fa-plus text-[8px]"></i> Charge</span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-emerald-100 text-emerald-700"><i class="fas fa-minus text-[8px]"></i> Discount</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2.5 text-sm font-bold text-right {{ $adj->type === 'charge' ? 'text-amber-600' : 'text-emerald-600' }}">
                                                            {{ $adj->type === 'charge' ? '+' : '-' }}₱{{ number_format($adj->amount, 2) }}
                                                        </td>
                                                        <td class="px-4 py-2.5 text-xs text-slate-400 text-right">{{ $adj->created_at->format('M d, Y') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- ── Fee History / Changelog ── --}}
                                    @if(isset($feeHistory) && $feeHistory->count() > 0)
                                    <div x-data="{ historyExpanded: false }" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                                                <i class="fas fa-clock-rotate-left text-indigo-400"></i> Fee History
                                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ $feeHistory->count() }}</span>
                                            </h4>
                                            @if($feeHistory->count() > 5)
                                            <button @click="historyExpanded = !historyExpanded" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                                <span x-text="historyExpanded ? 'Show Less' : 'Show All ({{ $feeHistory->count() }})'"></span>
                                                <i class="fas fa-chevron-down ml-1 text-[10px] transition-transform" :class="historyExpanded && 'rotate-180'"></i>
                                            </button>
                                            @endif
                                        </div>

                                        <div class="relative">
                                            {{-- Timeline line --}}
                                            <div class="absolute left-[15px] top-0 bottom-0 w-px bg-gradient-to-b from-indigo-200 via-slate-200 to-transparent"></div>

                                            <div class="space-y-0">
                                                @foreach($feeHistory as $idx => $log)
                                                <div x-show="historyExpanded || {{ $idx }} < 5"
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 translate-y-1"
                                                     x-transition:enter-end="opacity-100 translate-y-0"
                                                     class="relative flex items-start gap-3 py-2.5 pl-1 group">
                                                    {{-- Timeline dot --}}
                                                    @php
                                                        $actionColors = [
                                                            'Charge Added' => 'bg-amber-400 ring-amber-100',
                                                            'Charge Removed' => 'bg-amber-300 ring-amber-50',
                                                            'Discount Assigned' => 'bg-emerald-400 ring-emerald-100',
                                                            'Discount Removed' => 'bg-red-400 ring-red-100',
                                                            'Fee Adjustment Applied' => 'bg-purple-400 ring-purple-100',
                                                            'Fees Recalculated' => 'bg-blue-400 ring-blue-100',
                                                            'Student Enrolled' => 'bg-sky-400 ring-sky-100',
                                                            'Fee Assignment Created' => 'bg-sky-400 ring-sky-100',
                                                            'Payment Added' => 'bg-green-400 ring-green-100',
                                                            'Payment Approved' => 'bg-green-500 ring-green-100',
                                                        ];
                                                        $actionIcons = [
                                                            'Charge Added' => 'fa-plus',
                                                            'Charge Removed' => 'fa-minus',
                                                            'Discount Assigned' => 'fa-tag',
                                                            'Discount Removed' => 'fa-tag',
                                                            'Fee Adjustment Applied' => 'fa-sliders-h',
                                                            'Fees Recalculated' => 'fa-calculator',
                                                            'Student Enrolled' => 'fa-user-plus',
                                                            'Fee Assignment Created' => 'fa-file-invoice',
                                                            'Payment Added' => 'fa-money-bill-wave',
                                                            'Payment Approved' => 'fa-check-circle',
                                                        ];
                                                        $dotColor = $actionColors[$log->action] ?? 'bg-slate-400 ring-slate-100';
                                                        $icon = $actionIcons[$log->action] ?? 'fa-circle';
                                                        $isError = str_contains($log->action, 'FAILED');
                                                        if ($isError) {
                                                            $dotColor = 'bg-red-500 ring-red-100';
                                                            $icon = 'fa-exclamation-triangle';
                                                        }
                                                    @endphp
                                                    <div class="relative z-10 flex-shrink-0 w-[30px] h-[30px] rounded-full {{ $dotColor }} ring-4 flex items-center justify-center shadow-sm">
                                                        <i class="fas {{ $icon }} text-white text-[10px]"></i>
                                                    </div>

                                                    {{-- Content --}}
                                                    <div class="flex-1 min-w-0 -mt-0.5">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <span class="text-xs font-bold text-slate-800">{{ str_replace('_', ' ', $log->action) }}</span>
                                                            <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        @if($log->details)
                                                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $log->details }}</p>
                                                        @endif

                                                        {{-- Changed values (expandable) --}}
                                                        @if($log->new_values || $log->old_values)
                                                        <div x-data="{ showDetails: false }" class="mt-1">
                                                            <button @click="showDetails = !showDetails" class="text-[10px] font-semibold text-indigo-500 hover:text-indigo-700 transition-colors">
                                                                <i class="fas fa-code text-[8px] mr-0.5"></i>
                                                                <span x-text="showDetails ? 'Hide details' : 'View details'"></span>
                                                            </button>
                                                            <div x-show="showDetails" x-cloak x-transition class="mt-1.5 bg-slate-50 border border-slate-100 rounded-lg p-2.5 text-[11px] font-mono text-slate-600 space-y-1">
                                                                @if($log->old_values)
                                                                <div>
                                                                    <span class="text-red-400 font-bold text-[10px] uppercase tracking-wider">Before:</span>
                                                                    <div class="mt-0.5 space-y-0.5">
                                                                        @foreach((array)$log->old_values as $key => $val)
                                                                            <div class="flex gap-2"><span class="text-slate-400">{{ $key }}:</span> <span class="text-slate-700">{{ is_array($val) ? json_encode($val) : $val }}</span></div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                @if($log->new_values)
                                                                <div>
                                                                    <span class="text-emerald-500 font-bold text-[10px] uppercase tracking-wider">After:</span>
                                                                    <div class="mt-0.5 space-y-0.5">
                                                                        @foreach((array)$log->new_values as $key => $val)
                                                                            <div class="flex gap-2"><span class="text-slate-400">{{ $key }}:</span> <span class="text-slate-700">{{ is_array($val) ? json_encode($val) : $val }}</span></div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @endif

                                                        {{-- Performed-by & meta --}}
                                                        <div class="flex items-center gap-3 mt-1">
                                                            @if($log->user)
                                                            <span class="text-[10px] text-slate-400">
                                                                <i class="fas fa-user text-[8px] mr-0.5"></i>
                                                                {{ $log->user->name ?? $log->user->email ?? 'System' }}
                                                                @if($log->user_role)
                                                                    <span class="ml-0.5 px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded text-[9px] font-semibold uppercase">{{ $log->user_role }}</span>
                                                                @endif
                                                            </span>
                                                            @else
                                                            <span class="text-[10px] text-slate-400"><i class="fas fa-robot text-[8px] mr-0.5"></i> System</span>
                                                            @endif
                                                            <span class="text-[10px] text-slate-300">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                            {{-- Fade-out overlay when collapsed --}}
                                            @if($feeHistory->count() > 5)
                                            <div x-show="!historyExpanded" class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
                                            @endif
                                        </div>

                                        @if($feeHistory->count() > 5)
                                        <div x-show="!historyExpanded" class="text-center mt-2">
                                            <button @click="historyExpanded = true" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                                <i class="fas fa-arrow-down mr-1 text-[10px]"></i> Show {{ $feeHistory->count() - 5 }} more entries
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                    @else
                                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                                        <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2 mb-3">
                                            <i class="fas fa-clock-rotate-left text-indigo-400"></i> Fee History
                                        </h4>
                                        <div class="text-center py-6">
                                            <i class="fas fa-history text-slate-200 text-3xl mb-2"></i>
                                            <p class="text-xs text-slate-400">No fee-related activity recorded yet.</p>
                                        </div>
                                    </div>
                                    @endif

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

                            <!-- Siblings Tab -->
                            <div x-show="activeTab === 'siblings'" class="space-y-6" x-cloak
                                 x-data="{
                                    siblings: [],
                                    familyCount: 0,
                                    discountEligible: false,
                                    discountName: '',
                                    discountValue: '',
                                    parentName: '',
                                    parentId: null,
                                    loading: true,
                                    searchQuery: '',
                                    searchResults: [],
                                    searching: false,
                                    showSearch: false,
                                    linking: false,
                                    unlinking: null,
                                    message: '',
                                    messageType: 'success',
                                    async fetchSiblings() {
                                        this.loading = true;
                                        try {
                                            const res = await fetch('{{ $selectedStudent ? route('super_admin.students.siblings', $selectedStudent) : '' }}');
                                            const data = await res.json();
                                            this.siblings = data.siblings || [];
                                            this.familyCount = data.family_count || 0;
                                            this.discountEligible = data.discount_eligible || false;
                                            this.discountName = data.discount_name || '';
                                            this.discountValue = data.discount_value || '';
                                            this.parentName = data.parent_name || '';
                                            this.parentId = data.parent_id;
                                        } catch(e) { console.error(e); }
                                        this.loading = false;
                                    },
                                    async searchSiblings() {
                                        if (this.searchQuery.length < 2) { this.searchResults = []; return; }
                                        this.searching = true;
                                        try {
                                            const res = await fetch('{{ route('super_admin.students.searchForSibling') }}?q=' + encodeURIComponent(this.searchQuery) + '&exclude={{ $selectedStudent?->student_id ?? '' }}');
                                            this.searchResults = await res.json();
                                        } catch(e) { this.searchResults = []; }
                                        this.searching = false;
                                    },
                                    async linkSibling(siblingId) {
                                        this.linking = true;
                                        this.message = '';
                                        try {
                                            const res = await fetch('{{ $selectedStudent ? route('super_admin.students.siblings.link', $selectedStudent) : '' }}', {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                                                body: JSON.stringify({ sibling_id: siblingId })
                                            });
                                            const data = await res.json();
                                            if (data.error) {
                                                this.message = data.error;
                                                this.messageType = 'error';
                                            } else {
                                                this.message = data.message;
                                                this.messageType = 'success';
                                                this.searchQuery = '';
                                                this.searchResults = [];
                                                this.showSearch = false;
                                                await this.fetchSiblings();
                                            }
                                        } catch(e) {
                                            this.message = 'Failed to link sibling.';
                                            this.messageType = 'error';
                                        }
                                        this.linking = false;
                                        setTimeout(() => this.message = '', 5000);
                                    },
                                    async unlinkSibling(siblingId) {
                                        if (!confirm('Are you sure you want to unlink this sibling? This will remove the shared parent connection and may affect family discount eligibility.')) return;
                                        this.unlinking = siblingId;
                                        this.message = '';
                                        try {
                                            const res = await fetch('{{ $selectedStudent ? route('super_admin.students.siblings.unlink', $selectedStudent) : '' }}', {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                                                body: JSON.stringify({ sibling_id: siblingId })
                                            });
                                            const data = await res.json();
                                            if (data.error) {
                                                this.message = data.error;
                                                this.messageType = 'error';
                                            } else {
                                                this.message = data.message;
                                                this.messageType = 'success';
                                                await this.fetchSiblings();
                                            }
                                        } catch(e) {
                                            this.message = 'Failed to unlink sibling.';
                                            this.messageType = 'error';
                                        }
                                        this.unlinking = null;
                                        setTimeout(() => this.message = '', 5000);
                                    }
                                 }"
                                 x-init="fetchSiblings()">

                                <!-- Family Discount Status Banner -->
                                <div :class="discountEligible ? 'bg-green-50 border-green-200' : 'bg-slate-50 border-slate-200'" class="rounded-2xl border p-5 flex items-start gap-4">
                                    <div :class="discountEligible ? 'bg-green-100 text-green-600' : 'bg-slate-200 text-slate-500'" class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-users text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="text-sm font-bold" :class="discountEligible ? 'text-green-800' : 'text-slate-700'">Family Discount Status</h3>
                                            <span x-show="discountEligible" class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] uppercase font-bold">Eligible</span>
                                            <span x-show="!discountEligible && !loading" class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px] uppercase font-bold">Not Eligible</span>
                                        </div>
                                        <p x-show="discountEligible" class="text-sm text-green-700">
                                            <span x-text="discountName"></span> (<span x-text="discountValue"></span>) applies — 
                                            <strong x-text="familyCount"></strong> enrolled family member(s) via parent <strong x-text="parentName"></strong>.
                                        </p>
                                        <p x-show="!discountEligible && !loading" class="text-sm text-slate-600">
                                            <template x-if="familyCount < 2">
                                                <span>Sibling discount requires at least 2 enrolled siblings sharing the same parent. Link a sibling below to enable family discounts.</span>
                                            </template>
                                            <template x-if="familyCount >= 2">
                                                <span>Family has <strong x-text="familyCount"></strong> members but no active sibling discount is configured.</span>
                                            </template>
                                        </p>
                                        <p x-show="loading" class="text-sm text-slate-400"><i class="fas fa-spinner fa-spin mr-1"></i> Loading family info...</p>
                                    </div>
                                </div>

                                <!-- Flash Message -->
                                <div x-show="message" x-transition class="rounded-xl px-4 py-3 text-sm font-medium border" :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'">
                                    <span x-text="message"></span>
                                </div>

                                <!-- Linked Siblings List -->
                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                                    <div class="flex items-center justify-between mb-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
                                                <i class="fas fa-user-friends text-lg"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-800">Linked Siblings</h3>
                                                <p class="text-sm text-slate-500">Students sharing a parent/guardian with this student</p>
                                            </div>
                                        </div>
                                        @unless($isReadOnly ?? false)
                                        <button @click="showSearch = !showSearch" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-purple-200 transition-all flex items-center gap-2">
                                            <i class="fas fa-plus text-xs"></i>
                                            Link Sibling
                                        </button>
                                        @endunless
                                    </div>

                                    <!-- Search & Link New Sibling -->
                                    <div x-show="showSearch" x-transition class="mb-5 p-4 bg-purple-50/50 rounded-xl border border-purple-100">
                                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-2 block">Search Student to Link as Sibling</label>
                                        <div class="relative">
                                            <input type="text" x-model="searchQuery" @input.debounce.400ms="searchSiblings()"
                                                   placeholder="Type student name or ID..."
                                                   class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 pr-10 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all shadow-sm text-sm" />
                                            <div x-show="searching" class="absolute right-3 top-1/2 -translate-y-1/2">
                                                <i class="fas fa-spinner fa-spin text-slate-400 text-sm"></i>
                                            </div>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-1.5">The selected student will be linked to the same parent as this student. Family discounts will be recalculated automatically.</p>

                                        <!-- Search Results -->
                                        <div x-show="searchResults.length > 0" class="mt-3 space-y-2 max-h-64 overflow-y-auto">
                                            <template x-for="result in searchResults" :key="result.student_id">
                                                <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-slate-200 hover:border-purple-300 transition-colors">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm">
                                                            <span x-text="result.full_name.charAt(0)"></span>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-bold text-slate-800" x-text="result.full_name"></div>
                                                            <div class="text-xs text-slate-500">
                                                                <span x-text="result.student_id"></span> •
                                                                <span x-text="result.level"></span> -
                                                                <span x-text="result.section"></span> •
                                                                <span x-text="result.school_year"></span>
                                                            </div>
                                                            <div x-show="result.parent_name" class="text-xs text-blue-600 mt-0.5">
                                                                <i class="fas fa-user text-[9px] mr-0.5"></i> Parent: <span x-text="result.parent_name"></span>
                                                                <template x-if="result.parent_id == parentId">
                                                                    <span class="text-green-600 font-bold ml-1">(Same parent — already a sibling!)</span>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button @click="linkSibling(result.student_id)" :disabled="linking" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg transition-colors disabled:opacity-50 flex items-center gap-1.5">
                                                        <i x-show="linking" class="fas fa-spinner fa-spin text-[10px]"></i>
                                                        <i x-show="!linking" class="fas fa-link text-[10px]"></i>
                                                        <span>Link</span>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                        <p x-show="searchQuery.length >= 2 && !searching && searchResults.length === 0" class="mt-2 text-xs text-slate-500 italic">No students found matching your search.</p>
                                    </div>

                                    <!-- Siblings Table -->
                                    <div x-show="!loading && siblings.length > 0">
                                        <div class="space-y-2">
                                            <template x-for="sib in siblings" :key="sib.student_id">
                                                <div class="flex items-center justify-between p-4 rounded-xl border transition-colors"
                                                     :class="sib.same_year ? 'bg-white border-slate-200' : 'bg-slate-50 border-slate-100'">
                                                    <div class="flex items-center gap-3.5">
                                                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm"
                                                             :class="sib.enrollment_status === 'Active' ? 'bg-purple-100 text-purple-700' : 'bg-slate-200 text-slate-500'">
                                                            <span x-text="sib.full_name.charAt(0)"></span>
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-2">
                                                                <a :href="'{{ route('super_admin.students.index') }}?id=' + sib.student_id" class="text-sm font-bold text-slate-800 hover:text-blue-600 transition-colors" x-text="sib.full_name"></a>
                                                                <span class="px-2 py-0.5 rounded-full text-[10px] uppercase font-bold"
                                                                      :class="{
                                                                          'bg-green-100 text-green-700': sib.enrollment_status === 'Active',
                                                                          'bg-amber-100 text-amber-700': sib.enrollment_status === 'Irregular',
                                                                          'bg-red-100 text-red-700': sib.enrollment_status === 'Withdrawn',
                                                                          'bg-slate-200 text-slate-600': sib.enrollment_status === 'Archived'
                                                                      }"
                                                                      x-text="sib.enrollment_status"></span>
                                                                <span x-show="!sib.same_year" class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-600 text-[10px] uppercase font-bold">Different SY</span>
                                                            </div>
                                                            <div class="text-xs text-slate-500 mt-0.5">
                                                                <span x-text="sib.student_id"></span> •
                                                                <span x-text="sib.level"></span> -
                                                                <span x-text="sib.section"></span> •
                                                                <span x-text="sib.school_year"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @unless($isReadOnly ?? false)
                                                    <button @click="unlinkSibling(sib.student_id)" :disabled="unlinking === sib.student_id"
                                                            class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Unlink sibling">
                                                        <i x-show="unlinking !== sib.student_id" class="fas fa-unlink text-sm"></i>
                                                        <i x-show="unlinking === sib.student_id" class="fas fa-spinner fa-spin text-sm"></i>
                                                    </button>
                                                    @endunless
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Empty State -->
                                    <div x-show="!loading && siblings.length === 0" class="text-center py-8">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-user-friends text-2xl text-slate-300"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-600 mb-1">No Siblings Linked</h4>
                                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                            This student has no linked siblings. Click "Link Sibling" above to associate another student sharing the same parent/guardian. 
                                            Family discounts are automatically applied when 2+ siblings are linked.
                                        </p>
                                    </div>

                                    <!-- Loading State -->
                                    <div x-show="loading" class="flex items-center justify-center py-8">
                                        <i class="fas fa-spinner fa-spin text-slate-400 text-lg mr-2"></i>
                                        <span class="text-sm text-slate-500">Loading siblings...</span>
                                    </div>
                                </div>

                                <!-- How It Works -->
                                <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5">
                                    <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-2"><i class="fas fa-info-circle mr-1"></i> How Sibling Linking Works</h4>
                                    <ul class="text-xs text-amber-700 space-y-1.5">
                                        <li><i class="fas fa-check text-amber-500 mr-1.5"></i> Siblings are students who share the same parent/guardian contact</li>
                                        <li><i class="fas fa-check text-amber-500 mr-1.5"></i> When you link a sibling, they are connected to this student's primary parent</li>
                                        <li><i class="fas fa-check text-amber-500 mr-1.5"></i> <strong>Family discount is automatically recalculated</strong> for all family members when siblings are linked or unlinked</li>
                                        <li><i class="fas fa-check text-amber-500 mr-1.5"></i> Sibling discount applies when 2+ active siblings are enrolled in the same school year</li>
                                        <li><i class="fas fa-exclamation-triangle text-amber-500 mr-1.5"></i> SHS Voucher recipients are excluded from sibling discounts</li>
                                    </ul>
                                </div>
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
                        <a href="{{ route('super_admin.students.index', ['create' => true]) }}" class="mt-6 px-6 py-2.5 bg-blue-600 text-white rounded-xl font-semibold shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all">
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
                        <form action="{{ route('super_admin.students.storeSection') }}" method="POST">
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

        <!-- Promote Section Modal -->
        @if($viewState === 'sections' && $nextLevel)
        <template x-teleport="body">
            <div x-show="promoteModalOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="promoteModalOpen" @click="promoteModalOpen = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="promoteModalOpen"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form action="{{ route('super_admin.students.promoteSection') }}" method="POST">
                            @csrf
                            <input type="hidden" name="level"       value="{{ $currentLevel }}">
                            <input type="hidden" name="school_year" value="{{ $currentSchoolYear }}">
                            <input type="hidden" name="section"     :value="promoteTargetSection">

                            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-green-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                        <i class="fas fa-graduation-cap text-green-600"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Promote Section</h3>
                                        <div class="mt-1">
                                            <p class="text-sm text-gray-500">
                                                Moving <strong x-text="promoteStudentCount"></strong> student(s) from
                                                <strong>{{ $currentLevel }} – <span x-text="promoteTargetSection"></span></strong>
                                                ({{ $currentSchoolYear }}) →
                                                <span class="text-green-700 font-semibold">{{ $nextLevel }}</span>.
                                            </p>
                                        </div>

                                        <div class="mt-4 space-y-4">
                                            <!-- New School Year -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">New School Year</label>
                                                <select name="new_school_year" required
                                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-green-500 focus:border-green-500">
                                                    @foreach($schoolYears as $sy)
                                                        <option value="{{ $sy }}" {{ $sy === $activeSy ? 'selected' : '' }}>{{ $sy }}</option>
                                                    @endforeach
                                                    @php
                                                        // Auto-suggest next school year (e.g. 2025-2026 → 2026-2027)
                                                        $parts = explode('-', $currentSchoolYear);
                                                        if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                                                            $suggestedSy = ($parts[0]+1) . '-' . ($parts[1]+1);
                                                        } else {
                                                            $suggestedSy = null;
                                                        }
                                                    @endphp
                                                    @if($suggestedSy && !$schoolYears->contains($suggestedSy))
                                                        <option value="{{ $suggestedSy }}" selected>{{ $suggestedSy }} (next year)</option>
                                                    @endif
                                                </select>
                                                <p class="mt-1 text-xs text-gray-400">Choose the school year the promoted students will be enrolled in.</p>
                                            </div>

                                            <!-- Keep Section Name -->
                                            <div class="flex items-start gap-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                                <div class="flex items-center h-5 mt-0.5">
                                                    <input id="keep_section" name="keep_section" type="checkbox" value="1"
                                                           class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                                </div>
                                                <div>
                                                    <label for="keep_section" class="text-sm font-medium text-gray-700 cursor-pointer">Keep same section name</label>
                                                    <p class="text-xs text-gray-500 mt-0.5">If unchecked, section is cleared so students can be re-assigned to new sections in {{ $nextLevel }}.</p>
                                                </div>
                                            </div>

                                            <!-- Warning -->
                                            <div class="flex items-start gap-2 p-3 bg-red-50 rounded-lg border border-red-200 text-xs text-red-700">
                                                <i class="fas fa-exclamation-triangle mt-0.5 flex-shrink-0"></i>
                                                <span>This updates all <strong x-text="promoteStudentCount"></strong> eligible student(s) at once and <strong>cannot be undone in bulk</strong>. Existing fee records for {{ $currentSchoolYear }} are preserved. New fee assignments will be generated for {{ $nextLevel }} when each student is first viewed.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit"
                                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    <i class="fas fa-graduation-cap mr-2"></i> Promote Students
                                </button>
                                <button type="button" @click="promoteModalOpen = false"
                                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
                        <form action="{{ route('super_admin.students.storeStrand') }}" method="POST">
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

        <!-- CSV Import Modal removed; importer is centralized under Bulk Operations -->
    </div>
</body>
</html>
