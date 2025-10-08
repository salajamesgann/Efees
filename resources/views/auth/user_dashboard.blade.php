<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>
   Efees Dashboard
  </title>
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries">
  </script>
  <style>
    body { font-family: 'Inter', 'Noto Sans', sans-serif; }
    .gradient-bg {
      background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
    }
    .gradient-text {
      background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .btn-primary {
      background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
    }
    .card-hover {
      transition: all 0.3s ease;
    }
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1);
    }
    .glassmorphism {
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(37, 99, 235, 0.2);
    }
    .fade-in {
      animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .chart-container {
      position: relative;
      height: 120px;
    }
  </style>
 </head>
 <body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-slate-800">
  <!-- Sidebar -->
  <aside class="flex flex-col bg-white text-slate-700 w-full md:w-72 min-h-screen border-r border-slate-200 overflow-y-auto" id="sidebar" style="scrollbar-width: thin; scrollbar-color: #7c3aed transparent;">
   <div class="flex items-center gap-3 px-8 py-6 border-b border-slate-200">
    <div class="w-8 h-8 flex-shrink-0 text-blue-600">
     <svg class="w-full h-full" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
      <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor">
      </path>
     </svg>
    </div>
    <h1 class="text-blue-600 font-extrabold text-xl tracking-tight select-none" style="letter-spacing: -0.015em;">
     Efees
    </h1>
   </div>
  <nav class="flex flex-col mt-6 px-4 space-y-1 flex-grow">
    <a aria-current="page" class="flex items-center gap-3 px-4 py-3 rounded-lg text-blue-600 bg-slate-100 border-r-4 border-blue-600 font-semibold transition-colors duration-200" href="#">
     <i class="fas fa-tachometer-alt w-5"></i>
     <span class="text-sm font-semibold">
      Student Dashboard
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-100 hover:text-blue-600 transition-colors duration-200" href="#">
      <i class="fas fa-credit-card w-5"></i>
      <span class="text-sm font-semibold">Online Payment</span>
     </a>
     
     <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-100 hover:text-blue-600 transition-colors duration-200" href="#">
      <i class="fas fa-history w-5">
      </i>
      <span class="text-sm font-semibold">
       Payment History
     </span>
    </a>
    <a class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg hover:bg-slate-100 hover:text-blue-600 transition-colors duration-300" href="#">
      <div class="flex items-center gap-3">
        <i class="fas fa-sms w-5">
        </i>
        <span class="text-sm font-semibold">
         SMS Notifications
        </span>
      </div>
      <span id="notif-badge" class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-blue-600 rounded-full select-none">
       0
      </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-100 hover:text-blue-600 transition-colors duration-200" href="{{ route('student.profile.show') }}">
      <i class="fas fa-user-cog w-5"></i>
      <span class="text-sm font-semibold">Profile Management</span>
     </a>
   </nav>
   <div class="px-4 py-4 border-t border-slate-200">
    <form method="POST" action="{{ route('logout') }}">
     @csrf
     <button class="w-full flex items-center gap-3 bg-blue-600 text-white font-bold text-sm rounded-lg h-10 justify-center cursor-pointer select-none transition-colors duration-300 hover:bg-purple-600" type="submit" aria-label="Logout">
      <i class="fas fa-sign-out-alt"></i>
      <span>Logout</span>
     </button>
    </form>
   </div>
  </aside>
  <!-- Main content -->
  <main class="flex-1 p-8 overflow-y-auto bg-gray-50">
   <div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-extrabold select-none text-slate-800" style="letter-spacing: -0.015em;">
     Dashboard
    </h1>
    <!-- User Profile Dropdown -->
    <div class="relative" id="user-menu">
     <button class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 transition-colors duration-200" id="user-menu-button" type="button" aria-expanded="false" aria-haspopup="true">
      <div class="text-right">
       <p class="text-sm font-semibold text-blue-600">{{ Auth::user()->student ? Auth::user()->student->first_name . ' ' . Auth::user()->student->last_name : 'User' }}</p>
       <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
      </div>
      @php($photo = optional(Auth::user()->student)->profile_picture_url)
      @if(!empty($photo))
       <img src="{{ $photo }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-blue-600" />
      @else
       <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
        {{ Auth::user()->student ? strtoupper(substr(Auth::user()->student->first_name, 0, 1) . substr(Auth::user()->student->last_name, 0, 1)) : 'U' }}
       </div>
      @endif
      <i class="fas fa-chevron-down text-slate-400"></i>
     </button>
     <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden" id="user-menu-dropdown" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
      <a class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-blue-600 transition-colors duration-200" href="{{ route('student.profile.show') }}" role="menuitem" tabindex="-1" id="user-menu-item-0">Profile</a>
      <a class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-blue-600 transition-colors duration-200" href="{{ route('student.settings') }}" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
      <a class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-blue-600 transition-colors duration-200" href="{{ route('logout') }}" role="menuitem" tabindex="-1" id="user-menu-item-2">Logout</a>
     </div>
    </div>
   </div>

   @if(session('success'))
     <div class="mb-6 border border-green-200 text-green-700 bg-green-50 rounded-md px-4 py-3">
       {{ session('success') }}
     </div>
   @endif
   <!-- Search bar -->
   <div class="max-w-md mb-8">
    <label class="sr-only" for="search">
     Search
    </label>
    <div class="relative text-slate-500 focus-within:text-blue-600 transition-colors duration-200">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
       <i class="fas fa-search text-slate-400">
       </i>
      </div>
     <input class="block w-full rounded-lg border border-slate-300 bg-white py-2 pl-10 pr-3 text-sm placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" id="search" name="search" placeholder="Search..." type="search"/>
    </div>
   </div>
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Current Balance -->
    <section class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 flex flex-col justify-center items-center text-center card-hover">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-blue-600">
      <i class="fas fa-wallet text-blue-600">
      </i>
      Current Balance
     </h2>
     <p class="text-4xl font-extrabold mb-2 text-slate-800 select-text">
      ${{ number_format((float) ($balanceDue ?? 0), 2) }}
     </p>
     <p class="text-slate-500 select-text">
      Amount due
     </p>
    </section>
    <!-- Total Paid -->
    <section class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 flex flex-col justify-center items-center text-center card-hover">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-green-600">
      <i class="fas fa-check-circle text-green-600">
      </i>
      Total Paid
     </h2>
     <p class="text-4xl font-extrabold mb-2 text-slate-800 select-text">
      ${{ number_format((float) ($totalPaid ?? 0), 2) }}
     </p>
     <p class="text-slate-500 select-text">
      Amount paid
     </p>
    </section>
    <!-- Upcoming Payments -->
    <section class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 flex flex-col card-hover">
      <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-blue-600">
        <i class="fas fa-calendar-alt text-blue-600">
        </i>
        Payment Deadlines
      </h2>
      <ul class="divide-y divide-slate-200 overflow-auto scrollbar-thin max-h-64">
        @forelse(($upcomingFees ?? []) as $fee)
          <li class="py-3 flex justify-between items-center">
            <div>
              <p class="font-semibold text-slate-800">Fee #{{ $fee->fee_id }}</p>
              <p class="text-sm text-slate-500">Due: {{ $fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('M d, Y') : 'No due date' }}</p>
            </div>
            <p class="font-semibold text-blue-600">
              ${{ number_format((float) (is_numeric($fee->balance) ? $fee->balance : 0), 2) }}
            </p>
          </li>
        @empty
          <li class="py-6 text-center text-slate-500">No pending payments.</li>
        @endforelse
      </ul>
    </section>
    <!-- Recent Paid Fees -->
    <section class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 flex flex-col card-hover">
      <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-green-600">
        <i class="fas fa-receipt text-green-600">
        </i>
        Recent Payments
      </h2>
      <ul class="divide-y divide-slate-200 overflow-auto scrollbar-thin max-h-64">
        @forelse(($paidFees ?? []) as $fee)
          <li class="py-3 flex justify-between items-center">
            <div>
              <p class="font-semibold text-green-600">Fee #{{ $fee->fee_id }}</p>
              <p class="text-sm text-slate-500">Paid: {{ $fee->id ? 'Record #' . $fee->id : 'N/A' }}</p>
            </div>
            <p class="font-semibold text-green-600">
              ${{ number_format((float) (is_numeric($fee->balance) ? $fee->balance : 0), 2) }}
            </p>
          </li>
        @empty
          <li class="py-6 text-center text-slate-500">No payments yet.</li>
        @endforelse
      </ul>
    </section>
    <!-- Staff Notifications -->
    <section class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 flex flex-col md:col-span-2 card-hover">
      <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-blue-600">
        <i class="fas fa-bell text-blue-600"></i>
        Notifications
      </h2>
      <ul class="divide-y divide-slate-200 overflow-auto scrollbar-thin max-h-64">
        @forelse(($notifications ?? []) as $n)
          <li class="py-3 fade-in">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="font-semibold text-blue-600">{{ $n->title }}</p>
                <p class="text-sm text-slate-600">{{ $n->body }}</p>
              </div>
              <div class="text-xs text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</div>
            </div>
          </li>
        @empty
          <li class="py-6 text-center text-slate-500">No notifications yet.</li>
        @endforelse
      </ul>
    </section>
    <!-- Recent Transactions -->
    <section class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 flex flex-col md:col-span-2 card-hover">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-blue-600">
      <i class="fas fa-history text-blue-600">
      </i>
      Transaction History
     </h2>
     <div class="overflow-x-auto scrollbar-thin max-h-64">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 sticky top-0 z-10">
          <tr>
            <th class="px-4 py-2 text-left font-semibold text-slate-600" scope="col">Date</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-600" scope="col">Type</th>
            <th class="px-4 py-2 text-right font-semibold text-slate-600" scope="col">Amount</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-600" scope="col">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          @forelse(($transactions ?? []) as $t)
            <tr class="hover:bg-slate-50 transition-colors duration-200">
              <td class="px-4 py-3 whitespace-nowrap text-slate-600">{{ \Carbon\Carbon::parse($t->created_at)->format('Y-m-d H:i') }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-slate-600">{{ ucfirst($t->type) }}{{ $t->note ? ' - ' . $t->note : '' }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-right font-semibold {{ ($t->type === 'payment' || $t->type === 'refund') ? 'text-green-600' : 'text-blue-600' }}">
                {{ ($t->type === 'payment' || $t->type === 'refund') ? '+' : '' }}${{ number_format((float) (is_numeric($t->amount) ? $t->amount : 0), 2) }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-300">Completed</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-4 py-6 text-center text-slate-500">No transactions yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
     </div>
    </section>
   </div>
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

    if (!url || !anon) {
      console.warn('Supabase URL/ANON KEY not configured. Set SUPABASE_URL and SUPABASE_ANON_KEY in your .env.');
    }

    if (!authUserId) {
      console.warn('No authenticated user id found; skipping realtime subscription.');
    }

    const supabase = (url && anon) ? createClient(url, anon) : null;

    const badgeEl = document.getElementById('notif-badge');
    const toastContainer = document.getElementById('toast-container');

    function setBadge(n) {
      if (badgeEl) badgeEl.textContent = String(n);
    }
    function incBadge() {
      if (!badgeEl) return;
      const current = parseInt(badgeEl.textContent || '0', 10) || 0;
      badgeEl.textContent = String(current + 1);
    }
    function showToast(title, body) {
      if (!toastContainer) return;
      const wrap = document.createElement('div');
      wrap.style.background = '#ffffff';
      wrap.style.border = '1px solid #e2e8f0'; /* slate-200 */
      wrap.style.color = '#334155'; /* slate-700 */
      wrap.style.padding = '0.75rem 1rem';
      wrap.style.borderRadius = '0.5rem';
      wrap.style.boxShadow = '0 6px 16px rgba(0,0,0,0.08)';
      wrap.style.minWidth = '260px';
      wrap.innerHTML = `<div style="font-weight:700;color:#2563eb;margin-bottom:4px;">${title}</div><div>${body}</div>`;
      toastContainer.appendChild(wrap);
      setTimeout(() => { wrap.remove(); }, 6000);
    }

    async function refreshCount() {
      if (!supabase || !authUserId) return;
      const { count, error } = await supabase
        .from('notifications')
        .select('*', { count: 'exact', head: true })
        .eq('user_id', authUserId);
      if (!error && typeof count === 'number') setBadge(count);
    }

    async function initRealtime() {
      if (!supabase || !authUserId) return;
      await refreshCount();
      const channel = supabase.channel(`notifications-${authUserId}`);
      channel.on(
        'postgres_changes',
        { event: 'INSERT', schema: 'public', table: 'notifications', filter: `user_id=eq.${authUserId}` },
        (payload) => {
          const n = payload.new || {};
          incBadge();
          showToast(n.title || 'Notification', n.body || '');
        }
      );
      channel.subscribe((status) => {
        if (status === 'SUBSCRIBED') {
          console.log('Realtime: subscribed to notifications for', authUserId);
        }
      });
    }

    initRealtime();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // User dropdown toggle
    document.getElementById('user-menu-button').addEventListener('click', function() {
      const dropdown = document.getElementById('user-menu-dropdown');
      dropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('user-menu-dropdown');
      const button = document.getElementById('user-menu-button');
      if (!button.contains(event.target)) {
        dropdown.classList.add('hidden');
      }
    });

    // Chart.js for mini charts
    document.addEventListener('DOMContentLoaded', function() {
      // Transaction Line Chart
      const transactionCtx = document.getElementById('transactionChart');
      if (transactionCtx) {
        new Chart(transactionCtx, {
          type: 'line',
          data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
              label: 'Transactions',
              data: [12, 19, 3, 5, 2, 3],
              borderColor: '#2563eb',
              backgroundColor: 'rgba(37, 99, 235, 0.2)',
              tension: 0.1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              }
            },
            scales: {
              x: {
                display: false
              },
              y: {
                display: false
              }
            }
          }
        });
      }

      // Balance Donut Chart
      const balanceCtx = document.getElementById('balanceChart');
      if (balanceCtx) {
        new Chart(balanceCtx, {
          type: 'doughnut',
          data: {
            labels: ['Paid', 'Pending'],
            datasets: [{
              data: [70, 30],
              backgroundColor: [
                '#10b981',
                '#2563eb'
              ],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              }
            }
          }
        });
      }
    });
  </script>
</body>
</html>