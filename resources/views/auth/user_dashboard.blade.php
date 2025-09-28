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
  <!-- styles removed: using Tailwind utility classes -->
 </head>
 <body class="flex flex-col md:flex-row min-h-screen bg-neutral-950 text-neutral-200">
  <!-- Sidebar -->
  <aside class="flex flex-col bg-neutral-950 text-neutral-200 w-full md:w-72 min-h-screen border-r border-neutral-800 overflow-y-auto" id="sidebar" style="scrollbar-width: thin; scrollbar-color: #f97316 transparent;">
   <div class="flex items-center gap-3 px-8 py-6 border-b border-neutral-800">
    <div class="w-8 h-8 flex-shrink-0 text-[#f97316]">
     <svg class="w-full h-full" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
      <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor">
      </path>
     </svg>
    </div>
    <h1 class="text-[#f97316] font-extrabold text-xl tracking-tight select-none" style="letter-spacing: -0.015em;">
     Efees
    </h1>
   </div>
   <nav class="flex flex-col mt-6 px-4 space-y-1 flex-grow">
    <a aria-current="page" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-[#f97316] text-black font-semibold transition-colors duration-300" href="#">
     <i class="fas fa-tachometer-alt w-5">
     </i>
     <span class="text-sm font-semibold">
      Dashboard
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="{{ route('student.profile.show') }}">
     <i class="fas fa-user w-5"></i>
     <span class="text-sm font-semibold">Student Profile</span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="#">
     <i class="fas fa-file-invoice-dollar w-5">
     </i>
     <span class="text-sm font-semibold">
      Fee Records
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="#">
     <i class="fas fa-history w-5">
     </i>
     <span class="text-sm font-semibold">
      Payment History
     </span>
    </a>
    <a class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="#">
      <div class="flex items-center gap-3">
        <i class="fas fa-bell w-5">
        </i>
        <span class="text-sm font-semibold">
         Notification
        </span>
      </div>
      <span id="notif-badge" class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-black bg-[#f97316] rounded-full select-none">
       0
      </span>
    </a>
   </nav>
   <div class="px-4 py-4 border-t border-[#e7f4e7]">
    <form method="POST" action="{{ route('logout') }}">
     @csrf
     <button class="w-full flex items-center gap-3 bg-[#f97316] text-black font-bold text-sm rounded-lg h-10 justify-center cursor-pointer select-none transition-colors duration-300 hover:bg-[#ea7a11]" type="submit" aria-label="Logout">
      <i class="fas fa-sign-out-alt"></i>
      <span>Logout</span>
     </button>
    </form>
   </div>
  </aside>
  <!-- Main content -->
  <main class="flex-1 p-8 overflow-y-auto" style="color: #fb923c; background-color: #121212;">
   <div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-extrabold select-none" style="letter-spacing: -0.015em;">
     Dashboard
    </h1>
    <!-- User Profile Circle -->
    <div class="flex items-center gap-3">
     <div class="text-right">
      <p class="text-sm font-semibold text-[#f97316]">{{ Auth::user()->student ? Auth::user()->student->first_name . ' ' . Auth::user()->student->last_name : 'User' }}</p>
      <p class="text-xs text-[#fb923c]">{{ Auth::user()->email }}</p>
     </div>
     @php($photo = optional(Auth::user()->student)->profile_picture_url)
     @if(!empty($photo))
       <img src="{{ $photo }}" alt="Profile" class="w-12 h-12 rounded-full object-cover border border-[#ea9e4a]" />
     @else
       <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#f97316] to-[#facc15] flex items-center justify-center text-black font-bold text-lg">
        {{ Auth::user()->student ? strtoupper(substr(Auth::user()->student->first_name, 0, 1) . substr(Auth::user()->student->last_name, 0, 1)) : 'U' }}
       </div>
     @endif
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
     Search
    </label>
    <div class="relative text-neutral-400 focus-within:text-orange-500 transition-colors duration-200">
     <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <i class="fas fa-search">
      </i>
     </div>
     <input class="block w-full rounded-lg border border-neutral-700 bg-neutral-900 py-2 pl-10 pr-3 text-sm placeholder-neutral-500 text-neutral-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200" id="search" name="search" placeholder="Search..." type="search"/>
    </div>
   </div>
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Upcoming Payments -->
    <section class="bg-neutral-900 rounded-2xl shadow-lg border border-neutral-800 p-6 flex flex-col">
      <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-orange-400">
        <i class="fas fa-calendar-alt">
        </i>
        Upcoming Payments
      </h2>
      <ul class="divide-y divide-neutral-800 overflow-auto scrollbar-thin max-h-64">
        @forelse(($upcomingFees ?? []) as $fee)
          <li class="py-3 flex justify-between items-center">
            <div>
              <p class="font-semibold">Fee #{{ $fee->fee_id }}</p>
              <p class="text-sm text-neutral-400">Status: {{ $fee->status ?? 'unpaid' }}</p>
            </div>
            <p class="font-semibold text-orange-400">
              ${{ number_format((float) (is_numeric($fee->balance) ? $fee->balance : 0), 2) }}
            </p>
          </li>
        @empty
          <li class="py-6 text-center text-neutral-400">No upcoming payments.</li>
        @endforelse
      </ul>
    </section>
    <!-- SMS Notification (Received Message) -->
    <section class="bg-neutral-900 rounded-2xl shadow-lg border border-neutral-800 p-6 flex flex-col">
      <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-orange-400">
        <i class="fas fa-sms"></i>
        Staff Notifications
      </h2>
      <ul class="divide-y divide-neutral-800 overflow-auto scrollbar-thin max-h-64">
        @forelse(($notifications ?? []) as $n)
          <li class="py-3">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="font-semibold text-orange-400">{{ $n->title }}</p>
                <p class="text-sm text-neutral-300">{{ $n->body }}</p>
              </div>
              <div class="text-xs text-neutral-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</div>
            </div>
          </li>
        @empty
          <li class="py-6 text-center text-neutral-400">No notifications yet.</li>
        @endforelse
      </ul>
    </section>
    <!-- Recent Transactions -->
    <section class="bg-neutral-900 rounded-2xl shadow-lg border border-neutral-800 p-6 flex flex-col">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-orange-400">
      <i class="fas fa-history">
      </i>
      Recent Transactions
     </h2>
     <div class="overflow-x-auto scrollbar-thin max-h-64">
      <table class="min-w-full text-sm">
        <thead class="bg-neutral-950 sticky top-0 z-10">
          <tr>
            <th class="px-4 py-2 text-left font-semibold text-neutral-300" scope="col">Date</th>
            <th class="px-4 py-2 text-left font-semibold text-neutral-300" scope="col">Description</th>
            <th class="px-4 py-2 text-right font-semibold text-neutral-300" scope="col">Amount</th>
            <th class="px-4 py-2 text-left font-semibold text-neutral-300" scope="col">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-neutral-800">
          @forelse(($transactions ?? []) as $t)
            <tr>
              <td class="px-4 py-3 whitespace-nowrap text-neutral-200">{{ \Carbon\Carbon::parse($t->created_at)->format('Y-m-d H:i') }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-neutral-200">{{ ucfirst($t->type) }}{{ $t->note ? ' - ' . $t->note : '' }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-right font-semibold {{ ($t->type === 'approval') ? 'text-green-500' : 'text-orange-400' }}">
                {{ ($t->type === 'approval') ? '+' : '' }}${{ number_format((float) (is_numeric($t->amount) ? $t->amount : 0), 2) }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-600/20 text-green-300 border border-green-600/40">Recorded</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-4 py-6 text-center text-neutral-400">No recent transactions.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
     </div>
    </section>
    <!-- Balance -->
    <section class="bg-neutral-900 rounded-2xl shadow-lg border border-neutral-800 p-6 flex flex-col justify-center items-center text-center">
     <h2 class="text-base md:text-lg font-semibold mb-4 flex items-center gap-2 select-none text-orange-400">
      <i class="fas fa-wallet">
      </i>
      Balance
     </h2>
     <p class="text-5xl font-extrabold mb-2 text-orange-400 select-text">
      ${{ number_format((float) ($balanceDue ?? 0), 2) }}
    </p>
     <p class="text-neutral-400 select-text">
      Available balance in your account
     </p>
     <img alt="Illustration of a wallet with money and coins representing available balance" class="mt-6 w-40 h-auto mx-auto select-none" height="120" loading="lazy" src="https://storage.googleapis.com/a1aa/image/69d3effd-7a93-4b8b-9ca0-44b840beddfa.jpg" width="200"/>
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
      wrap.style.background = '#1a1a1a';
      wrap.style.border = '1px solid #ea9e4a';
      wrap.style.color = '#fb923c';
      wrap.style.padding = '0.75rem 1rem';
      wrap.style.borderRadius = '0.5rem';
      wrap.style.boxShadow = '0 6px 16px rgba(0,0,0,0.4)';
      wrap.style.minWidth = '260px';
      wrap.innerHTML = `<div style="font-weight:700;color:#f97316;margin-bottom:4px;">${title}</div><div>${body}</div>`;
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
 </body>
</html>