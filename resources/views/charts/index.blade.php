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
// Placeholder JS for charts, to be replaced with AJAX data
const yearlySalesChart = new Chart(document.getElementById('yearlySalesChart'), {
    type: 'line',
    data: { labels: [], datasets: [{ label: 'Yearly Sales', data: [] }] },
    options: { responsive: true }
});
const salesBarChart = new Chart(document.getElementById('salesBarChart'), {
    type: 'bar',
    data: { labels: [], datasets: [{ label: 'Sales', data: [] }] },
    options: { responsive: true }
});
const productPieChart = new Chart(document.getElementById('productPieChart'), {
    type: 'pie',
    data: { labels: [], datasets: [{ label: 'Product %', data: [] }] },
    options: { responsive: true }
});
</script>
@endpush
