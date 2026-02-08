@extends('auth.user_dashboard')

@section('content')
<div class="min-h-[calc(100vh-4rem)] p-6 flex flex-col">
    <!-- Back Link -->
    <div class="w-full mb-6 no-print">
        <a href="{{ route('parent.history') }}" class="text-slate-500 hover:text-blue-600 inline-flex items-center gap-2 font-medium transition-colors text-sm">
            <i class="fas fa-arrow-left"></i> Back to Payment History
        </a>
    </div>

    <!-- Receipt Container Wrapper -->
    <div class="flex-1 flex items-center justify-center pb-10">
        <!-- Receipt Container -->
        <div class="w-full max-w-sm bg-white rounded-2xl overflow-hidden shadow-xl receipt-container border border-blue-100 relative">
        <!-- Header -->
        <div class="bg-blue-600 p-4 text-center text-white relative">
            <h1 class="text-lg font-bold mb-6">E-Fees Portal</h1>
            
            <!-- Avatar/Icon -->
            <div class="absolute left-1/2 transform -translate-x-1/2 -bottom-7">
                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center border-4 border-white shadow-sm overflow-hidden">
                    @if(isset($payment->student->sex) && str_contains(strtolower($payment->student->sex), 'female'))
                        <!-- Female Silhouette -->
                        <svg class="w-full h-full text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 4a4 4 0 014 4 4 4 0 01-4 4 4 4 0 01-4-4 4 4 0 014-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4z"/>
                        </svg>
                    @else
                        <!-- Male/Generic Silhouette -->
                        <svg class="w-full h-full text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-6 pt-9 pb-4">
            <!-- Contact Pill -->
            <div class="flex justify-center mb-2">
                <span class="bg-blue-400 text-white px-3 py-0.5 rounded-full text-xs font-bold shadow-sm">
                    +63 993 269 7592
                </span>
            </div>

            <div class="text-center mb-3">
                <h2 class="text-lg font-bold text-gray-900">Payment Successful</h2>
                <p class="text-blue-500 font-bold text-xs">E-Fees Portal</p>
            </div>

            <!-- Compact Info Grid -->
            <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-100">
                <!-- Paid To -->
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-gray-200">
                    <div class="flex items-center gap-1.5">
                        <i class="fas fa-school text-green-600 text-sm"></i>
                        <span class="font-bold text-gray-900 text-xs">{{ \App\Models\SystemSetting::where('key', 'school_name')->value('value') ?: 'E-Fees School' }}</span>
                    </div>
                    <span class="text-xs text-gray-500">{{ \App\Models\SystemSetting::where('key', 'school_year')->value('value') ?: date('Y') . '-' . (date('Y') + 1) }}</span>
                </div>

                <!-- Student Info -->
                <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                    <div>
                        <span class="text-gray-500 block">Student Name</span>
                        <span class="font-bold text-gray-900 truncate block" title="{{ $payment->student->full_name }}">{{ $payment->student->full_name }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-gray-500 block">Student ID</span>
                        <span class="font-bold text-gray-900">{{ $payment->student->student_id }}</span>
                    </div>
                </div>
                
                <!-- Payment Details -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-gray-500 block">Bill Type</span>
                        <span class="font-bold text-gray-900">Tuition Fee</span>
                    </div>
                    <div class="text-right">
                        <span class="text-gray-500 block">Method</span>
                        <span class="font-bold text-gray-900 capitalize">{{ str_replace('_', ' ', $payment->method ?? 'Cash') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="text-center mb-3">
                <h1 class="text-3xl font-extrabold text-gray-900">₱{{ number_format($payment->amount_paid, 2) }}</h1>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total Amount Paid</p>
                <div class="text-xs text-gray-400 mt-1 flex justify-center gap-3">
                    <span>{{ $payment->created_at->format('M d, Y - h:i A') }}</span>
                    <span class="text-green-600 font-bold">Completed</span>
                </div>
            </div>

            <!-- Notification Box -->
            <div class="bg-blue-50 rounded-lg p-2 flex items-start gap-2 mb-3">
                <div class="bg-blue-100 rounded-full p-1 shrink-0 mt-0.5">
                    <i class="fas fa-envelope text-blue-600 text-xs"></i>
                </div>
                <div>
                    <p class="font-bold text-blue-900 text-xs">SMS Sent</p>
                    <p class="text-blue-700 text-[10px] leading-tight">Confirmation sent to registered mobile.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <div class="flex items-center justify-center gap-1 text-[10px] text-gray-400">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Powered By: <span class="font-bold text-gray-500">E-Fees Portal</span></span>
                </div>
                <div class="mt-2 text-[9px] text-gray-300 font-mono">
                    REF: {{ $payment->reference_number ?? 'REC-' . str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="bg-blue-600 p-3 no-print hover:bg-blue-700 transition-colors cursor-pointer text-center" onclick="window.print()">
            <span class="text-white font-bold text-sm flex items-center justify-center gap-2">
                Download receipt <i class="fas fa-download"></i>
            </span>
        </div>
        </div>
    </div>
</div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .receipt-container, .receipt-container * {
            visibility: visible;
        }
        .receipt-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none;
            border: none;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection
