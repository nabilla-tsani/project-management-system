<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen Proyek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-item-active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .nav-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }
        
        /* Hilangkan scrollbar tapi tetap bisa scroll */
        .scrollbar-custom {
            scrollbar-width: none;          /* Firefox */
            -ms-overflow-style: none;       /* IE & Edge */
        }

        .scrollbar-custom::-webkit-scrollbar {
            display: none;                  /* Chrome, Safari */
        }
        
        .scrollbar-custom::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 10px;
        }
        
        .scrollbar-custom::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        
        .scrollbar-custom::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .icon-gradient {
            fill: url(#iconGradient);
        }
    </style>
</head>
<body class="bg-gray-50">

<svg width="0" height="0" style="position: absolute;">
    <defs>
        <linearGradient id="iconGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
        </linearGradient>
    </defs>
</svg>

<div x-data="{ open: false }" class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside 
        x-cloak
        :class="open ? 'translate-x-0' : '-translate-x-64'" 
        class="fixed top-0 left-0 w-64 bg-white h-screen shadow-xl py-5 px-3 flex flex-col justify-between transform transition-transform duration-300 ease-in-out z-50">

        <div class="flex-1 overflow-y-auto scrollbar-custom">
            <!-- App Title -->
            <div class="mb-3 flex flex-col items-start px-2">
                <div class="flex flex-col mb-2">
                    <h1 class="text-lg font-semibold leading-tight
    bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600
    bg-clip-text text-transparent">
    Manajemen Proyek
</h1>

                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex flex-col space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="nav-item flex items-center py-2.5 px-3 rounded-xl transition-all duration-200 text-sm {{ request()->routeIs('dashboard') ? 'nav-item-active' : 'text-gray-600 hover:text-purple-600' }}">
                    <svg class="w-4 h-4 flex-shrink-0 mr-2.5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-purple-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"></path>
                    </svg>
                    <span>Dasbor</span>
                </a>

                <!-- Proyek -->
                <div 
                    x-data="{
                        openProyek: {{ request()->routeIs('proyek.detail') ? 'true' : 'false' }},
                    }"
                    class="flex flex-col"
                >
                    <div class="flex items-center justify-between">
                        <a href="{{ route('proyek') }}"
                           class="nav-item flex items-center flex-1 py-2.5 px-3 rounded-xl transition-all duration-200 text-sm {{ request()->routeIs('proyek') ? 'nav-item-active' : 'text-gray-600 hover:text-purple-600' }}">
                            <svg class="w-4 h-4 flex-shrink-0 mr-2.5 {{ request()->routeIs('proyek') ? 'text-white' : 'text-purple-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                                <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                            </svg>
                            <span>Proyek</span>
                        </a>

                        <button @click.stop="openProyek = !openProyek"
                                class="flex items-center justify-center w-7 h-7 mr-2 rounded-lg hover:bg-purple-50 transition text-purple-500">
                            <svg :class="{ 'rotate-180': openProyek }"
                                 class="w-3.5 h-3.5 transition-transform duration-200"
                                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Dropdown daftar proyek -->
                    <ul x-show="openProyek" x-transition 
                        class="ml-8 mt-1 space-y-0.5 text-xs max-h-48 overflow-y-auto scrollbar-custom">
                        @forelse ($proyeks as $p)
                            <li>
                                <a href="{{ route('proyek.detail', $p->id) }}"
                                   title="{{ $p->nama_proyek }}"
                                   class="block py-1.5 px-2.5 rounded-lg truncate transition
                                          {{ request()->routeIs('proyek.detail') && request()->route('proyekId') == $p->id 
                                              ? 'bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700' 
                                              : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600' }}">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full mr-2 {{ request()->routeIs('proyek.detail') && request()->route('proyekId') == $p->id ? 'bg-purple-500' : 'bg-gray-300' }}"></span>
                                    {{ $p->nama_proyek }}
                                </a>
                            </li>
                        @empty
                            <li class="italic text-gray-400 text-xs px-2.5 py-1.5">Tidak ada proyek</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Notes -->
                <a href="{{ url('/tasks') }}"
                   class="nav-item flex items-center py-2.5 px-3 rounded-xl transition-all duration-200 text-sm {{ request()->is('tasks') ? 'nav-item-active' : 'text-gray-600 hover:text-purple-600' }}">
                    <svg class="w-4 h-4 flex-shrink-0 mr-2.5 {{ request()->is('tasks') ? 'text-white' : 'text-purple-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6h11m0 6H9m0 6h11M4 6l1.5 1.5L8 5m-4 7l1.5 1.5L8 11m-4 7l1.5 1.5L8 17" />
                    </svg>
                    <span>Catatan</span>
                </a>

                <!-- Customer -->
                <a href="{{ url('/customer') }}"
                   class="nav-item flex items-center py-2.5 px-3 rounded-xl transition-all duration-200 text-sm {{ request()->is('customer') ? 'nav-item-active' : 'text-gray-600 hover:text-purple-600' }}">
                    <svg class="w-4 h-4 flex-shrink-0 mr-2.5 {{ request()->is('customer') ? 'text-white' : 'text-purple-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 21V9a1 1 0 011-1h5V4a1 1 0 011-1h4a1 1 0 011 1v4h5a1 1 0 011 1v12"></path>
                        <path d="M9 21V12h6v9"></path>
                    </svg>
                    <span>Klien</span>
                </a>

                <!-- Profile -->
                <a href="{{ route('profile') }}"
                   class="nav-item flex items-center py-2.5 px-3 rounded-xl transition-all duration-200 text-sm {{ request()->routeIs('profile') ? 'nav-item-active' : 'text-gray-600 hover:text-purple-600' }}">
                    <svg class="w-4 h-4 flex-shrink-0 mr-2.5 {{ request()->routeIs('profile') ? 'text-white' : 'text-purple-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="7" r="4"></circle>
                        <path d="M6 21v-1a4 4 0 014-4h4a4 4 0 014 4v1"></path>
                    </svg>
                    <span>Profil</span>
                </a>
            </nav>
        </div>

        <!-- Logout -->
        <div class="mt-2 pt-2 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="text-sm flex items-center py-3 px-4 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 hover:from-red-100 hover:to-pink-100 text-red-600 transition duration-200 w-full shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay -->
    <div x-show="open" 
         x-transition.opacity 
         @click="open = false"
         class="fixed inset-0 bg-black/50  z-30" 
         x-cloak></div>

    <!-- Hamburger toggle -->
    <button @click="open = true" 
            x-show="!open"
            class="fixed left-1 top-4 z-50 w-8 h-8 rounded-xl bg-white shadow-lg flex items-center justify-center hover:shadow-xl transition-all duration-200 border border-purple-200" 
            x-cloak>
        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
</div>

</body>
</html>