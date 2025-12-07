<div class="p-6 bg-white shadow rounded-xl space-y-6">

    {{-- ====== INFORMASI PROYEK ====== --}}
    <div class="p-4 border rounded-lg bg-gray-50 space-y-4">

        {{-- Customer --}}
        <div>
            <div class="text-xs text-gray-500">Customer</div>
            <div class="text-xs font-semibold text-gray-800">
                {{ $proyek->customer->nama ?? '-' }}
            </div>
        </div>

        {{-- Lokasi --}}
        <div>
            <div class="text-xs text-gray-500">Location</div>
            <div class="text-xs font-semibold text-gray-800">
                {{ $proyek->lokasi }}
            </div>
        </div>

        {{-- Status --}}
        <div>
            <div class="text-xs text-gray-500">Status</div>

            @php
                $statusMap = [
                    'belum_dimulai'   => 'Upcoming',
                    'sedang_berjalan' => 'Ongoing',
                    'selesai'         => 'Done',
                ];
            @endphp

            <span class="
                px-3 py-1 rounded-full text-xs font-semibold
                @if($proyek->status === 'belum_dimulai') bg-blue-100 text-blue-800
                @elseif($proyek->status === 'sedang_berjalan') bg-yellow-100 text-yellow-800
                @elseif($proyek->status === 'selesai') bg-green-100 text-green-800
                @else bg-gray-100 text-gray-700 
                @endif
            ">
                {{ $statusMap[$proyek->status] ?? 'Unknown' }}
            </span>
        </div>

        {{-- Countdown --}}
        <div class="
            p-4 border rounded-lg 
            @if($sisaHari <= 30) bg-red-50 border-red-300 
            @else bg-blue-50 border-blue-300 
            @endif
        ">
            <div class="text-xs text-gray-600">Remaining Time Until Deadline</div>

            @if($sisaHari < 0)
                <div class="text-xs font-bold text-red-600">
                    Exceeded deadline {{ abs($sisaHari) }} days ago
                </div>
            @elseif($sisaHari > 30)
                <div class="text-xs font-bold text-blue-600">
                    {{ $sisaBulan }} months • {{ $sisaHariDetail }} days remaining
                </div>
            @else
                <div class="text-xs font-bold text-red-600">
                    {{ $sisaHari }} days left!
                </div>
            @endif
        </div>

        {{-- ====== MEMBER INFO ====== --}}
        <div class="p-4 border rounded-lg bg-white">
            <div class="text-xs text-gray-600">Project Members</div>

            <div class="grid grid-cols-3 gap-3 mt-2">

                <div class="p-2 bg-blue-50 border border-blue-200 rounded text-center">
                    <div class="text-[10px] text-blue-700">Programmers</div>
                    <div class="text-sm font-bold text-blue-900">{{ $totalProgrammer }}</div>
                </div>

                <div class="p-2 bg-yellow-50 border border-yellow-200 rounded text-center">
                    <div class="text-[10px] text-yellow-700">Testers</div>
                    <div class="text-sm font-bold text-yellow-900">{{ $totalTester }}</div>
                </div>

                <div class="p-2 bg-green-50 border border-green-200 rounded text-center">
                    <div class="text-[10px] text-green-700">Managers</div>
                    <div class="text-sm font-bold text-green-900">{{ $totalManajer }}</div>
                </div>

            </div>
        </div>

        {{-- ====== FITUR PROGRESS ====== --}}
        <div class="p-4 border rounded-lg bg-white space-y-4">
            <div class="text-xs text-gray-600">Feature Progress</div>

            <div class="grid grid-cols-3 gap-3 text-center">

                <div class="p-2 bg-gray-50 border rounded">
                    <div class="text-[10px] text-gray-700">Total Features</div>
                    <div class="text-sm font-bold">{{ $totalFitur }}</div>
                </div>

                <div class="p-2 bg-green-50 border border-green-200 rounded">
                    <div class="text-[10px] text-green-700">Done</div>
                    <div class="text-sm font-bold text-green-900">{{ $fiturSelesai }}</div>
                </div>

                <div class="p-2 bg-blue-50 border border-blue-200 rounded">
                    <div class="text-[10px] text-blue-700">In Progress</div>
                    <div class="text-sm font-bold text-blue-900">{{ $fiturBerjalan }}</div>
                </div>

                <div class="p-2 bg-yellow-50 border border-yellow-200 rounded">
                    <div class="text-[10px] text-yellow-700">Pending</div>
                    <div class="text-sm font-bold text-yellow-900">{{ $fiturPending }}</div>
                </div>

                <div class="p-2 bg-purple-50 border border-purple-200 rounded">
                    <div class="text-[10px] text-purple-700">Upcoming</div>
                    <div class="text-sm font-bold text-purple-900">{{ $fiturUpcoming }}</div>
                </div>

                <div class="p-2 bg-red-50 border border-red-200 rounded">
                    <div class="text-[10px] text-red-700">Overdue</div>
                    <div class="text-sm font-bold text-red-900">{{ $fiturOverdue }}</div>
                </div>

            </div>

            {{-- Grafik Progress Ring --}}
            <div class="flex justify-center mt-4">
                <div class="relative w-28 h-28">

                    @php
                        $percent = $totalFitur > 0 
                            ? round(($progressComplete / $totalFitur) * 100) 
                            : 0;
                    @endphp

                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <circle 
                            cx="50" cy="50" r="45" 
                            stroke="#e5e7eb" 
                            stroke-width="10" 
                            fill="none" 
                        />
                        <circle 
                            cx="50" cy="50" r="45"
                            stroke="#10b981"
                            stroke-width="10"
                            fill="none"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $percent * 2.83 }} 283"
                        />
                    </svg>

                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-sm font-bold text-gray-700">{{ $percent }}%</span>
                    </div>
                </div>
            </div>
            </div>

            {{-- ====== 3 FITUR TERBARU ====== --}}
            <div class="p-4 border rounded-lg bg-white space-y-3">
                <div class="text-xs text-gray-600">Latest Features</div>

                @forelse($fiturTerbaru as $f)
                    <div class="p-2 border rounded bg-gray-50 flex justify-between items-start">
                        <div>
                            <div class="text-sm font-semibold text-gray-800">
                                {{ $f->nama_fitur ?? '-' }}
                            </div>
                            <div class="text-[10px] text-gray-500">
                                Target: 
                                {{ $f->target ? \Carbon\Carbon::parse($f->target)->format('d M Y') : '-' }}
                            </div>

                        </div>

                        <span class="
                            px-2 py-1 rounded-full text-[10px] font-semibold
                            @if($f->status_fitur === 'Done') bg-green-100 text-green-700
                            @elseif($f->status_fitur === 'In Progress') bg-blue-100 text-blue-700
                            @elseif($f->status_fitur === 'Pending') bg-yellow-100 text-yellow-700
                            @elseif($f->status_fitur === 'Upcoming') bg-purple-100 text-purple-700
                            @else bg-gray-100 text-gray-700
                            @endif
                        ">
                            {{ $f->status_fitur }}
                        </span>
                    </div>
                @empty
                    <div class="text-xs text-gray-500">No features yet.</div>
                @endforelse
            </div>
            
            {{-- ====== 5 NOTES TERBARU ====== --}}
            <div class="p-4 border rounded-lg bg-white space-y-3">
    <div class="text-xs text-gray-600 font-semibold">
        Latest Notes
    </div>

    @forelse ($catatanTerbaru as $c)
        <div class="p-3 border rounded bg-gray-50 space-y-1">

            {{-- Notes --}}
            <div class="text-xs font-semibold text-gray-800">
                {{ Str::limit($c->catatan, 80) }}
            </div>

            {{-- User Pembuat --}}
            <div class="text-[10px] text-gray-600">
                @if ($c->jenis === 'pekerjaan')
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-blue-50 text-[#5ca9ff]">
                        Task
                        @if (!empty($c->fitur?->nama_fitur))
                            - {{ $c->fitur->nama_fitur }}
                        @endif
                    </span>
                @else
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-purple-100 text-[#9c62ff]">
                        Bug
                        @if (!empty($c->fitur?->nama_fitur))
                            - {{ $c->fitur->nama_fitur }}
                        @endif
                    </span>
                @endif
                <span class="italic ml-1 text-gray-500">Report by: {{ $c->user->name ?? 'Unknown User' }}</span>
            </div>

            {{-- Tanggal Mulai & Selesai --}}
            <div class="text-[10px] text-gray-400 mt-1">
                Start: 
                {{ $c->tanggal_mulai ? \Carbon\Carbon::parse($c->tanggal_mulai)->format('d M Y') : '-' }}
                • End:
                {{ $c->tanggal_selesai ? \Carbon\Carbon::parse($c->tanggal_selesai)->format('d M Y') : '-' }}
            </div>

        </div>
    @empty
        <div class="text-[10px] text-gray-500">No notes yet.</div>
    @endforelse
</div>



    </div>

</div>
