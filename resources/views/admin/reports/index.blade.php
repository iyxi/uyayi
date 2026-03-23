@extends('layouts.admin')

@section('title', 'Reports - Uyayi Admin')

@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<span class="separator">&gt;</span>
<span class="current">Reports</span>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Reports</h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Sales</p>
                        <h3 class="mb-0">&#8369;{{ number_format($stats['total_sales'], 2) }}</h3>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Orders</p>
                        <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Customers</p>
                        <h3 class="mb-0">{{ $stats['total_customers'] }}</h3>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Products</p>
                        <h3 class="mb-0">{{ $stats['total_products'] }}</h3>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Yearly Sales</h5>
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="yearlySalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Top Products</h5>
            </div>
            <div class="card-body p-0">
                @if($topProducts->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($topProducts as $index => $product)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-secondary me-2">{{ $index + 1 }}</span>
                                {{ $product->name }}
                            </div>
                            <span class="badge bg-primary">{{ $product->order_items_count }} sold</span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam display-4 text-muted"></i>
                        <p class="mt-3 text-muted">No product data</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h5 class="mb-0">Sales Bar Chart by Date Range</h5>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <input type="date" id="startDate" class="form-control form-control-sm report-date-input">
                    <span class="text-muted small">to</span>
                    <input type="date" id="endDate" class="form-control form-control-sm report-date-input">
                    <button class="btn btn-primary btn-sm" id="updateBarChart">
                        <i class="bi bi-arrow-repeat"></i> Update
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="salesBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Product Sales Share</h5>
            </div>
            <div class="card-body">
                <div class="chart-wrapper chart-wrapper--pie">
                    <canvas id="productPieChart"></canvas>
                </div>
                <div id="productShareSummary" class="small text-muted mt-3"></div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
.chart-wrapper {
    position: relative;
    min-height: 340px;
}
.chart-wrapper--pie {
    min-height: 320px;
}
.report-date-input {
    width: 150px;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const updateButton = document.getElementById('updateBarChart');
    const productShareSummary = document.getElementById('productShareSummary');

    const today = new Date();
    const monthAgo = new Date();
    monthAgo.setDate(today.getDate() - 29);

    startDateInput.value = monthAgo.toISOString().split('T')[0];
    endDateInput.value = today.toISOString().split('T')[0];

    const moneyFormatter = new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    });

    const yearlySalesChart = new Chart(document.getElementById('yearlySalesChart'), {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Yearly Sales',
                data: [],
                borderColor: '#5a9fd4',
                backgroundColor: 'rgba(90, 159, 212, 0.15)',
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#5a9fd4',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return moneyFormatter.format(context.parsed.y || 0);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return moneyFormatter.format(value);
                        }
                    }
                }
            }
        },
    });

    const salesBarChart = new Chart(document.getElementById('salesBarChart'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Sales',
                data: [],
                backgroundColor: '#7cb9e8',
                borderRadius: 10,
                maxBarThickness: 34,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return moneyFormatter.format(context.parsed.y || 0);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return moneyFormatter.format(value);
                        }
                    }
                }
            }
        },
    });

    const productPieChart = new Chart(document.getElementById('productPieChart'), {
        type: 'pie',
        data: {
            labels: [],
            datasets: [{
                label: 'Product % of Total Units Sold',
                data: [],
                backgroundColor: [
                    '#5a9fd4', '#7cb9e8', '#a89078', '#d4c4b0', '#9ed3f6',
                    '#87b2d8', '#c7a887', '#e2d4c2', '#6e9dc8', '#b89f88'
                ],
                borderColor: '#ffffff',
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const units = context.dataset.units?.[context.dataIndex] || 0;
                            return `${context.label}: ${context.parsed}% (${units} unit${units === 1 ? '' : 's'} sold)`;
                        },
                    },
                },
            },
        },
    });

    async function loadYearlySales() {
        const response = await fetch('{{ route('charts.data.yearly') }}');
        const data = await response.json();

        yearlySalesChart.data.labels = data.labels || [];
        yearlySalesChart.data.datasets[0].data = data.values || [];
        yearlySalesChart.update();
    }

    async function loadDateRangeCharts() {
        const params = new URLSearchParams({
            start_date: startDateInput.value,
            end_date: endDateInput.value,
        });

        const [rangeResponse, pieResponse] = await Promise.all([
            fetch(`{{ route('charts.data.range') }}?${params.toString()}`),
            fetch(`{{ route('charts.data.product-share') }}?${params.toString()}`),
        ]);

        const rangeData = await rangeResponse.json();
        const pieData = await pieResponse.json();

        salesBarChart.data.labels = rangeData.labels || [];
        salesBarChart.data.datasets[0].data = rangeData.values || [];
        salesBarChart.update();

        productPieChart.data.labels = pieData.labels || [];
        productPieChart.data.datasets[0].data = pieData.values || [];
        productPieChart.data.datasets[0].units = pieData.units || [];
        productPieChart.update();

        const summaryItems = (pieData.labels || []).map((label, index) => {
            const percentage = pieData.values?.[index] ?? 0;
            const units = pieData.units?.[index] ?? 0;
            return `<div class="d-flex justify-content-between gap-2 py-1 border-bottom"><span>${label}</span><strong>${percentage}%</strong><span>${units} sold</span></div>`;
        });

        productShareSummary.innerHTML = summaryItems.length > 0
            ? summaryItems.join('')
            : '<p class="mb-0 text-center">No product sales data for the selected range.</p>';
    }

    updateButton.addEventListener('click', function () {
        if (!startDateInput.value || !endDateInput.value) {
            alert('Please select both start and end dates.');
            return;
        }

        if (startDateInput.value > endDateInput.value) {
            alert('Start date cannot be later than end date.');
            return;
        }

        loadDateRangeCharts();
    });

    loadYearlySales();
    loadDateRangeCharts();
});
</script>
@endpush
