<aside class="w-64 bg-gray-800 text-white min-h-screen p-4">
    <div class="text-2xl font-bold mb-6">Super Admin</div>
    <nav>
        <ul>
            <li class="mb-2">
                <a href="{{ route('super_admin.dashboard') }}" class="block py-2 px-4 rounded hover:bg-gray-700">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('super_admin.students.index') }}" class="block py-2 px-4 rounded hover:bg-gray-700">
                    <i class="fas fa-users mr-2"></i> Student Management
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('super_admin.users.index') }}" class="block py-2 px-4 rounded hover:bg-gray-700">
                    <i class="fas fa-user-shield mr-2"></i> User Management
                </a>
            </li>
            <li class="mb-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left py-2 px-4 rounded hover:bg-gray-700">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>
