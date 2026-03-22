@extends('layouts.admin')

@section('title', 'Reviews - Uyayi Admin')

@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<span class="separator">&gt;</span>
<span class="current">Reviews</span>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Reviews</h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Reviews</p>
                    <h3 class="mb-0">{{ $stats['count'] }}</h3>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-chat-left-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Average Rating</p>
                    <h3 class="mb-0">{{ number_format($stats['average'], 2) }} / 5</h3>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-star-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">All Reviews ({{ $reviews->total() }})</h5>
            <input type="text" id="reviewSearch" class="form-control" style="max-width: 280px;" placeholder="Search reviews...">
        </div>
    </div>
    <div class="card-body p-0">
        @if($reviews->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="reviewsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Target</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                            <tr>
                                <td>{{ $review->created_at ? $review->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                <td>{{ $review->user->name ?? 'Unknown User' }}</td>
                                <td>
                                    @if($review->reviewable)
                                        {{ class_basename($review->reviewable_type) }}: {{ $review->reviewable->name ?? ('#' . $review->reviewable_id) }}
                                    @else
                                        {{ class_basename($review->reviewable_type) }} #{{ $review->reviewable_id }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $review->rating }}/5</span>
                                </td>
                                <td style="max-width: 420px; white-space: normal;">
                                    {{ $review->comment ?: 'No comment provided.' }}
                                </td>
                                <td>
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this review?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-chat-square-text display-4 text-muted"></i>
                <p class="mt-3 text-muted">No reviews yet</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('reviewSearch');
    const table = document.getElementById('reviewsTable');
    if (!searchInput || !table) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
});
</script>
@endpush
@endsection
