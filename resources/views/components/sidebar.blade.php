<div x-data="{ open: false }" x-cloak class="flex h-screen bg-gray-50">
    {{-- x-data & x-cloak untuk Alpine.js state & cegah FOUC --}}
    
    <!-- Sidebar -->
    <aside 
        x-bind:class="open ? 'translate-x-0' : '-translate-x-full'" 
        class="fixed md:static md:translate-x-0 top-0 left-0 w-52 md:w-56 bg-white h-screen shadow-lg p-3 flex flex-col justify-between transform transition-transform duration-300 ease-in-out z-40 text-sm">
        
        <div>
            <!-- App Title -->
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-800 tracking-tight">Project Management</h1>
                <!-- Close button hanya tampil di mobile -->
                <button type="button" class="md:hidden text-gray-500 hover:text-gray-800" @click="open = false">
                    ✖
                </button>
            </div>

            <!-- Navigation -->
            <nav class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-3 py-2 rounded-lg transition-all
                          {{ request()->routeIs('dashboard')
                              ? 'bg-gray-100 text-gray-900 font-semibold border-l-4 border-blue-500'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"></path>
                    </svg>
                    Dashboard
                </a>

                <!-- Proyek -->
                <a href="{{ route('proyek') }}"
                   class="flex items-center px-3 py-2 rounded-lg transition-all
                          {{ request()->routeIs('proyek')
                              ? 'bg-gray-100 text-gray-900 font-semibold border-l-4 border-blue-500'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                    </svg>
                    Proyek
                </a>

                <!-- Customer -->
                <a href="{{ url('/customer') }}"
                   class="flex items-center px-3 py-2 rounded-lg transition-all
                          {{ request()->is('customer')
                              ? 'bg-gray-100 text-gray-900 font-semibold border-l-4 border-blue-500'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 21V9a1 1 0 011-1h5V4a1 1 0 011-1h4a1 1 0 011 1v4h5a1 1 0 011 1v12"></path>
                        <path d="M9 21V12h6v9"></path>
                    </svg>
                    Customer
                </a>

                <!-- Profile -->
                <a href="{{ route('profile') }}"
                   class="flex items-center px-3 py-2 rounded-lg transition-all
                          {{ request()->routeIs('profile')
                              ? 'bg-gray-100 text-gray-900 font-semibold border-l-4 border-blue-500'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="7" r="4"></circle>
                        <path d="M6 21v-1a4 4 0 014-4h4a4 4 0 014 4v1"></path>
                    </svg>
                    Profile
                </a>
            </nav>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center px-3 py-2 rounded-lg hover:bg-red-50 text-red-600 font-medium transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"></path>
                </svg>
                Logout
            </button>
        </form>
    </aside>

    <!-- Overlay (mobile) -->
    <div x-show="open" x-transition.opacity @click="open = false"
         class="fixed inset-0 bg-black bg-opacity-40 md:hidden z-30"></div>
</div>
