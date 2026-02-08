<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Tuition Fee - E-Fees Portal</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar for Sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
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
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white text-slate-800 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 border-r border-slate-200 flex flex-col shrink-0 shadow-[4px_0_24px_-12px_rgba(0,0,0,0.1)]" id="sidebar">
        <div class="flex items-center justify-between gap-3 px-8 py-6 border-b border-slate-100 bg-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <div>
                    <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Efees Admin</h1>
                    <p class="text-xs text-slate-500 font-medium">Administration</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow pb-6 overflow-y-auto custom-scrollbar">
            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
            
            <!-- Dashboard -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin_dashboard') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin_dashboard') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('admin_dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Dashboard</span>
            </a>


            <!-- Student Management -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.students.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.students.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-users text-lg {{ request()->routeIs('admin.students.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Student Management</span>
            </a>

            <!-- Parent Management removed -->

            <!-- User Management -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.staff.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.staff.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-chalkboard-teacher text-lg {{ request()->routeIs('admin.staff.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">User Management</span>
            </a>

            <!-- Fee Management -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fees.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-file-invoice-dollar text-lg {{ request()->routeIs('admin.fees.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Fee Management</span>
            </a>

            <!-- Payment Approvals -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.payment_approvals.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.payment_approvals.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-check-double text-lg {{ request()->routeIs('admin.payment_approvals.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Payment Approvals</span>
            </a>

            <!-- Reports & Analytics -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.reports.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-chart-bar text-lg {{ request()->routeIs('admin.reports.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Reports & Analytics</span>
            </a>

            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-6">System</p>

            <!-- Audit Logs -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.audit-logs.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-shield-alt text-lg {{ request()->routeIs('admin.audit-logs.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Audit Logs</span>
            </a>

            <!-- SMS Control -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.sms.logs') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-comment-alt text-lg {{ request()->routeIs('admin.sms.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">SMS Control</span>
            </a>

            <!-- Settings -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.settings.index') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-cog text-lg {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Settings</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 px-4 pb-6">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 hover:shadow-sm transition-all duration-200 group border border-red-100">
                    <div class="w-8 flex justify-center">
                        <i class="fas fa-sign-out-alt text-lg group-hover:scale-110 transition-transform"></i>
                    </div>
                    <span class="text-sm font-bold">Logout</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Mobile Header -->
        <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-slate-800 tracking-tight">Efees Admin</span>
            </div>
            <button @click="sidebarOpen = true" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-50 rounded-lg transition-all">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

    <!-- Main Content -->
    <main class="flex-1 p-4 md:p-8 overflow-y-auto bg-slate-50">
        <header class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Tuition Fee</h1>
                <p class="text-gray-500 mt-1">Update tuition fee details, components, and attached charges.</p>
            </div>
            <a href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </header>

        @php
            $notes = (string) ($tuitionFee->notes ?? '');
            $feeName = $notes;
            $pos = mb_strpos($notes, ' — ');
            if ($pos !== false) $feeName = mb_substr($notes, 0, $pos);
            if (!$feeName) $feeName = $tuitionFee->grade_level.' Tuition – SY '.($tuitionFee->school_year ?? 'N/A');
            $ps = is_array($tuitionFee->payment_schedule) ? $tuitionFee->payment_schedule : [];
            $installmentAllowed = (bool) ($ps['installment_allowed'] ?? false);
            $plan = (string) ($ps['plan'] ?? '');
            $defaultChargeIds = is_array($tuitionFee->default_charge_ids ?? null) ? $tuitionFee->default_charge_ids : [];
            $defaultDiscountIds = is_array($tuitionFee->default_discount_ids ?? null) ? $tuitionFee->default_discount_ids : [];
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 m-6 mb-0">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                There were errors with your submission:
                            </p>
                            <ul class="mt-1 list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 m-6 mb-0">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.fees.update-tuition', $tuitionFee) }}" id="editTuitionForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="subject_fees" id="edit_subject_fees" value="">
                <input type="hidden" name="selected_charge_ids" id="edit_selected_charge_ids" value="">
                <input type="hidden" name="selected_discount_ids" id="edit_selected_discount_ids" value="">

                <!-- Section 1: Basic Information -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <label for="edit_fee_name" class="block text-sm font-medium text-gray-700">Fee Name <span class="text-red-500">*</span></label>
                            <input type="text" name="fee_name" id="edit_fee_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ $feeName }}" required>
                        </div>
                        <div>
                            <label for="edit_is_active" class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-2 flex items-center">
                                <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ $tuitionFee->is_active ? 'checked' : '' }}>
                                <label for="edit_is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                            </div>
                        </div>
                        <div>
                            <label for="edit_grade_level" class="block text-sm font-medium text-gray-700">Grade Level</label>
                            <select name="grade_level" id="edit_grade_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                @foreach($gradeLevels as $gl)
                                    <option value="{{ $gl }}" {{ $tuitionFee->grade_level === $gl ? 'selected' : '' }}>{{ $gl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="edit_school_year" class="block text-sm font-medium text-gray-700">Academic Year</label>
                            <input type="text" name="school_year" id="edit_school_year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ $tuitionFee->school_year }}">
                        </div>
                        <div>
                            <label for="edit_fee_deadline" class="block text-sm font-medium text-gray-700">Fee Deadline</label>
                            <input type="date" name="fee_deadline" id="edit_fee_deadline" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ $tuitionFee->fee_deadline ? \Illuminate\Support\Str::substr($tuitionFee->fee_deadline,0,10) : '' }}">
                        </div>
                        
                        <!-- SHS Fields -->
                        <div>
                            <label for="edit_track" class="block text-sm font-medium text-gray-700">Senior High Track</label>
                            <select name="track" id="edit_track" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" {{ in_array($tuitionFee->grade_level,['Grade 11','Grade 12']) ? '' : 'disabled' }}>
                                <option value="" {{ empty($tuitionFee->track) ? 'selected' : '' }}>None</option>
                                <option value="Academic" {{ ($tuitionFee->track ?? '') === 'Academic' ? 'selected' : '' }}>Academic</option>
                                <option value="TVL" {{ ($tuitionFee->track ?? '') === 'TVL' ? 'selected' : '' }}>TVL</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_strand" class="block text-sm font-medium text-gray-700">Senior High Strand</label>
                            <select name="strand" id="edit_strand" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" {{ in_array($tuitionFee->grade_level,['Grade 11','Grade 12']) ? '' : 'disabled' }}>
                                <option value="" {{ empty($tuitionFee->strand) ? 'selected' : '' }}>None</option>
                                <option value="STEM" {{ ($tuitionFee->strand ?? '') === 'STEM' ? 'selected' : '' }}>STEM</option>
                                <option value="ABM" {{ ($tuitionFee->strand ?? '') === 'ABM' ? 'selected' : '' }}>ABM</option>
                                <option value="HUMSS" {{ ($tuitionFee->strand ?? '') === 'HUMSS' ? 'selected' : '' }}>HUMSS</option>
                                <option value="ICT" {{ ($tuitionFee->strand ?? '') === 'ICT' ? 'selected' : '' }}>ICT</option>
                            </select>
                        </div>
                        <div class="col-span-1 md:col-span-2 lg:col-span-3">
                            <label for="edit_notes" class="block text-sm font-medium text-gray-700">Notes / Description</label>
                            <textarea name="notes" id="edit_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ $tuitionFee->notes }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Installment Settings -->
                <div class="p-6 border-b border-gray-200 bg-gray-50/50">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Payment Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="edit_allow_installment" class="block text-sm font-medium text-gray-700">Allow Installment?</label>
                            <select name="allow_installment" id="edit_allow_installment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="0" {{ ! $installmentAllowed ? 'selected' : '' }}>No, Full Payment Only</option>
                                <option value="1" {{ $installmentAllowed ? 'selected' : '' }}>Yes, Allow Installments</option>
                            </select>
                        </div>
                        <div class="{{ $installmentAllowed ? '' : 'hidden' }}">
                            <label for="edit_payment_plan" class="block text-sm font-medium text-gray-700">Payment Plan</label>
                            <select name="payment_plan" id="edit_payment_plan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select Plan</option>
                                <option value="monthly" {{ $plan === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ $plan === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="semester" {{ $plan === 'semester' ? 'selected' : '' }}>Per Semester</option>
                            </select>
                        </div>
                    </div>
                    <!-- Schedule Preview -->
                    <div id="schedule_preview_container" class="mt-6 {{ $installmentAllowed && $plan ? '' : 'hidden' }}">
                        <h4 class="text-sm font-bold text-gray-700 mb-2">Generated Schedule Preview</h4>
                        <div class="bg-white border border-gray-200 rounded-md p-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Installment</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="schedule_preview_body" class="bg-white divide-y divide-gray-200">
                                    <!-- Populated via JS -->
                                </tbody>
                            </table>
                            <p class="text-xs text-gray-500 mt-2 italic">* Amounts are estimates based on current total tuition. Actual amounts may vary slightly due to rounding.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Components & Charges -->
                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Tuition Components -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Tuition Components</h3>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                                <input type="text" id="edit_comp_name" placeholder="Name (e.g. Misc)" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <input type="number" id="edit_comp_amount" placeholder="Amount" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <button type="button" id="edit_add_component_btn" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Add
                                </button>
                            </div>
                        </div>
                        <div id="edit_components_list" class="space-y-2">
                            <!-- Populated via JS -->
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                            <span class="font-bold text-gray-700">Total Tuition:</span>
                            <span class="font-bold text-xl text-blue-600" id="edit_total_tuition">₱0.00</span>
                        </div>
                    </div>

                    <!-- Additional Charges & Discounts -->
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Additional Charges</h3>
                            @if(isset($availableCharges) && count($availableCharges) > 0)
                                <div class="bg-white border border-gray-200 rounded-lg max-h-60 overflow-y-auto">
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($availableCharges as $charge)
                                            <li class="flex items-center px-4 py-3 hover:bg-gray-50">
                                                <input type="checkbox" 
                                                       class="edit-tuition-charge-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                       value="{{ $charge->id }}"
                                                       data-amount="{{ $charge->amount }}"
                                                       data-type="{{ $charge->charge_type }}"
                                                       {{ in_array($charge->id, $defaultChargeIds) ? 'checked' : '' }}>
                                                <div class="ml-3 flex-1">
                                                    <span class="block text-sm font-medium text-gray-900">{{ $charge->charge_name }}</span>
                                                    <span class="block text-xs text-gray-500">{{ ucfirst($charge->charge_type) }} — ₱{{ number_format($charge->amount, 2) }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">No additional charges available.</p>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Applicable Discounts</h3>
                            @if(isset($availableDiscounts) && count($availableDiscounts) > 0)
                                <div class="bg-white border border-gray-200 rounded-lg max-h-60 overflow-y-auto">
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($availableDiscounts as $discount)
                                            <li class="flex items-center px-4 py-3 hover:bg-gray-50">
                                                <input type="checkbox" 
                                                       class="edit-tuition-discount-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                       value="{{ $discount->id }}"
                                                       {{ in_array($discount->id, $defaultDiscountIds) ? 'checked' : '' }}>
                                                <div class="ml-3 flex-1">
                                                    <span class="block text-sm font-medium text-gray-900">{{ $discount->name }}</span>
                                                    <span class="block text-xs text-gray-500">{{ $discount->type === 'percentage' ? $discount->value . '%' : '₱' . number_format($discount->value, 2) }} Off</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">No discounts available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm">
                        Update Tuition Fee
                    </button>
                </div>
            </form>
        </div>
    </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Existing Logic for Edit Tuition
            const gradeSelect = document.getElementById('edit_grade_level');
            const trackSelect = document.getElementById('edit_track');
            const strandSelect = document.getElementById('edit_strand');
            
            function toggleSHSFields() {
                const val = gradeSelect.value;
                const isSHS = (val === 'Grade 11' || val === 'Grade 12');
                if(isSHS) {
                    trackSelect.removeAttribute('disabled');
                    strandSelect.removeAttribute('disabled');
                } else {
                    trackSelect.setAttribute('disabled','disabled');
                    strandSelect.setAttribute('disabled','disabled');
                    trackSelect.value = '';
                    strandSelect.value = '';
                }
            }
            gradeSelect.addEventListener('change', toggleSHSFields);
            // Initial run
            toggleSHSFields();

            // Installment Logic & Preview
            const installSelect = document.getElementById('edit_allow_installment');
            const planSelect = document.getElementById('edit_payment_plan');
            const previewContainer = document.getElementById('schedule_preview_container');
            const previewBody = document.getElementById('schedule_preview_body');
            const deadlineInput = document.getElementById('edit_fee_deadline');
            
            // Pre-loaded schedule if exists
            let currentSchedule = @json(isset($ps['items']) ? $ps['items'] : []);

            function updatePreview() {
                const isAllowed = installSelect.value == '1';
                const plan = planSelect.value;
                
                if(isAllowed && plan) {
                    previewContainer.classList.remove('hidden');
                    // Calculate mock schedule
                    // This is CLIENT-SIDE approximation for preview. Server logic is authoritative.
                    generateClientSidePreview(plan);
                } else {
                    previewContainer.classList.add('hidden');
                }
            }

            function generateClientSidePreview(plan) {
                previewBody.innerHTML = '';
                // Get total tuition
                let total = parseFloat(document.getElementById('edit_total_tuition').textContent.replace('₱','').replace(',','')) || 0;
                if(total <= 0) {
                    previewBody.innerHTML = '<tr><td colspan="3" class="px-3 py-2 text-sm text-gray-500 text-center">Add tuition components to see preview</td></tr>';
                    return;
                }

                let count = 1;
                if(plan === 'monthly') count = 9; // Aug-Apr default
                if(plan === 'quarterly') count = 4;
                if(plan === 'semester') count = 2;

                let amountPer = total / count;
                let runningTotal = 0;
                
                let startDate = new Date(deadlineInput.value || new Date().toISOString().split('T')[0]);
                
                for(let i=1; i<=count; i++) {
                    let amt = (i === count) ? (total - runningTotal) : amountPer;
                    runningTotal += amt;
                    
                    let dueDate = new Date(startDate);
                    if(i > 1) {
                        if(plan === 'monthly') dueDate.setMonth(startDate.getMonth() + (i-1));
                        if(plan === 'quarterly') dueDate.setMonth(startDate.getMonth() + ((i-1)*3));
                        if(plan === 'semester') dueDate.setMonth(startDate.getMonth() + ((i-1)*5));
                    }

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${i}${getOrdinal(i)} Installment</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">${dueDate.toLocaleDateString()}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">₱${amt.toFixed(2)}</td>
                    `;
                    previewBody.appendChild(row);
                }
            }
            
            function getOrdinal(n) {
                let s=["th","st","nd","rd"], v=n%100;
                return s[(v-20)%10]||s[v]||s[0];
            }

            installSelect.addEventListener('change', function() {
                if(this.value == '1') {
                    planSelect.parentElement.classList.remove('hidden');
                } else {
                    planSelect.parentElement.classList.add('hidden');
                    planSelect.value = '';
                }
                updatePreview();
            });
            
            planSelect.addEventListener('change', updatePreview);
            deadlineInput.addEventListener('change', updatePreview);
            
            // Components Logic
            let components = @json(is_array($tuitionFee->subject_fees) ? $tuitionFee->subject_fees : []);
            
            // Normalize components
            components = components.map(c => ({
                type: c.type || 'Base Tuition',
                label: c.label || c.subject_name || c.name || 'Tuition Fee',
                amount: parseFloat(c.amount) || 0
            }));

            const compList = document.getElementById('edit_components_list');
            const totalDisplay = document.getElementById('edit_total_tuition');
            const hiddenInput = document.getElementById('edit_subject_fees');
            const totalAmountInput = document.getElementById('edit_total_amount_input');
            const compTypeIn = document.getElementById('edit_comp_type');
            const compNameIn = document.getElementById('edit_comp_name');
            const compAmtIn = document.getElementById('edit_comp_amount');
            const addBtn = document.getElementById('edit_add_component_btn');

            function renderComponents() {
                compList.innerHTML = '';
                let total = 0;
                components.forEach((c, idx) => {
                    const amt = parseFloat(c.amount) || 0;
                    total += amt;
                    const row = document.createElement('div');
                    row.className = 'flex items-center justify-between bg-white p-3 rounded border border-gray-200 shadow-sm';
                    row.innerHTML = `
                        <div class="flex flex-col">
                            <span class="text-xs text-blue-600 font-bold uppercase tracking-wider">${c.type}</span>
                            <span class="text-sm font-medium text-gray-700">${c.label}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-gray-900">₱${amt.toFixed(2)}</span>
                            <button type="button" class="text-red-500 hover:text-red-700" onclick="removeComponent(${idx})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    `;
                    compList.appendChild(row);
                });
                totalDisplay.textContent = '₱' + total.toFixed(2);
                hiddenInput.value = JSON.stringify(components);
                if(totalAmountInput) totalAmountInput.value = total.toFixed(2);
                updatePreview();
            }

            // Global remove function
            window.removeComponent = function(index) {
                components.splice(index, 1);
                renderComponents();
            };

            if(addBtn) {
                addBtn.addEventListener('click', function() {
                    const type = compTypeIn ? compTypeIn.value : 'Base Tuition';
                    const name = compNameIn.value.trim();
                    const amt = parseFloat(compAmtIn.value);
                    
                    if(!name) {
                        alert('Please enter a description or subject name.');
                        return;
                    }
                    if(isNaN(amt) || amt <= 0) {
                        alert('Please enter a valid amount greater than 0.');
                        return;
                    }
                    
                    components.push({ type: type, label: name, amount: amt });
                    compNameIn.value = '';
                    compAmtIn.value = '';
                    compNameIn.focus();
                    renderComponents();
                });
            }

            // Initial render
            renderComponents();

            // Form Submit - Gather Checkboxes
            const form = document.getElementById('editTuitionForm');
            form.addEventListener('submit', function(e) {
                // Charges
                const chargeBoxes = document.querySelectorAll('.edit-tuition-charge-checkbox:checked');
                const chargeIds = Array.from(chargeBoxes).map(cb => cb.value);
                document.getElementById('edit_selected_charge_ids').value = JSON.stringify(chargeIds);

                // Discounts
                const discountBoxes = document.querySelectorAll('.edit-tuition-discount-checkbox:checked');
                const discountIds = Array.from(discountBoxes).map(cb => cb.value);
                document.getElementById('edit_selected_discount_ids').value = JSON.stringify(discountIds);
            });
        });
    </script>
</body>
</html>
