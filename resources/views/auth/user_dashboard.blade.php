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
     <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-black bg-[#f97316] rounded-full select-none">
      3
     </span>
    </a>
   </nav>
   <div class="px-4 py-4 border-t border-[#e7f4e7]">
    <button class="w-full flex items-center gap-3 bg-[#f97316] text-black font-bold text-sm rounded-lg h-10 justify-center cursor-pointer select-none transition-colors duration-300 hover:bg-[#ea7a11]" type="button" aria-label="Logout">
     <i class="fas fa-sign-out-alt"></i>
     <span>Logout</span>
    </button>
   </div>
  </aside>
  <!-- Main content -->
  <main class="flex-1 p-8 overflow-y-auto" style="color: #fb923c; background-color: #121212;">
   <h1 class="text-3xl font-extrabold mb-8 select-none" style="letter-spacing: -0.015em;">
    Dashboard
   </h1>
   <!-- Search bar -->
   <div class="max-w-md mb-8">
    <label class="sr-only" for="search">
     Search
    </label>
    <div class="relative text-[#fb923c] focus-within:text-[#f97316] transition-colors duration-300">
     <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <i class="fas fa-search">
      </i>
     </div>
     <input class="block w-full rounded-lg border border-[#ea9e4a] bg-[#1a1a1a] py-2 pl-10 pr-3 text-sm placeholder-[#fb923c] text-[#fb923c] focus:outline-none focus:ring-2 focus:ring-[#f97316] focus:border-[#f97316] transition-colors duration-300" id="search" name="search" placeholder="Search..." type="search"/>
    </div>
   </div>
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Upcoming Payments -->
    <section class="bg-[#1a1a1a] rounded-lg shadow-lg p-6 flex flex-col text-[#fb923c]">
     <h2 class="text-lg font-bold mb-4 flex items-center gap-2 select-none" style="color: #f97316;">
      <i class="fas fa-calendar-alt">
      </i>
      Upcoming Payments
     </h2>
     <ul class="divide-y divide-[#ea9e4a] overflow-auto scrollbar-thin max-h-64">
      <li class="py-3 flex justify-between items-center">
       <div>
        <p class="font-semibold">
         Tuition Fee
        </p>
        <p class="text-sm text-[#fdba74]">
         Due: 2024-07-10
        </p>
       </div>
       <p class="font-semibold text-[#f97316]">
        $1,200
       </p>
      </li>
      <li class="py-3 flex justify-between items-center">
       <div>
        <p class="font-semibold">
         Library Fee
        </p>
        <p class="text-sm text-[#fdba74]">
         Due: 2024-07-15
        </p>
       </div>
       <p class="font-semibold text-[#f97316]">
        $150
       </p>
      </li>
      <li class="py-3 flex justify-between items-center">
       <div>
        <p class="font-semibold">
         Lab Fee
        </p>
        <p class="text-sm text-[#fdba74]">
         Due: 2024-07-20
        </p>
       </div>
       <p class="font-semibold text-[#f97316]">
        $300
       </p>
      </li>
      <li class="py-3 flex justify-between items-center">
       <div>
        <p class="font-semibold">
         Sports Fee
        </p>
        <p class="text-sm text-[#fdba74]">
         Due: 2024-07-25
        </p>
       </div>
       <p class="font-semibold text-[#f97316]">
        $100
       </p>
      </li>
      <li class="py-3 flex justify-between items-center">
       <div>
        <p class="font-semibold">
         Exam Fee
        </p>
        <p class="text-sm text-[#fdba74]">
         Due: 2024-07-30
        </p>
       </div>
       <p class="font-semibold text-[#f97316]">
        $200
       </p>
      </li>
     </ul>
    </section>
    <!-- SMS Notification (Received Message) -->
    <section class="bg-[#1a1a1a] rounded-lg shadow-lg p-6 flex flex-col text-[#fb923c]">
     <h2 class="text-lg font-bold mb-4 flex items-center gap-2 select-none" style="color: #f97316;">
      <i class="fas fa-sms">
      </i>
      SMS Notification - Received Messages
     </h2>
     <div class="flex-grow flex flex-col justify-between">
      <p class="mb-4 text-[#fdba74]">
       View SMS messages received from students or payers.
      </p>
      <form class="space-y-4" novalidate="">
       <div>
        <label class="block text-[#fb923c] font-medium text-base mb-2" for="senderPhone">
         Sender Phone Number
        </label>
        <input class="w-full rounded-lg border border-[#ea9e4a] bg-[#1a1a1a] py-3 px-4 text-[#fb923c] text-base focus:outline-none focus:ring-2 focus:ring-[#f97316] focus:border-[#f97316] transition-colors duration-300" id="senderPhone" name="senderPhone" placeholder="+1 234 567 8900" type="tel" required/>
       </div>
       <div>
        <label class="block text-[#fb923c] font-medium text-base mb-2" for="receivedMessage">
         Received Message
        </label>
        <textarea class="w-full resize-none rounded-lg border border-[#ea9e4a] bg-[#1a1a1a] py-3 px-4 text-[#fb923c] text-base focus:outline-none focus:ring-2 focus:ring-[#f97316] focus:border-[#f97316] transition-colors duration-300" id="receivedMessage" name="receivedMessage" placeholder="Message content here..." rows="3" required></textarea>
       </div>
       <button class="w-full bg-gradient-to-r from-[#f97316] to-[#facc15] text-black font-bold py-2 rounded-lg shadow-md hover:from-[#facc15] hover:to-[#f97316] transition-colors duration-300" type="submit">
        Acknowledge Message
       </button>
      </form>
     </div>
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
          Date
         </th>
         <th class="px-4 py-2 text-left font-semibold text-[#fb923c]" scope="col">
          Description
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
          2024-06-25
         </td>
         <td class="px-4 py-3 whitespace-nowrap text-[#fb923c]">
          Tuition Fee Payment
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
          2024-06-20
         </td>
         <td class="px-4 py-3 whitespace-nowrap text-[#fb923c]">
          Library Fee Payment
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
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-[#fb923c]">
          2024-06-15
         </td>
         <td class="px-4 py-3 whitespace-nowrap text-[#fb923c]">
          Lab Fee Payment
         </td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-yellow-400">
          -$300
         </td>
         <td class="px-4 py-3 whitespace-nowrap">
          <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-700 text-yellow-300">
           Pending
          </span>
         </td>
        </tr>
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-[#fb923c]">
          2024-06-10
         </td>
         <td class="px-4 py-3 whitespace-nowrap text-[#fb923c]">
          Sports Fee Payment
         </td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-green-500">
          +$100
         </td>
         <td class="px-4 py-3 whitespace-nowrap">
          <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-700 text-green-300">
           Completed
          </span>
         </td>
        </tr>
        <tr>
         <td class="px-4 py-3 whitespace-nowrap text-[#fb923c]">
          2024-06-05
         </td>
         <td class="px-4 py-3 whitespace-nowrap text-[#fb923c]">
          Exam Fee Payment
         </td>
         <td class="px-4 py-3 whitespace-nowrap text-right font-semibold text-yellow-400">
          -$200
         </td>
         <td class="px-4 py-3 whitespace-nowrap">
          <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-700 text-yellow-300">
           Pending
          </span>
         </td>
        </tr>
       </tbody>
      </table>
     </div>
    </section>
    <!-- Balance -->
    <section class="bg-[#1a1a1a] rounded-lg shadow-lg p-6 flex flex-col justify-center items-center text-center text-[#fb923c]">
     <h2 class="text-lg font-bold mb-4 flex items-center gap-2 select-none" style="color: #f97316;">
      <i class="fas fa-wallet">
      </i>
      Balance
     </h2>
     <p class="text-5xl font-extrabold mb-2 text-[#f97316] select-text">
      $2,450.00
     </p>
     <p class="text-[#fdba74] select-text">
      Available balance in your account
     </p>
     <img alt="Illustration of a wallet with money and coins representing available balance" class="mt-6 w-40 h-auto mx-auto select-none" height="120" loading="lazy" src="https://storage.googleapis.com/a1aa/image/69d3effd-7a93-4b8b-9ca0-44b840beddfa.jpg" width="200"/>
    </section>
   </div>
  </main>
 </body>
</html>