<div class="pt-3 p-6 space-y-2 bg-white">

    {{-- Header Judul --}}
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="pl-2 text-md font-medium text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-folder-open text-[#9c62ff] text-[15px]"></i>
            {{ $proyek->nama_proyek }}
        </h1>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-gray-200">
        <nav class="flex flex-wrap gap-1 md:space-x-3">
            @foreach ([
                'dashboard' => ['label' => 'Overview', 'icon' => 'fa-solid fa-chart-line'],
                'informasi' => ['label' => 'Info', 'icon' => 'fa-solid fa-circle-info'],
                'team' => ['label' => 'Members', 'icon' => 'fa-solid fa-users'],
                'fitur' => ['label' => 'Features', 'icon' => 'fa-solid fa-layer-group'],
                'tasks' => ['label' => 'Tasks', 'icon' => 'fa-solid fa-list-check'],
                'timeline' => ['label' => 'Calendar', 'icon' => 'fa-solid fa-calendar'],
                'file' => ['label' => 'Files', 'icon' => 'fa-solid fa-folder']
            ] as $key => $tabData)
                <button wire:click="setTab('{{ $key }}')"
                    class="flex items-center gap-1.5 px-2.5 py-1.5 text-[13px] font-medium border-b-2 transition-all
                        {{ $tab === $key 
                            ? 'border-[#9c62ff] text-[#9c62ff]' 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="{{ $tabData['icon'] }} 
                        {{ $tab === $key ? 'text-[#9c62ff]' : 'text-gray-400' }} text-[13px]"></i>
                    {{ $tabData['label'] }}
                </button>
            @endforeach

            {{-- Tab khusus manajer proyek --}}
            @if($isManajerProyek)
                @foreach ([
                    'invoice' => ['label' => 'Invoices', 'icon' => 'fa-solid fa-file-invoice-dollar'],
                    'kwitansi' => ['label' => 'Receipts', 'icon' => 'fa-solid fa-file-invoice']
                ] as $key => $tabData)
                    <button wire:click="setTab('{{ $key }}')"
                        class="flex items-center gap-1.5 px-2.5 py-1.5 text-[13px] font-medium border-b-2 transition-all
                            {{ $tab === $key 
                                ? 'border-[#9c62ff] text-[#9c62ff]' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <i class="{{ $tabData['icon'] }} 
                            {{ $tab === $key ? 'text-[#9c62ff]' : 'text-gray-400' }} text-[13px]"></i>
                        {{ $tabData['label'] }}
                    </button>
                @endforeach
            @endif
        </nav>
    </div>

    {{-- Tab Content --}}
    <div class="mt-4 h-[calc(100vh-118apx)] overflow-y-auto pr-1"
        style="
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        ">
        @if($tab === 'dashboard')
            @livewire('dashboard-proyek', ['proyekId' => $proyek->id], key('dashboard-'.$proyek->id))
        @endif

        @if($tab === 'informasi')
            @livewire('detail-proyek', ['id' => $proyek->id], key('detail-'.$proyek->id))
        @endif

        @if($tab === 'team')
            @livewire('all-proyek-user', ['proyekId' => $proyek->id], key('team-'.$proyek->id))
        @endif

        @if($tab === 'fitur')
            @livewire('all-proyek-fitur', ['proyekId' => $proyek->id], key('fitur-'.$proyek->id))
        @endif

        @if($tab === 'tasks')
            @livewire('all-proyek-tasks', ['proyekId' => $proyek->id], key('tasks-'.$proyek->id))
        @endif

        @if($tab === 'timeline')
            @livewire('timeline-proyek', ['proyekId' => $proyek->id], key('timeline-'.$proyek->id))
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

</div>
