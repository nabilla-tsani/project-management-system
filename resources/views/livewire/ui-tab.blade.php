<div class="pt-3 p-6 space-y-2">

    {{-- Header Judul + Tombol PDF --}}
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="pl-2 text-xl font-medium text-gray-800">{{ $proyek->nama_proyek }}</h1>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-gray-200">
        <nav class="flex space-x-4">
            @foreach ([
                'informasi' => 'Informasi',
                'dashboard' => 'Dashboard',
                'team' => 'Team',
                'fitur' => 'Fitur',
                'tasks' => 'Tasks',
                'file' => 'File',
                'timeline' => 'Timeline' {{-- ✅ Tambahan --}}
            ] as $key => $label)
                <button wire:click="setTab('{{ $key }}')"
                        class="px-3 py-2 text-sm font-medium border-b-2 focus:outline-none
                            {{ $tab === $key ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $label }}
                </button>
            @endforeach

            {{-- Hanya tampil jika role = manajer proyek --}}
            @if($isManajerProyek)
                @foreach (['invoice' => 'Invoice', 'kwitansi' => 'Kwitansi'] as $key => $label)
                    <button wire:click="setTab('{{ $key }}')"
                            class="px-3 py-2 text-sm font-medium border-b-2 focus:outline-none
                                {{ $tab === $key ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        {{ $label }}
                    </button>
                @endforeach
            @endif
        </nav>
    </div>

    {{-- Tab Content --}}
    <div class="mt-4">
        @if($tab === 'informasi')
            @livewire('detail-proyek', ['id' => $proyek->id], key('detail-'.$proyek->id))
        @endif

        @if($tab === 'dashboard')
            @livewire('dashboard-proyek', ['proyekId' => $proyek->id], key('dashboard-'.$proyek->id))
        @endif

        @if($tab === 'team')
            @livewire('all-proyek-user', ['proyekId' => $proyek->id], key('team-'.$proyek->id))
        @endif

         @if($tab === 'timeline')
            @livewire('timeline-proyek', ['proyekId' => $proyek->id], key('timeline-'.$proyek->id))
        @endif

        @if($tab === 'fitur')
            @livewire('all-proyek-fitur', ['proyekId' => $proyek->id], key('fitur-'.$proyek->id))
        @endif

        @if($tab === 'tasks')
            @livewire('all-proyek-tasks', ['proyekId' => $proyek->id], key('tasks-'.$proyek->id))
        @endif

        @if($tab === 'file')
            @livewire('all-proyek-file', ['proyekId' => $proyek->id], key('file-'.$proyek->id))
        @endif

        @if($isManajerProyek)
            @if($tab === 'invoice')
                @livewire('all-proyek-invoice', ['proyekId' => $proyek->id], key('invoice-'.$proyek->id))
            @endif

            @if($tab === 'kwitansi')
                @livewire('all-proyek-kwitansi', ['proyekId' => $proyek->id], key('kwitansi-'.$proyek->id))
            @endif
        @endif
    </div>

    @livewire('chatbot', [], key('chatbot-'.$proyek->id))

    {{-- Footer Tombol Kembali --}}
    <div class="flex justify-start pt-4">
        <a href="{{ route('proyek') }}"
           class="px-4 py-2 bg-blue-600 text-white text-sm rounded-xl shadow hover:bg-blue-700 transition">
            ← Kembali
        </a>
    </div>
</div>
