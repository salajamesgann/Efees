<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Additional Charge - E-Fees Portal</title>
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
                    <h1 class="text-3xl font-bold text-gray-900">Edit Additional Charge</h1>
                    <p class="text-gray-500 mt-1">Update details for {{ $additionalCharge->charge_name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('admin.fees.destroy-charge', $additionalCharge->id) }}" onsubmit="return confirm('Are you sure you want to delete this charge? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash-alt mr-2"></i> Delete
                        </button>
                    </form>
                    <a href="{{ route('admin.fees.index', ['tab' => 'charges']) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i> Back to List
                    </a>
                </div>
            </header>
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex justify-between items-center">
                    <p>{{ session('success') }}</p>
                    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm flex justify-between items-center">
                    <p>{{ session('error') }}</p>
                    <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form method="POST" action="{{ route('admin.fees.update-charge', $additionalCharge->id) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <label for="charge_name" class="block text-sm font-medium text-gray-700">Charge Name <span class="text-red-500">*</span></label>
                            <input type="text" name="charge_name" id="charge_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('charge_name', $additionalCharge->charge_name) }}" required>
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount <span class="text-red-500">*</span></label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="amount" id="amount" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md" value="{{ old('amount', $additionalCharge->amount) }}" step="0.01" required>
                            </div>
                        </div>

                        <div>
                            <label for="charge_type" class="block text-sm font-medium text-gray-700">Type <span class="text-red-500">*</span></label>
                            <select name="charge_type" id="charge_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="one_time" {{ old('charge_type', $additionalCharge->charge_type) == 'one_time' ? 'selected' : '' }}>One-Time</option>
                                <option value="recurring" {{ old('charge_type', $additionalCharge->charge_type) == 'recurring' ? 'selected' : '' }}>Recurring</option>
                            </select>
                        </div>

                        <div>
                            <label for="school_year" class="block text-sm font-medium text-gray-700">School Year <span class="text-red-500">*</span></label>
                            <input type="text" name="school_year" id="school_year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('school_year', $additionalCharge->school_year) }}" required>
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('due_date', $additionalCharge->due_date) }}">
                        </div>

                        <div>
                            <label for="applies_to" class="block text-sm font-medium text-gray-700">Applies To <span class="text-red-500">*</span></label>
                            <select name="applies_to" id="applies_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" x-data @change="$dispatch('applies-change', $el.value)">
                                <option value="all" {{ old('applies_to', $additionalCharge->applies_to) == 'all' ? 'selected' : '' }}>All Students</option>
                                <option value="grades" {{ old('applies_to', $additionalCharge->applies_to) == 'grades' ? 'selected' : '' }}>Specific Grades</option>
                            </select>
                        </div>

                        <div class="col-span-1 md:col-span-2" x-data="{ show: '{{ old('applies_to', $additionalCharge->applies_to) }}' === 'grades' }" @applies-change.window="show = ($event.detail === 'grades')" x-show="show" x-cloak>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Grade Levels</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach(['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'] as $grade)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="grade_levels[]" value="{{ $grade }}" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                            {{ (is_array(old('grade_levels')) && in_array($grade, old('grade_levels'))) || (is_array($additionalCharge->grade_levels) && in_array($grade, $additionalCharge->grade_levels)) ? 'checked' : '' }}>
                                        <label class="ml-2 block text-sm text-gray-900">{{ $grade }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description / Notes</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('description', $additionalCharge->description) }}</textarea>
                        </div>
                        
                        <div>
                             <label for="required_or_optional" class="block text-sm font-medium text-gray-700">Required or Optional</label>
                             <select name="required_or_optional" id="required_or_optional" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                 <option value="required" {{ old('required_or_optional', $additionalCharge->required_or_optional) == 'required' ? 'selected' : '' }}>Required</option>
                                 <option value="optional" {{ old('required_or_optional', $additionalCharge->required_or_optional) == 'optional' ? 'selected' : '' }}>Optional</option>
                             </select>
                        </div>
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="allow_installment" id="allow_installment" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('allow_installment', $additionalCharge->allow_installment) ? 'checked' : '' }}>
                                <label for="allow_installment" class="ml-2 block text-sm text-gray-900">Allow Installment</label>
                            </div>
                            <div class="flex items-center">
                                <label for="status" class="mr-3 block text-sm text-gray-700">Status</label>
                                <select name="status" id="status" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="active" {{ old('status', $additionalCharge->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $additionalCharge->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pt-5 border-t border-gray-200 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.fees.index', ['tab' => 'charges']) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Charge
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
