<div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-5 py-4" id="tuition-root">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Tuition Fee Schedules</h3>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.fees.summary') }}" class="inline-flex items-center gap-2 h-9 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 font-medium hover:bg-slate-50 hover:border-slate-300 transition-colors text-sm">
                <i class="fas fa-table text-slate-500 text-xs"></i>
                <span>Summary</span>
            </a>
            <a href="{{ route('admin.fees.create-tuition') }}" class="inline-flex items-center gap-2 h-9 px-4 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow-sm transition-colors text-sm">
                <i class="fas fa-plus text-xs"></i>
                <span>Add Tuition</span>
            </a>
        </div>
    </div>

    <div class="mb-4">
        <details class="group">
            <summary class="flex items-center justify-between text-sm font-medium text-slate-600 cursor-pointer select-none py-1">
                <div class="inline-flex items-center gap-2">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-slate-500 text-[10px]">
                        <i class="fas fa-filter"></i>
                    </span>
                    <span class="text-slate-500">Click to expand filters</span>
                </div>
                <i class="fas fa-chevron-down text-[10px] text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <form method="GET" action="{{ route('admin.fees.index', ['tab' => 'tuition']) }}" class="mt-3 grid grid-cols-1 md:grid-cols-4 gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                <div>
                    <label class="text-xs text-gray-600 font-medium">Academic Year</label>
                    <input type="text" name="school_year" value="{{ request('school_year') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. 2025-2026" />
                </div>
                <div>
                    <label class="text-xs text-gray-600 font-medium">Grade Level</label>
                    <select name="grade_level" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All</option>
                        @php $gls = $gradeLevels ?? ['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12']; @endphp
                        @foreach($gls as $g)
                            <option value="{{ $g }}" {{ request('grade_level') === $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-600 font-medium">Status</label>
                    <select name="status" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full h-9 px-4 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
                        Apply
                    </button>
                </div>
            </form>
        </details>
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

    @if(($tuitionFees instanceof \Illuminate\Support\Collection ? $tuitionFees->count() : count($tuitionFees)) > 0)
        <div class="overflow-x-auto max-h-[60vh] overflow-y-auto rounded-lg border border-slate-200 custom-scrollbar" id="tuition-table-wrap">
            <table class="min-w-full divide-y divide-slate-200" id="tuition-table">
                <thead class="bg-slate-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Fee Name</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">S.Y.</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Grade</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Gross Amount</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Net Payable</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($tuitionFees as $fee)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm text-slate-900">
                                @php
                                    $feeName = is_array($fee) ? ($fee['fee_name'] ?? null) : ($fee->notes ?: null);
                                    $computedName = $feeName ?: ((is_array($fee) ? ($fee['grade_level'] ?? 'N/A') : $fee->grade_level).' Tuition – SY '.(is_array($fee) ? ($fee['school_year'] ?? 'N/A') : ($fee->school_year ?? 'N/A')));
                                    $semester = is_array($fee) ? ($fee['semester'] ?? null) : ($fee->semester ?? null);
                                @endphp
                                <div class="flex flex-col">
                                    <span class="font-semibold text-slate-900">{{ $computedName }}</span>
                                    <span class="text-xs text-slate-400">
                                        {{ ($semester && $semester !== 'N/A') ? $semester : 'Regular Semester' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ is_array($fee) ? ($fee['school_year'] ?? 'N/A') : ($fee->school_year ?? 'N/A') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                @php $gl = is_array($fee) ? $fee['grade_level'] : $fee->grade_level; @endphp
                                {{ $gl }}
                                @if(in_array($gl, ['Grade 11','Grade 12']))
                                    <span class="text-[11px] text-slate-400 block">
                                        {{ is_array($fee) ? ($fee['track'] ?? '—') : ($fee->track ?? '—') }} - 
                                        {{ is_array($fee) ? ($fee['strand'] ?? '—') : ($fee->strand ?? '—') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-900 text-right">
                                @php $amt = is_array($fee) ? ($fee['amount'] ?? 0) : (float) $fee->amount; @endphp
                                ₱{{ number_format($amt, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-blue-600 text-right">
                                @php $net = is_array($fee) ? ($fee['net_payable'] ?? $amt) : (float) $fee->amount; @endphp
                                ₱{{ number_format($net, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @php $active = is_array($fee) ? (bool) ($fee['is_active'] ?? false) : (bool) $fee->is_active; @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $active ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                    {{ $active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2 text-slate-400">
                                    @php $fid = is_array($fee) ? $fee['id'] : $fee->id; @endphp
                                    <a href="{{ route('admin.fees.show-tuition', $fid) }}"
                                       class="hover:text-blue-600 transition-colors" title="View details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.fees.edit-tuition', $fid) }}"
                                       class="hover:text-blue-600 transition-colors" title="Edit">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.fees.toggle-tuition', $fid) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="active" value="{{ $active ? '0' : '1' }}">
                                        <button type="submit" class="hover:text-{{ $active ? 'yellow' : 'green' }}-600 transition-colors" title="{{ $active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $active ? 'ban' : 'check-circle' }} text-xs"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.fees.destroy-tuition', $fid) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this tuition fee?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="hover:text-red-600 transition-colors" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @php
            $totalRows = ($tuitionFees instanceof \Illuminate\Support\Collection)
                ? $tuitionFees->count()
                : (is_array($tuitionFees ?? null) ? count($tuitionFees) : 0);
        @endphp
        <div class="flex items-center justify-between mt-3 text-xs text-slate-500">
            <div>
                @if($totalRows > 0)
                    Showing 1 to {{ $totalRows }} of {{ $totalRows }} results
                @else
                    Showing 0 results
                @endif
            </div>
            <div class="inline-flex items-center gap-1">
                <button type="button" class="px-2.5 py-1 rounded-md border border-slate-200 bg-white text-slate-500 hover:bg-slate-50" disabled>
                    Previous
                </button>
                <span class="px-2.5 py-1 rounded-md bg-blue-600 text-white font-medium">
                    1
                </span>
                <button type="button" class="px-2.5 py-1 rounded-md border border-slate-200 bg-white text-slate-500 hover:bg-slate-50" disabled>
                    Next
                </button>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                <i class="fas fa-graduation-cap text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No tuition fees found</h3>
            <p class="text-gray-500 text-sm mb-6">Get started by creating a new tuition fee configuration.</p>
            <a href="{{ route('admin.fees.create-tuition') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                <i class="fas fa-plus"></i>
                Create Tuition Fee
            </a>
        </div>
    @endif
</div>
