<div class="bg-gray-50 min-h-screen">
    <div class="py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ===================== METRIC CARDS ===================== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-2">

                <div class="bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl shadow-lg p-4 text-white">
                    <div class="text-xs font-medium mb-1 opacity-90">Total Proyek Saya</div>
                    <div class="text-md font-bold">{{ $totalProyek }}</div>
                </div>

                <div class="bg-gradient-to-br from-cyan-400 to-cyan-500 rounded-xl shadow-lg p-4 text-white">
                    <div class="text-xs font-medium mb-1 opacity-90">Total Customer</div>
                    <div class="text-md font-bold">{{ $totalCustomer }}</div>
                </div>

                <div class="bg-gradient-to-br from-pink-400 to-pink-500 rounded-xl shadow-lg p-4 text-white">
                    <div class="text-xs font-medium mb-1 opacity-90">Total Notes</div>
                    <div class="text-md font-bold">{{ $totalNotes }}</div>
                </div>

                <div class="bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl shadow-lg p-4 text-white">
                    <div class="text-xs font-medium mb-1 opacity-90">Spent Time</div>
                    <div class="text-md font-bold">{{ number_format($totalProyek * 2.5, 1) }}h</div>
                </div>

            </div>

            {{-- ===================== LIST PROYEK ===================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-2">

                {{-- ===== PROYEK TERBARU ===== --}}
                <div class="bg-white rounded-xl shadow-sm p-4 h-[160px] flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-xs font-semibold text-gray-800">Proyek Terbaru</div>
                        <button class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat Semua →</button>
                    </div>

                    <div class="flex-1 overflow-y-auto space-y-2">
                        @forelse ($latestProjects->take(3) as $proyek)
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100 last:border-0">
                                <div class="flex-1 min-w-0 pr-2">
                                    <div class="text-xs font-medium text-gray-900 truncate">{{ $proyek->nama_proyek }}</div>
                                    <div class="text-xs text-gray-500">{{ $proyek->created_at->diffForHumans() }}</div>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium whitespace-nowrap
                                    {{ $proyek->status === 'selesai' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $proyek->status === 'sedang_berjalan' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $proyek->status === 'belum_dimulai' ? 'bg-gray-100 text-gray-700' : '' }}
                                    {{ $proyek->status === 'ditunda' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $proyek->status)) }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400">
                                <div class="text-xs">Belum ada proyek</div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- ===== DEADLINE TERDEKAT ===== --}}
                <div class="bg-white rounded-xl shadow-sm p-4 h-[160px] flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-xs font-semibold text-gray-800">Deadline Terdekat</div>
                        <button class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat Semua →</button>
                    </div>

                    <div class="flex-1 overflow-y-auto space-y-2">
                        @forelse ($upcomingDeadlines->take(3) as $proyek)
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100 last:border-0">
                                <div class="flex-1 min-w-0 pr-2">
                                    <div class="text-xs font-medium text-gray-900 truncate">{{ $proyek->nama_proyek }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($proyek->deadline)->format('d M Y') }}</div>
                                </div>
                                @php
                                    $daysLeft = \Carbon\Carbon::parse($proyek->deadline)->diffInDays(now());
                                @endphp
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium whitespace-nowrap
                                    {{ $daysLeft <= 3 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $daysLeft }}d
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400">
                                <div class="text-2xl mb-1">✅</div>
                                <div class="text-xs">Tidak ada deadline</div>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- ===================== CHARTS GRID ===================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">

                {{-- ===== BAR CHART STATUS PROYEK ===== --}}
                <div class="bg-white rounded-xl shadow-sm p-4 h-[240px] flex flex-col" wire:ignore>
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-xs font-semibold text-gray-800">Status Proyek</div>
                        <div class="text-xs text-gray-500">{{ $totalProyek }}</div>
                    </div>
                    <div class="flex-1 relative">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                {{-- ===== DOUGHNUT CHART STATUS CUSTOMER ===== --}}
                <div class="bg-white rounded-xl shadow-sm p-4 h-[240px] flex flex-col" wire:ignore>
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-xs font-semibold text-gray-800">Status Customer</div>
                        <div class="text-xs text-gray-500">{{ $totalCustomer }}</div>
                    </div>
                    <div class="flex-1 flex items-center justify-center">
                        <div class="w-[160px] h-[160px]">
                            <canvas id="customerStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- ===== HORIZONTAL BAR CUSTOMER ===== --}}
                <div class="bg-white rounded-xl shadow-sm p-4 h-[240px] flex flex-col" wire:ignore>
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-xs font-semibold text-gray-800">Top Customer</div>
                        <div class="text-xs text-gray-500">Top 5</div>
                    </div>
                    <div class="flex-1 relative">
                        <canvas id="customerProjectChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- ===================== LINE CHART BUDGET ===================== --}}
            <div class="bg-white rounded-xl shadow-sm p-4 h-[200px] flex flex-col" wire:ignore>
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs font-semibold text-gray-800">Budget Proyek (Terendah → Tertinggi)</div>
                    <div class="text-xs text-gray-500">Dalam Rupiah</div>
                </div>
                <div class="flex-1 relative">
                    <canvas id="budgetLineChart"></canvas>
                </div>
            </div>

        </div>
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
            labels: ['Belum', 'Berjalan', 'Selesai', 'Ditunda'],
            datasets: [{
                data: [
                    @json($statusChart['belum_dimulai']),
                    @json($statusChart['sedang_berjalan']),
                    @json($statusChart['selesai']),
                    @json($statusChart['ditunda'])
                ],
                backgroundColor: [
                    'rgba(156, 163, 175, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    'rgba(156, 163, 175, 1)',
                    'rgba(59, 130, 246, 1)',
                    'rgba(34, 197, 94, 1)',
                    'rgba(239, 68, 68, 1)'
                ],
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 8,
                    titleFont: { size: 11 },
                    bodyFont: { size: 10 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        precision: 0,
                        font: { size: 10 }
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: {
                    ticks: { font: { size: 10 } },
                    grid: { display: false }
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
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 8,
                    titleFont: { size: 11 },
                    bodyFont: { size: 10 },
                    callbacks: {
                        label: ctx => 'Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        font: { size: 10 },
                        callback: (value, index) =>
                            index === 0 || index === labels.length - 1
                                ? labels[index]
                                : ''
                    },
                    grid: { display: false }
                },
                y: {
                    ticks: {
                        font: { size: 10 },
                        callback: value => 'Rp ' + (value / 1000000).toFixed(0) + 'jt'
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
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
            labels: @json($customerStatusLabels),
            datasets: [{
                data: @json($customerStatusValues),
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    'rgba(34, 197, 94, 1)',
                    'rgba(239, 68, 68, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 8,
                        font: { size: 10 },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 8,
                    titleFont: { size: 11 },
                    bodyFont: { size: 10 },
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
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 8,
                    titleFont: { size: 11 },
                    bodyFont: { size: 10 }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { 
                        precision: 0,
                        font: { size: 10 }
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                y: {
                    ticks: { font: { size: 10 } },
                    grid: { display: false }
                }
            }
        }
    });
}
</script>