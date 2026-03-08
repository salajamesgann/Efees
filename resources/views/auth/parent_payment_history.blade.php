@extends('auth.user_dashboard')

@section('content')
<div class="max-w-6xl mx-auto pb-16 md:pb-0">
    <!-- Header -->
    <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">Payment History</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-0.5 sm:mt-1 text-sm">View past transactions and download receipts.</p>
        </div>
        <div>
            <a href="{{ route('parent.pay') }}" class="w-full sm:w-auto inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-5 sm:px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 dark:shadow-blue-900/30 transition-all hover:-translate-y-0.5 gap-2 text-sm active:translate-y-0">
                <i class="fas fa-plus"></i> New Payment
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('parent.history') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4 mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row items-end gap-2 sm:gap-3">
            @if($myChildren->count() > 1)
            <div class="w-full sm:w-auto sm:min-w-[180px]">
                <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-0.5">Student</label>
                <select name="student_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5">
                    <option value="">All Students</option>
                    @foreach($myChildren as $child)
                        <option value="{{ $child->student_id }}" {{ ($filterStudentId ?? '') == $child->student_id ? 'selected' : '' }}>
                            {{ $child->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="grid grid-cols-2 gap-2 w-full sm:w-auto sm:flex sm:gap-3">
                <div>
                    <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-0.5">From</label>
                    <input type="date" name="date_from" value="{{ $filterDateFrom ?? '' }}"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5">
                </div>
                <div>
                    <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-0.5">To</label>
                    <input type="date" name="date_to" value="{{ $filterDateTo ?? '' }}"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5">
                </div>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition-colors active:bg-blue-800">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                @if(($filterStudentId ?? '') !== '' || ($filterDateFrom ?? '') !== '' || ($filterDateTo ?? '') !== '')
                <a href="{{ route('parent.history') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-1.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-times text-xs"></i> Clear
                </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Mobile Card Layout (< 768px) -->
    <div class="md:hidden space-y-4">
        @forelse($payments as $payment)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4">
                    <!-- Top Row: Student + Amount -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {{ substr($payment->student->first_name, 0, 1) }}{{ substr($payment->student->last_name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $payment->student->full_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $payment->student->student_id }}</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">₱{{ number_format($payment->amount_paid, 2) }}</p>
                            <x-payment-status-badge :status="$payment->status" size="xs" />
                        </div>
                    </div>

                    <!-- Details Row -->
                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-hashtag text-gray-400 dark:text-gray-500"></i>
                            <span class="font-mono">{{ $payment->reference_number ?? 'REC-'.str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-wallet text-gray-400 dark:text-gray-500"></i>
                            <span class="capitalize">{{ str_replace('_', ' ', $payment->method) }}</span>
                        </div>
                    </div>

                    <!-- Date + Actions Row -->
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ $payment->paid_at->format('M d, Y \a\t h:i A') }}
                        </p>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('parent.receipts.download', $payment->id) }}" class="inline-flex items-center gap-1.5 text-blue-600 dark:text-blue-400 font-medium px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors text-xs">
                                <i class="fas fa-receipt"></i> Receipt
                            </a>
                            <a href="{{ route('parent.receipt.pdf', $payment->id) }}" class="inline-flex items-center justify-center text-green-600 dark:text-green-400 font-medium w-8 h-8 rounded-lg bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors text-xs" title="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-400 dark:text-gray-500 text-xl mx-auto mb-3">
                    <i class="fas fa-history"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400">No payment history found.</p>
            </div>
        @endforelse

        @if($payments->hasPages())
            <div class="pt-2">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Desktop Table Layout (>= 768px) -->
    <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Reference</th>
                        <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Date</th>
                        <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Student</th>
                        <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Amount</th>
                        <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Method</th>
                        <th class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                {{ $payment->reference_number ?? 'REC-'.str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $payment->paid_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold">
                                        {{ substr($payment->student->first_name, 0, 1) }}{{ substr($payment->student->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $payment->student->full_name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->student->student_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-gray-100">
                                ₱{{ number_format($payment->amount_paid, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <x-payment-status-badge :status="$payment->status" size="sm" />
                            </td>
                            <td class="px-6 py-4 capitalize">
                                {{ str_replace('_', ' ', $payment->method) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('parent.receipts.download', $payment->id) }}" class="inline-flex items-center gap-1.5 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium px-3 py-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                        <i class="fas fa-receipt"></i> View
                                    </a>
                                    <a href="{{ route('parent.receipt.pdf', $payment->id) }}" class="inline-flex items-center gap-1.5 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 font-medium px-3 py-1.5 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors" title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-400 dark:text-gray-500 text-xl">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <p>No payment history found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
