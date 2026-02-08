@extends('auth.user_dashboard')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payment History</h1>
            <p class="text-gray-600 mt-1">View past transactions and download receipts.</p>
        </div>
        <div>
            <a href="{{ route('parent.pay') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5 gap-2">
                <i class="fas fa-plus"></i> New Payment
            </a>
        </div>
    </div>

    <!-- Payment List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-900">Reference</th>
                        <th class="px-6 py-4 font-semibold text-gray-900">Date</th>
                        <th class="px-6 py-4 font-semibold text-gray-900">Student</th>
                        <th class="px-6 py-4 font-semibold text-gray-900">Amount</th>
                        <th class="px-6 py-4 font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-900">Method</th>
                        <th class="px-6 py-4 font-semibold text-gray-900 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $payment->reference_number ?? 'REC-'.str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $payment->paid_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                        {{ substr($payment->student->first_name, 0, 1) }}{{ substr($payment->student->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $payment->student->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $payment->student->student_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                ₱{{ number_format($payment->amount_paid, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($payment->status === 'approved' || $payment->status === 'paid' || $payment->status === null)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle"></i> Paid
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 capitalize">
                                {{ str_replace('_', ' ', $payment->method) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('parent.receipts.download', $payment->id) }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                                    <i class="fas fa-receipt"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 text-xl">
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
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
