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
  <style>
   body {
        font-family: 'Inter', 'Noto Sans', sans-serif;
        background-color: black;
        min-height: 100vh;
        margin: 0;
        overflow-x: hidden;
      }
      /* Scrollbar for sidebar */
      #sidebar::-webkit-scrollbar {
        width: 6px;
      }
      #sidebar::-webkit-scrollbar-thumb {
        background-color: #f97316;
        border-radius: 3px;
      }
      #sidebar::-webkit-scrollbar-track {
        background: transparent;
      }
      /* Scrollbar for upcoming payments and recent transactions */
      .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
        height: 6px;
      }
      .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: #f97316;
        border-radius: 3px;
      }
      .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
      }
  </style>
 </head>
 <body class="flex flex-col md:flex-row min-h-screen">
  <!-- Sidebar -->
  <aside class="flex flex-col bg-black text-[#fb923c] w-full md:w-64 min-h-screen border-r border-[#ea9e4a] overflow-y-auto" id="sidebar" style="scrollbar-width: thin; scrollbar-color: #f97316 transparent;">
   <div class="flex items-center gap-3 px-8 py-6 border-b border-[#e7f4e7]">
    <div class="w-8 h-8 flex-shrink-0 text-[#f97316]">
     <svg class="w-full h-full" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
      <path d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z" fill="currentColor">
      </path>
     </svg>
    </div>
    <h1 class="text-[#f97316] font-extrabold text-xl tracking-tight select-none" style="letter-spacing: -0.015em;">
     Efees Admin
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
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="{{ route('admin.students.index') }}">
     <i class="fas fa-users w-5">
     </i>
     <span class="text-sm font-semibold">
      Manage Students
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="#">
     <i class="fas fa-file-invoice-dollar w-5">
     </i>
     <span class="text-sm font-semibold">
      Fee Management
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="#">
     <i class="fas fa-chart-bar w-5">
     </i>
     <span class="text-sm font-semibold">
      Reports
     </span>
    </a>
    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#1a1a1a] transition-colors duration-300" href="#">
     <i class="fas fa-cog w-5">
     </i>
     <span class="text-sm font-semibold">
      Settings
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
     Admin Dashboard
    </h1>
    <!-- Admin Profile Circle -->
    <div class="flex items-center gap-3">
     <div class="text-right">
      <p class="text-sm font-semibold text-[#f97316]">{{ Auth::user()->student ? Auth::user()->student->first_name . ' ' . Auth::user()->student->last_name : 'Admin' }}</p>
      <p class="text-xs text-[#fb923c]">{{ Auth::user()->email }}</p>
      <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-700 text-red-300">Admin</span>
     </div>
     <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#dc2626] to-[#991b1b] flex items-center justify-center text-white font-bold text-lg">
      {{ Auth::user()->student ? strtoupper(substr(Auth::user()->student->first_name, 0, 1) . substr(Auth::user()->student->last_name, 0, 1)) : 'A' }}
     </div>
    </div>
   </div>
   <!-- Search bar -->
   <div class="max-w-md mb-8">
    <label class="sr-only" for="search">
     Search Students
    </label>
    <div class="relative text-[#fb923c] focus-within:text-[#f97316] transition-colors duration-300">
     <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <i class="fas fa-search">
      </i>
     </div>
     <input class="block w-full rounded-lg border border-[#ea9e4a] bg-[#1a1a1a] py-2 pl-10 pr-3 text-sm placeholder-[#fb923c] text-[#fb923c] focus:outline-none focus:ring-2 focus:ring-[#f97316] focus:border-[#f97316] transition-colors duration-300" id="search" name="search" placeholder="Search students..." type="search"/>
    </div>
   </div>
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Total Students -->
    <section class="bg-[#1a1a1a] rounded-lg shadow-lg p-6 flex flex-col justify-center items-center text-center text-[#fb923c]">
     <h2 class="text-lg font-bold mb-4 flex items-center gap-2 select-none" style="color: #f97316;">
      <i class="fas fa-users">
      </i>
      Total Students
     </h2>
     <p class="text-5xl font-extrabold mb-2 text-[#f97316] select-text">
      156
     </p>
     <p class="text-[#fdba74] select-text">
      Registered students
     </p>
    </section>
    <!-- Pending Payments -->
    <section class="bg-[#1a1a1a] rounded-lg shadow-lg p-6 flex flex-col text-[#fb923c]">
     <h2 class="text-lg font-bold mb-4 flex items-center gap-2 select-none" style="color: #f97316;">
      <i class="fas fa-exclamation-triangle">
      </i>
      Pending Payments
     </h2>
     <ul class="divide-y divide-[#ea9e4a] overflow-auto scrollbar-thin max-h-64">
      <li class="py-3 flex justify-between items-center">
       <div>
        <p class="font-semibold">
         John Doe
        </p>
        <p class="text-sm text-[#fdba74]">
         Tuition Fee - Due: 2024-07-10
        </p>
       </div>
       <p class="font-semibold text-[#dc2626]">
        $1,200
       </p>
      </li>
      <li class="py-3 flex justify-between items-center">
       <div>
        <p class="font-semibold">
         Jane Smith
        </p>
        <p class="text-sm text-[#fdba74]">
         Library Fee - Due: 2024-07-15
        </p>
       </div>
       <p class="font-semibold text-[#dc2626]">
        $150
       </p>
      </li>
      <li class="py-3 flex justify-between items-center">
       <div>
        <p class="font-semibold">
         Mike Johnson
        </p>
        <p class="text-sm text-[#fdba74]">
         Lab Fee - Due: 2024-07-20
        </p>
       </div>
       <p class="font-semibold text-[#dc2626]">
        $300
       </p>
      </li>
     </ul>
    </section>
    <!-- Recent Transactions -->
    <section class="bg-[#1a1a1a] rounded-lg shadow-lg p-6 flex flex-col text-[#fb923c]">
     <h2 class="text-lg font-bold mb-4 flex items-center gap-2 select-none" style="color: #f97316;">
      <i class="fas fa-history">
      </i>
      Recent Transactions
     </h2>
     <div class="overflow-x-auto scrollbar-thin max-h-64">
      <table class="min-w-full divide-y divide-[#ea9e4a] text-sm">
       <thead class="bg-[#121212] sticky top-0 z-10">
        <tr>
         <th class="px-4 py-2 text-left font-semibold text-[#fb923c]" scope="col">
          Student
         </th>
         <th class="px-4 py-2 text-right font-semibold text-[#fb923c]" scope="col">
          Amount
         </th>
         <th class="px-4 py-2 text-left font-semibold text-[#fb923c]" scope="col">
          Status
         </th>
        </tr>
       </thead>
       <tbody class="divide-y divide-[#ea9e4a]">
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-[#fb923c]">
          John Doe
         </td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-green-500">
          +$1,200
         </td>
         <td class="px-4 py-3 whitespace-nowrap">
          <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-700 text-green-300">
           Completed
          </span>
         </td>
        </tr>
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-[#fb923c]">
          Jane Smith
         </td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-green-500">
          +$150
         </td>
         <td class="px-4 py-3 whitespace-nowrap">
          <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-700 text-green-300">
           Completed
          </span>
         </td>
        </tr>
       </tbody>
      </table>
     </div>
    </section>
    <!-- Total Revenue -->
    <section class="bg-[#1a1a1a] rounded-lg shadow-lg p-6 flex flex-col justify-center items-center text-center text-[#fb923c]">
     <h2 class="text-lg font-bold mb-4 flex items-center gap-2 select-none" style="color: #f97316;">
      <i class="fas fa-dollar-sign">
      </i>
      Total Revenue
     </h2>
     <p class="text-5xl font-extrabold mb-2 text-[#f97316] select-text">
      $89,450
     </p>
     <p class="text-[#fdba74] select-text">
      This month
     </p>
    </section>
   </div>
  </main>
 </body>
</html>