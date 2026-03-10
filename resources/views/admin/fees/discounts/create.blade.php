<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Discount - E-Fees Portal</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" style="display: none;" x-cloak></div>

    <!-- Sidebar -->
    @include('layouts.admin_sidebar')

    <!-- Main Content -->
    <main class="flex-1 p-4 md:p-8 overflow-y-auto bg-slate-50 custom-scrollbar">
            <header class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create Discount Rule</h1>
                    <p class="text-gray-500 mt-1">Configure new discount type and eligibility.</p>
                </div>
                <a href="{{ route('admin.fees.index', ['tab' => 'discounts']) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </header>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form method="POST" action="{{ route('admin.fees.store-discount') }}" class="p-6 space-y-6">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-md bg-red-50 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <label for="discount_name" class="block text-sm font-medium text-gray-700">Discount Name <span class="text-red-500">*</span></label>
                            <input type="text" name="discount_name" id="discount_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('discount_name') }}" required placeholder="e.g. Academic Scholar - Full">
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type <span class="text-red-500">*</span></label>
                            <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" x-data @change="$dispatch('type-change', $el.value)">
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₱)</option>
                            </select>
                        </div>

                        <div x-data="{ isPercentage: '{{ old('type', 'percentage') }}' === 'percentage' }" @type-change.window="isPercentage = ($event.detail === 'percentage')">
                            <label for="value" class="block text-sm font-medium text-gray-700" x-text="isPercentage ? 'Percentage Value' : 'Amount Value'">Percentage Value <span class="text-red-500">*</span></label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" x-show="!isPercentage" x-cloak>
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="value" id="value" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" :class="!isPercentage ? 'pl-7' : ''" value="{{ old('value') }}" step="0.01" required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" x-show="isPercentage">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Scope and Target Charges -->
                        <div class="col-span-1 md:col-span-2" x-data="{ scope: '{{ old('apply_scope', 'total') }}' }">
                            <label for="apply_scope" class="block text-sm font-medium text-gray-700">Application Scope</label>
                            <select name="apply_scope" id="apply_scope" x-model="scope" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="total">Total Fee</option>
                                <option value="tuition_only">Tuition Only</option>
                                <option value="charges_only">All Additional Charges</option>
                                <option value="specific_charges">Specific Charges</option>
                            </select>

                            <div class="mt-4" x-show="scope === 'specific_charges'" x-cloak>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Charges</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($additionalCharges as $charge)
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="target_charge_ids[]" value="{{ $charge->id }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" {{ in_array($charge->id, old('target_charge_ids', [])) ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700">{{ $charge->charge_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Automatic Application -->
                        <div class="col-span-1 md:col-span-2">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_automatic" name="is_automatic" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ old('is_automatic') ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_automatic" class="font-medium text-gray-700">Automatic Application</label>
                                    <p class="text-gray-500">Automatically apply this discount to students in selected grades.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Applicable Grades (Visible if Automatic) -->
                        <div class="col-span-1 md:col-span-2" x-data="{ showGrades: {{ old('is_automatic') ? 'true' : 'false' }} }" @toggle-grades.window="showGrades = $event.detail" x-show="showGrades" x-cloak>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Applicable Grades</label>
                            <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                                @foreach($gradeLevels as $grade)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="applicable_grades[]" value="{{ $grade }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" {{ in_array($grade, old('applicable_grades', [])) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">{{ $grade }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Priority/Stackable controls removed for simplicity and consistency -->

                        <div class="col-span-1 md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                        </div>
                    </div>

                    <div class="pt-5 border-t border-gray-200 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.fees.index', ['tab' => 'discounts']) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Discount
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var nameInput = document.getElementById('discount_name');
        var autoCb = document.getElementById('is_automatic');
        function syncAutoLock() {
            var v = (nameInput.value || '').trim().toLowerCase();
            if (v.indexOf('academic scholar') !== -1) {
                autoCb.checked = false;
                autoCb.disabled = true;
            } else {
                autoCb.disabled = false;
            }
        }
        if (nameInput && autoCb) {
            nameInput.addEventListener('input', syncAutoLock);
            syncAutoLock();
        }
    });
</script>
</body>
</html>
