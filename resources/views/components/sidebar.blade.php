<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Project Management</title>
    <!-- Use Tailwind CDN for standalone rendering; in production the app uses compiled assets -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    [x-cloak] { display: none !important; }

</style>

    </head>
<body>

<div x-data="{ open: false }" x-cloak class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside 
    x-cloak
    :class="open ? 'translate-x-0' : '-translate-x-60'" 
    class="fixed top-0 left-0 w-60 bg-white h-screen shadow-lg p-2 flex flex-col justify-between transform transition-transform duration-300 ease-in-out z-50 text-sm">

        <div>
            <!-- App Title -->
            <div class="mb-6 flex justify-start items-center px-2">
                <h1 class="text-xl font-medium tracking-tight pt-4">
                    <span class="text-[#ac7bff]">Project</span>
                    <span class="text-[#77b6ff]">Management</span>
                </h1>
            </div>


            <!-- Navigation -->
            <nav class="flex flex-col space-y-1 ml-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center py-2 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'text-blue-500 font-semibold' : 'text-black hover:bg-gray-100 hover:text-gray-900' }}">
                    <span class="w-1 h-6 rounded-full {{ request()->routeIs('dashboard') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                    <svg class="w-4 h-4 flex-shrink-0 mx-2 {{ request()->routeIs('dashboard') ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"></path>
                    </svg>
                    <span class="truncate">Dashboard</span>
                </a>

                <!-- Proyek -->
                <div 
                    x-data="{
                        openProyek: JSON.parse(localStorage.getItem('openProyek')) ?? {{ request()->routeIs('proyek.detail') ? 'true' : 'false' }},
                    }"
                    x-init="$watch('openProyek', val => localStorage.setItem('openProyek', JSON.stringify(val)))"
                    class="flex flex-col"
                >
                    <div class="flex items-center justify-between rounded transition-all duration-200
                                hover:text-blue-600 text-gray-700">

                        <!-- Area klik (ikon + teks) → menuju halaman utama proyek -->
                        <a href="{{ route('proyek') }}"
                        class="flex items-center flex-1 py-2 transition-all duration-200
                                {{ request()->routeIs('proyek') ? 'text-blue-500 font-semibold' : 'text-black hover:bg-gray-100 hover:text-gray-900' }}">
                            <span class="w-1 h-6 rounded-full {{ request()->routeIs('proyek') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                            <svg class="w-4 h-4 flex-shrink-0 mx-2 {{ request()->routeIs('proyek') ? 'text-blue-500' : 'text-gray-400' }}"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                                <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                            </svg>
                            <span class="truncate">Proyek</span>
                        </a>

                        <!-- Area panah (tidak ikut klik ke halaman proyek) -->
                        <button @click.stop="openProyek = !openProyek"
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded hover:bg-gray-200 transition">
                            <svg :class="{ 'rotate-180': openProyek }"
                                class="w-4 h-4 text-black transition-transform duration-200"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Dropdown daftar proyek -->
                    <ul x-show="openProyek" x-transition 
                        class="ml-7 space-y-1 text-sm text-gray-600 max-h-60 overflow-y-auto">
                        @forelse ($proyeks as $p)
                            <li>
                                <a href="{{ route('proyek.detail', $p->id) }}"
                                title="{{ $p->nama_proyek }}"
                                class="block py-1 px-2 rounded truncate
                                        {{ request()->routeIs('proyek.detail') && request()->route('proyekId') == $p->id
                                            ? 'text-blue-600 font-medium'
                                            : 'hover:bg-gray-100 text-gray-600' }}">
                                    {{ $p->nama_proyek }}
                                </a>
                            </li>
                        @empty
                            <li class="italic text-gray-400 text-xs px-2">Tidak ada proyek</li>
                        @endforelse
                    </ul>

                </div>

                <!-- Tasks -->
                <a href="{{ url('/tasks') }}"
                   class="flex items-center py-2 transition-all duration-200 {{ request()->is('tasks') ? 'text-blue-500 font-semibold' : 'text-black hover:bg-gray-100 hover:text-gray-900' }}">
                    <span class="w-1 h-6 rounded-full {{ request()->is('tasks') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                    <svg class="w-4 h-4 flex-shrink-0 mx-2  {{ request()->is('tasks') ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"" 
                        xmlns="http://www.w3.org/2000/svg" fill="none" 
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                            d="M9 6h11m0 6H9m0 6h11M4 6l1.5 1.5L8 5m-4 7l1.5 1.5L8 11m-4 7l1.5 1.5L8 17" />
                    </svg>
                    <span class="truncate">Notes</span>
                </a>

                <!-- Customer -->
                <a href="{{ url('/customer') }}"
                   class="flex items-center py-2 transition-all duration-200 {{ request()->is('customer') ? 'text-blue-500 font-semibold' : 'text-black hover:bg-gray-100 hover:text-gray-900' }}">
                    <span class="w-1 h-6 rounded-full {{ request()->is('customer') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                    <svg class="w-4 h-4 flex-shrink-0 mx-2 {{ request()->is('customer') ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 21V9a1 1 0 011-1h5V4a1 1 0 011-1h4a1 1 0 011 1v4h5a1 1 0 011 1v12"></path>
                        <path d="M9 21V12h6v9"></path>
                    </svg>
                    <span class="truncate">Customer</span>
                </a>

                <!-- Profile -->
                <a href="{{ route('profile') }}"
                   class="flex items-center py-2 transition-all duration-200 {{ request()->routeIs('profile') ? 'text-blue-500 font-semibold' : 'text-black hover:bg-gray-100 hover:text-gray-900' }}">
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
                class="flex items-center py-2 px-5 rounded-lg hover:bg-red-50 text-red-600 font-medium transition duration-200 w-full">
                <!-- Icon Logout -->
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                </svg>
                Logout
            </button>
        </form>
    </aside>

    <!-- Overlay (klik untuk menutup sidebar) -->
    <div x-show="open" x-transition.opacity @click="open = false"
         class="fixed inset-0 bg-black bg-opacity-40 z-30" x-cloak></div>

    <!-- Hamburger toggle -->
    <button @click="open = true" x-show="!open"
    class="fixed left-4 mt-3 z-50 text-2xl text-gray-800" x-cloak>
    ☰
</button>

</div>
</body>
