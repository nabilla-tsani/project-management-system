<div class="pt-0 p-2 space-y-2 overflow-hidden"> 

    <div class="flex items-center gap-3">
        <h2 class="text-md font-medium flex items-center gap-2 text-[#5ca9ff]">
            <i class="fa-solid fa-calendar"></i>
            Calendar
        </h2>
    </div>

    <!-- Tinggi maksimum tabel = 75% layar -->
    <div class="border h-[78vh] flex flex-col overflow-hidden">

        <!-- Hanya tabel yang boleh scroll -->
        <div class="flex-1 overflow-auto">

            <table class="min-w-max border-collapse w-full">

                {{-- HEADER --}}
                <thead>

                    {{-- ========== ROW BULAN ========== --}}
                    <tr class="sticky top-0 z-20 bg-white">
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
                                <th class="border px-2 py-1 text-xs text-center font-bold"
                                    colspan="{{ $colspan }}">
                                    {{ $bulanSekarang }}
                                </th>
                                @php
                                    $bulanSekarang = $bulanName;
                                    $colspan = 1;
                                @endphp
                            @endif
                        @endforeach

                        <th class="border px-2 py-1 text-xs text-center font-bold" colspan="{{ $colspan }}">
                            {{ $bulanSekarang }}
                        </th>
                    </tr>



                    {{-- ========== ROW TANGGAL ========== --}}
                    <tr class="sticky top-[24px] z-20 bg-white">
                        @foreach($allDays as $day)
                            <th class="border px-1 py-1 text-[10px] min-w-[35px] text-center">
                                {{ \Carbon\Carbon::parse($day)->format('d') }}
                            </th>
                        @endforeach
                    </tr>

                </thead>


                {{-- ====================== --}}
                {{-- BODY --}}
                {{-- ====================== --}}
                <tbody>


                {{-- ========== ROW PROYEK ========== --}}
                @php
                    $pMulai = \Carbon\Carbon::parse($proyek->tanggal_mulai);
                    $pSelesai = \Carbon\Carbon::parse($proyek->tanggal_selesai);

                    $pStart = $allDays->search(fn($d) => \Carbon\Carbon::parse($d)->gte($pMulai));
                    $pEnd   = $allDays->search(fn($d) => \Carbon\Carbon::parse($d)->gte($pSelesai));

                    // fallback jika tidak ditemukan
                    if ($pStart === false) {
                        $pStart = 0;
                    }
                    if ($pEnd === false) {
                        $pEnd = max(0, $allDays->count() - 1);
                    }

                    $pSpan  = max(1, $pEnd - $pStart + 1);
                @endphp

                <tr>
                    
                    {{-- kolom kosong sebelum proyek --}}
                    @for ($i = 0; $i < $pStart; $i++)
                        <td class="border min-w-[35px] p-0"></td>
                    @endfor

                    {{-- bar proyek --}}
                    <td class="border p-0 relative" colspan="{{ $pSpan }}">
                        <div class="bg-[#24c1ddff] h-7 rounded-sm relative">
                            <div class="absolute inset-0 text-[10px] p-1 text-black font-medium whitespace-nowrap">
                                {{ $proyek->nama_proyek }}
                                ({{ $pMulai->format('d M Y') }} - {{ $pSelesai->format('d M Y') }})
                            </div>
                        </div>
                    </td>

                    {{-- kolom kosong sesudah proyek --}}
                    @for ($i = $pEnd + 1; $i < $allDays->count(); $i++)
                        <td class="border min-w-[35px] p-0"></td>
                    @endfor
                </tr>

                {{-- ========== ROW PEKERJAAN ========== --}}
                @foreach($catatan as $row)
                    @php
                        $mulai = \Carbon\Carbon::parse($row->tanggal_mulai);
                        $selesai = \Carbon\Carbon::parse($row->tanggal_selesai ?? $proyek->tanggal_selesai);

                        $startIndex = $allDays->search(fn($d) => \Carbon\Carbon::parse($d)->gte($mulai));
                        $endIndex   = $allDays->search(fn($d) => \Carbon\Carbon::parse($d)->gte($selesai));

                        // fallback jika tidak ditemukan
                        if ($startIndex === false) {
                            $startIndex = 0;
                        }
                        if ($endIndex === false) {
                            $endIndex = max(0, $allDays->count() - 1);
                        }

                        $colspan = max(1, $endIndex - $startIndex + 1);

                        // cek overdue terhadap fitur (jika ada relasi fitur)
                        $isOverdue = false;
                        $target = null;
                        if (!empty($row->fitur) && $row->fitur->target) {
                            $target = \Carbon\Carbon::parse($row->fitur->target);
                            $isOverdue = $selesai->gt($target);
                        }

                        $barColor = $isOverdue
                        ? '#ff8383ff' 
                        : match ($row->jenis ?? '') {
                            'bug'       => '#D6C4FF', 
                            'pekerjaan' => '#A7D4FF', 
                            default     => '#B8F2F6', 
                        };

                    @endphp

                    <tr>
                        {{-- kolom sebelum bar --}}
                        @for ($i = 0; $i < $startIndex; $i++)
                            <td class="border min-w-[35px] p-0"></td>
                        @endfor

                    {{-- bar pekerjaan --}}
                        <td class="border p-0 relative" colspan="{{ $colspan }}">
                            <div 
                                class="h-8 rounded-sm relative cursor-pointer"
                                style="background: {{ $barColor }}"
                                wire:click="openModal({{ $row->id }})"
                            >
                                <div class="absolute inset-0 text-[10px] p-1 text-black leading-tight flex flex-col justify-center">

                                    {{-- Judul catatan --}}
                                    <div class="truncate leading-tight">
                                        {{ $row->catatan }}
                                    </div>

                                    {{-- Target + Overdue dalam satu baris --}}
                                    <div class="flex items-center gap-2 text-[9px] opacity-90">
                                        @if($isOverdue)
                                            <span class="text-black">
                                                ⚠ Overdue
                                            </span>
                                        @endif
                                        @if($target)
                                            <span>Feature Goal Date = {{ $target->format('d M Y') }}</span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                </td>

                        {{-- kolom setelah bar --}}
                        @for ($i = $endIndex + 1; $i < $allDays->count(); $i++)
                            <td class="border min-w-[35px] p-0"></td>
                        @endfor
                    </tr>
                @endforeach

                </tbody>

            </table>
        </div>
    </div>

    {{-- Footer Tombol Kembali --}}
    <div class="flex justify-start pt-4">
        <a href="{{ route('proyek') }}"
           class="px-4 py-2 bg-[#5ca9ff] text-white text-[10px] rounded-3xl shadow hover:bg-[#884fd9] transition">
            Back to Project List
        </a>
    </div>

   {{-- MODAL DETAIL CATATAN --}}
@if($showModal && $selectedCatatan)
<div 
    class="fixed inset-0 bg-black/30 flex items-center justify-center z-50"
    wire:click.self="closeModal"
>

    <div class="bg-white p-4 shadow-md w-full max-w-sm text-xs space-y-3">

        {{-- Header --}}
        <h3 class="font-semibold text-sm text-gray-700 border-b pb-2 text-center"> 
            <i class=" fa-solid fa-circle-info"></i>
            Notes Detail
        </h3>

       {{-- Body --}}
        <div class="space-y-1 text-[11px] text-gray-700">

            {{-- Catatan --}}
            <div class="text-justify pb-4">
                {{ $selectedCatatan->catatan }}
            </div>

            {{-- USER & JENIS --}}
            <div class="flex justify-between items-center">
                <div class="text-gray-500 italic">
                    Report by : {{ $selectedCatatan->user->name ?? '-' }}
                </div>

                @php
                    $jenisRaw = strtolower($selectedCatatan->jenis ?? '-');
                    $jenisLabel = $jenisRaw === 'pekerjaan' ? 'Task' : ucfirst($jenisRaw);
                    $jenisColor = $jenisRaw === 'pekerjaan' 
                        ? 'text-blue-600 bg-blue-100'
                        : 'text-purple-600 bg-purple-100';
                @endphp

                <div class="px-2 py-1 rounded-full text-[10px] font-medium {{ $jenisColor }}">
                    {{ $jenisLabel }}
                </div>
            </div>

            {{-- TANGGAL MULAI & TANGGAL SELESAI --}}
            <div class="flex items-center">
                <div>
                    <span class="text-gray-500"></span>
                    {{ \Carbon\Carbon::parse($selectedCatatan->tanggal_mulai)->format('d M Y') }} 
                    <span> - </span>
                </div>

                <div>
                    <span class="text-gray-500"></span>
                    {{ \Carbon\Carbon::parse($selectedCatatan->tanggal_selesai)->format('d M Y') }}
                </div>
            </div>

            {{-- FITUR & TARGET FITUR --}}
            @if($selectedCatatan->fitur)
                @php
                    $target = $selectedCatatan->fitur->target ? \Carbon\Carbon::parse($selectedCatatan->fitur->target) : null;
                    $selesai = \Carbon\Carbon::parse($selectedCatatan->tanggal_selesai ?? $selectedCatatan->tanggal_mulai);
                    $isOverdue = $target && $selesai->gt($target);
                @endphp

                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-500">Fitur:</span>
                        {{ $selectedCatatan->fitur->nama_fitur ?? '-' }}
                    </div>

                    <div class="flex items-center gap-1">
                        <span class="text-gray-500">Target:</span>
                        {{ $target ? $target->format('d M Y') : '-' }}

                        {{-- Badge Overdue --}}
                        @if($isOverdue)
                            <span class="px-2 py-0.5 bg-red-50 text-red-700 rounded-full text-[10px]">
                                ⚠ Overdue
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            {{-- KETERANGAN --}}
            @if($selectedCatatan->keterangan)
                <div>
                    <span class="text-gray-500">Keterangan:</span><br>
                    {{ $selectedCatatan->keterangan }}
                </div>
            @endif

        </div>


    </div>
</div>
@endif


</div>
