@extends('auth.user_dashboard')

@section('content')
<div class="max-w-5xl mx-auto pb-16 md:pb-0">
    <!-- Actions -->
    <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <a href="{{ route('parent.fees.show', $student->student_id) }}" class="flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition-colors text-sm">
            <i class="fas fa-arrow-left"></i> Back to Fee Breakdown
        </a>
        <a href="{{ route('parent.pay', ['student_id' => $student->student_id]) }}" class="w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 sm:py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 dark:shadow-blue-900/30 transition-all hover:-translate-y-0.5 flex items-center gap-2 active:translate-y-0">
            <i class="fas fa-credit-card"></i> Pay Now
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 md:p-8 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-5 sm:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start gap-3 sm:gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2.5 sm:gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-blue-100 dark:bg-blue-900/40 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600 dark:text-blue-400 text-sm sm:text-base"></i>
                    </div>
                    Payment Schedule
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Installment plan for <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $student->full_name }}</span></p>
            </div>
            <div class="text-left md:text-right bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-100 dark:border-gray-600 min-w-[200px]">
                <div class="space-y-1">
                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">Total Fees:</span>
                        <span class="font-bold text-gray-900 dark:text-gray-100">₱{{ number_format((float) ($totals['totalAmount'] ?? 0), 2) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">Total Paid:</span>
                        <span class="font-bold text-green-600 dark:text-green-400">₱{{ number_format((float) $totalPaid, 2) }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-t border-gray-200 dark:border-gray-600 pt-1 mt-1">
                        <span class="text-gray-700 dark:text-gray-300 text-sm font-bold">Balance:</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">₱{{ number_format((float) ($totals['remainingBalance'] ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($installments->count() > 0)
    <!-- Progress Bar -->
    @php
        $paidCount = $installments->where('status', 'paid')->count();
        $totalCount = $installments->count();
        $progressPercent = $totalCount > 0 ? round(($paidCount / $totalCount) * 100) : 0;
    @endphp
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Overall Progress</p>
            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $paidCount }} / {{ $totalCount }} installments paid</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $progressPercent }}% complete</p>
    </div>

    <!-- Mobile Card Layout (< md) -->
    <div class="md:hidden space-y-3 mb-6">
        @foreach($installments as $index => $installment)
            @php
                $isPaid = $installment->status === 'paid';
                $isPartial = $installment->status === 'partial';
                $isOverdue = !$isPaid && $installment->payment_date && \Carbon\Carbon::parse($installment->payment_date)->isPast();
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $isOverdue ? 'border-red-200 dark:border-red-800/50 bg-red-50/30 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-700' }} p-4">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                            {{ $isPaid ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400' : ($isOverdue ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400') }}">
                            @if($isPaid)
                                <i class="fas fa-check"></i>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $installment->notes ?: 'Installment ' . ($index + 1) }}</p>
                            <p class="text-xs {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-500 dark:text-gray-400' }}">
                                @if($installment->payment_date)
                                    Due: {{ \Carbon\Carbon::parse($installment->payment_date)->format('M d, Y') }}
                                    @if($isOverdue) &mdash; <span class="font-bold">Overdue</span> @endif
                                @else
                                    Due: TBD
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($isPaid)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-400 flex-shrink-0">
                            <i class="fas fa-check-circle"></i> Paid
                        </span>
                    @elseif($isPartial)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-400 flex-shrink-0">
                            <i class="fas fa-adjust"></i> Partial
                        </span>
                    @elseif($isOverdue)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-400 flex-shrink-0">
                            <i class="fas fa-exclamation-triangle"></i> Overdue
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 flex-shrink-0">
                            <i class="fas fa-clock"></i> Upcoming
                        </span>
                    @endif
                </div>
                <div class="flex items-center justify-between text-sm mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Amount</span>
                        <p class="font-medium text-gray-900 dark:text-gray-100">₱{{ number_format((float) $installment->amount, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Balance</span>
                        <p class="font-bold {{ $isPaid ? 'text-green-600 dark:text-green-400' : ($isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100') }}">₱{{ number_format((float) $installment->balance, 2) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
        <!-- Mobile Total -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 p-4 flex items-center justify-between">
            <span class="font-bold text-gray-900 dark:text-gray-100">Total</span>
            <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-400">Amount: <span class="font-bold text-gray-900 dark:text-gray-100">₱{{ number_format($installments->sum('amount'), 2) }}</span></p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Balance: <span class="font-bold text-blue-600 dark:text-blue-400">₱{{ number_format($installments->sum('balance'), 2) }}</span></p>
            </div>
        </div>
    </div>

    <!-- Desktop Table Layout (>= md) -->
    <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Due Date</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                        <th class="px-6 py-4 text-right">Balance</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($installments as $index => $installment)
                        @php
                            $isPaid = $installment->status === 'paid';
                            $isPartial = $installment->status === 'partial';
                            $isOverdue = !$isPaid && $installment->payment_date && \Carbon\Carbon::parse($installment->payment_date)->isPast();
                            $isCurrent = !$isPaid && !$isOverdue && $installment->payment_date && \Carbon\Carbon::parse($installment->payment_date)->isFuture();
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ $isOverdue ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                            <td class="px-6 py-4">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold 
                                    {{ $isPaid ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400' : ($isOverdue ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400') }}">
                                    @if($isPaid)
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                {{ $installment->notes ?: 'Installment ' . ($index + 1) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                                @if($installment->payment_date)
                                    {{ \Carbon\Carbon::parse($installment->payment_date)->format('M d, Y') }}
                                    @if($isOverdue)
                                        <span class="block text-xs text-red-500 font-bold uppercase tracking-wider">Overdue</span>
                                    @endif
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">TBD</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-gray-100">
                                ₱{{ number_format((float) $installment->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold {{ $isPaid ? 'text-green-600 dark:text-green-400' : ($isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100') }}">
                                ₱{{ number_format((float) $installment->balance, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isPaid)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-400">
                                        <i class="fas fa-check-circle"></i> Paid
                                    </span>
                                @elseif($isPartial)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-400">
                                        <i class="fas fa-adjust"></i> Partial
                                    </span>
                                @elseif($isOverdue)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-400">
                                        <i class="fas fa-exclamation-triangle"></i> Overdue
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-clock"></i> Upcoming
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                    <tr>
                        <td colspan="3" class="px-6 py-4 font-bold text-gray-900 dark:text-gray-100">Total</td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-gray-100">₱{{ number_format($installments->sum('amount'), 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-blue-600 dark:text-blue-400">₱{{ number_format($installments->sum('balance'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @else
    <!-- No Installments -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
            <i class="fas fa-calendar-times text-gray-400 dark:text-gray-500 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">No Payment Schedule</h3>
        <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-md mx-auto">
            No installment plan has been set up for this student yet. The school may not have configured a payment schedule, or the full amount may be due in a single payment.
        </p>
        <a href="{{ route('parent.pay', ['student_id' => $student->student_id]) }}" class="inline-flex items-center gap-2 mt-6 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold transition-colors">
            <i class="fas fa-credit-card"></i> Pay Full Amount
        </a>
    </div>
    @endif
</div>
@endsection
