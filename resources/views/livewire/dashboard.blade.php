<div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen py-1 px-3">
    <div class="pt-2 py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ===================== HEADER SECTION ===================== --}}
            <div class="mb-2">
                <h1 class="text-lg font-semibold text-gray-900">Dasbor</h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    Pantau proyek dan klien secara real-time
                </p>
            </div>

            {{-- ===================== METRIC CARDS ===================== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-2">

                {{-- PROJECT --}}
                <a href="{{ route('proyek') }}" class="block">
                    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300 overflow-hidden hover:scale-[1.01] transition-transform">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 px-4 py-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-[10px] font-semibold text-blue-100 uppercase tracking-wide mb-0.5">
                                        Total Proyek
                                    </div>
                                    <div class="text-lg font-bold text-white">
                                        {{ $totalProyek }}
                                    </div>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded-full p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5 text-white"
                                        fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- CUSTOMER --}}
                <a href="/customer" class="block cursor-pointer">
                    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300 overflow-hidden hover:scale-[1.01] transition-transform">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 px-4 py-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-[10px] font-semibold text-indigo-100 uppercase tracking-wide mb-0.5">
                                        Total Klien
                                    </div>
                                    <div class="text-lg font-bold text-white">
                                        {{ $totalCustomer }}
                                    </div>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded-full p-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>


                {{-- NOTES --}}
                <a href="/tasks" class="block cursor-pointer">
                    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300 overflow-hidden hover:scale-[1.01] transition-transform">
                        <div class="bg-gradient-to-br from-purple-500 to-pink-600 px-4 py-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-[10px] font-semibold text-purple-100 uppercase tracking-wide mb-0.5">
                                        Total Catatan
                                    </div>
                                    <div class="text-lg font-bold text-white">
                                        {{ $totalNotes }}
                                    </div>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded-full p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5 text-white"
                                        fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 8h6M9 12h6M9 16h4" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

            </div>

            {{-- ===================== BUDGET & DEADLINE SECTION ===================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-2">

                {{-- ===================== LINE CHART BUDGET ===================== --}}
                <div class="lg:col-span-2 bg-white rounded-lg shadow p-4" wire:ignore>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-800">Ringkasan Anggaran Proyek</h3>
                            <p class="text-[10px] text-gray-500 mt-0.5">Alokasi anggaran dari terendah ke tertinggi</p>
                        </div>
                        <div class="flex items-center space-x-1.5 text-xs text-gray-500">
                            <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span class="text-[10px]">Rupiah (Rp)</span>
                        </div>
                    </div>
                    <div class="relative h-[130px]">
                        <canvas id="budgetLineChart"></canvas>
                    </div>
                </div>

                {{-- ===== DEADLINE TERDEKAT ===== --}}
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-800">Tenggat Waktu Terdekat</h3>
                            <p class="text-[10px] text-gray-500 mt-0.5">3 proyek terdekat</p>
                        </div>
                    </div>

                    <div class="space-y-1">
                        @forelse ($upcomingDeadlines->take(3) as $proyek)
                            <div class="flex items-start space-x-2 p-1.5 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 transition-colors duration-200">
                                <div class="flex-shrink-0 w-7 h-7 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-[10px]">
                                    {{ \Carbon\Carbon::parse($proyek->tanggal_selesai)->format('d') }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-semibold text-gray-900 truncate">{{ $proyek->nama_proyek }}</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($proyek->tanggal_selesai)->format('d M Y') }}</p>
                                  
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs text-gray-500 mt-1.5">Tidak ada tenggat waktu terdekat</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- ===================== CHARTS GRID ===================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">

                {{-- ===== BAR CHART STATUS PROYEK ===== --}}
                <div class="bg-white rounded-lg shadow p-4" wire:ignore>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-800">Status Proyek</h3>
                            <p class="text-[10px] text-gray-500 mt-0.5">
                                Distribusi status proyek
                            </p>
                        </div>
                        <div class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-full">
                            {{ $totalProyek }}
                        </div>
                    </div>
                    <div class="relative h-[130px]">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                {{-- ===== DOUGHNUT CHART STATUS CUSTOMER ===== --}}
                <div class="bg-white rounded-lg shadow p-4" wire:ignore>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-800">Status Klien</h3>
                            <p class="text-[10px] text-gray-500 mt-0.5">
                                Aktif vs Tidak Aktif
                            </p>
                        </div>
                        <div class="bg-indigo-100 text-indigo-800 text-[10px] font-bold px-2 py-0.5 rounded-full">
                            {{ $totalCustomer }}
                        </div>
                    </div>
                    <div class="flex items-center justify-center h-[130px]">
                        <div class="w-[140px] h-[140px]">
                            <canvas id="customerStatusChart"></canvas>
                        </div>
                    </div>
                </div>


                {{-- ===== HORIZONTAL BAR CUSTOMER ===== --}}
                <div class="bg-white rounded-lg shadow p-4" wire:ignore>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-800">Klien Teratas</h3>
                            <p class="text-[10px] text-gray-500 mt-0.5">
                                Berdasarkan jumlah proyek
                            </p>
                        </div>
                        <div class="bg-purple-100 text-purple-800 text-[10px] font-bold px-2 py-0.5 rounded-full">
                            Top 5
                        </div>
                    </div>
                    <div class="relative h-[130px]">
                        <canvas id="customerProjectChart"></canvas>
                    </div>
                </div>


                {{-- ===== PROYEK TERBARU ===== --}}
                <div class="bg-white rounded-lg shadow p-4" wire:ignore>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-800">Proyek Terbaru</h3>
                            <p class="text-[10px] text-gray-500 mt-0.5">
                                Pembaruan terbaru
                            </p>
                        </div>
                        <a href="{{ route('proyek') }}"
                        class="text-[10px] text-blue-600 hover:text-indigo-700 font-semibold hover:underline transition-colors">
                            Lihat Semua →
                        </a>
                    </div>


                    <div class="space-y-2">
                        @forelse ($latestProjects->take(3) as $proyek)
                            <div class="flex items-start space-x-2 p-1 rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 hover:from-blue-100 hover:to-purple-100 transition-colors duration-200 group cursor-pointer">
                                <div class="flex-shrink-0 w-7 h-7 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-semibold text-gray-900 truncate group-hover:text-indigo-600 transition-colors">
                                        {{ $proyek->nama_proyek }}
                                    </p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">{{ $proyek->created_at->diffForHumans() }}</p>
                                    
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-xs text-gray-500 mt-1.5">Tidak Proyek</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Chatbot --}}
    <div class="font-sans" wire:ignore>
        @livewire('chatbot', [], key('chatbot-'.$proyek->id))
    </div>
</div>

{{-- ===================== CHART.JS ===================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', initCharts);
document.addEventListener('livewire:navigated', initCharts);

function initCharts() {
    renderStatusChart();
    renderBudgetChart();
    renderCustomerStatusChart();
    renderCustomerProjectChart();
}

/* ===== STATUS PROYEK ===== */
function renderStatusChart() {
    const ctx = document.getElementById('statusChart');
    if (!ctx) return;

    if (window.statusChartInstance) {
        window.statusChartInstance.destroy();
    }

    window.statusChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Belum Dimulai', 'Sedang Berjalan', 'Selesai', 'Ditunda'],
            datasets: [{
                data: [
                    @json($statusChart['belum_dimulai']),
                    @json($statusChart['sedang_berjalan']),
                    @json($statusChart['selesai']),
                    @json($statusChart['ditunda'])
                ],
                backgroundColor: [
                    'rgba(156, 163, 175, 0.8)',
                    'rgba(79, 70, 229, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    'rgba(156, 163, 175, 1)',
                    'rgba(79, 70, 229, 1)',
                    'rgba(34, 197, 94, 1)',
                    'rgba(239, 68, 68, 1)'
                ],
                borderWidth: 2,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                    padding: 10,
                    titleFont: { size: 11, weight: 'bold' },
                    bodyFont: { size: 10 },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 6
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        precision: 0,
                        font: { size: 9 },
                        color: '#6B7280'
                    },
                    grid: { 
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    border: { display: false }
                },
                x: {
                    ticks: { 
                        font: { size: 9 },
                        color: '#6B7280'
                    },
                    grid: { display: false },
                    border: { display: false }
                }
            }
        }
    });
}

/* ===== LINE CHART BUDGET ===== */
function renderBudgetChart() {
    const ctx = document.getElementById('budgetLineChart');
    if (!ctx) return;

    if (window.budgetChart) {
        window.budgetChart.destroy();
    }

    const labels = @json($budgetLabels);

    window.budgetChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                data: @json($budgetValues),
                tension: 0.4,
                borderWidth: 2.5,
                pointRadius: 3,
                pointHoverRadius: 5,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2,
                borderColor: 'rgba(79, 70, 229, 1)',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                    padding: 10,
                    titleFont: { size: 11, weight: 'bold' },
                    bodyFont: { size: 10 },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 6,
                    callbacks: {
                        label: ctx => 'Anggaran: Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        font: { size: 9 },
                        color: '#6B7280',
                        callback: (value, index) =>
                            index === 0 || index === labels.length - 1
                                ? labels[index]
                                : ''
                    },
                    grid: { display: false },
                    border: { display: false }
                },
                y: {
                    ticks: {
                        font: { size: 9 },
                        color: '#6B7280',
                        callback: value => 'Rp ' + (value / 1000000).toFixed(0) + 'Juta'
                    },
                    grid: { 
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    border: { display: false }
                }
            }
        }
    });
}

/* ===== DOUGHNUT CHART CUSTOMER ===== */
function renderCustomerStatusChart() {
    const ctx = document.getElementById('customerStatusChart');
    if (!ctx) return;

    if (window.customerStatusChartInstance) {
        window.customerStatusChartInstance.destroy();
    }

    window.customerStatusChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Tidak Aktif'],
            datasets: [{
                data: @json($customerStatusValues),
                backgroundColor: [
                    'rgba(79, 70, 229, 0.8)',   
                    'rgba(239, 68, 68, 0.8)'   
                ],
                borderColor: [
                    'rgba(79, 70, 229, 1)',
                    'rgba(239, 68, 68, 1)'   
                ],
                borderWidth: 2,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        font: { size: 10 },
                        usePointStyle: true,
                        pointStyle: 'circle',
                        color: '#6B7280'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                    padding: 10,
                    titleFont: { size: 11, weight: 'bold' },
                    bodyFont: { size: 10 },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 6,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

/* ===== HORIZONTAL BAR CUSTOMER ===== */
function renderCustomerProjectChart() {
    const ctx = document.getElementById('customerProjectChart');
    if (!ctx) return;

    if (window.customerProjectChartInstance) {
        window.customerProjectChartInstance.destroy();
    }

    const labels = @json($customerProjectLabels);
    const values = @json($customerProjectValues);

    window.customerProjectChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.slice(0, 5),
            datasets: [{
                data: values.slice(0, 5),
                backgroundColor: 'rgba(147, 51, 234, 0.8)',
                borderColor: 'rgba(147, 51, 234, 1)',
                borderWidth: 2,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                    padding: 10,
                    titleFont: { size: 11, weight: 'bold' },
                    bodyFont: { size: 10 },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 6,
                    callbacks: {
                        label: ctx => 'Jumlah Proyek: ' + ctx.parsed.x
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { 
                        precision: 0,
                        font: { size: 9 },
                        color: '#6B7280'
                    },
                    grid: { 
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    border: { display: false }
                },
                y: {
                    ticks: { 
                        font: { size: 9 },
                        color: '#6B7280'
                    },
                    grid: { display: false },
                    border: { display: false }
                }
            }
        }
    });
}
</script>

