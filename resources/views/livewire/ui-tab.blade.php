<div class="pt-3 px-11 space-y-2 bg-white flex flex-col h-full">

    {{-- Sticky Header (Judul + Tabs) --}}
    <div class="sticky top-0 z-20 bg-white">

        {{-- Header Judul --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <h1 class="pl-2 text-md font-medium text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-folder-open text-[#9c62ff] text-[15px]"></i>
                {{ $proyek->nama_proyek }}
            </h1>
        </div>

        {{-- Navigasi Tab --}}
        <div class="border-b border-gray-200">
            <nav class="flex flex-wrap gap-1 md:space-x-3">
                @php
                    $mainTabs = [
                        'dashboard' => ['label' => 'Ringkasan', 'icon' => 'fa-solid fa-chart-line'],
                        'informasi' => ['label' => 'Informasi', 'icon' => 'fa-solid fa-circle-info'],
                        'team'      => ['label' => 'Anggota', 'icon' => 'fa-solid fa-users'],
                        'fitur'     => ['label' => 'Fitur', 'icon' => 'fa-solid fa-layer-group'],
                        'tasks'     => ['label' => 'Catatan', 'icon' => 'fa-solid fa-list-check'],
                        'timeline'  => ['label' => 'Kalender', 'icon' => 'fa-solid fa-calendar'],
                        'file'      => ['label' => 'Berkas', 'icon' => 'fa-solid fa-folder']
                    ];
                @endphp

                @foreach ($mainTabs as $key => $tabData)
                    <button 
                        wire:key="btn-tab-{{ $key }}"
                        wire:click.prevent="setTab('{{ $key }}')"
                        wire:loading.attr="disabled"
                        class="flex items-center gap-1.5 px-2.5 py-1.5 text-[13px] font-medium border-b-2 transition-all
                            {{ $tab === $key 
                                ? 'border-[#9c62ff] text-[#9c62ff]' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <i class="{{ $tabData['icon'] }} {{ $tab === $key ? 'text-[#9c62ff]' : 'text-gray-400' }} text-[13px]"></i>
                        {{ $tabData['label'] }}
                    </button>
                @endforeach

                {{-- Tab khusus Manajer Proyek --}}
                @if($isManajerProyek)
                    @foreach ([
                        'invoice'  => ['label' => 'Tagihan', 'icon' => 'fa-solid fa-file-invoice-dollar'],
                        'kwitansi' => ['label' => 'Kwitansi', 'icon' => 'fa-solid fa-file-invoice']
                    ] as $key => $tabData)
                        <button 
                            wire:key="btn-tab-{{ $key }}"
                            wire:click.prevent="setTab('{{ $key }}')"
                            wire:loading.attr="disabled"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-[13px] font-medium border-b-2 transition-all
                                {{ $tab === $key 
                                    ? 'border-[#9c62ff] text-[#9c62ff]' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i class="{{ $tabData['icon'] }} {{ $tab === $key ? 'text-[#9c62ff]' : 'text-gray-400' }} text-[13px]"></i>
                            {{ $tabData['label'] }}
                        </button>
                    @endforeach
                @endif
            </nav>
        </div>
    </div>

    {{-- Konten Scroll --}}
    <div class="flex-1 overflow-y-auto pr-1" style="-ms-overflow-style: none !important; scrollbar-width: none !important;">
        
        {{-- KUNCI UTAMA: Wrapper dengan wire:key dinamis --}}
        <div wire:key="content-wrapper-{{ $tab }}-{{ $proyek->id }}">
            
            @if($tab === 'dashboard')
                @livewire('dashboard-proyek', ['proyekId' => $proyek->id], key('comp-dash-'.$proyek->id))
            @elseif($tab === 'informasi')
                @livewire('detail-proyek', ['id' => $proyek->id], key('comp-info-'.$proyek->id))
            @elseif($tab === 'team')
                @livewire('all-proyek-user', ['proyekId' => $proyek->id], key('comp-team-'.$proyek->id))
            @elseif($tab === 'fitur')
                @livewire('all-proyek-fitur', ['proyekId' => $proyek->id], key('comp-fitur-'.$proyek->id))
            @elseif($tab === 'tasks')
                @livewire('all-proyek-tasks', ['proyekId' => $proyek->id], key('comp-tasks-'.$proyek->id))
            @elseif($tab === 'timeline')
                @livewire('timeline-proyek', ['proyekId' => $proyek->id], key('comp-time-'.$proyek->id))
            @elseif($tab === 'file')
                @livewire('all-proyek-file', ['proyekId' => $proyek->id], key('comp-file-'.$proyek->id))
            @elseif($isManajerProyek && $tab === 'invoice')
                @livewire('all-proyek-invoice', ['proyekId' => $proyek->id], key('comp-inv-'.$proyek->id))
            @elseif($isManajerProyek && $tab === 'kwitansi')
                @livewire('all-proyek-kwitansi', ['proyekId' => $proyek->id], key('comp-kwi-'.$proyek->id))
            @endif

        </div>
    </div>

    {{-- Chatbot --}}
    <div class="font-sans" wire:ignore>
        @livewire('chatbot', [], key('chatbot-'.$proyek->id))
    </div>

</div>