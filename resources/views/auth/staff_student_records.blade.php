<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Efees Staff Dashboard</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(37, 99, 235, 0.25); }
        .badge-status { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; }
        .badge-paid { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-partial { background-color: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .badge-unpaid { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .scrollbar-thin { scrollbar-width: thin; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    <style>[x-cloak]{display:none!important}</style>
    <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-user-shield text-lg"></i>
            </div>
            <span class="font-bold text-lg text-blue-900">Efees Staff</span>
        </div>
        <button @click="sidebarOpen = true" class="text-slate-600 hover:text-slate-800">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 md:hidden" x-cloak></div>
    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 z-40 w-72 bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 md:translate-x-0 overflow-y-auto shadow-2xl md:shadow-none">
        <div class="flex items-center justify-between gap-3 px-8 py-6 border-b border-gray-200 bg-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <div>
                    <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Efees Staff</h1>
                    <p class="text-xs text-slate-500 font-medium">Staff Panel</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow pb-6">
            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff_dashboard') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff_dashboard') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('staff_dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Student Records</span>
            </a>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff.payment_processing') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff.payment_processing') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-credit-card text-lg {{ request()->routeIs('staff.payment_processing') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Payment Processing</span>
            </a>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff.sms_reminders') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff.sms_reminders') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-sms text-lg {{ request()->routeIs('staff.sms_reminders') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">SMS Reminders</span>
            </a>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff.reports') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff.reports') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-chart-line text-lg {{ request()->routeIs('staff.reports') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Reports</span>
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center gap-3 bg-blue-600 text-white font-bold text-sm rounded-lg h-10 justify-center cursor-pointer transition-colors duration-300 hover:bg-blue-700" type="submit">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-8 overflow-y-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-gray-900">Student Records</h1>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-semibold text-blue-600">
                        {{ optional(Auth::user()->roleable)->full_name ?? 'Staff Member' }}
                    </p>
                    <p class="text-xs text-gray-600">{{ Auth::user()->email }}</p>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">Staff</span>
                </div>
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-lg">
                    {{ optional(Auth::user()->roleable)->initials ?? 'S' }}
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 border border-green-200 text-green-800 bg-green-50 rounded-md px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 border border-red-200 text-red-800 bg-red-50 rounded-md px-4 py-3">
                {{ session('error') }}
            </div>
        @endif

        
        <!-- Filters -->
        <section class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-8">
            <form method="GET" action="{{ route('staff_dashboard') }}" class="space-y-4 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Search Students</span>
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                            <input id="search" name="q" value="{{ $query ?? '' }}" placeholder="Name or ID" type="search"
                                   class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                        </div>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Level</span>
                        <select id="level" name="level"
                                class="mt-2 block w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All levels</option>
                            @foreach(($levels ?? []) as $lvl)
                                <option value="{{ $lvl }}" {{ ($level ?? '') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Status</span>
                        <select id="status" name="status"
                                class="mt-2 block w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All statuses</option>
                            <option value="paid" {{ ($status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partially-paid" {{ ($status ?? '') === 'partially-paid' ? 'selected' : '' }}>Partially paid</option>
                            <option value="unpaid" {{ ($status ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </label>
                    <div class="flex items-end gap-3">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center bg-blue-600 text-white font-semibold rounded-lg h-10 transition-colors duration-200 hover:bg-blue-700">
                            <i class="fas fa-filter mr-2"></i>Apply filters
                        </button>
                        <a href="{{ route('staff_dashboard') }}" class="inline-flex items-center justify-center bg-gray-200 text-gray-700 font-semibold rounded-lg h-10 px-4 transition-colors duration-200 hover:bg-gray-300">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </section>

        <!-- Student Table -->
        <section class="bg-white rounded-2xl shadow-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Student Fee Overview</h2>
                    <p class="text-sm text-gray-500">Detailed balances and payment status per student</p>
                </div>
                <span class="text-sm text-gray-500">Last updated {{ now()->format('M d, Y') }}</span>
            </div>

            <div class="overflow-x-auto scrollbar-thin">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-gray-600 uppercase text-xs tracking-wide">
                            <th class="px-6 py-3 font-semibold">
                                <a href="{{ route('staff_dashboard', array_merge(request()->query(), ['sort' => 'name', 'direction' => (request('sort') === 'name' && request('direction') !== 'desc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600">
                                    Student
                                    @if(($sort ?? '') === 'name')
                                        <i class="fas fa-sort-{{ ($direction ?? 'asc') === 'desc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold">Level</th>
                            <th class="px-6 py-3 font-semibold text-right">Total Fee</th>
                            <th class="px-6 py-3 font-semibold text-right">
                                <a href="{{ route('staff_dashboard', array_merge(request()->query(), ['sort' => 'paid', 'direction' => (request('sort') === 'paid' && request('direction') !== 'desc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600">
                                    Paid
                                    @if(($sort ?? '') === 'paid')
                                        <i class="fas fa-sort-{{ ($direction ?? 'asc') === 'desc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold text-right">
                                <a href="{{ route('staff_dashboard', array_merge(request()->query(), ['sort' => 'due', 'direction' => (request('sort') === 'due' && request('direction') !== 'desc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600">
                                    Outstanding
                                    @if(($sort ?? '') === 'due')
                                        <i class="fas fa-sort-{{ ($direction ?? 'asc') === 'desc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold text-center">
                                <a href="{{ route('staff_dashboard', array_merge(request()->query(), ['sort' => 'status', 'direction' => (request('sort') === 'status' && request('direction') !== 'desc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600">
                                    Status
                                    @if(($sort ?? '') === 'status')
                                        <i class="fas fa-sort-{{ ($direction ?? 'asc') === 'desc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold">
                                <a href="{{ route('staff_dashboard', array_merge(request()->query(), ['sort' => 'latest_payment', 'direction' => (request('sort') === 'latest_payment' && request('direction') !== 'desc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600">
                                    Last Payment
                                    @if(($sort ?? '') === 'latest_payment')
                                        <i class="fas fa-sort-{{ ($direction ?? 'asc') === 'desc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" id="students-table-body">
                        @forelse ($studentRecords as $record)
                            <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $record->student->full_name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $record->student->student_id }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $record->student->level }}</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-900">₱{{ number_format($record->totalFee, 2) }}</td>
                                <td class="px-6 py-4 text-right text-green-600 font-semibold">₱{{ number_format($record->paidAmount, 2) }}</td>
                                <td class="px-6 py-4 text-right {{ $record->dueAmount > 0 ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold' }}">
                                    ₱{{ number_format($record->dueAmount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $badgeClass = [
                                            'paid' => 'badge-status badge-paid',
                                            'partially-paid' => 'badge-status badge-partial',
                                            'unpaid' => 'badge-status badge-unpaid'
                                        ][$record->status] ?? 'badge-status badge-unpaid';
                                    @endphp
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="{{ $badgeClass }}">
                                            <span class="w-2 h-2 rounded-full bg-current"></span>
                                            {{ $record->statusText }}
                                        </span>

                                        @if(isset($record->latestRejectedPayment) && $record->latestRejectedPayment)
                                            <div class="group relative mt-1">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200 cursor-help">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                    Rejected
                                                </span>
                                                <!-- Tooltip -->
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10 text-center">
                                                    {{ $record->latestRejectedPayment->remarks ?? 'No reason provided' }}
                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ optional($record->latestPaymentAt)?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('staff.student_details', $record->student) }}" class="inline-flex items-center gap-1 text-blue-600 font-semibold hover:text-blue-700">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    @if($query || $status)
                                        No student records match your filter.
                                    @else
                                        No student records found. Please add students in the admin panel.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($studentRecords->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $studentRecords->appends(request()->query())->links() }}
                </div>
            @endif
        </section>
    </main>

    <!-- Toast container -->
    <div id="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem;"></div>

    <!-- Supabase Realtime Notifications -->
    <script>
        window.SUPABASE_URL = "{{ env('SUPABASE_URL', '') }}";
        window.SUPABASE_ANON_KEY = "{{ env('SUPABASE_ANON_KEY', '') }}";
        window.AUTH_USER_ID = {{ Auth::user()->user_id ?? 'null' }};
    </script>
    <script type="module">
        import { createClient } from 'https://esm.sh/@supabase/supabase-js@2';

        const url = window.SUPABASE_URL;
        const anon = window.SUPABASE_ANON_KEY;
        const authUserId = window.AUTH_USER_ID;

        const supabase = (url && anon) ? createClient(url, anon) : null;

        const toastContainer = document.getElementById('toast-container');

        function showToast(title, body, ts) {
            if (!toastContainer) return;
            const wrap = document.createElement('div');
            wrap.style.background = '#ffffff';
            wrap.style.border = '1px solid #e2e8f0';
            wrap.style.color = '#334155';
            wrap.style.padding = '0.75rem 1rem';
            wrap.style.borderRadius = '0.5rem';
            wrap.style.boxShadow = '0 6px 16px rgba(0,0,0,0.08)';
            wrap.style.minWidth = '260px';
            const when = ts ? new Date(ts).toLocaleString() : new Date().toLocaleString();
            wrap.innerHTML = `<div style="font-weight:700;color:#2563eb;margin-bottom:4px;">${title}</div><div>${body}</div><div style="margin-top:6px;font-size:12px;color:#64748b;">Updated at ${when}</div>`;
            toastContainer.appendChild(wrap);
            setTimeout(() => { wrap.remove(); }, 6000);
        }

        async function initRealtime() {
            if (!supabase || !authUserId) {
                console.warn('Realtime not initialized: missing client or user id');
                return;
            }
            const channel = supabase.channel(`notifications-${authUserId}`);
            channel.on(
                'postgres_changes',
                { event: 'INSERT', schema: 'public', table: 'notifications', filter: `user_id=eq.${authUserId}` },
                (payload) => {
                    const n = payload.new || {};
                    showToast(n.title || 'Notification', n.body || '', n.created_at);
                }
            );
            channel.subscribe((status) => {
                if (status === 'SUBSCRIBED') {
                    console.log('Realtime: subscribed to notifications for', authUserId);
                } else {
                    showToast('Connection Issue', 'Realtime subscription not active.', null);
                }
            });
        }

        initRealtime();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search');
            const statusSelect = document.getElementById('status');
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenuDropdown = document.getElementById('user-menu-dropdown');

            if (userMenuButton && userMenuDropdown) {
                userMenuButton.addEventListener('click', function() {
                    userMenuDropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function(event) {
                    if (!userMenuButton.contains(event.target) && !userMenuDropdown.contains(event.target)) {
                        userMenuDropdown.classList.add('hidden');
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('keyup', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        event.target.form.submit();
                    }
                });
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', () => statusSelect.form.submit());
            }

            // Real-time updates for student list
            function fetchStudentList() {
                const urlParams = new URLSearchParams(window.location.search);
                fetch('{{ route("staff_dashboard.list") }}?' + urlParams.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 419) {
                            window.location.reload();
                            return;
                        }
                        if (!response.ok) throw new Error('Network response was not ok');
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            // Likely redirected to login page
                            window.location.reload();
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data) return; // Handle void return from reload
                        updateStudentTable(data.studentRecords.data);
                        const timestampEl = document.getElementById('last-updated-timestamp');
                        if (timestampEl) {
                            timestampEl.textContent = 'Last updated ' + new Date().toLocaleString();
                        }
                    })
                    .catch(error => console.error('Error fetching student list:', error));
            }

            function updateStudentTable(records) {
                const tbody = document.getElementById('students-table-body');
                if (!tbody) return;

                if (records.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No student records match your filter.
                            </td>
                        </tr>`;
                    return;
                }

                tbody.innerHTML = records.map(record => {
                    const badgeClass = {
                        'paid': 'badge-status badge-paid',
                        'partially-paid': 'badge-status badge-partial',
                        'unpaid': 'badge-status badge-unpaid'
                    }[record.status] || 'badge-status badge-unpaid';

                    const latestPayment = record.latestPaymentAt 
                        ? new Date(record.latestPaymentAt).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
                        : '—';

                    const dueClass = record.dueAmount > 0 ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold';

                    return `
                        <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">${record.student.first_name} ${record.student.last_name}</p>
                                <p class="text-xs text-gray-500">ID: ${record.student.student_id}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600">${record.student.level}</td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">₱${Number(record.totalFee).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            <td class="px-6 py-4 text-right text-green-600 font-semibold">₱${Number(record.paidAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            <td class="px-6 py-4 text-right ${dueClass}">
                                ₱${Number(record.dueAmount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="${badgeClass}">
                                    <span class="w-2 h-2 rounded-full bg-current"></span>
                                    ${record.statusText}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                ${latestPayment}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="/staff/student-details/${record.student.student_id}" class="inline-flex items-center gap-1 text-blue-600 font-semibold hover:text-blue-700">
                                    <i class="fas fa-eye"></i>
                                    View
                                </a>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            // Poll every 10 seconds
            setInterval(fetchStudentList, 10000);
        });
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Supabase Realtime -->
    @include('partials.supabase_realtime')
</body>
</html>
