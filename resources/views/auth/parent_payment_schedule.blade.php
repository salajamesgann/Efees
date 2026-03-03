@extends('auth.user_dashboard')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <a href="{{ route('parent.fees.show', $student->student_id) }}" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition-colors">
            <i class="fas fa-arrow-left"></i> Back to Fee Breakdown
        </a>
        <a href="{{ route('parent.pay', ['student_id' => $student->student_id]) }}" class="w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5 flex items-center gap-2">
            <i class="fas fa-credit-card"></i> Pay Now
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-200 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                    </div>
                    Payment Schedule
                </h1>
                <p class="text-gray-500 mt-2">Installment plan for <span class="font-semibold text-gray-700">{{ $student->full_name }}</span></p>
            </div>
            <div class="text-left md:text-right bg-gray-50 rounded-xl p-4 border border-gray-100 min-w-[200px]">
                <div class="space-y-1">
                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500 text-sm">Total Fees:</span>
                        <span class="font-bold text-gray-900">₱{{ number_format((float) ($totals['totalAmount'] ?? 0), 2) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500 text-sm">Total Paid:</span>
                        <span class="font-bold text-green-600">₱{{ number_format((float) $totalPaid, 2) }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-t border-gray-200 pt-1 mt-1">
                        <span class="text-gray-700 text-sm font-bold">Balance:</span>
                        <span class="font-bold text-blue-600">₱{{ number_format((float) ($totals['remainingBalance'] ?? 0), 2) }}</span>
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
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mb-6">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-semibold text-gray-700">Overall Progress</p>
            <span class="text-sm font-bold text-blue-600">{{ $paidCount }} / {{ $totalCount }} installments paid</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
        </div>
        <p class="text-xs text-gray-500 mt-2">{{ $progressPercent }}% complete</p>
    </div>

    <!-- Installments Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-900 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Due Date</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                        <th class="px-6 py-4 text-right">Balance</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($installments as $index => $installment)
                        @php
                            $isPaid = $installment->status === 'paid';
                            $isPartial = $installment->status === 'partial';
                            $isOverdue = !$isPaid && $installment->payment_date && \Carbon\Carbon::parse($installment->payment_date)->isPast();
                            $isCurrent = !$isPaid && !$isOverdue && $installment->payment_date && \Carbon\Carbon::parse($installment->payment_date)->isFuture();
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $isOverdue ? 'bg-red-50/50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold 
                                    {{ $isPaid ? 'bg-green-100 text-green-700' : ($isOverdue ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                    @if($isPaid)
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $installment->notes ?: 'Installment ' . ($index + 1) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                @if($installment->payment_date)
                                    {{ \Carbon\Carbon::parse($installment->payment_date)->format('M d, Y') }}
                                    @if($isOverdue)
                                        <span class="block text-[10px] text-red-500 font-bold uppercase tracking-wider">Overdue</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">TBD</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">
                                ₱{{ number_format((float) $installment->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold {{ $isPaid ? 'text-green-600' : ($isOverdue ? 'text-red-600' : 'text-gray-900') }}">
                                ₱{{ number_format((float) $installment->balance, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isPaid)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle"></i> Paid
                                    </span>
                                @elseif($isPartial)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-adjust"></i> Partial
                                    </span>
                                @elseif($isOverdue)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle"></i> Overdue
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                        <i class="fas fa-clock"></i> Upcoming
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="3" class="px-6 py-4 font-bold text-gray-900">Total</td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">₱{{ number_format($installments->sum('amount'), 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-blue-600">₱{{ number_format($installments->sum('balance'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @else
    <!-- No Installments -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
            <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900">No Payment Schedule</h3>
        <p class="text-gray-500 mt-2 max-w-md mx-auto">
            No installment plan has been set up for this student yet. The school may not have configured a payment schedule, or the full amount may be due in a single payment.
        </p>
        <a href="{{ route('parent.pay', ['student_id' => $student->student_id]) }}" class="inline-flex items-center gap-2 mt-6 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold transition-colors">
            <i class="fas fa-credit-card"></i> Pay Full Amount
        </a>
    </div>
    @endif
</div>
@endsection
