<div x-data="{ open: false }" x-cloak class="flex h-screen bg-gray-50">

    <!-- Sidebar -->
    <aside 
        :class="open ? 'translate-x-0' : '-translate-x-48'" 
        class="fixed top-0 left-0 w-48 bg-white h-screen shadow-lg p-2 flex flex-col justify-between transform transition-transform duration-300 ease-in-out z-40 text-sm">

        <div>
            <!-- App Title -->
            <div class="mb-6 flex justify-start items-center px-2">
                <h1 class="text-xl font-bold text-gray-800 tracking-tight pt-4">Project Management</h1>
            </div>

            <!-- Navigation -->
            <nav class="flex flex-col space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center py-2 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'text-blue-500 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <span class="w-1 h-6 rounded-full {{ request()->routeIs('dashboard') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                    <svg class="w-4 h-4 flex-shrink-0 mx-2 {{ request()->routeIs('dashboard') ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"></path>
                    </svg>
                    <span class="truncate">Dashboard</span>
                </a>

                <!-- Proyek -->
                <a href="{{ route('proyek') }}"
                   class="flex items-center py-2 transition-all duration-200 {{ request()->routeIs('proyek') ? 'text-blue-500 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <span class="w-1 h-6 rounded-full {{ request()->routeIs('proyek') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                    <svg class="w-4 h-4 flex-shrink-0 mx-2 {{ request()->routeIs('proyek') ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                    </svg>
                    <span class="truncate">Proyek</span>
                </a>

                <!-- Customer -->
                <a href="{{ url('/customer') }}"
                   class="flex items-center py-2 transition-all duration-200 {{ request()->is('customer') ? 'text-blue-500 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <span class="w-1 h-6 rounded-full {{ request()->is('customer') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                    <svg class="w-4 h-4 flex-shrink-0 mx-2 {{ request()->is('customer') ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 21V9a1 1 0 011-1h5V4a1 1 0 011-1h4a1 1 0 011 1v4h5a1 1 0 011 1v12"></path>
                        <path d="M9 21V12h6v9"></path>
                    </svg>
                    <span class="truncate">Customer</span>
                </a>

                <!-- Profile -->
                <a href="{{ route('profile') }}"
                   class="flex items-center py-2 transition-all duration-200 {{ request()->routeIs('profile') ? 'text-blue-500 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <span class="w-1 h-6 rounded-full {{ request()->routeIs('profile') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                    <svg class="w-4 h-4 flex-shrink-0 mx-2 {{ request()->routeIs('profile') ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="7" r="4"></circle>
                        <path d="M6 21v-1a4 4 0 014-4h4a4 4 0 014 4v1"></path>
                    </svg>
                    <span class="truncate">Profile</span>
                </a>
            </nav>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center py-2 px-3 rounded-lg hover:bg-red-50 text-red-600 font-medium transition duration-200">
                Logout
            </button>
        </form>
    </aside>

    <!-- Overlay (klik untuk menutup sidebar) -->
    <div x-show="open" x-transition.opacity @click="open = false"
         class="fixed inset-0 bg-black bg-opacity-40 z-30" x-cloak></div>

    <!-- Hamburger toggle -->
    <button @click="open = true" x-show="!open"
            class="fixed top-4 left-4 z-50 text-2xl text-gray-800" x-cloak>
        ☰
    </button>

</div>
