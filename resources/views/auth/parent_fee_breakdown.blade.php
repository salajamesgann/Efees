@extends('auth.user_dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('parent.dashboard', ['student_id' => $student->student_id]) }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2 mb-4 font-medium transition-colors">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Fee Breakdown</h1>
                <p class="text-gray-600 mt-1">Detailed fee structure for <span class="font-bold text-gray-800">{{ $student->full_name }}</span></p>
            </div>
            <a href="{{ route('parent.soa', $student->student_id) }}" class="inline-flex items-center gap-2 text-blue-600 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl font-medium transition-colors">
                <i class="fas fa-file-invoice-dollar"></i> View SOA
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="space-y-4">
                <!-- Tuition -->
                <div class="flex justify-between items-center gap-4 py-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <span class="font-medium text-gray-700">Base Tuition</span>
                    </div>
                    <span class="font-bold text-gray-900 whitespace-nowrap">₱{{ number_format((float)($totals['baseTuition'] ?? 0), 2) }}</span>
                </div>

                <!-- Charges -->
                <div class="flex justify-between items-center gap-4 py-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-600">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <span class="font-medium text-gray-700">Additional Charges</span>
                    </div>
                    <span class="font-bold text-gray-900 whitespace-nowrap">₱{{ number_format((float)($totals['chargesTotal'] ?? 0), 2) }}</span>
                </div>

                <!-- Discounts -->
                <div class="flex justify-between items-center gap-4 py-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                            <i class="fas fa-tags"></i>
                        </div>
                        <span class="font-medium text-gray-700">Discounts Applied</span>
                    </div>
                    <span class="font-bold text-green-600 whitespace-nowrap">-₱{{ number_format((float)($totals['discountsTotal'] ?? 0), 2) }}</span>
                </div>
                
                <!-- SHS Voucher Exclusion Notice -->
                @if($student->is_shs_voucher)
                <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg border border-gray-100 mx-3 mb-2">
                    <div class="flex items-center gap-3 pl-10">
                         <span class="text-sm text-gray-500 italic">Sibling Discount</span>
                    </div>
                    <span class="text-sm text-gray-400 italic">Not Applicable (SHS Voucher)</span>
                </div>
                @endif

                <!-- Penalties -->
                @if(($totals['penaltiesTotal'] ?? 0) > 0)
                <div class="flex justify-between items-center gap-4 py-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-600">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <span class="font-medium text-gray-700">Penalties & Fines</span>
                    </div>
                    <span class="font-bold text-red-600 whitespace-nowrap">+₱{{ number_format((float)($totals['penaltiesTotal'] ?? 0), 2) }}</span>
                </div>
                @endif

                <!-- Total Payable (Assessment + Penalties) -->
                <div class="flex justify-between items-center gap-4 py-3 border-b border-gray-100 bg-gray-50/30 px-3 rounded-lg mt-2">
                    <span class="font-bold text-gray-700">Total Payable</span>
                    <span class="font-bold text-gray-900 whitespace-nowrap">₱{{ number_format((float)($totals['totalAmount'] ?? 0) + (float)($totals['penaltiesTotal'] ?? 0), 2) }}</span>
                </div>

                <!-- Paid Amount -->
                <div class="flex justify-between items-center gap-4 py-3 border-b border-gray-100 px-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <i class="fas fa-check-circle text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-700">Total Paid</span>
                    </div>
                    <span class="font-bold text-emerald-600 whitespace-nowrap">-₱{{ number_format((float)($totals['paidAmount'] ?? 0), 2) }}</span>
                </div>

                <!-- Remaining Balance -->
                <div class="flex justify-between items-center gap-4 pt-4 mt-4 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                    <span class="text-lg font-bold text-gray-900">Remaining Balance</span>
                    <span class="text-2xl font-bold text-blue-700 whitespace-nowrap">₱{{ number_format((float)($totals['remainingBalance'] ?? 0), 2) }}</span>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-end gap-3">
             <a href="{{ route('parent.pay', ['student_id' => $student->student_id]) }}" class="w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <i class="fas fa-credit-card"></i> Pay Now
            </a>
        </div>
    </div>
</div>
@endsection
