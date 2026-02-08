@extends('auth.user_dashboard')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 no-print">
        <a href="{{ route('parent.dashboard', ['student_id' => $student->student_id]) }}" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition-colors">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <a href="{{ route('parent.pay', ['student_id' => $student->student_id]) }}" class="w-full sm:w-auto justify-center bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2">
                <i class="fas fa-credit-card"></i> Pay Now
            </a>
            <button onclick="window.print()" class="w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <i class="fas fa-print"></i> Print Statement
            </button>
        </div>
    </div>

    <div class="bg-white p-8 md:p-12 rounded-2xl shadow-sm border border-gray-200 soa-container">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start border-b border-gray-200 pb-8 mb-8 gap-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Statement of Account</h1>
                <p class="text-gray-500 mt-1">Generated on {{ now()->format('F d, Y') }}</p>
            </div>
            <div class="text-left md:text-right">
                <h2 class="text-xl font-bold text-blue-900">EFees School Management</h2>
                <p class="text-gray-600 text-sm mt-1">123 Education Lane</p>
                <p class="text-gray-600 text-sm">Knowledge City, KC 12345</p>
                <p class="text-gray-600 text-sm">finance@efees.edu</p>
            </div>
        </div>

        <!-- Student Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10 bg-gray-50 p-6 rounded-xl border border-gray-100">
            <div>
                <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Student Details</p>
                <p class="text-lg font-bold text-gray-900">{{ $student->full_name }}</p>
                <p class="text-gray-600 font-mono text-sm">{{ $student->student_id }}</p>
                <p class="text-gray-600 text-sm">{{ $student->level }} - {{ $student->section }}</p>
            </div>
            <div class="text-left md:text-right">
                <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-1">Account Summary</p>
                <div class="space-y-1">
                    <div class="flex justify-between md:justify-end gap-4">
                        <span class="text-gray-600">Total Fees:</span>
                        <span class="font-medium text-gray-900">₱{{ number_format((float) ($totals['totalAmount'] ?? 0), 2) }}</span>
                    </div>
                    <div class="flex justify-between md:justify-end gap-4">
                        <span class="text-gray-600">Total Paid:</span>
                        <span class="font-medium text-green-600">-₱{{ number_format((float) ($totals['paidAmount'] ?? 0), 2) }}</span>
                    </div>
                    <div class="flex justify-between md:justify-end gap-4 border-t border-gray-200 pt-1 mt-1">
                        <span class="text-gray-800 font-bold">Balance Due:</span>
                        <span class="font-bold text-blue-600">₱{{ number_format((float) ($totals['remainingBalance'] ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Breakdown -->
        <div class="mb-10">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Fee Breakdown</h3>
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-900 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                         @php
                            $assignment = $student->feeAssignments()->where('school_year', $student->school_year)->latest()->first();
                         @endphp
                         @if($assignment)
                            <tr>
                                <td class="px-6 py-4 text-gray-900">Base Tuition</td>
                                <td class="px-6 py-4 text-right text-gray-900">₱{{ number_format($assignment->base_tuition, 2) }}</td>
                            </tr>
                            @foreach($assignment->additionalCharges as $charge)
                                <tr>
                                    <td class="px-6 py-4 text-gray-600">{{ $charge->name }}</td>
                                    <td class="px-6 py-4 text-right text-gray-600">₱{{ number_format($charge->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            
                            @foreach($assignment->discounts as $discount)
                                <tr class="bg-green-50">
                                    <td class="px-6 py-4 text-green-700">
                                        {{ $discount->discount_name }}
                                        <span class="text-xs block text-green-600">Discount Applied</span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-green-700">-₱{{ number_format($discount->pivot->applied_amount ?? 0, 2) }}</td>
                                </tr>
                            @endforeach

                            <!-- SHS Voucher Exclusion Notice -->
                            @if($student->is_shs_voucher)
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4 text-gray-500 italic">
                                        Sibling Discount
                                        <span class="text-xs block text-gray-400">Not Applicable (SHS Voucher)</span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-400 italic">--</td>
                                </tr>
                            @endif
                         @else
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-gray-500">No fee assessment found for this school year.</td>
                            </tr>
                         @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transaction Table -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Transaction History</h3>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-[820px] w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-900 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 whitespace-nowrap">Date</th>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3 text-right whitespace-nowrap">Debit (Fee)</th>
                            <th class="px-6 py-3 text-right whitespace-nowrap">Credit (Payment)</th>
                            <th class="px-6 py-3 text-right whitespace-nowrap">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php $runningBalance = 0; @endphp
                        @forelse($transactions as $trx)
                            @php
                                $debit = $trx->record_type !== 'payment' ? $trx->amount : 0;
                                $credit = $trx->record_type === 'payment' ? $trx->amount : 0;
                                // Simple logic: Fee increases balance, Payment decreases it
                                // Assuming transactions are ordered correctly (oldest first)
                                if ($trx->record_type !== 'payment') {
                                    $runningBalance += $debit;
                                } else {
                                    $runningBalance -= $credit;
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    {{ $trx->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-gray-900 font-medium">
                                    {{ $trx->description }}
                                    @if($trx->record_type === 'payment')
                                        <span class="text-xs text-gray-500 block font-normal">Ref: {{ $trx->reference_number }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-gray-600">
                                    @if($debit > 0)
                                        ₱{{ number_format($debit, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-green-600 font-medium">
                                    @if($credit > 0)
                                        ₱{{ number_format($credit, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900">
                                    ₱{{ number_format($runningBalance, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="text-center text-gray-500 text-xs mt-12 border-t border-gray-100 pt-6">
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
