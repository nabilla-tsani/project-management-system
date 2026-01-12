<div class="min-h-screen w-full bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 py-3 px-11">
    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('reset-alert', () => {
            const alertType = @this.get('alert')?.type;
            const delay = alertType === 'error' ? 5000 : 1500;
            setTimeout(() => {
                @this.call('clearAlert');
            }, delay);
        });
    });
    </script>

    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-5">
            <div>
                <h1 class="text-2xl font-bold tracking-tight bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Catatan Saya
                </h1>
                <p class="text-xs text-gray-500 mt-1">
                    Lihat semua catatan proyek, bug, dan pekerjaan yang Anda buat di sini.
                </p>
            </div>

        </div>

        <!-- Search & Filter Bar -->
        <div class="flex flex-col sm:flex-row gap-3 mb-2">
            <!-- Search Bar -->
            <div class="relative w-full sm:flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" wire:model.live="search" placeholder="Cari proyek..."
                    class="w-full pl-9 pr-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-900 
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none 
                        placeholder-gray-400 text-xs transition shadow-sm" />
            </div>

            <!-- Filter Buttons -->
            <div class="flex gap-2 flex-wrap">
                <button
                    wire:click="setFilter('all')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold transition
                    {{ $filter === 'all'
                        ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md'
                        : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'
                    }}">
                    Semua Catatan
                </button>

                <button
                    wire:click="setFilter('pekerjaan')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold transition
                    {{ $filter === 'pekerjaan'
                        ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md'
                        : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'
                    }}">
                    Catatan Pekerjaan
                </button>

                <button
                    wire:click="setFilter('bug')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold transition
                    {{ $filter === 'bug'
                        ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md'
                        : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'
                    }}">
                    Catatan Bug
                </button>

                <button
                    wire:click="setFilter('tambahan')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold transition
                    {{ $filter === 'tambahan'
                        ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md'
                        : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'
                    }}">
                    Catatan Proyek
                </button>
            </div>

        </div>

        <!-- Notes List -->
        <div class="bg-white rounded-lg border border-gray-200">
            @php
                $allTasks = $tasks;
            @endphp

            @forelse($allTasks as $index => $t)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 transition {{ $index > 0 ? 'border-t border-gray-200' : '' }}">
                    <div class="flex items-start gap-3 flex-1">
                        <!-- Avatar/Icon -->
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm flex-shrink-0
                            @if($t->jenis === 'bug')
                                bg-red-500
                            @elseif($t->jenis === 'pekerjaan' && $t->fitur)
                                bg-green-700
                            @elseif($t->jenis === 'pekerjaan' && !$t->fitur)
                                bg-indigo-700
                            @else
                                bg-gray-500
                            @endif
                        ">
                            @if($t->jenis === 'bug')
                                <i class="fas fa-bug text-xs"></i>
                            @elseif($t->jenis === 'pekerjaan' && $t->fitur)
                                <i class="fas fa-check-circle text-xs"></i>
                            @elseif($t->jenis === 'pekerjaan' && !$t->fitur)
                                <i class="fas fa-clipboard text-xs"></i>
                            @else
                                <i class="fas fa-note-sticky text-xs"></i>
                            @endif
                        </div>


                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <!-- Title & Info -->
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <h3 class="text-xs font-semibold text-gray-900">
                                    {{ $t->proyek->nama_proyek ?? 'Catatan Umum' }}
                                </h3>
                                @if($t->fitur)
                                    <span class="text-gray-400 text-xs">•</span>
                                    <span class="text-xs text-gray-800">{{ $t->fitur->nama_fitur }}</span>
                                @endif
                            </div>

                            <!-- Description -->
                            <p class="text-xs text-gray-500 text-justify">
                                {{ $t->catatan }}
                            </p>
                        </div>
                    </div>

                    <!-- Status Badge & Date -->
                    <div class="flex-shrink-0 ml-3 text-right">

                        @if($t->jenis === 'bug')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full
                                bg-red-100 text-red-700 text-[10px] font-medium mb-1">
                                <i class="fas fa-bug text-[8px]"></i>
                                Bug
                            </span>

                        @elseif($t->jenis === 'pekerjaan' && $t->fitur)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full
                                bg-green-100 text-green-700 text-[10px] font-medium mb-1">
                                <i class="fas fa-check-circle text-[8px]"></i>
                                Pekerjaan
                            </span>

                        @elseif($t->jenis === 'pekerjaan' && !$t->fitur)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full
                                bg-indigo-100 text-indigo-700 text-[10px] font-medium mb-1">
                                <i class="fas fa-clipboard text-[8px]"></i>
                                Proyek
                            </span>

                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full
                                bg-gray-100 text-gray-700 text-[10px] font-medium mb-1">
                                <i class="fas fa-note-sticky text-[8px]"></i>
                                Umum
                            </span>
                        @endif

                        
                        <!-- Date -->
                        <div class="flex items-center gap-1 text-[10px] text-gray-500 justify-end mt-1">
                            <i class="far fa-calendar text-[9px]"></i>
                            <span>
                                {{ $t->tanggal_mulai ? $t->tanggal_mulai->format('d M Y') : '-' }}
                                - 
                                {{ $t->tanggal_selesai 
                                    ? $t->tanggal_selesai->format('d M Y') 
                                    : ($t->proyek?->tanggal_selesai 
                                        ? $t->proyek->tanggal_selesai->format('d M Y') 
                                        : '-')
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500 text-xs italic">Belum ada catatan yang dibuat</p>
                </div>
            @endforelse
        </div>
    </div>
</div>