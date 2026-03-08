@extends('auth.user_dashboard')

@section('content')
<div class="max-w-5xl mx-auto pb-16 md:pb-0">
    <!-- Actions -->
    <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 no-print">
        <a href="{{ route('parent.dashboard', ['student_id' => $student->student_id]) }}" class="flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition-colors text-sm">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <div class="flex flex-row sm:flex-row sm:items-center gap-2 sm:gap-3">
            <a href="{{ route('parent.soa.pdf', $student->student_id) }}" class="flex-1 sm:flex-initial sm:w-auto justify-center bg-green-600 hover:bg-green-700 text-white px-4 sm:px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-green-200 dark:shadow-green-900/30 transition-all hover:-translate-y-0.5 flex items-center gap-2 text-sm active:translate-y-0">
                <i class="fas fa-file-pdf"></i> <span class="hidden xs:inline">Download</span> PDF
            </a>
            <a href="{{ route('parent.pay', ['student_id' => $student->student_id]) }}" class="flex-1 sm:flex-initial sm:w-auto justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 sm:px-6 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 text-sm">
                <i class="fas fa-credit-card"></i> Pay Now
            </a>
            <button onclick="window.print()" class="flex-1 sm:flex-initial sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 dark:shadow-blue-900/30 transition-all hover:-translate-y-0.5 flex items-center gap-2 text-sm active:translate-y-0">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-5 sm:p-8 md:p-12 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 soa-container">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start border-b border-gray-200 dark:border-gray-700 pb-5 sm:pb-8 mb-5 sm:mb-8 gap-4 sm:gap-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">Statement of Account</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Generated on {{ now()->format('F d, Y') }}</p>
            </div>
            <div class="text-left md:text-right">
                <h2 class="text-xl font-bold text-blue-900 dark:text-blue-400">{{ $schoolName ?? 'EFees School Management' }}</h2>
                @if(!empty($schoolAddress))
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ $schoolAddress }}</p>
                @endif
                @if(!empty($schoolEmail))
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $schoolEmail }}</p>
                @endif
            </div>
        </div>

        <!-- Student Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-8 mb-6 sm:mb-10 bg-gray-50 dark:bg-gray-700/50 p-4 sm:p-6 rounded-xl border border-gray-100 dark:border-gray-600">
            <div>
                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-1">Student Details</p>
                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $student->full_name }}</p>
                <p class="text-gray-600 dark:text-gray-400 font-mono text-sm">{{ $student->student_id }}</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $student->level }} - {{ $student->section }}</p>
            </div>
            <div class="text-left md:text-right">
                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-1">Account Summary</p>
                <div class="space-y-1">
                    <div class="flex justify-between md:justify-end gap-4">
                        <span class="text-gray-600 dark:text-gray-400">Total Fees:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">₱{{ number_format((float) ($totals['totalAmount'] ?? 0), 2) }}</span>
                    </div>
                    <div class="flex justify-between md:justify-end gap-4">
                        <span class="text-gray-600 dark:text-gray-400">Total Paid:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">-₱{{ number_format((float) ($totals['paidAmount'] ?? 0), 2) }}</span>
                    </div>
                    <div class="flex justify-between md:justify-end gap-4 border-t border-gray-200 dark:border-gray-600 pt-1 mt-1">
                        <span class="text-gray-800 dark:text-gray-200 font-bold">Balance Due:</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">₱{{ number_format((float) ($totals['remainingBalance'] ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Breakdown -->
        <div class="mb-10">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Fee Breakdown</h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                         @if($assignment)
                            <tr>
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">Base Tuition</td>
                                <td class="px-6 py-4 text-right text-gray-900 dark:text-gray-100">₱{{ number_format($assignment->base_tuition, 2) }}</td>
                            </tr>
                            @foreach($assignment->additionalCharges as $charge)
                                <tr>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $charge->name }}</td>
                                    <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">₱{{ number_format($charge->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            
                            @foreach($assignment->discounts as $discount)
                                <tr class="bg-green-50 dark:bg-green-900/20">
                                    <td class="px-6 py-4 text-green-700 dark:text-green-400">
                                        {{ $discount->discount_name }}
                                        <span class="text-xs block text-green-600 dark:text-green-500">Discount Applied</span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-green-700 dark:text-green-400">-₱{{ number_format($discount->pivot->applied_amount ?? 0, 2) }}</td>
                                </tr>
                            @endforeach

                            <!-- SHS Voucher Exclusion Notice -->
                            @if($student->is_shs_voucher)
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 italic">
                                        Sibling Discount
                                        <span class="text-xs block text-gray-400 dark:text-gray-500">Not Applicable (SHS Voucher)</span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-400 dark:text-gray-500 italic">--</td>
                                </tr>
                            @endif
                         @else
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No fee assessment found for this school year.</td>
                            </tr>
                         @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Transaction History</h3>

            <!-- Mobile Card Layout (< md) -->
            <div class="md:hidden space-y-3">
                @forelse($transactions as $trx)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ $trx->description }}</p>
                                @if($trx->record_type === 'payment')
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">Ref: {{ $trx->reference_number }}</p>
                                @endif
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap flex-shrink-0">{{ $trx->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm pt-2 border-t border-gray-100 dark:border-gray-700">
                            @if($trx->record_type !== 'payment')
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Debit (Fee)</span>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">₱{{ number_format($trx->amount, 2) }}</p>
                                </div>
                            @else
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Credit (Payment)</span>
                                    <p class="font-medium text-green-600 dark:text-green-400">₱{{ number_format($trx->amount, 2) }}</p>
                                </div>
                            @endif
                            <div class="text-right">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Balance</span>
                                <p class="font-bold text-gray-900 dark:text-gray-100">₱{{ number_format($trx->running_balance, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 dark:text-gray-400">
                        No transactions found.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table Layout (>= md) -->
            <div class="hidden md:block overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                <table class="min-w-[820px] w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3 whitespace-nowrap">Date</th>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3 text-right whitespace-nowrap">Debit (Fee)</th>
                            <th class="px-6 py-3 text-right whitespace-nowrap">Credit (Payment)</th>
                            <th class="px-6 py-3 text-right whitespace-nowrap">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                    {{ $trx->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100 font-medium">
                                    {{ $trx->description }}
                                    @if($trx->record_type === 'payment')
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block font-normal">Ref: {{ $trx->reference_number }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                    @if($trx->record_type !== 'payment')
                                        ₱{{ number_format($trx->amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-green-600 dark:text-green-400 font-medium">
                                    @if($trx->record_type === 'payment')
                                        ₱{{ number_format($trx->amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-gray-100">
                                    ₱{{ number_format($trx->running_balance, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </div>
        
        <div class="text-center text-gray-500 dark:text-gray-400 text-xs mt-12 border-t border-gray-100 dark:border-gray-700 pt-6">
            <p>This is a computer-generated statement. No signature required.</p>
            <p class="mt-1">For any discrepancies, please contact the Finance Office immediately.</p>
        </div>
    </div>
</div>

<style>
    @media print {
        .soa-container {
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
        }
        /* Ensure table fits */
        table { width: 100% !important; }
        /* Hide layout elements managed by dashboard layout via .no-print class */
    }
</style>
@endsection
