<div class="pt-0 p-2 space-y-2">

    {{-- Header: Judul, Cari, Tabs, Tombol Tambah --}}
    <div class="flex items-center justify-between mb-3 gap-3">

        {{-- Kiri: Judul --}}
        <div class="flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-calendar text-blue-500 text-xl"></i>
            <h2 class="text-sm font-semibold text-gray-700">
                Kalender Proyek
            </h2>
        </div>

        {{-- Tengah: Cari + Tabs --}}
        <div class="flex items-center gap-3 flex-1">

            {{-- Cari --}}
            <input 
                type="text"
                wire:model.live="search" 
                placeholder="Cari catatan..."
                class="text-xs px-3 py-1.5 border border-gray-300 rounded-full
                    focus:ring-1 focus:ring-[#5ca9ff] focus:border-[#5ca9ff]
                    outline-none w-64"
            />
        </div>
    </div>

    <div class="border h-[78vh] flex flex-col overflow-hidden rounded-xl shadow-sm bg-white">

        <div class="flex-1 overflow-auto">

            <table class="min-w-max border-collapse w-full">

                {{-- HEADER --}}
                <thead>

                    {{-- ========== BARIS BULAN ========== --}}
                    <tr class="sticky top-0 z-20 bg-gradient-to-r from-blue-50 to-indigo-50">
                        @php
                            $bulanSekarang = null;
                            $colspan = 0;
                        @endphp

                        @foreach($allDays as $day)
                            @php
                                $dateObj = \Carbon\Carbon::parse($day);
                                $bulanName = $dateObj->translatedFormat('F Y');
                            @endphp

                            @if ($bulanSekarang === null)
                                @php
                                    $bulanSekarang = $bulanName;
                                    $colspan = 1;
                                @endphp
                            @elseif ($bulanName == $bulanSekarang)
                                @php $colspan++; @endphp
                            @else
                                <th class="border-r border-b border-blue-200 px-2 py-1.5 text-[10px] text-center font-bold text-blue-700"
                                    colspan="{{ $colspan }}">
                                    {{ $bulanSekarang }}
                                </th>
                                @php
                                    $bulanSekarang = $bulanName;
                                    $colspan = 1;
                                @endphp
                            @endif
                        @endforeach

                        <th class="border-r border-b border-blue-200 px-2 py-1.5 text-[10px] text-center font-bold text-blue-700" colspan="{{ $colspan }}">
                            {{ $bulanSekarang }}
                        </th>
                    </tr>

                    {{-- ========== BARIS TANGGAL ========== --}}
                    <tr class="sticky top-[28px] z-20 bg-white">
                        @foreach($allDays as $day)
                            <th class="border border-gray-200 px-1 py-1 text-[9px] min-w-[35px] text-center text-gray-600 font-semibold">
                                {{ \Carbon\Carbon::parse($day)->format('d') }}
                            </th>
                        @endforeach
                    </tr>

                </thead>

                {{-- ====================== --}}
                {{-- BODY --}}
                {{-- ====================== --}}
                <tbody>

                {{-- ========== BARIS PROYEK ========== --}}
                @php
                    $pMulai = \Carbon\Carbon::parse($proyek->tanggal_mulai);
                    $pSelesai = \Carbon\Carbon::parse($proyek->tanggal_selesai);

                    $pStart = $allDays->search(fn($d) => \Carbon\Carbon::parse($d)->gte($pMulai));
                    $pEnd   = $allDays->search(fn($d) => \Carbon\Carbon::parse($d)->gte($pSelesai));

                    if ($pStart === false) { $pStart = 0; }
                    if ($pEnd === false) { $pEnd = max(0, $allDays->count() - 1); }

                    $pSpan  = max(1, $pEnd - $pStart + 1);
                @endphp

                <tr>
                    {{-- kolom kosong sebelum proyek --}}
                    @for ($i = 0; $i < $pStart; $i++)
                        <td class="border border-gray-100 min-w-[35px] p-0 bg-gray-50/30"></td>
                    @endfor

                    {{-- bar proyek --}}
                    <td class="border border-gray-200 p-0.5 relative bg-white" colspan="{{ $pSpan }}">
                        <div class="relative overflow-hidden rounded-lg shadow-sm h-8 group hover:shadow-md transition-all duration-200">
                            {{-- Solid Background --}}
                            <div class="absolute inset-0 bg-blue-500"></div>
                            
                            {{-- Content --}}
                            <div class="absolute inset-0 px-2 py-1 flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                                    <span class="text-[10px] font-bold text-white drop-shadow">
                                        {{ $proyek->nama_proyek }}
                                    </span>
                                </div>
                                <span class="text-[8px] text-white/90 font-medium">
                                    {{ $pMulai->format('d M') }} - {{ $pSelesai->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </td>

                    {{-- kolom kosong sesudah proyek --}}
                    @for ($i = $pEnd + 1; $i < $allDays->count(); $i++)
                        <td class="border border-gray-100 min-w-[35px] p-0 bg-gray-50/30"></td>
                    @endfor
                </tr>

                {{-- ========== BARIS PEKERJAAN ========== --}}
                @foreach($catatan as $row)
                    @php
                        $mulai = \Carbon\Carbon::parse($row->tanggal_mulai);
                        $selesai = \Carbon\Carbon::parse($row->tanggal_selesai ?? $proyek->tanggal_selesai);

                        $startIndex = $allDays->search(fn($d) => \Carbon\Carbon::parse($d)->gte($mulai));
                        $endIndex   = $allDays->search(fn($d) => \Carbon\Carbon::parse($d)->gte($selesai));

                        if ($startIndex === false) { $startIndex = 0; }
                        if ($endIndex === false) { $endIndex = max(0, $allDays->count() - 1); }

                        $colspan = max(1, $endIndex - $startIndex + 1);

                        $isOverdue = false;
                        $target = null;
                        if (!empty($row->fitur) && $row->fitur->target) {
                            $target = \Carbon\Carbon::parse($row->fitur->target);
                            $isOverdue = $selesai->gt($target);
                        }

                        // Solid colors based on type
                        if ($isOverdue) {
                            $barColor = 'bg-red-500';
                            $iconColor = 'text-white';
                        } else {
                            $barColor = match ($row->jenis ?? '') {
                                'bug'       => 'bg-purple-400',
                                'pekerjaan' => 'bg-blue-400',
                                default     => 'bg-indigo-400',
                            };
                            $iconColor = 'text-white';
                        }
                    @endphp

                    <tr>
                        @for ($i = 0; $i < $startIndex; $i++)
                            <td class="border border-gray-100 min-w-[35px] p-0 bg-gray-50/30"></td>
                        @endfor

                        <td class="border border-gray-200 p-0.5 relative bg-white" colspan="{{ $colspan }}">
                            <div 
                                class="relative overflow-hidden rounded-lg shadow-sm h-7 cursor-pointer group hover:shadow-md hover:scale-[1.01] transition-all duration-200 {{ $barColor }}"
                                wire:click="openModal({{ $row->id }})"
                            >
                                {{-- Content --}}
                                <div class="absolute inset-0 px-2 py-1 flex items-center gap-2">
                                    {{-- Icon --}}
                                    <div class="flex-shrink-0">
                                        @if($isOverdue)
                                            <i class="fa-solid fa-exclamation-triangle text-[10px] {{ $iconColor }} animate-pulse"></i>
                                        @elseif($row->jenis === 'bug')
                                            <i class="fa-solid fa-bug text-[10px] {{ $iconColor }}"></i>
                                        @else
                                            <i class="fa-solid fa-tasks text-[10px] {{ $iconColor }}"></i>
                                        @endif
                                    </div>

                                    {{-- Text --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="text-[9px] font-semibold text-white truncate leading-tight">
                                            {{ $row->catatan }}
                                        </div>
                                        
                                        @if($isOverdue || $target)
                                            <div class="flex items-center gap-1 text-[8px] text-white/90 mt-0.5">
                                                @if($isOverdue)
                                                    <span class="font-bold">⚠ Terlambat</span>
                                                @endif
                                                @if($target)
                                                    <span>Target: {{ $target->format('d M') }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Hover Arrow --}}
                                    <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fa-solid fa-arrow-right text-[10px] text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </td>

                        @for ($i = $endIndex + 1; $i < $allDays->count(); $i++)
                            <td class="border border-gray-100 min-w-[35px] p-0 bg-gray-50/30"></td>
                        @endfor
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>

   {{-- Tombol Kembali --}}
    <div class="flex justify-start pt-2">
        <a href="{{ route('proyek') }}"
            class="px-3 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-[10px] rounded-lg shadow hover:shadow-lg hover:scale-105 transition-all duration-200 font-semibold flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left text-[9px]"></i>
            Kembali ke Daftar Proyek
        </a>
    </div>

   {{-- MODAL DETAIL CATATAN --}}
@if($showModal && $selectedCatatan)
<div 
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    wire:click.self="closeModal"
>
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-100 animate-fade-in overflow-hidden">

        {{-- Header with Gradient --}}
        <div class="relative bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-3">
            <div class="relative flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <i class="fa-solid fa-circle-info text-white text-xs"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-white">Rincian Catatan</h3>
                        <p class="text-[8px] text-blue-100">Informasi lengkap catatan</p>
                    </div>
                </div>
                
                <button 
                    wire:click="closeModal"
                    class="w-6 h-6 rounded-lg hover:bg-white/20 flex items-center justify-center transition-colors"
                >
                    <i class="fa-solid fa-times text-white text-xs"></i>
                </button>
            </div>
        </div>

       {{-- Body --}}
        <div class="p-4 space-y-2">

            {{-- Catatan Text --}}
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-2.5 border border-gray-200">
                <p class="text-[11px] text-gray-700 leading-relaxed text-justify">
                    {{ $selectedCatatan->catatan }}
                </p>
            </div>

            {{-- Baris 1: User & Timeline --}}
            <div class="grid grid-cols-2 gap-2">
                
                {{-- User Info --}}
                <div class="bg-blue-50 rounded-lg p-2 border border-blue-100">
                    <div class="flex items-center gap-1.5 mb-1">
                        <i class="fa-solid fa-user text-blue-500 text-[9px]"></i>
                        <span class="text-[8px] text-blue-600 font-semibold uppercase tracking-wide">Pelapor</span>
                    </div>
                    <p class="text-[10px] text-gray-700 font-medium truncate">
                        {{ $selectedCatatan->user->name ?? '-' }}
                    </p>
                </div>

                {{-- Timeline --}}
                <div class="bg-indigo-50 rounded-lg p-2 border border-indigo-100">
                    <div class="flex items-center gap-1.5 mb-1">
                        <i class="fa-solid fa-calendar-days text-indigo-500 text-[9px]"></i>
                        <span class="text-[8px] text-indigo-600 font-semibold uppercase tracking-wide">Waktu</span>
                    </div>
                    <div class="flex items-center gap-1 text-[9px] text-gray-700">
                        <span class="font-medium">{{ \Carbon\Carbon::parse($selectedCatatan->tanggal_mulai)->format('d M') }}</span>
                        <i class="fa-solid fa-arrow-right text-[7px] text-gray-400"></i>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($selectedCatatan->tanggal_selesai)->format('d M') }}</span>
                    </div>
                </div>

            </div>

            {{-- Baris 2: Jenis & Fitur --}}
            <div class="grid grid-cols-2 gap-2">

                {{-- Jenis Badge --}}
                <div class="bg-purple-50 rounded-lg p-2 border border-purple-100">
                    <div class="flex items-center gap-1.5 mb-1">
                        <i class="fa-solid fa-tag text-purple-500 text-[9px]"></i>
                        <span class="text-[8px] text-purple-600 font-semibold uppercase tracking-wide">Jenis</span>
                    </div>
                    @php
                        $jenisRaw = strtolower($selectedCatatan->jenis ?? '-');
                        $jenisLabel = $jenisRaw === 'pekerjaan' ? 'Catatan' : ucfirst($jenisRaw);
                        $jenisColor = $jenisRaw === 'pekerjaan' 
                            ? 'bg-blue-500 text-white'
                            : 'bg-purple-500 text-white';
                    @endphp
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold {{ $jenisColor }}">
                        {{ $jenisLabel }}
                    </span>
                </div>

               {{-- FITUR --}}
                @if($selectedCatatan->fitur)
                    @php
                        $target = $selectedCatatan->fitur->target ? \Carbon\Carbon::parse($selectedCatatan->fitur->target) : null;
                        $selesai = \Carbon\Carbon::parse($selectedCatatan->tanggal_selesai ?? $selectedCatatan->tanggal_mulai);
                        $isOverdue = $target && $selesai->gt($target);
                    @endphp

                    <div class="bg-{{ $isOverdue ? 'red' : 'emerald' }}-50 rounded-lg p-2 border border-{{ $isOverdue ? 'red' : 'emerald' }}-100">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-1.5">
                                <i class="fa-solid fa-cube text-{{ $isOverdue ? 'red' : 'emerald' }}-500 text-[9px]"></i>
                                <span class="text-[8px] text-{{ $isOverdue ? 'red' : 'emerald' }}-600 font-semibold uppercase tracking-wide">Fitur</span>
                            </div>
                            @if($isOverdue)
                                <span class="px-1.5 py-0.5 bg-red-500 text-white rounded-full text-[7px] font-bold flex items-center gap-0.5">
                                    <i class="fa-solid fa-exclamation-triangle"></i>
                                    Telat
                                </span>
                            @endif
                        </div>
                        
                        <div class="space-y-0.5">
                            <div class="text-[9px] font-bold text-gray-800 truncate" title="{{ $selectedCatatan->fitur->nama_fitur ?? '-' }}">
                                {{ Str::limit($selectedCatatan->fitur->nama_fitur ?? '-', 15) }}
                            </div>
                            @if($target)
                                <div class="text-[8px] text-gray-600">
                                    Target: <span class="font-semibold">{{ $target->format('d M') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Tampilan jika tidak ada fitur --}}
                    <div class="bg-blue-50 rounded-lg p-2 border border-blue-100">
                        <div class="flex items-center gap-1.5 mb-1">
                            <i class="fa-solid fa-clipboard-list text-blue-500 text-[9px]"></i>
                            <span class="text-[8px] text-blue-600 font-semibold uppercase tracking-wide">Kategori</span>
                        </div>
                        <div class="text-[9px] text-gray-700 font-medium">
                            Catatan Proyek
                        </div>
                    </div>
                @endif

            </div>

            {{-- KETERANGAN --}}
            @if($selectedCatatan->keterangan)
                <div class="bg-amber-50 rounded-lg p-2 border border-amber-200">
                    <div class="flex items-center gap-1.5 mb-1">
                        <i class="fa-solid fa-note-sticky text-amber-500 text-[9px]"></i>
                        <span class="text-[8px] text-amber-600 font-semibold uppercase tracking-wide">Keterangan</span>
                    </div>
                    <p class="text-[10px] text-gray-700 leading-relaxed">
                        {{ $selectedCatatan->keterangan }}
                    </p>
                </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 px-4 py-2.5 border-t border-gray-200 flex justify-end">
            <button 
                wire:click="closeModal"
                class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg shadow hover:shadow-lg hover:scale-105 transition-all duration-200 text-[10px] font-semibold flex items-center gap-1"
            >
                <i class="fa-solid fa-check text-[9px]"></i>
                Tutup
            </button>
        </div>
    </div>
</div>
@endif

{{-- Custom Styles --}}
<style>
    /* Animation */
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.2s ease-out;
    }

    /* Scrollbar styling */
    .overflow-auto::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .overflow-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .overflow-auto::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #3b82f6, #6366f1);
        border-radius: 10px;
    }

    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #2563eb, #4f46e5);
    }
</style>

</div>