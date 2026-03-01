@props(['student' => null, 'mode' => 'create', 'isReadOnly' => false])

<form method="POST" action="{{ $mode === 'create' ? route('admin.students.store') : route('admin.students.update', $student) }}" class="space-y-8">
    @csrf
    @if($mode === 'edit')
        @method('PUT')
    @endif
    
    <fieldset @disabled($isReadOnly) class="contents">

    <!-- Error Display -->
    @if ($errors->any())
        <div class="mb-6 border border-red-200 text-red-800 bg-red-50 rounded-xl px-4 py-3 shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Personal Information -->
    <div x-show="activeTab === 'personal' || isCreating" class="space-y-6">
        @if($mode === 'edit')
        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Personal Information</h3>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6"
             x-data="{
                sections: [],
                loading: false,
                strandName: '{{ old('strand', $student->strand ?? '') }}',
                async fetchSections() {
                    if (!gradeLevel) { this.sections = []; return; }
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        params.set('level', gradeLevel);
                        if (['Grade 11','Grade 12'].includes(gradeLevel) && this.strandName) {
                            params.set('strand', this.strandName);
                        }
                        const res = await fetch('{{ route('admin.students.sections.list') }}' + '?' + params.toString());
                        const data = await res.json();
                        this.sections = Array.isArray(data) ? data : [];
                    } catch (e) {
                        this.sections = [];
                    } finally {
                        this.loading = false;
                    }
                }
             }"
             x-init="fetchSections()"
             x-effect="fetchSections()">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">First Name <span class="text-red-500">*</span></label>
                <input name="first_name" value="{{ old('first_name', $student->first_name ?? '') }}" type="text" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required />
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Last Name <span class="text-red-500">*</span></label>
                <input name="last_name" value="{{ old('last_name', $student->last_name ?? '') }}" type="text" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required />
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Middle Name</label>
                <input name="middle_name" value="{{ old('middle_name', $student->middle_name ?? '') }}" type="text" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Suffix</label>
                <input name="suffix" value="{{ old('suffix', $student->suffix ?? '') }}" type="text" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Jr., Sr." />
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Gender <span class="text-red-500">*</span></label>
                <select name="sex" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required>
                    <option value="">Select Gender</option>
                    @foreach(['Male', 'Female', 'Other'] as $g)
                        <option value="{{ $g }}" {{ old('sex', $student->sex ?? '') === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Date of Birth</label>
                <input name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth ?? '') }}" type="date" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
            </div>
            <div class="space-y-2 md:col-span-2">
                <label class="text-sm font-semibold text-slate-700">Address</label>
                <input name="address" value="{{ old('address', $student->address ?? '') }}" type="text" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Complete address" />
            </div>
        </div>
    </div>

    <!-- Academic Information -->
    <div x-show="activeTab === 'academic' || isCreating" class="space-y-6" x-cloak>
        @if($mode === 'edit')
        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Academic Information</h3>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Grade Level <span class="text-red-500">*</span></label>
                <select name="level" x-model="gradeLevel" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required>
                    <option value="">Select Grade</option>
                    @foreach(['Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6','Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $grade)
                        <option value="{{ $grade }}" {{ old('level', $student->level ?? request('level') ?? '') === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Section <span class="text-red-500">*</span></label>
                <input name="section" list="sectionOptions" value="{{ old('section', $student->section ?? request('section') ?? '') }}" type="text" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Select or type section" required />
                <datalist id="sectionOptions">
                    <template x-for="sec in sections" :key="sec">
                        <option :value="sec" x-text="sec"></option>
                    </template>
                </datalist>
                <p class="text-xs text-slate-500" x-show="loadingSections">Loading sections...</p>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">School Year <span class="text-red-500">*</span></label>
                <input name="school_year" value="{{ old('school_year', $student->school_year ?? '') }}" type="text" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="2024-2025" required />
            </div>
            <div class="space-y-2" x-show="['Grade 11', 'Grade 12'].includes(gradeLevel)" x-cloak>
                <label class="text-sm font-semibold text-slate-700">Strand</label>
                <select name="strand" x-model="strandName" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    <option value="">Select Strand</option>
                    @foreach(['STEM','ABM','HUMSS','GAS'] as $s)
                        <option value="{{ $s }}" {{ old('strand', $student->strand ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2" x-show="['Grade 11', 'Grade 12'].includes(gradeLevel)" x-cloak>
                <label class="text-sm font-semibold text-slate-700">SHS Enrollment Type</label>
                @php
                    $shsVoucherType = old('shs_voucher_type');
                    if ($shsVoucherType === null && $student) {
                        $shsVoucherType = $student->is_shs_voucher ? 'shs_voucher' : 'regular';
                    }
                @endphp
                <select name="shs_voucher_type" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    <option value="">Select Type</option>
                    <option value="regular" {{ $shsVoucherType === 'regular' ? 'selected' : '' }}>Regular</option>
                    <option value="shs_voucher" {{ $shsVoucherType === 'shs_voucher' ? 'selected' : '' }}>Senior High School (SHS) Voucher</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Parent/Guardian Information -->
    <div x-show="activeTab === 'parent' || isCreating" class="space-y-6" x-cloak>
        @if($mode === 'edit')
        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Parent / Guardian Information</h3>
        
        <!-- Linked Parents List (View Mode) -->
        @if($student && $student->parents->count() > 0)
        <div class="mb-6">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Linked Parents</h4>
            <div class="grid grid-cols-1 gap-3">
                @foreach($student->parents as $parent)
                <div class="flex items-center justify-between p-4 bg-white border {{ $parent->pivot->is_primary ? 'border-blue-200 bg-blue-50/50' : 'border-slate-200' }} rounded-xl shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $parent->pivot->is_primary ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center font-bold">
                            {{ substr($parent->full_name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                {{ $parent->full_name }}
                                @if($parent->pivot->is_primary)
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-600 text-[10px] uppercase font-bold">Primary</span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500">
                                {{ $parent->pivot->relationship }} • {{ $parent->phone }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        @php
            // Resolve parent data
            $parentContact = $student ? $student->parents->where('pivot.is_primary', true)->first() : null;
            if(!$parentContact && $student) {
                 $parentContact = $student->parents->first();
            }
            
            // Determine current data
            $pgName = $parentContact ? $parentContact->full_name : '';
            $pgPhone = $parentContact ? $parentContact->phone : '';
            $pgEmail = $parentContact ? $parentContact->email : '';
            $pgAddress = $parentContact ? $parentContact->address_street : '';
            $pgRel = $parentContact ? $parentContact->pivot->relationship : 'Parent';
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ parentMode: '{{ old('parent_mode', $mode === 'create' ? 'new' : 'current') }}' }">
            
            <div class="md:col-span-2 space-y-2">
                <label class="text-sm font-semibold text-slate-700">Parent Account Mode</label>
                <div class="flex flex-col gap-2">
                    @if($mode === 'edit')
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="parent_mode" value="current" x-model="parentMode" class="text-blue-600 focus:ring-blue-500" />
                        <span class="text-sm text-slate-700">Update Current Parent ({{ $pgName ?: 'No Parent Linked' }})</span>
                    </label>
                    @endif
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="parent_mode" value="new" x-model="parentMode" class="text-blue-600 focus:ring-blue-500" />
                            <span class="text-sm text-slate-700">Create New Parent Account</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="parent_mode" value="existing" x-model="parentMode" class="text-blue-600 focus:ring-blue-500" />
                            <span class="text-sm text-slate-700">Link Existing Parent</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- New/Current Parent Fields -->
            <div x-show="['new', 'current'].includes(parentMode)" class="contents">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Parent Name</label>
                    <input name="parent_guardian_name" value="{{ old('parent_guardian_name', $pgName) }}" type="text" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Phone Number (11 digits)</label>
                    <input name="parent_contact_number" value="{{ old('parent_contact_number', $pgPhone) }}" type="text" maxlength="11" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="09xxxxxxxxx" />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700">Email (Optional)</label>
                    <input name="parent_email" value="{{ old('parent_email', $pgEmail) }}" type="email" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
                </div>
                
                <!-- Password Field for New Parent -->
                <div class="space-y-2" x-show="parentMode === 'new'">
                    <label class="text-sm font-semibold text-slate-700">Password <span class="text-red-500">*</span></label>
                    <input name="parent_password" type="password" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Create password for parent" />
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-semibold text-slate-700">Address (Optional)</label>
                    <input name="parent_address" value="{{ old('parent_address', $pgAddress) }}" type="text" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
                </div>
            </div>

            <!-- Relationship Field (Always Visible for all modes) -->
            <div class="md:col-span-2 space-y-2">
                <label class="text-sm font-semibold text-slate-700">Relationship <span class="text-red-500">*</span></label>
                <input name="relationship" value="{{ old('relationship', $pgRel) }}" type="text" class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="e.g. Father, Mother, Guardian" required />
            </div>

            <!-- Existing Parent Fields -->
            <div x-show="parentMode === 'existing'" class="md:col-span-2 space-y-2" x-cloak 
                 x-data="{
                    search: '',
                    parents: {{ json_encode($existingParents ?? []) }},
                    showDropdown: false,
                    get filteredParents() {
                        if (this.search === '') return []; 
                        return this.parents.filter(p => p.label.toLowerCase().includes(this.search.toLowerCase())).slice(0, 10);
                    },
                    selectParent(p) {
                        this.search = p.label;
                        $refs.parentIdInput.value = p.id;
                        this.showDropdown = false;
                    }
                 }">
                <label class="text-sm font-semibold text-slate-700">Search Existing Parent</label>
                <div class="relative">
                    <input type="text" x-model="search" @focus="showDropdown = true" @click.away="showDropdown = false"
                           placeholder="Search by name or phone..." 
                           class="w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" />
                    <input type="hidden" name="existing_parent_id" x-ref="parentIdInput">
                    
                    <div x-show="showDropdown && filteredParents.length > 0" class="absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg border border-slate-100 max-h-60 overflow-y-auto">
                        <ul class="py-1">
                            <template x-for="p in filteredParents" :key="p.id">
                                <li @click="selectParent(p)" class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm text-slate-700 transition-colors">
                                    <span x-text="p.label"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <p class="text-xs text-slate-500">Start typing to search existing parents.</p>
            </div>
            
            <!-- SMS Toggle -->
             <div class="md:col-span-2 flex items-center gap-3 p-4 bg-blue-50 rounded-xl border border-blue-100 mt-4">
                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                    <input type="checkbox" name="send_sms" id="send_sms" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer peer checked:right-0 right-6 checked:border-blue-600"/>
                    <label for="send_sms" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer peer-checked:bg-blue-600"></label>
                </div>
                <div>
                    <label for="send_sms" class="font-semibold text-slate-800 text-sm cursor-pointer">Send SMS Notification</label>
                    <p class="text-xs text-slate-500">Notify parent about enrollment/update and credentials</p>
                </div>
            </div>
        </div>
    </div>

    </fieldset>

    <!-- Actions -->
    <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
        @if($mode === 'create')
        <a href="{{ route('admin.students.index') }}" class="px-6 py-2.5 rounded-xl text-slate-600 font-semibold hover:bg-slate-50 transition-colors">Cancel</a>
        @endif
        
        @unless($isReadOnly)
        <button type="submit" class="bg-blue-600 text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-blue-300 transition-all transform hover:-translate-y-0.5">
            {{ $mode === 'create' ? 'Enroll Student' : 'Save Changes' }}
        </button>
        @endunless
    </div>
</form>
