@extends('auth.user_dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Make a Payment</h1>
        <p class="text-gray-600 mt-1">Securely pay tuition and fees for your children.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
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

            <form method="POST" action="{{ route('parent.pay.store') }}" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Student</label>
                        <div class="relative">
                            <select name="student_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 pl-3 pr-10" required onchange="if(this.value) window.location.href='{{ route('parent.pay') }}?student_id=' + this.value">
                                <option value="">Select a student</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->student_id }}" {{ (old('student_id') == $s->student_id || (isset($selectedStudentId) && $selectedStudentId == $s->student_id)) ? 'selected' : '' }}>
                                        {{ $s->full_name }} ({{ $s->level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if(isset($currentBalance))
                    <div class="col-span-1 md:col-span-2 bg-blue-50 border border-blue-100 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0">
                        <div>
                             <p class="text-sm text-blue-800 font-medium">Current Outstanding Balance</p>
                             <p class="text-2xl font-bold text-blue-700">₱{{ number_format((float)$currentBalance, 2) }}</p>
                        </div>
                        <button type="button" onclick="document.querySelector('input[name=amount_paid]').value = '{{ $currentBalance }}'" class="w-full sm:w-auto text-sm bg-white border border-blue-200 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-50 font-medium transition-colors shadow-sm" {{ (float) $currentBalance <= 0 ? 'disabled' : '' }}>
                            Pay Full Amount
                        </button>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount (₱)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                            <input type="number" name="amount_paid" step="0.01" min="0.01" value="{{ old('amount_paid', (request()->boolean('pay_full') && isset($currentBalance) ? (float) $currentBalance : null)) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 pl-8" placeholder="0.00" required />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select name="method" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5" required>
                            <option value="gcash" {{ old('method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                            <option value="grab_pay" {{ old('method') == 'grab_pay' ? 'selected' : '' }}>GrabPay</option>
                            <option value="paymaya" {{ old('method') == 'paymaya' ? 'selected' : '' }}>Maya</option>
                            <option value="card" {{ old('method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                        </select>
                    </div>

                    <div class="col-span-1 md:col-span-2 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5" placeholder="e.g. TRX-123456789" />
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Remarks <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <textarea name="remarks" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5" placeholder="Add any additional notes here...">{{ old('remarks') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                    <a href="{{ route('parent.dashboard') }}" class="w-full sm:w-auto text-center px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">Cancel</a>
                    <button type="submit" class="w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                        <i class="fas fa-lock"></i> Secure Pay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
