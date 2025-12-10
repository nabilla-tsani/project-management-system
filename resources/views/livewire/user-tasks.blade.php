<div class="min-h-screen w-full bg-white text-gray-900 pt-0 p-8 relative overflow-hidden">
    <!-- Efek glow ungu  -->
    <div
        style="
        position: fixed;
        bottom: -350px;
        left: -230px;
        width: 500px;
        height: 400px;
        background: radial-gradient(circle, #5ca9ff 100%);
        filter: blur(120px);
        opacity: 0.7;
        z-index: 0;
        pointer-events: none;
        "
    ></div>

    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('reset-alert', () => {
            // Ambil nilai type alert dari komponen Livewire
            const alertType = @this.get('alert')?.type;

            // Tentukan durasi berdasarkan tipe
            const delay = alertType === 'error' ? 5000 : 1500;

            setTimeout(() => {
                @this.call('clearAlert');
            }, delay);
        });
    });
    </script>


    <div class="max-w-7xl mx-auto">
        <!-- Title Halaman -->
        <div class="flex items-center justify-between mb-4 pt-1.5 pl-1">
           <h1 class="text-3xl font-medium tracking-tight">
                    <span class="text-[#77b6ff]">My</span>
                    <span class="text-[#ac7bff]">Tasks</span>
            </h1>
        </div>


    <!-- Search + Filter -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4">

        <!-- Search Bar -->
        <div class="relative w-full sm:w-1/2">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fas fa-search"></i>
            </span>
            <input 
                type="text"
                wire:model.live="search"
                placeholder="Find tasks by project..."
                class="w-full pl-10 pr-3 py-2 rounded-3xl bg-white border border-gray-300 
                    text-gray-900 placeholder-gray-400 text-xs
                    focus:ring-1 focus:ring-[#5ca9ff] focus:border-transparent 
                    outline-none transition"
            />
        </div>

        <!-- Filter -->
        <div class="w-full sm:w-48">
            <select 
                wire:model.live="filter"
                class="w-full py-2 px-3 rounded-3xl bg-white border border-gray-300 text-gray-900 text-xs 
                    focus:ring-1 focus:ring-[#5ca9ff] focus:border-transparent outline-none transition"
            >
                <option value="nearest">Closest Deadline</option>
                <option value="farthest">Latest Deadline</option>
                <option value="newest">Recently Created</option>
                <option value="oldest">Oldest Created</option>

            </select>
        </div>

    </div>

</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">

    {{-- ======================== --}}
    {{-- KOLOM KIRI: proyek_fitur_id null --}}
    {{-- ======================== --}}
    <div>
        @php
            $general = $tasks->where('proyek_fitur_id', null);
        @endphp

        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-note-sticky"></i> General Tasks
        </h3>

        @forelse($general as $t)
            <div class="border rounded-lg p-3 bg-white shadow-sm mb-3">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-gray-200 text-gray-600">{{ $t->proyek->nama_proyek ?? '-' }}</span>
                    <span class="text-[10px] text-gray-400 italic">
                        {{ $t->tanggal_mulai ? $t->tanggal_mulai->format('d M Y') : '-' }}
                        –
                        {{ $t->tanggal_selesai
                            ? $t->tanggal_selesai->format('d M Y')
                            : ($t->proyek?->tanggal_selesai
                                ? $t->proyek->tanggal_selesai->format('d M Y')
                                : '-')
                        }}
                    </span>

                </div>

                <p class="text-xs text-gray-700 mt-3 text-justify">{{ $t->catatan }}</p>
            </div>
        @empty
            <p class="text-xs text-gray-400 italic">
            You haven't created any notes yet.
            </p>
        @endforelse
    </div>


    {{-- ======================== --}}
    {{-- KOLOM TENGAH: pekerjaan --}}
    {{-- ======================== --}}
    <div>
        @php
            $pekerjaan = $tasks
                ->where('proyek_fitur_id', '!=', null)
                ->where('jenis', 'pekerjaan');
        @endphp

        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-check-circle"></i> Tasks
        </h3>

        @forelse($pekerjaan as $t)
            <div class="border rounded-lg p-3 bg-white shadow-sm mb-3">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-blue-50 text-[#5ca9ff]">{{ $t->proyek->nama_proyek ?? '-' }}</span>
                    <span class="text-[10px] text-gray-400 italic">
                        {{ $t->tanggal_mulai ? $t->tanggal_mulai->format('d M Y') : '-' }}
                        –
                        {{ $t->tanggal_selesai
                            ? $t->tanggal_selesai->format('d M Y')
                            : ($t->proyek?->tanggal_selesai
                                ? $t->proyek->tanggal_selesai->format('d M Y')
                                : '-')
                        }}
                    </span>

                </div>
                <div class="mt-2 text-xs text-gray-400">
                    <div>Feature: <span class="font-semibold">{{ $t->fitur->nama_fitur ?? '-' }}</span></div>
                </div>

                <p class="text-xs text-gray-700 mt-1 text-justify">{{ $t->catatan }}</p>
            </div>
        @empty
            <p class="text-xs text-gray-400 italic">            
                You haven't created any notes yet.
            </p>
        @endforelse
    </div>


    {{-- ======================== --}}
    {{-- KOLOM KANAN: bug --}}
    {{-- ======================== --}}
    <div>
        @php
            $bug = $tasks
                ->where('proyek_fitur_id', '!=', null)
                ->where('jenis', 'bug');
        @endphp

        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-bug"></i> Bugs
        </h3>

        @forelse($bug as $t)
            <div class="border rounded-lg p-3 bg-white shadow-sm mb-3">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-purple-100 text-[#9c62ff]">{{ $t->proyek->nama_proyek ?? '-' }}</span>
                    <span class="text-[10px] text-gray-400 italic">
                        {{ $t->tanggal_mulai ? $t->tanggal_mulai->format('d M Y') : '-' }}
                        –
                        {{ $t->tanggal_selesai
                            ? $t->tanggal_selesai->format('d M Y')
                            : ($t->proyek?->tanggal_selesai
                                ? $t->proyek->tanggal_selesai->format('d M Y')
                                : '-')
                        }}
                    </span>
                </div>

                <div class="mt-2 text-xs text-gray-400">
                    <div>Feature: <span class="font-semibold">{{ $t->fitur->nama_fitur ?? '-' }}</span></div>
                </div>

                <p class="text-xs text-gray-700 mt-1 text-justify">{{ $t->catatan }}</p>
            </div>
        @empty
            <p class="text-xs text-gray-400 italic">            
                You haven't created any notes yet.
            </p>
        @endforelse
    </div>

</div>



</div>
</body>
