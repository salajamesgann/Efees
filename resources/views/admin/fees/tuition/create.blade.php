<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Tuition Fee - E-Fees Portal</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        [x-cloak] { display: none !important; }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
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

            <!-- Student Enrollment -->

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
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fees.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.fees.index') }}">
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
                <h1 class="text-3xl font-bold text-gray-900">Create Tuition Fee</h1>
                <p class="text-gray-500 mt-1">Configure new tuition fee structure, components, and attached charges.</p>
            </div>
            <a href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </header>

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

            <form method="POST" action="{{ route('admin.fees.store-tuition') }}" id="createTuitionForm">
                @csrf
                <input type="hidden" name="subject_fees" id="create_subject_fees" value="">
                <input type="hidden" name="selected_charge_ids" id="create_selected_charge_ids" value="">
                <input type="hidden" name="selected_discount_ids" id="create_selected_discount_ids" value="">

                <!-- Section 1: Basic Information -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <label for="create_fee_name" class="block text-sm font-medium text-gray-700">Fee Name <span class="text-red-500">*</span></label>
                            <input type="text" name="fee_name" id="create_fee_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('fee_name') }}" required placeholder="e.g. Grade 10 Regular Tuition">
                        </div>
                        <div>
                            <label for="create_is_active" class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-2 flex items-center">
                                <input type="checkbox" name="is_active" id="create_is_active" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                                <label for="create_is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                            </div>
                        </div>
                        <div>
                            <label for="create_grade_level" class="block text-sm font-medium text-gray-700">Grade Level</label>
                            <select name="grade_level" id="create_grade_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                @foreach($gradeLevels as $gl)
                                    <option value="{{ $gl }}" {{ old('grade_level') === $gl ? 'selected' : '' }}>{{ $gl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="create_school_year" class="block text-sm font-medium text-gray-700">Academic Year</label>
            <input type="text" name="school_year" id="create_school_year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('school_year', $activeSchoolYear ?? date('Y').'-'.(date('Y')+1)) }}" placeholder="e.g. 2024-2025">
                        </div>
                        <div>
                            <label for="create_fee_deadline" class="block text-sm font-medium text-gray-700">Fee Deadline</label>
                            <input type="date" name="fee_deadline" id="create_fee_deadline" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('fee_deadline') }}">
                        </div>
                        
                        <!-- SHS Fields -->
                        <div>
                            <label for="create_track" class="block text-sm font-medium text-gray-700">Senior High Track</label>
                            <select name="track" id="create_track" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" disabled>
                                <option value="" selected>None</option>
                                <option value="Academic">Academic</option>
                                <option value="TVL">TVL</option>
                            </select>
                        </div>
                        <div>
                            <label for="create_strand" class="block text-sm font-medium text-gray-700">Senior High Strand</label>
                            <select name="strand" id="create_strand" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" disabled>
                                <option value="" selected>None</option>
                                <option value="STEM">STEM</option>
                                <option value="ABM">ABM</option>
                                <option value="HUMSS">HUMSS</option>
                                <option value="ICT">ICT</option>
                            </select>
                        </div>
                        <div class="col-span-1 md:col-span-2 lg:col-span-3">
                            <label for="create_notes" class="block text-sm font-medium text-gray-700">Notes / Description</label>
                            <textarea name="notes" id="create_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Additional details about this tuition fee...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Installment Settings -->
                <div class="p-6 border-b border-gray-200 bg-gray-50/50">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Payment Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="create_allow_installment" class="block text-sm font-medium text-gray-700">Allow Installment?</label>
                            <select name="allow_installment" id="create_allow_installment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="0" selected>No, Full Payment Only</option>
                                <option value="1">Yes, Allow Installments</option>
                            </select>
                        </div>
                        <div class="hidden" id="payment_plan_container">
                            <label for="create_payment_plan" class="block text-sm font-medium text-gray-700">Payment Plan</label>
                            <select name="payment_plan" id="create_payment_plan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select Plan</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="semester">Semester</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Tuition Components -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Tuition Components</h3>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                                <select id="create_comp_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="Base Tuition">Base Tuition</option>
                                    <option value="Miscellaneous">Miscellaneous</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Description / Subject</label>
                                <input type="text" id="create_comp_name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="e.g. Science Lab Fee">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Amount</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" step="0.01" id="create_comp_amount" class="block w-full rounded-md border-gray-300 pl-7 focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="0.00">
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <button type="button" id="create_btn_add_comp" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-plus mr-1"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="create_comp_table_body">
                                <!-- JS will populate this -->
                                <tr id="create_no_comps_row">
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No components added yet.</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="2" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total Tuition:</td>
                                    <td class="px-6 py-3 text-right text-sm font-bold text-blue-600" id="create_comp_total">₱0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Section 4: Attach Charges & Discounts -->
                <div class="p-6 border-b border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Additional Charges -->
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Attach Additional Charges</h3>
                        <p class="text-sm text-gray-500 mb-4">Select charges that will be automatically added to this tuition fee.</p>
                        
                        <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-md p-2 space-y-2 custom-scrollbar">
                            @forelse($availableCharges as $charge)
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input id="create_charge_{{ $charge->id }}" name="charges[]" value="{{ $charge->id }}" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="create_charge_{{ $charge->id }}" class="font-medium text-gray-700">{{ $charge->charge_name }}</label>
                                        <p class="text-gray-500">₱{{ number_format((float)$charge->amount, 2) }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 italic">No additional charges available.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Discounts -->
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Applicable Discounts</h3>
                        <p class="text-sm text-gray-500 mb-4">Select discounts that can be applied to this fee.</p>
                        
                        <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-md p-2 space-y-2 custom-scrollbar">
                            @forelse($availableDiscounts as $discount)
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input id="create_discount_{{ $discount->id }}" name="discounts[]" value="{{ $discount->id }}" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="create_discount_{{ $discount->id }}" class="font-medium text-gray-700">{{ $discount->discount_name }}</label>
                                        <p class="text-gray-500">
                                            {{ $discount->type === 'percentage' ? $discount->value.'%' : '₱'.number_format((float)$discount->value, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 italic">No discounts available.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.fees.index') }}" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Tuition Fee
                    </button>
                </div>
            </form>
        </div>
    </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SHS Track/Strand Logic
            const gradeSelect = document.getElementById('create_grade_level');
            const trackSelect = document.getElementById('create_track');
            const strandSelect = document.getElementById('create_strand');

            function toggleSHSFields() {
                const grade = gradeSelect.value;
                const isSHS = grade === 'Grade 11' || grade === 'Grade 12';
                
                trackSelect.disabled = !isSHS;
                strandSelect.disabled = !isSHS;
                
                if (!isSHS) {
                    trackSelect.value = "";
                    strandSelect.value = "";
                }
            }

            if(gradeSelect) {
                gradeSelect.addEventListener('change', toggleSHSFields);
                toggleSHSFields(); // Init
            }

            // Installment Logic
            const allowInstallment = document.getElementById('create_allow_installment');
            const paymentPlanContainer = document.getElementById('payment_plan_container');
            const paymentPlan = document.getElementById('create_payment_plan');

            if(allowInstallment) {
                allowInstallment.addEventListener('change', function() {
                    if (this.value == "1") {
                        paymentPlanContainer.classList.remove('hidden');
                        paymentPlan.required = true;
                    } else {
                        paymentPlanContainer.classList.add('hidden');
                        paymentPlan.required = false;
                        paymentPlan.value = "";
                    }
                });
            }

            // Tuition Components Logic
            let components = [];
            const btnAddComp = document.getElementById('create_btn_add_comp');
            const compType = document.getElementById('create_comp_type');
            const compName = document.getElementById('create_comp_name');
            const compAmount = document.getElementById('create_comp_amount');
            const tbody = document.getElementById('create_comp_table_body');
            const totalEl = document.getElementById('create_comp_total');
            const hiddenInput = document.getElementById('create_subject_fees');
            const noCompsRow = document.getElementById('create_no_comps_row');
            const form = document.getElementById('createTuitionForm');
            const hiddenCharges = document.getElementById('create_selected_charge_ids');
            const hiddenDiscounts = document.getElementById('create_selected_discount_ids');

            function renderTable() {
                while(tbody.firstChild) {
                    tbody.removeChild(tbody.firstChild);
                }

                if (components.length === 0) {
                    tbody.appendChild(noCompsRow);
                    noCompsRow.style.display = 'table-row';
                    totalEl.innerText = '₱0.00';
                    hiddenInput.value = JSON.stringify([]);
                    return;
                }
                
                noCompsRow.style.display = 'none'; // Keep it in DOM but hidden
                tbody.appendChild(noCompsRow);

                let total = 0;
                components.forEach((c, index) => {
                    total += parseFloat(c.amount);
                    
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${c.type}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${c.label || c.name || c.subject_name || ''}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₱${parseFloat(c.amount).toFixed(2)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" class="text-red-600 hover:text-red-900" onclick="removeComponent(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                totalEl.innerText = '₱' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                hiddenInput.value = JSON.stringify(components);
            }

            // Expose remove function globally
            window.removeComponent = function(index) {
                components.splice(index, 1);
                renderTable();
            };

            if(btnAddComp) {
                btnAddComp.addEventListener('click', function() {
                    const type = compType.value;
                    const name = compName.value.trim();
                    const amount = parseFloat(compAmount.value);

                    if (!name) {
                        alert('Please enter a description or subject name.');
                        return;
                    }
                    if (isNaN(amount) || amount <= 0) {
                        alert('Please enter a valid amount.');
                        return;
                    }

                    components.push({ type, label: name, name, amount });
                    renderTable();
                    
                    // Reset inputs
                    compName.value = '';
                    compAmount.value = '';
                    compName.focus();
                });
            }
            
            if(form) {
                form.addEventListener('submit', function() {
                    if (components.length === 0) {
                        const pendingName = compName.value.trim();
                        const pendingAmount = parseFloat(compAmount.value);
                        if (pendingName && !isNaN(pendingAmount) && pendingAmount > 0) {
                            const pendingType = compType.value;
                            components.push({ type: pendingType, label: pendingName, name: pendingName, amount: pendingAmount });
                            renderTable();
                        }
                    }
                    const chargeIds = Array.from(document.querySelectorAll('input[name="charges[]"]:checked')).map(el => el.value);
                    const discountIds = Array.from(document.querySelectorAll('input[name="discounts[]"]:checked')).map(el => el.value);
                    hiddenCharges.value = chargeIds.join(',');
                    hiddenDiscounts.value = discountIds.join(',');
                });
            }
        });
    </script>
</body>
</html>
