<div class="space-y-3">

    {{-- ===================== TOP METRICS ===================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        {{-- Total Fitur --}}
        <div class="bg-gradient-to-br from-blue-400 via-indigo-500 to-purple-600 border border-gray-300 rounded-2xl p-3 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                <i class="fa-solid fa-layer-group text-white text-lg"></i>
            </div>

            <div>
                <p class="text-xs text-white">Total Fitur</p>
                <p class="text-xs font-semibold text-white mt-1">
                    {{ $totalFitur }}
                </p>
            </div>
        </div>

        {{-- Total Catatan --}}
        <div class="bg-gradient-to-br from-purple-500 to-pink-600 border border-gray-300 rounded-2xl p-3 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                <i class="fa-solid fa-list-check text-white text-lg"></i>
            </div>

            <div>
                <p class="text-xs text-white">Total Catatan</p>
                <p class="text-xs font-semibold text-white mt-1">
                    {{ $totalCatatan }}
                </p>
            </div>
        </div>

        {{-- Anggota Proyek --}}
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 border border-gray-300 rounded-2xl p-3 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                <i class="fa-solid fa-users text-white text-lg"></i>
            </div>

            <div>
                <p class="text-xs text-white">Anggota Proyek</p>
                <p class="text-xs font-semibold text-white mt-1">
                    {{ $totalProgrammer + $totalTester + $totalManajer }}
                </p>
            </div>
        </div>

        {{-- Total Berkas --}}
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 border border-gray-300 rounded-2xl p-3 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                <i class="fa-solid fa-folder text-white text-lg"></i>
            </div>

            <div>
                <p class="text-xs text-white">Total Berkas</p>
                <p class="text-xs font-semibold text-white mt-1">
                    {{ $totalFile }}
                </p>
            </div>
        </div>

    </div>


    {{-- ===================== MAIN WRAPPER ===================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ===================== LEFT CONTENT ===================== --}}
        <div class="space-y-3">

            {{-- PROJECT HEADER --}}
            <div class="border border-gray-300 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl py-3 px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div>
                    <p class="text-xs text-gray-400">Klien</p>
                    <p class="text-xs text-gray-900">{{ $proyek->customer->nama ?? '-' }}</p>

                    <p class="text-xs text-gray-400 mt-3">Lokasi Proyek</p>
                    <p class="text-xs text-gray-700">{{ $proyek->lokasi ?? '-' }}</p>
                </div>

                <div class="flex items-center gap-10">

                    {{-- STATUS --}}
                    <div>
                        <p class="text-xs text-gray-400">Status</p>
                        @php
                            $statusMap = [
                                'belum_dimulai' => 'Belum Dimulai',
                                'sedang_berjalan' => 'Sedang Berjalan',
                                'selesai' => 'Selesai',
                                'ditunda' => 'Ditunda',
                            ];
                        @endphp

                        <span class="mt-1 inline-block px-3 py-1 rounded-full text-xs font-semibold
                            @if($proyek->status === 'belum_dimulai') bg-blue-50 text-blue-700
                            @elseif($proyek->status === 'sedang_berjalan') bg-yellow-50 text-yellow-700
                            @elseif($proyek->status === 'selesai') bg-green-50 text-green-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ $statusMap[$proyek->status] ?? 'Tidak diketahui' }}
                        </span>
                    </div>

                    {{-- DEADLINE --}}
                    <div class="text-right">
                        <p class="text-xs text-gray-400">Tenggat</p>

                        @if($sisaHari < 0)
                            <p class="text-sm font-bold text-red-600">Terlambat {{ abs($sisaHari) }} hari</p>
                        @elseif($sisaHari > 30)
                            <p class="text-sm font-bold text-blue-600">{{ $sisaBulan }} bulan · {{ $sisaHariDetail }} hari</p>
                        @else
                            <p class="text-sm font-bold text-red-600">{{ $sisaHari }} hari tersisa</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- FEATURE PROGRESS CARD --}}
            <div class="border border-gray-300 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl py-3 px-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-gray-700">Progres Fitur</h3>
                </div>

                {{-- Small stat boxes --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-1 mt-2">
                    <div class="bg-white px-4 py-2 rounded-xl">
                        <p class="text-[10px] text-gray-500">Berjalan</p>
                        <p class="text-xs font-semibold text-blue-600">{{ $fiturBerjalan }}</p>
                    </div>

                    <div class="bg-white px-4 py-2 rounded-xl">
                        <p class="text-[10px] text-gray-500">Ditunda</p>
                        <p class="text-sm font-semibold text-blue-500">{{ $fiturPending }}</p>
                    </div>

                    <div class="bg-white px-4 py-2 rounded-xl">
                        <p class="text-[10px] text-gray-500">Selesai</p>
                        <p class="text-sm font-semibold text-blue-700">{{ $fiturSelesai }}</p>
                    </div>

                    <div class="bg-white px-4 py-2 rounded-xl">
                        <p class="text-[10px] text-gray-500">Belum Dimulai</p>
                        <p class="text-sm font-semibold text-blue-700">{{ $fiturUpcoming }}</p>
                    </div>

                    <div class="bg-white px-4 py-2 rounded-xl">
                        <p class="text-[10px] text-gray-500">Terlambat</p>
                        <p class="text-sm font-semibold text-red-500">{{ $fiturOverdue }}</p>
                    </div>
                </div>


                {{-- Progress & latest features --}}
                <h3 class="text-xs text-gray-500 pt-4">Fitur Terbaru</h3>
                <div class="flex items-center gap-8">

                    <div class="flex-1">
                        <div class="space-y-3">

                            @forelse($fiturTerbaru as $f)
                                <div class="flex items-center justify-between border-b border-gray-200 py-2">

                                    <!-- Nama Fitur -->
                                    <div class="max-w-[70%]">
                                        <p class="text-xs text-gray-800 truncate">
                                            {{ $f->nama_fitur ?? '-' }}
                                        </p>
                                    </div>

                                    <!-- Status Badge (tanpa card) -->
                                    @php
                                        $status = strtolower(trim($f->status_fitur));
                                    @endphp

                                    <span class="text-[10px] font-semibold
                                        @if($status === 'belum_dimulai') text-gray-500
                                        @elseif($status === 'sedang_berjalan') text-blue-600
                                        @elseif($status === 'selesai') text-green-600
                                        @elseif($status === 'ditunda') text-yellow-600
                                        @else text-gray-400
                                        @endif
                                    ">
                                        @if($status === 'belum_dimulai')
                                            Belum Dimulai
                                        @elseif($status === 'sedang_berjalan')
                                            Sedang Berjalan
                                        @elseif($status === 'selesai')
                                            Selesai
                                        @elseif($status === 'ditunda')
                                            Ditunda
                                        @else
                                            Tidak Diketahui
                                        @endif
                                    </span>

                                </div>

                            @empty
                                <p class="text-xs text-gray-400 italic">Belum ada fitur.</p>
                            @endforelse

                        </div>
                    </div>



                    {{-- Progress Ring --}}
                    <div class="w-28 h-28 relative flex-shrink-0">
                        @php
                            $percent = $totalFitur > 0 ? round(($progressComplete / $totalFitur) * 100) : 0;
                        @endphp
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" stroke="#edf2f7" stroke-width="10" fill="none" />
                            <circle cx="50" cy="50" r="45"
                                stroke="url(#grad)" stroke-width="10" fill="none"
                                stroke-linecap="round"
                                stroke-dasharray="{{ $percent * 2.83 }} 283" />
                            <defs>
                                <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#7c3aed"/>
                                    <stop offset="100%" stop-color="#06b6d4"/>
                                </linearGradient>
                            </defs>
                        </svg>

                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-lg font-semibold text-gray-800">{{ $percent }}%</p>
                                <p class="text-xs text-gray-400">Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== RIGHT SIDEBAR ===================== --}}
        <div class="space-y-2">

            {{-- ========== User dan File =========== --}}
           <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

                {{-- Members --}}
                <div class="border border-gray-300 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl px-6 py-3 h-full">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-semibold text-gray-700">Anggota Proyek</h4>
                    </div>

                    <div class="flex items-center justify-center mt-3">
                        <canvas id="membersChart" width="150" height="150"></canvas>
                    </div>
                </div>

                {{-- Files --}}
                <div class="border border-gray-300 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl px-6 py-3 h-full">
                    <div class="flex justify-between items-center">
                        <h4 class="text-xs font-semibold text-gray-700">Berkas Terbaru</h4>
                    </div>

                    <div>
                        @forelse($fileTerbaru as $file)
                            <div class="py-3">
                                <div class="flex justify-between items-center">
                                    <p class="text-xs text-gray-800">{{ $file->nama_file }}</p>
                                    <span class="text-xs font-medium">{{ $file->status }}</span>
                                </div>
                                <p class="text-[8px] text-gray-400">{{ $file->created_at->diffForHumans() }}</p>
                            </div>
                            <hr class="border-gray-200">
                        @empty
                            <p class="text-xs text-gray-400 mt-2 italic">Belum ada Berkas yang diunggah.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($isManagerUser)
            <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-6 items-start">

                {{-- Financial Summary --}}
                <div class="border border-gray-300 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl px-6 pb-6">
                    <div class="py-3 h-44">
                        <h5 class="text-xs font-semibold text-gray-700 mb-4">Ringkasan Keuangan</h5>
                        <canvas id="financialChart" class="w-full h-44"></canvas>
                    </div>
                </div>

                {{-- Invoice vs Receipt --}}
                <div class="border border-gray-300 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl px-6 pb-6">
                    <div class="py-3 h-44">
                        <h5 class="text-xs font-semibold text-gray-700 mb-4">Tagihan vs Kwitansi</h5>
                        <canvas id="invoiceChart" class="w-full h-44"></canvas>
                    </div>
                </div>
            </div>
            @endif


            {{-- Notes --}}
                <div class="border border-gray-300 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl px-6 pt-3 pb-2">
                    <div class="flex justify-between items-center">
                        <h4 class="text-xs font-semibold text-gray-700">Catatan Terbaru</h4>
                    </div>

                    <div class="mt-1">
                        @forelse($catatanTerbaru as $c)
                            <div class="py-2 px-0 border-b border-gray-300">
                                <p class="text-xs text-gray-800">
                                    {{ Str::limit($c->catatan, 85) }}
                                </p>

                                <div class="flex justify-between items-center mt-2 text-[10px] text-gray-500">
                                <div>
                                    @php
                                        $isTask = $c->jenis === 'pekerjaan';
                                        $baseLabel = $isTask ? 'Task' : 'Bug';
                                        $colorBg = $isTask ? 'bg-white' : 'bg-white';
                                        $colorText = $isTask ? 'text-blue-600' : 'text-purple-700';
                                    @endphp

                                    <span class="px-2 py-0.5 rounded-full {{ $colorBg }} {{ $colorText }}">
                                        {{ $baseLabel }}
                                        @if($c->fitur?->nama_fitur)
                                            – {{ $c->fitur->nama_fitur }}
                                        @endif
                                    </span>
                                </div>


                                    <span class="italic text-[9px]">By {{ $c->user->name ?? 'Unknown' }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 italic mt-2">Belum ada Caatan.</p>
                        @endforelse
                    </div>
                </div>
             
            </div>




    {{-- ===================== CHART SCRIPTS ===================== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <script>
        const centerTextPlugin = {
            id: 'centerText',
            afterDraw(chart) {
                if (chart.config.type !== 'doughnut') return;
                const { ctx, chartArea: { width, height } } = chart;

                ctx.save();
                ctx.font = '600 10px Inter, sans-serif'; // <- ukuran teks 10px
                ctx.fillStyle = '#374151';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';

                const text = "{{ $numberOfInvoices ?? 0 }} / {{ $numberOfReceipts ?? 0 }}";
                ctx.fillText(text, width / 2, height / 2);
                ctx.restore();
            }
        };

        function renderCharts() {
            if (window.financialChartInstance)
                window.financialChartInstance.destroy();

            if (window.invoiceChartInstance)
                window.invoiceChartInstance.destroy();

            // Financial Chart (Bar)
            const fctx = document.getElementById('financialChart');
            if (fctx) {
                window.financialChartInstance = new Chart(fctx, {
                    type: 'bar',
                    data: {
                        labels: ['Total Tagihan', 'Pembayaran Diterima', 'Sisa Tagihan'],
                        datasets: [{
                            label: 'Jumlah (Rp)',
                            data: [
                                {{ $totalInvoiceAmount ?? 0 }},
                                {{ $totalPaymentReceived ?? 0 }},
                                {{ $outstandingBalance ?? 0 }}
                            ],
                            backgroundColor: '#2fd5ffff', // satu warna untuk semua bar
                        }]

                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                                labels: { font: { size: 10 } } // <- legend 10px
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                color: '#111827',
                                font: { weight: '600', size: 10 }, // <- datalabels 10px
                                formatter(value) {
                                    return new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { 
                                    font: { size: 10 },
                                    maxRotation: 0,
                                    minRotation: 0
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { font: { size: 10 } }
                            }
                        }

                    },
                    plugins: [ChartDataLabels]
                });
            }

            // Invoice Doughnut Chart
            const ictx = document.getElementById('invoiceChart');
                if (ictx) {
                    window.invoiceChartInstance = new Chart(ictx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Tagihan', 'Kwitansi'],
                            datasets: [{
                                data: [
                                    {{ $numberOfInvoices ?? 0 }},
                                    {{ $numberOfReceipts ?? 0 }}
                                ],
                                backgroundColor: [
                                    '#6366F1', // indigo-500 → Tagihan
                                    '#DB2777'  // purple-500 → Kwitansi
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { font: { size: 10 } }
                                }
                            }
                        },
                        plugins: [centerTextPlugin]
                    });
                }

        }

        document.addEventListener("DOMContentLoaded", renderCharts);

        document.addEventListener("livewire:load", () => {
            Livewire.hook('message.processed', () => renderCharts());
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        const ctxMembers = document.getElementById('membersChart').getContext('2d');

        new Chart(ctxMembers, {
            type: 'pie',
            data: {
                labels: ['Programmer', 'Penguji', 'Manajer'],
                datasets: [{
                    data: [
                        {{ $totalProgrammer }},
                        {{ $totalTester }},
                        {{ $totalManajer }}
                    ],
                    backgroundColor: [
                        '#3B82F6',   // programmer
                        '#7C3AED',   // tester
                        '#DB2777'    // manager
                    ],
                    borderColor: [
                        '#3B82F6',
                        '#7C3AED',
                        '#DB2777'
                    ],
                    borderWidth: 1
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                responsive: false,
                plugins: {
                    legend: {
                        position: 'bottom',   // ← KETERANGAN DI BAWAH
                        labels: {
                            font: { size: 8 },
                            padding: 3
                        }
                    },
                    datalabels: {
                        color: "#fff",
                        font: { weight: "bold", size: 12 },
                        formatter: (value) => value,
                    }
                },
                layout: {
                    padding: { bottom: 10 } // memberi ruang bawah jika perlu
                }
            }
        });
    </script>



</div>
