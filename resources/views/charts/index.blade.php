@extends('layouts.app')

@section('title', 'Sales Charts')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Sales Charts</h1>
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Yearly Sales</h4>
            <canvas id="yearlySalesChart" height="100"></canvas>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Sales Bar Chart (Date Range)</h4>
            <input type="date" id="startDate"> to <input type="date" id="endDate">
            <button class="btn btn-primary btn-sm" id="updateBarChart">Update</button>
            <canvas id="salesBarChart" height="100"></canvas>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Product Sales Percentage</h4>
            <canvas id="productPieChart" height="100"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const updateButton = document.getElementById('updateBarChart');

    const today = new Date();
    const monthAgo = new Date();
    monthAgo.setDate(today.getDate() - 29);

    startDateInput.value = monthAgo.toISOString().split('T')[0];
    endDateInput.value = today.toISOString().split('T')[0];

    const yearlySalesChart = new Chart(document.getElementById('yearlySalesChart'), {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Yearly Sales',
                data: [],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.15)',
                tension: 0.25,
                fill: true,
            }],
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });

    const salesBarChart = new Chart(document.getElementById('salesBarChart'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Sales',
                data: [],
                backgroundColor: '#198754',
            }],
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });

    const productPieChart = new Chart(document.getElementById('productPieChart'), {
        type: 'pie',
        data: {
            labels: [],
            datasets: [{
                label: 'Product % of Total Sales',
                data: [],
                backgroundColor: [
                    '#0d6efd', '#20c997', '#ffc107', '#dc3545', '#6f42c1',
                    '#fd7e14', '#198754', '#0dcaf0', '#6610f2', '#adb5bd',
                ],
            }],
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.parsed}%`;
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
        productPieChart.update();
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
