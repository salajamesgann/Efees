<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fee Management - E-Fees Portal</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    @include('layouts.admin_sidebar')

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Mobile Header -->
        <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-blue-900">Efees</span>
            </div>
            <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-700">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-6 lg:p-8 custom-scrollbar">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Fee Management</h1>
            </header>

            @php
                $tuitionCount = isset($tuitionFees) ? (($tuitionFees instanceof \Illuminate\Support\Collection) ? $tuitionFees->count() : (is_array($tuitionFees) ? count($tuitionFees) : 0)) : 0;
                $chargesCount = isset($additionalCharges) ? (($additionalCharges instanceof \Illuminate\Support\Collection) ? $additionalCharges->count() : (is_array($additionalCharges) ? count($additionalCharges) : 0)) : 0;
                $discountsCount = isset($discounts) ? (($discounts instanceof \Illuminate\Support\Collection) ? $discounts->count() : (is_array($discounts) ? count($discounts) : 0)) : 0;
            @endphp
            

            <!-- Tab Navigation -->
            <div class="mb-6">
                @php
                    $isTuition = ($currentTab ?? 'tuition') === 'tuition';
                    $isCharges = ($currentTab ?? 'tuition') === 'charges';
                    $isDiscounts = ($currentTab ?? 'tuition') === 'discounts';
                @endphp
                <div class="flex items-center justify-between mb-3">
                    <nav class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-medium transition-colors duration-200 {{ $isTuition ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Tuition Fees</span>
                        </a>
                        <a href="{{ route('admin.fees.index', ['tab' => 'charges']) }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-medium transition-colors duration-200 {{ $isCharges ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fas fa-list"></i>
                            <span>Additional Charges</span>
                        </a>
                        <a href="{{ route('admin.fees.index', ['tab' => 'discounts']) }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-medium transition-colors duration-200 {{ $isDiscounts ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fas fa-percent"></i>
                            <span>Discounts</span>
                        </a>
                    </nav>
                </div>
                <div class="border-b border-gray-200"></div>
            </div>

            @if(($currentTab ?? 'tuition') === 'tuition')
                @include('admin.fees.tuition.index')
            @elseif(($currentTab ?? 'tuition') === 'charges')
                @include('admin.fees.charges.index')
            @elseif(($currentTab ?? 'tuition') === 'discounts')
                @include('admin.fees.discounts.index')
            @endif
        </main>
    </div>
</body>
</html>
