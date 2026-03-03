<div class="bg-white rounded-xl shadow border border-gray-200 p-4" x-data="{ groupType: 'grade' }">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-gray-900">Assign Discount to Group</h3>
  </div>
  <form method="POST" action="{{ route('admin.fees.assign-discount-group') }}" class="space-y-4">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
        <select name="discount_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
          @foreach(($discounts ?? collect()) as $discount)
            <option value="{{ $discount->id }}">{{ $discount->discount_name }} ({{ $discount->type === 'percentage' ? $discount->value.'%' : '₱'.number_format($discount->value, 2) }})</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Group Type</label>
        <select name="group_type" x-model="groupType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
          <option value="grade">Grade Level</option>
          <option value="section">Section</option>
          <option value="strand">Strand</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Group Value</label>
        <div x-show="groupType === 'grade'">
          <select name="group_value" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" :disabled="groupType !== 'grade'">
            @foreach(($gradeLevels ?? []) as $gl)
              <option value="{{ $gl }}">{{ $gl }}</option>
            @endforeach
          </select>
        </div>
        <div x-show="groupType !== 'grade'" x-cloak>
          <input type="text" name="group_value" placeholder="Enter section or strand" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" :disabled="groupType === 'grade'">
        </div>
      </div>
    </div>
    <div class="pt-2">
      <button type="submit" class="inline-flex items-center h-10 px-4 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors">
        <i class="fas fa-users mr-2"></i> Assign to Group
      </button>
    </div>
  </form>
</div>
{{-- ── Assign to Specific Students ──────────────────────────────────────── --}}
<div class="bg-white rounded-xl shadow border border-gray-200 p-4 mt-6"
     x-data="discountStudentPicker({{ ($students ?? collect())->map(fn($s) => [
         'id'      => $s->student_id,
         'name'    => trim($s->last_name . ', ' . $s->first_name),
         'level'   => $s->level ?? '',
         'section' => $s->section ?? '',
         'sy'      => $s->school_year ?? '',
     ])->values()->toJson() }})">

  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
      <i class="fas fa-user-tag text-indigo-500"></i>
      Assign Discount to Specific Students
    </h3>
    <span class="text-xs text-slate-500" x-text="selected.length + ' student(s) selected'"></span>
  </div>

  <form method="POST" action="{{ route('admin.fees.assign-discount-students') }}" @submit.prevent="submitForm($el.closest('form'))">
    @csrf

    {{-- Discount selector --}}
    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
      <select name="discount_id" required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        <option value="">— Select a discount —</option>
        @foreach(($discounts ?? collect()) as $discount)
          <option value="{{ $discount->id }}">
            {{ $discount->discount_name }}
            ({{ $discount->type === 'percentage' ? $discount->value . '%' : '₱' . number_format($discount->value, 2) }})
          </option>
        @endforeach
      </select>
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
      <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Search by name / ID</label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
            <i class="fas fa-search text-xs"></i>
          </span>
          <input type="text" x-model="search" placeholder="Type to filter…"
                 class="block w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Grade Level</label>
        <select x-model="filterGrade"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
          <option value="">All Grades</option>
          @foreach(['Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6','Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $gl)
            <option value="{{ $gl }}">{{ $gl }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">School Year</label>
        <select x-model="filterSy"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
          <option value="">All Years</option>
          @foreach(($students ?? collect())->pluck('school_year')->filter()->unique()->sort() as $sy)
            <option value="{{ $sy }}">{{ $sy }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Select-all / deselect-all helpers --}}
    <div class="flex items-center gap-3 mb-2 text-xs">
      <button type="button" @click="selectAll()" class="text-indigo-600 hover:underline font-medium">Select all visible</button>
      <span class="text-gray-300">|</span>
      <button type="button" @click="deselectAll()" class="text-red-500 hover:underline font-medium">Deselect all</button>
      <span class="ml-auto text-slate-400" x-text="filtered.length + ' shown / ' + allStudents.length + ' total'"></span>
    </div>

    {{-- Student list --}}
    <div class="border border-gray-200 rounded-lg overflow-hidden">
      <div class="max-h-64 overflow-y-auto divide-y divide-gray-100 custom-scrollbar" id="studentPickerList">
        <template x-for="s in filtered" :key="s.id">
          <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 cursor-pointer transition-colors"
                 :class="selected.includes(s.id) ? 'bg-indigo-50' : ''">
            <input type="checkbox" :value="s.id" name="student_ids[]"
                   :checked="selected.includes(s.id)"
                   @change="toggle(s.id)"
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"/>
            <span class="flex-1 min-w-0">
              <span class="block text-sm font-medium text-gray-900 truncate" x-text="s.name"></span>
              <span class="block text-xs text-gray-500">
                <span x-text="s.id"></span>
                · <span x-text="s.level"></span>
                <template x-if="s.section"> · <span x-text="s.section"></span></template>
                <template x-if="s.sy"> · <span x-text="s.sy"></span></template>
              </span>
            </span>
            <i class="fas fa-check text-indigo-500 text-xs" x-show="selected.includes(s.id)" x-cloak></i>
          </label>
        </template>
        <div x-show="filtered.length === 0" class="px-4 py-6 text-center text-gray-400 text-sm">
          No students match the current filters.
        </div>
      </div>
    </div>

    {{-- hidden inputs mirror for non-Alpine fallback --}}
    <div id="hiddenStudentInputs"></div>

    <div class="mt-4 flex items-center gap-3">
      <button type="submit"
              :disabled="selected.length === 0"
              :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
              class="inline-flex items-center h-10 px-5 rounded-lg bg-indigo-600 text-white font-semibold transition-colors text-sm">
        <i class="fas fa-user-tag mr-2"></i>
        Assign to <span class="mx-1 font-bold" x-text="selected.length"></span> Student(s)
      </button>
      <span x-show="selected.length === 0" class="text-xs text-gray-400">Select at least one student above.</span>
    </div>
  </form>
</div>

<script>
function discountStudentPicker(allStudents) {
    return {
        allStudents,
        search: '',
        filterGrade: '',
        filterSy: '',
        selected: [],
        get filtered() {
            const q = this.search.toLowerCase().trim();
            return this.allStudents.filter(s => {
                const matchSearch = !q || s.name.toLowerCase().includes(q) || s.id.toLowerCase().includes(q);
                const matchGrade  = !this.filterGrade || s.level === this.filterGrade;
                const matchSy     = !this.filterSy    || s.sy === this.filterSy;
                return matchSearch && matchGrade && matchSy;
            });
        },
        toggle(id) {
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(x => x !== id);
            } else {
                this.selected.push(id);
            }
        },
        selectAll() {
            this.filtered.forEach(s => {
                if (!this.selected.includes(s.id)) this.selected.push(s.id);
            });
        },
        deselectAll() {
            this.selected = [];
        },
        submitForm(form) {
            // Build hidden inputs for selected student IDs before native submit
            const container = form.querySelector('#hiddenStudentInputs');
            container.innerHTML = '';
            this.selected.forEach(id => {
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = 'student_ids[]';
                inp.value = id;
                container.appendChild(inp);
            });
            // Remove checkbox inputs to avoid duplicate submissions
            form.querySelectorAll('input[type="checkbox"][name="student_ids[]"]').forEach(cb => cb.removeAttribute('name'));
            form.submit();
        },
    };
}
</script>

{{-- ── Discount Rules ────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl shadow border border-gray-200 p-4 mt-6">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-gray-900">Discount Rules</h3>
    <a href="{{ route('admin.fees.create-discount') }}" class="inline-flex items-center h-9 px-3 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors">
      <i class="fas fa-plus mr-2"></i> Add Discount
    </a>
  </div>
  @if(session('success'))
    <div class="mb-3 rounded-lg border border-green-200 bg-green-50 text-green-700 px-4 py-2 text-sm">
        {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-3 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-2 text-sm">
        {{ session('error') }}
    </div>
  @endif
  <div class="overflow-x-auto max-h-[60vh] overflow-y-auto custom-scrollbar">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Name</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Type</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Value</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Scope</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Auto</th>
          <!-- Removed Stackable and Priority for cleaner UX -->
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Active</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Actions</th>
        </tr>
      </thead>
      <tbody id="discounts-table-body" class="divide-y divide-gray-200 bg-white">
        @forelse(($discounts ?? collect()) as $discount)
          <tr class="hover:bg-gray-50">
            <td class="px-3 py-2 text-sm text-gray-900">{{ $discount->discount_name }}</td>
            <td class="px-3 py-2 text-sm text-gray-600">{{ ucfirst($discount->type) }}</td>
            <td class="px-3 py-2 text-sm text-gray-900">
              @if($discount->type === 'percentage')
                {{ $discount->value }}%
              @else
                ₱{{ number_format($discount->value, 2) }}
              @endif
            </td>
            <td class="px-3 py-2 text-sm text-gray-600 capitalize">{{ str_replace('_',' ', $discount->getApplyScope()) }}</td>
            <td class="px-3 py-2 text-sm text-gray-900">{{ $discount->is_automatic ? 'Yes' : 'No' }}</td>
            <!-- Stackable/Priority removed from table -->
            <td class="px-3 py-2 text-sm text-gray-900">{{ $discount->is_active ? 'Yes' : 'No' }}</td>
            <td class="px-3 py-2">
              <a href="{{ route('admin.fees.edit-discount', $discount) }}" class="inline-flex items-center h-8 px-2 rounded-lg bg-blue-500 text-white text-xs font-semibold hover:bg-blue-600">
                <i class="fas fa-edit mr-1"></i> Edit
              </a>
              <form method="POST" action="{{ route('admin.fees.destroy-discount', $discount) }}" class="inline-block ml-1" onsubmit="return confirm('Are you sure you want to delete this discount?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center h-8 px-2 rounded-lg bg-red-500 text-white text-xs font-semibold hover:bg-red-600">
                  <i class="fas fa-trash mr-1"></i> Delete
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-4 py-6 text-center text-gray-600 text-sm">No discounts found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
