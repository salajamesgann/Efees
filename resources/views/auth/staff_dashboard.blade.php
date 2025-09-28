<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Efees</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background-color: #121212; color: #e5e5e5; }
        .gradient-bg { background: linear-gradient(135deg, #ff7a18 0%, #af002d 100%); }
        .gradient-text { background: linear-gradient(135deg, #ff7a18 0%, #ff3c00 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .card-hover { transition: all 0.3s ease; background-color: #1e1e1e; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 15px 30px -5px rgba(255, 122, 24, 0.4); }
        .btn-primary { background: linear-gradient(135deg, #ff7a18 0%, #ff3c00 100%); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255, 122, 24, 0.5); }
        footer { background-color: #1a1a1a; }
    </style>
</head>
<body class="bg-neutral-950 text-neutral-200">
    <!-- Navigation -->
    <nav class="bg-gray-900 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-graduation-cap text-3xl gradient-text mr-3"></i>
                        <span class="text-xl font-bold text-white">E-Fees Portal</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:block text-right">
                        <p class="text-sm font-medium text-white">{{ Auth::user()->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-[#f97316] to-[#fb923c] flex items-center justify-center text-white font-bold text-sm shadow-lg">
                            {{ Auth::user()->initials }}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-primary text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-[#1a1a1a] shadow-xl border-r border-gray-700">
            <!-- Logo Section -->
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-[#f97316]">Efees Staff</h1>
                <p class="text-sm text-gray-400 mt-1">Staff Portal</p>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="mt-6">
                <ul class="space-y-1">
                    <li>
                        <a href="#" class="flex items-center gap-3 px-6 py-3 text-[#f97316] bg-[#f97316]/10 border-r-3 border-[#f97316] font-medium">
                            <i class="fas fa-chart-line w-5"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-6 py-3 text-gray-400 hover:text-[#f97316] hover:bg-[#f97316]/5 transition-all duration-200">
                            <i class="fas fa-users w-5"></i>
                            Students
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-6 py-3 text-gray-400 hover:text-[#f97316] hover:bg-[#f97316]/5 transition-all duration-200">
                            <i class="fas fa-receipt w-5"></i>
                            Payments
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-6 py-3 text-gray-400 hover:text-[#f97316] hover:bg-[#f97316]/5 transition-all duration-200">
                            <i class="fas fa-file-alt w-5"></i>
                            Reports
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-6 py-3 text-gray-400 hover:text-[#f97316] hover:bg-[#f97316]/5 transition-all duration-200">
                            <i class="fas fa-calendar w-5"></i>
                            Schedule
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-6 py-3 text-gray-400 hover:text-[#f97316] hover:bg-[#f97316]/5 transition-all duration-200">
                            <i class="fas fa-cog w-5"></i>
                            Settings
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Gradient Header -->
            <section class="gradient-bg shadow-sm border-b border-gray-700 px-6 py-8">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-white">Staff Dashboard</h1>
                    <p class="text-orange-100 hidden md:block">Manage students, approvals, and reminders</p>
                </div>
            </section>

            <!-- Dashboard Content -->
            <main class="flex-1 overflow-y-auto bg-gray-900 p-6">
                @if (session('success'))
                    <div class="mb-4 px-4 py-3 rounded bg-green-500/10 text-green-300 border border-green-500/30">{{ session('success') }}</div>
                @endif
                @if (session('info'))
                    <div class="mb-4 px-4 py-3 rounded bg-blue-500/10 text-blue-300 border border-blue-500/30">{{ session('info') }}</div>
                @endif

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="rounded-2xl shadow-lg border border-neutral-800 p-6 bg-neutral-900">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-neutral-400">Paid Students</p>
                                <p class="text-2xl font-bold text-green-400">{{ $paidCount }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-400/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-400 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl shadow-lg border border-neutral-800 p-6 bg-neutral-900">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-neutral-400">Unpaid Students</p>
                                <p class="text-2xl font-bold text-orange-400">{{ $unpaidCount }}</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-500/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-circle text-orange-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paid vs Unpaid Chart -->
                <section class="rounded-2xl shadow-lg border border-neutral-800 p-6 mb-6 bg-neutral-900">
                    <h2 class="text-base md:text-lg font-semibold mb-2 flex items-center gap-2 text-orange-400">
                        <i class="fas fa-chart-pie"></i>
                        Paid vs Unpaid
                    </h2>
                    <div class="mt-2 max-h-64 overflow-y-auto pr-2">
                        <canvas id="paidChart" class="w-full" style="min-height:240px;"></canvas>
                    </div>
                </section>

                <!-- Students Table with Search -->
                <section class="rounded-2xl shadow-lg border border-neutral-800 p-6 bg-neutral-900">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base md:text-lg font-semibold text-neutral-200">Students</h2>
                        <form method="GET" action="{{ route('staff_dashboard') }}" class="flex gap-2">
                            <input type="text" name="q" value="{{ $query }}" placeholder="Search by ID, name, or level" class="h-10 px-3 bg-neutral-900 border border-neutral-700 rounded text-sm text-neutral-100 placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500" />
                            <button class="px-4 h-10 bg-orange-500 hover:bg-orange-600 text-white text-sm rounded-lg font-semibold shadow-lg">Search</button>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-neutral-800 text-neutral-300">
                                    <th class="text-left py-3">Student ID</th>
                                    <th class="text-left py-3">Name</th>
                                    <th class="text-left py-3">Level</th>
                                    <th class="text-left py-3">Total Balance</th>
                                    <th class="text-left py-3">Status</th>
                                    <th class="text-left py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-800">
                                @forelse ($students as $s)
                                    @php
                                        $totalBalance = (float) $s->feeRecords->sum('balance');
                                        $isPaid = $s->feeRecords->count() > 0 && $totalBalance <= 0;
                                    @endphp
                                    <tr>
                                        <td class="py-3 text-neutral-200">{{ $s->student_id }}</td>
                                        <td class="py-3 text-neutral-200">{{ $s->full_name }}</td>
                                        <td class="py-3 text-neutral-300">{{ $s->level }}</td>
                                        <td class="py-3 text-neutral-300">₱ {{ number_format($totalBalance, 2) }}</td>
                                        <td class="py-3">
                                            @if ($isPaid)
                                                <span class="px-2 py-1 text-xs font-medium bg-green-600/20 text-green-300 rounded-full border border-green-600/40">Paid</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium bg-orange-500/15 text-orange-400 rounded-full border border-orange-500/40">Unpaid</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <div class="flex gap-2">
                                                <form method="POST" action="{{ route('staff.remind', $s) }}">
                                                    @csrf
                                                    <button type="submit" class="px-3 h-9 bg-neutral-900 text-orange-400 rounded-lg hover:bg-neutral-800 border border-neutral-700">Remind</button>
                                                </form>
                                                <form method="POST" action="{{ route('staff.approve', $s) }}" onsubmit="return confirm('Approve payments for {{ $s->full_name }}?');">
                                                    @csrf
                                                    <button type="submit" class="px-3 h-9 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold shadow disabled:opacity-50" {{ $isPaid ? 'disabled' : '' }}>Approve</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-neutral-400">No students found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-neutral-300">
                        {{ $students->links() }}
                    </div>
                </section>
            </main>
            <script>
                (function() {
                    const el = document.getElementById('paidChart');
                    if (!el || !window.Chart) return;
                    const paid = {{ (int) ($paidCount ?? 0) }};
                    const unpaid = {{ (int) ($unpaidCount ?? 0) }};
                    new Chart(el, {
                        type: 'doughnut',
                        data: {
                            labels: ['Paid', 'Unpaid'],
                            datasets: [{
                                data: [paid, unpaid],
                                backgroundColor: ['#16a34a', '#f97316'],
                                borderColor: ['#16a34a', '#f97316'],
                                borderWidth: 1,
                                hoverOffset: 6
                            }]
                        },
                        options: {
                            plugins: {
                                legend: { position: 'bottom', labels: { color: '#d1d5db' } }
                            }
                        }
                    });
                })();
            </script>
        </div>
    </div>

</body>
</html>
