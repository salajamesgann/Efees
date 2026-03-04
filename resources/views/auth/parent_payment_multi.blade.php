@extends('auth.user_dashboard')

@section('content')
<div class="max-w-5xl mx-auto" x-data="multiChildPayment()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Multi-Child Payment</h1>
                <p class="text-gray-600 mt-1">Pay fees for multiple children in a single transaction.</p>
            </div>
            <a href="{{ route('parent.pay') }}" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                <i class="fas fa-arrow-left"></i> Single Payment
            </a>
        </div>
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

    @if(session('info'))
        <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-info-circle"></i>
            {{ session('info') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
            <div class="flex items-center gap-2 mb-2 font-medium">
                <i class="fas fa-exclamation-circle"></i>
                Please correct the following errors:
            </div>
            <ul class="list-disc list-inside text-sm ml-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(count($childrenData) < 2)
        <!-- Not enough children for multi-payment -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Single Student Linked</h3>
            <p class="text-gray-500 mb-4">Multi-child payment requires two or more linked students. You can use the regular payment page instead.</p>
            <a href="{{ route('parent.pay') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-blue-700 transition-colors">
                <i class="fas fa-credit-card"></i> Go to Payments
            </a>
        </div>
    @else
        <form method="POST" action="{{ route('parent.pay.multi.store') }}" x-data="{ showConfirm: false }" @submit.prevent="showConfirm = true">
            @csrf

            <!-- Student Selection Cards -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <h2 class="font-semibold text-gray-900">Select Students & Amounts</h2>
                                <p class="text-xs text-gray-500">Check the students to include and enter per-child amounts</p>
                            </div>
                        </div>
                        <button type="button" @click="toggleAll()" class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors px-3 py-1.5 rounded-lg hover:bg-blue-50">
                            <span x-text="allSelected ? 'Deselect All' : 'Select All'"></span>
                        </button>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($childrenData as $idx => $data)
                    <div class="px-6 py-4 transition-colors duration-200"
                         :class="children[{{ $idx }}].selected ? 'bg-blue-50/30' : 'hover:bg-gray-50'">
                        <div class="flex items-start gap-4">
                            <!-- Checkbox -->
                            <div class="pt-1">
                                <label class="relative flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           x-model="children[{{ $idx }}].selected"
                                           @change="recalculate()"
                                           class="peer sr-only">
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-md peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-colors flex items-center justify-center">
                                        <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity" :class="children[{{ $idx }}].selected ? 'opacity-100' : 'opacity-0'"></i>
                                    </div>
                                </label>
                            </div>

                            <!-- Student Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 transition-colors"
                                             :class="children[{{ $idx }}].selected ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'">
                                            {{ substr($data['student']->first_name, 0, 1) }}{{ substr($data['student']->last_name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 truncate">{{ $data['student']->full_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $data['student']->student_id }} &bull; {{ $data['student']->level }} {{ $data['student']->section ? '• ' . $data['student']->section : '' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
                                        <div class="text-right">
                                            <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Balance</p>
                                            <p class="font-bold text-gray-900">₱{{ number_format($data['balance'], 2) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Amount Input (visible when checked) -->
                                <div x-show="children[{{ $idx }}].selected" x-cloak class="mt-3 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                    <div class="relative flex-1 w-full sm:max-w-xs">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">₱</span>
                                        <input type="number"
                                               step="0.01"
                                               min="1"
                                               max="{{ $data['balance'] }}"
                                               x-model.number="children[{{ $idx }}].amount"
                                               @input="recalculate()"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 pl-8 pr-3 text-sm"
                                               placeholder="0.00">
                                        <input type="hidden"
                                               :name="'students[' + {{ $idx }} + '][student_id]'"
                                               value="{{ $data['student']->student_id }}"
                                               :disabled="!children[{{ $idx }}].selected">
                                        <input type="hidden"
                                               :name="'students[' + {{ $idx }} + '][amount]'"
                                               :value="children[{{ $idx }}].amount"
                                               :disabled="!children[{{ $idx }}].selected">
                                    </div>
                                    <button type="button"
                                            @click="children[{{ $idx }}].amount = {{ $data['balance'] }}; recalculate()"
                                            class="text-xs font-medium text-blue-600 hover:text-blue-800 bg-white border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-colors whitespace-nowrap"
                                            {{ (float) $data['balance'] <= 0 ? 'disabled' : '' }}>
                                        Pay Full (₱{{ number_format($data['balance'], 2) }})
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Summary Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calculator text-green-600 text-sm"></i>
                        </div>
                        <h2 class="font-semibold text-gray-900">Payment Summary</h2>
                    </div>
                </div>
                <div class="p-6">
                    <!-- Per-child summary -->
                    <div class="space-y-2 mb-4">
                        <template x-for="(child, i) in children" :key="i">
                            <div x-show="child.selected && child.amount > 0" class="flex items-center justify-between text-sm">
                                <span class="text-gray-600" x-text="child.name"></span>
                                <span class="font-medium text-gray-900">₱<span x-text="formatNumber(child.amount)"></span></span>
                            </div>
                        </template>
                    </div>

                    <div x-show="selectedCount === 0" class="text-center py-4 text-gray-400 text-sm">
                        <i class="fas fa-hand-pointer mr-1"></i> Select at least one student above
                    </div>

                    <div x-show="selectedCount > 0" class="border-t border-gray-200 pt-4 mt-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Grand Total (<span x-text="selectedCount"></span> student<span x-show="selectedCount > 1">s</span>)</p>
                            </div>
                            <p class="text-2xl font-bold text-blue-700">₱<span x-text="formatNumber(grandTotal)"></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-wallet text-purple-600 text-sm"></i>
                        </div>
                        <h2 class="font-semibold text-gray-900">Payment Method</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="method" value="gcash" class="peer sr-only" checked>
                            <div class="border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 rounded-xl p-3 text-center transition-all hover:border-gray-300">
                                <i class="fas fa-mobile-alt text-xl text-blue-500 mb-1"></i>
                                <p class="text-xs font-medium text-gray-700">GCash</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="method" value="grab_pay" class="peer sr-only">
                            <div class="border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 rounded-xl p-3 text-center transition-all hover:border-gray-300">
                                <i class="fas fa-taxi text-xl text-green-500 mb-1"></i>
                                <p class="text-xs font-medium text-gray-700">GrabPay</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="method" value="paymaya" class="peer sr-only">
                            <div class="border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 rounded-xl p-3 text-center transition-all hover:border-gray-300">
                                <i class="fas fa-wallet text-xl text-green-600 mb-1"></i>
                                <p class="text-xs font-medium text-gray-700">Maya</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="method" value="card" class="peer sr-only">
                            <div class="border-2 border-gray-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 rounded-xl p-3 text-center transition-all hover:border-gray-300">
                                <i class="fas fa-credit-card text-xl text-purple-500 mb-1"></i>
                                <p class="text-xs font-medium text-gray-700">Card</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-3">
                <!-- Minimum amount warning -->
                <div x-show="selectedCount > 0 && grandTotal > 0 && grandTotal < 20" x-cloak
                     class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
                    <i class="fas fa-exclamation-triangle"></i>
                    Minimum payment amount is <span class="font-bold">₱20.00</span>. Please increase the amount to proceed.
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                    <a href="{{ route('parent.dashboard') }}" class="w-full sm:w-auto text-center px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">Cancel</a>
                    <button type="submit"
                            :disabled="selectedCount === 0 || grandTotal < 20 || submitting"
                            class="w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                        <template x-if="!submitting">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-lock"></i>
                                <span x-text="selectedCount > 0 ? 'Pay ₱' + formatNumber(grandTotal) + ' for ' + selectedCount + ' Student' + (selectedCount > 1 ? 's' : '') : 'Select Students to Pay'"></span>
                            </span>
                        </template>
                        <template x-if="submitting">
                            <span class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Processing…</span>
                        </template>
                    </button>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div x-show="showConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.4);">
                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6" @click.away="showConfirm = false">
                    <div class="text-center mb-4">
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Confirm Payment</h3>
                        <p class="text-gray-500 text-sm mt-1">
                            Pay <span class="font-bold text-blue-600" x-text="'₱' + formatNumber(grandTotal)"></span>
                            for <span class="font-bold" x-text="selectedCount"></span> student<span x-show="selectedCount > 1">s</span>?
                        </p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button type="button"
                                @click="showConfirm = false; submitting = true; $el.closest('form').submit();"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-bold transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i> Yes, Pay Now
                        </button>
                        <button type="button" @click="showConfirm = false" class="w-full border border-gray-300 text-gray-700 py-2.5 rounded-xl font-medium hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

<script>
function multiChildPayment() {
    return {
        children: [
            @foreach($childrenData as $idx => $data)
            {
                name: @json($data['student']->full_name),
                studentId: @json($data['student']->student_id),
                balance: {{ $data['balance'] }},
                selected: false,
                amount: 0,
            },
            @endforeach
        ],
        grandTotal: 0,
        selectedCount: 0,
        submitting: false,

        get allSelected() {
            return this.children.length > 0 && this.children.every(c => c.selected);
        },

        toggleAll() {
            const newState = !this.allSelected;
            this.children.forEach(c => {
                c.selected = newState;
                if (newState && c.amount <= 0) {
                    c.amount = c.balance;
                }
                if (!newState) {
                    c.amount = 0;
                }
            });
            this.recalculate();
        },

        recalculate() {
            this.grandTotal = 0;
            this.selectedCount = 0;
            this.children.forEach(c => {
                if (c.selected && c.amount > 0) {
                    this.grandTotal += parseFloat(c.amount) || 0;
                    this.selectedCount++;
                } else if (c.selected) {
                    this.selectedCount++;
                }
            });
            this.grandTotal = Math.round(this.grandTotal * 100) / 100;
        },

        formatNumber(num) {
            return parseFloat(num || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
    };
}
</script>
@endsection
