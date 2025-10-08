<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>
   Efees Admin Dashboard
  </title>
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: 'Inter', 'Noto Sans', sans-serif; }
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
      height: 300px;
    }
  </style>
 </head>
 <body class="flex flex-col md:flex-row min-h-screen bg-slate-900 text-slate-100">
  <!-- Sidebar -->
  <aside class="flex flex-col bg-slate-800 text-slate-300 w-full md:w-72 min-h-screen border-r border-slate-700 overflow-y-auto" id="sidebar" style="scrollbar-width: thin; scrollbar-color: #8b5cf6 transparent;">
   <div class="flex items-center gap-3 px-8 py-6 border-b border-slate-700">
    <div class="w-8 h-8 flex-shrink-0 text-indigo-500">
     <svg class="w-full h-full" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
      <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor">
      </path>
     </svg>
    </div>
    <h1 class="text-indigo-400 font-extrabold text-xl tracking-tight select-none" style="letter-spacing: -0.015em;">
     Efees Admin
    </h1>
   </div>
   <nav class="flex flex-col mt-6 px-4 space-y-1 flex-grow">
    <a aria-current="page" class="flex items-center gap-3 px-4 py-3 rounded-lg text-indigo-400 bg-slate-700 border-r-4 border-indigo-500 font-semibold transition-colors duration-200" href="#">
     <i class="fas fa-tachometer-alt w-5">
     </i>
     <span class="text-sm font-semibold">
      Dashboard
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="{{ route('admin.students.index') }}">
     <i class="fas fa-users w-5">
     </i>
     <span class="text-sm font-semibold">
      Manage Students
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="{{ route('admin.staff.index') }}">
     <i class="fas fa-user-tie w-5">
     </i>
     <span class="text-sm font-semibold">
      Staff Management
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
     <i class="fas fa-file-invoice-dollar w-5">
     </i>
     <span class="text-sm font-semibold">
      Fee Management
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
     <i class="fas fa-chart-bar w-5">
     </i>
     <span class="text-sm font-semibold">
      Reports
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 hover:text-indigo-400 transition-colors duration-200" href="#">
     <i class="fas fa-cog w-5">
     </i>
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
  <!-- Main content -->
  <main class="flex-1 p-6 md:p-8 overflow-y-auto bg-slate-900">
   <div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-slate-100">
     Admin Dashboard
    </h1>
    <!-- Admin Profile Circle -->
    <div class="flex items-center gap-3">
     <div class="text-right">
      <p class="text-sm font-semibold text-indigo-400">{{ Auth::user()->student ? Auth::user()->student->first_name . ' ' . Auth::user()->student->last_name : 'Admin' }}</p>
      <p class="text-xs text-slate-400">{{ Auth::user()->email }}</p>
      <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-900/50 text-indigo-300 border border-indigo-600">Admin</span>
     </div>
     <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-bold text-lg">
      {{ Auth::user()->student ? strtoupper(substr(Auth::user()->student->first_name, 0, 1) . substr(Auth::user()->student->last_name, 0, 1)) : 'A' }}
     </div>
    </div>
   </div>

   @if(session('success'))
     <div class="mb-6 border border-green-600 text-green-300 bg-green-900/20 rounded-md px-4 py-3">
       {{ session('success') }}
     </div>
   @endif

   <!-- Search bar -->
   <div class="max-w-md mb-8">
    <label class="sr-only" for="search">
     Search Students
    </label>
    <div class="relative text-slate-500 focus-within:text-indigo-400 transition-colors duration-200">
     <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <i class="fas fa-search text-slate-400">
      </i>
     </div>
     <input class="block w-full rounded-lg border border-slate-700 bg-slate-800 py-2 pl-10 pr-3 text-sm placeholder-slate-400 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" id="search" name="search" placeholder="Search students..." type="search"/>
    </div>
   </div>

   <!-- Main Metrics Cards -->
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Fees Collected -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-green-400">
      <i class="fas fa-dollar-sign text-green-500">
      </i>
      Total Collected
     </h2>
     <p class="text-4xl font-extrabold mb-2 text-green-400 select-text">
      $89,450
     </p>
     <p class="text-slate-400 select-text">
      This month
     </p>
    </section>
    <!-- Pending Payments -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-orange-400">
      <i class="fas fa-exclamation-triangle text-orange-500">
      </i>
      Pending Payments
     </h2>
     <p class="text-4xl font-extrabold mb-2 text-orange-400 select-text">
      $12,650
     </p>
     <p class="text-slate-400 select-text">
      Outstanding amount
     </p>
    </section>
    <!-- Total Students -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-indigo-400">
      <i class="fas fa-users text-indigo-500">
      </i>
      Total Students
     </h2>
     <p class="text-4xl font-extrabold mb-2 text-indigo-400 select-text">
      156
     </p>
     <p class="text-slate-400 select-text">
      Registered students
     </p>
    </section>
    <!-- Reminders Sent -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 flex flex-col justify-center items-center text-center card-hover">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-purple-400">
      <i class="fas fa-bell text-purple-500">
      </i>
      Reminders Sent
     </h2>
     <p class="text-4xl font-extrabold mb-2 text-purple-400 select-text">
      47
     </p>
     <p class="text-slate-400 select-text">
      This week
     </p>
    </section>
   </div>

   <!-- Charts Section -->
   <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Payment Status Pie Chart -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover">
     <h2 class="text-xl font-semibold mb-6 flex items-center gap-2 select-none text-slate-100">
      <i class="fas fa-chart-pie text-indigo-500"></i>
      Payment Status Overview
     </h2>
     <div class="chart-container">
      <canvas id="paymentStatusChart"></canvas>
     </div>
    </section>

    <!-- Collections by Grade/Section Bar Chart -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover">
     <h2 class="text-xl font-semibold mb-6 flex items-center gap-2 select-none text-slate-100">
      <i class="fas fa-chart-bar text-green-500"></i>
      Collections by Grade/Section
     </h2>
     <div class="chart-container">
      <canvas id="collectionsByGradeChart"></canvas>
     </div>
    </section>
   </div>

   <!-- Payment Trends Line Chart (Full Width) -->
   <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6 card-hover mb-8">
    <h2 class="text-xl font-semibold mb-6 flex items-center gap-2 select-none text-slate-100">
     <i class="fas fa-chart-line text-orange-500"></i>
     Payment Trends (School Year 2024)
    </h2>
    <div class="chart-container">
     <canvas id="paymentTrendsChart"></canvas>
    </div>
   </section>

   <!-- Recent Activity Tables -->
   <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Pending Payments Table -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-orange-400">
      <i class="fas fa-exclamation-triangle text-orange-500">
      </i>
      Pending Payments
     </h2>
     <div class="overflow-x-auto scrollbar-thin max-h-64">
      <table class="min-w-full text-sm">
       <thead class="bg-slate-700 sticky top-0 z-10">
        <tr>
         <th class="px-4 py-2 text-left font-semibold text-slate-300" scope="col">Student</th>
         <th class="px-4 py-2 text-left font-semibold text-slate-300" scope="col">Fee Type</th>
         <th class="px-4 py-2 text-right font-semibold text-slate-300" scope="col">Amount</th>
        </tr>
       </thead>
       <tbody class="divide-y divide-slate-700">
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">John Doe</td>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Tuition Fee</td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-orange-400">$1,200</td>
        </tr>
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Jane Smith</td>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Library Fee</td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-orange-400">$150</td>
        </tr>
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Mike Johnson</td>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Lab Fee</td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-orange-400">$300</td>
        </tr>
       </tbody>
      </table>
     </div>
    </section>

    <!-- Recent Transactions -->
    <section class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 p-6">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-green-400">
      <i class="fas fa-history text-green-500">
      </i>
      Recent Transactions
     </h2>
     <div class="overflow-x-auto scrollbar-thin max-h-64">
      <table class="min-w-full text-sm">
       <thead class="bg-slate-700 sticky top-0 z-10">
        <tr>
         <th class="px-4 py-2 text-left font-semibold text-slate-300" scope="col">Student</th>
         <th class="px-4 py-2 text-right font-semibold text-slate-300" scope="col">Amount</th>
         <th class="px-4 py-2 text-left font-semibold text-slate-300" scope="col">Status</th>
        </tr>
       </thead>
       <tbody class="divide-y divide-slate-700">
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">John Doe</td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-green-400">+$1,200</td>
         <td class="px-4 py-3 whitespace-nowrap">
          <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-900/50 text-green-400 border border-green-600">Completed</span>
         </td>
        </tr>
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-slate-300">Jane Smith</td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-green-400">+$150</td>
         <td class="px-4 py-3 whitespace-nowrap">
          <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-900/50 text-green-400 border border-green-600">Completed</span>
         </td>
        </tr>
       </tbody>
      </table>
     </div>
    </section>
   </div>
  </main>

  <script>
   document.addEventListener('DOMContentLoaded', function() {
    // Payment Status Pie Chart
    const paymentStatusCtx = document.getElementById('paymentStatusChart');
    if (paymentStatusCtx) {
     new Chart(paymentStatusCtx, {
      type: 'doughnut',
      data: {
       labels: ['Paid', 'Pending'],
       datasets: [{
        data: [89450, 12650],
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
           return `${context.label}: $${context.parsed.toLocaleString()} (${percentage}%)`;
          }
         }
        }
       }
      }
     });
    }

    // Collections by Grade/Section Bar Chart
    const collectionsByGradeCtx = document.getElementById('collectionsByGradeChart');
    if (collectionsByGradeCtx) {
     new Chart(collectionsByGradeCtx, {
      type: 'bar',
      data: {
       labels: ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
       datasets: [{
        label: 'Amount Collected ($)',
        data: [12500, 15800, 14200, 18900, 13200, 14800],
        backgroundColor: [
         '#6366f1',
         '#8b5cf6',
         '#ec4899',
         '#f59e0b',
         '#10b981',
         '#3b82f6'
        ],
        borderRadius: 6,
        borderSkipped: false
       }]
      },
      options: {
       responsive: true,
       maintainAspectRatio: false,
       plugins: {
        legend: {
         display: false
        },
        tooltip: {
         callbacks: {
          label: function(context) {
           return `Collected: $${context.parsed.y.toLocaleString()}`;
          }
         }
        }
       },
       scales: {
        x: {
          grid: {
           color: '#374151'
          },
          ticks: {
           color: '#9ca3af'
          }
        },
        y: {
         grid: {
          color: '#374151'
         },
         ticks: {
          color: '#9ca3af',
          callback: function(value) {
           return '$' + value.toLocaleString();
          }
         }
        }
       }
      }
     });
    }

    // Payment Trends Line Chart
    const paymentTrendsCtx = document.getElementById('paymentTrendsChart');
    if (paymentTrendsCtx) {
     new Chart(paymentTrendsCtx, {
      type: 'line',
      data: {
       labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
       datasets: [{
        label: 'Monthly Collections',
        data: [6500, 7200, 8900, 12000, 15800, 14200, 18900, 16500, 13200, 14800, 11200, 13500],
        borderColor: '#f97316',
        backgroundColor: 'rgba(249, 115, 22, 0.1)',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#f97316',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 6,
        pointHoverRadius: 8
       }]
      },
      options: {
       responsive: true,
       maintainAspectRatio: false,
       plugins: {
        legend: {
         display: false
        },
        tooltip: {
         callbacks: {
          label: function(context) {
           return `Collections: $${context.parsed.y.toLocaleString()}`;
          }
         }
        }
       },
       scales: {
        x: {
         grid: {
          color: '#374151'
         },
         ticks: {
          color: '#9ca3af'
         }
        },
        y: {
         grid: {
          color: '#374151'
         },
         ticks: {
          color: '#9ca3af',
          callback: function(value) {
           return '$' + value.toLocaleString();
          }
         }
        }
       },
       interaction: {
        intersect: false,
        mode: 'index'
       }
      }
     });
    }
   });
  </script>
 </body>
</html>
