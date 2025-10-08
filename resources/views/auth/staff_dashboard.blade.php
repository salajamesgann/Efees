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
        .gradient-text { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .gradient-bg {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.2);
        }
        .chart-container {
            position: relative;
            height: 240px;
        }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-slate-900 text-slate-100">
    <!-- Sidebar -->
    <aside class="flex flex-col bg-slate-800 text-slate-300 w-full md:w-72 min-h-screen border-r border-slate-700 overflow-y-auto" id="sidebar" style="scrollbar-width: thin; scrollbar-color: #8b5cf6 transparent;">
        <div class="flex items-center gap-3 px-8 py-6 border-b border-slate-700">
            <div class="w-8 h-8 flex-shrink-0 text-indigo-500">
                <svg class="w-full h-full" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor"></path>
                </svg>
            </div>
            <h1 class="text-indigo-400 font-extrabold text-xl tracking-tight select-none" style="letter-spacing: -0.015em;">
                Efees Staff
            </h1>
        </div>
        <nav class="flex flex-col mt-6 px-4 space-y-1 flex-grow">
            <a aria-current="page" class="flex items-center gap-3 px-4 py-3 rounded-lg text-indigo-400 bg-slate-700 border-r-4 border-indigo-500 font-semibold transition-colors duration-200" href="#">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span class="text-sm font-semibold">
                    Dashboard
                </span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
                <i class="fas fa-users w-5"></i>
                <span class="text-sm font-semibold">
                    Students
                </span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
                <i class="fas fa-receipt w-5"></i>
                <span class="text-sm font-semibold">
                    Payments
                </span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
                <i class="fas fa-file-alt w-5"></i>
                <span class="text-sm font-semibold">
                    Reports
                </span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
                <i class="fas fa-calendar w-5"></i>
                <span class="text-sm font-semibold">
                    Schedule
                </span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
                <i class="fas fa-cog w-5"></i>
                <span class="text-sm font-semibold">
                    Settings
                </span>
            </a>
        </nav>
        <div class="px-4 py-4 border-t border-slate-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center gap-3 bg-indigo-500 text-white font-bold text-sm rounded-lg h-10 justify-center cursor-pointer select-none transition-colors duration-300 hover:bg-indigo-600" type="submit" aria-label="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-8 overflow-y-auto bg-slate-900">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-slate-100">
                Staff Dashboard
            </h1>
            <!-- Staff Profile Circle -->
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-semibold text-indigo-400">{{ Auth::user()->full_name }}</p>
                    <p class="text-xs text-slate-400">{{ Auth::user()->email }}</p>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-900/50 text-indigo-300 border border-indigo-600">Staff</span>
                </div>
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-bold text-lg">
                    {{ Auth::user()->initials }}
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 border border-green-600 text-green-300 bg-green-900/20 rounded-md px-4 py-3">
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mb-6 border border-blue-600 text-blue-300 bg-blue-900/20 rounded-md px-4 py-3">
                {{ session('info') }}
            </div>
        @endif

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Paid Students -->
            <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
                <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-green-400">
                    <i class="fas fa-check-circle text-green-500"></i>
                    Paid Students
                </h2>
                <p class="text-4xl font-extrabold mb-2 text-green-400 select-text">
                    {{ $paidCount ?? 0 }}
                </p>
                <p class="text-slate-400 select-text">
                    Fully paid
                </p>
            </section>

            <!-- Unpaid Students -->
            <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
                <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-orange-400">
                    <i class="fas fa-exclamation-triangle text-orange-500"></i>
                    Unpaid Students
                </h2>
                <p class="text-4xl font-extrabold mb-2 text-orange-400 select-text">
                    {{ $unpaidCount ?? 0 }}
                </p>
                <p class="text-slate-400 select-text">
                    Outstanding payments
                </p>
            </section>

            <!-- Pending Reminders -->
            <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
                <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-indigo-400">
                    <i class="fas fa-bell text-indigo-500"></i>
                    Pending Reminders
                </h2>
                <p class="text-4xl font-extrabold mb-2 text-indigo-400 select-text">
                    {{ $pendingReminders ?? 0 }}
                </p>
                <p class="text-slate-400 select-text">
                    Need follow-up
                </p>
            </section>

            <!-- Total Balance -->
            <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
                <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-purple-400">
                    <i class="fas fa-dollar-sign text-purple-500"></i>
                    Total Outstanding
                </h2>
                <p class="text-4xl font-extrabold mb-2 text-purple-400 select-text">
                    ₱{{ number_format($totalOutstanding ?? 0, 2) }}
                </p>
                <p class="text-slate-400 select-text">
                    Outstanding amount
                </p>
            </section>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Paid vs Unpaid Chart -->
            <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover">
                <h2 class="text-xl font-semibold mb-6 flex items-center gap-2 select-none text-slate-100">
                    <i class="fas fa-chart-pie text-indigo-500"></i>
                    Payment Status Overview
                </h2>
                <div class="chart-container">
                    <canvas id="paidChart"></canvas>
                </div>
            </section>

            <!-- Recent Activity -->
            <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6">
                <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-green-400">
                    <i class="fas fa-history text-green-500"></i>
                    Recent Activity
                </h2>
                <div class="overflow-x-auto scrollbar-thin max-h-64">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-700 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-slate-300" scope="col">Student</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-300" scope="col">Action</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-300" scope="col">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-300">John Doe</td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-300">Payment Approved</td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-400">2 hours ago</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-300">Jane Smith</td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-300">Reminder Sent</td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-400">4 hours ago</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-300">Mike Johnson</td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-300">Payment Approved</td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-400">1 day ago</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Students Table with Search -->
        <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base md:text-lg font-semibold text-slate-100">Students</h2>
                <form method="GET" action="{{ route('staff_dashboard') }}" class="flex gap-2">
                    <input type="text" name="q" value="{{ $query }}" placeholder="Search by ID, name, or level" class="h-10 px-3 bg-slate-700 border border-slate-600 rounded text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                    <button class="px-4 h-10 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg font-semibold shadow-lg">Search</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-700">
                        <tr class="border-b border-slate-600 text-slate-300">
                            <th class="text-left py-3 px-4 font-semibold">Student ID</th>
                            <th class="text-left py-3 px-4 font-semibold">Name</th>
                            <th class="text-left py-3 px-4 font-semibold">Level</th>
                            <th class="text-left py-3 px-4 font-semibold">Total Balance</th>
                            <th class="text-left py-3 px-4 font-semibold">Status</th>
                            <th class="text-left py-3 px-4 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @forelse ($students as $s)
                            @php
                                $totalBalance = (float) $s->feeRecords->sum('balance');
                                $isPaid = $s->feeRecords->count() > 0 && $totalBalance <= 0;
                            @endphp
                            <tr class="hover:bg-slate-700 transition-colors duration-200">
                                <td class="py-3 px-4 text-slate-300">{{ $s->student_id }}</td>
                                <td class="py-3 px-4 text-slate-300">{{ $s->full_name }}</td>
                                <td class="py-3 px-4 text-slate-400">{{ $s->level }}</td>
                                <td class="py-3 px-4 text-slate-400">₱ {{ number_format($totalBalance, 2) }}</td>
                                <td class="py-3 px-4">
                                    @if ($isPaid)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-900/50 text-green-400 border border-green-600">Paid</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-900/50 text-orange-400 border border-orange-600">Unpaid</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('staff.remind', $s) }}">
                                            @csrf
                                            <button type="submit" class="px-3 h-9 bg-slate-700 text-indigo-400 rounded-lg hover:bg-slate-600 border border-indigo-600 transition-colors duration-200">Remind</button>
                                        </form>
                                        <form method="POST" action="{{ route('staff.approve', $s) }}" onsubmit="return confirm('Approve payments for {{ $s->full_name }}?');">
                                            @csrf
                                            <button type="submit" class="px-3 h-9 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold shadow disabled:opacity-50 transition-colors duration-200" {{ $isPaid ? 'disabled' : '' }}>Approve</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-slate-400">
                {{ $students->links() }}
            </div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Paid vs Unpaid Chart
                const paidChartEl = document.getElementById('paidChart');
                if (paidChartEl) {
                    const paid = {{ (int) ($paidCount ?? 0) }};
                    const unpaid = {{ (int) ($unpaidCount ?? 0) }};

                    new Chart(paidChartEl, {
                        type: 'doughnut',
                        data: {
                            labels: ['Paid', 'Unpaid'],
                            datasets: [{
                                data: [paid, unpaid],
                                backgroundColor: [
                                    '#10b981',
                                    '#f97316'
                                ],
                                borderWidth: 0,
                                cutout: '70%'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#e2e8f0',
                                        padding: 20,
                                        usePointStyle: true
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((context.parsed / total) * 100);
                                            return `${context.label}: ${context.parsed} students (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    </main>
</body>
</html>


