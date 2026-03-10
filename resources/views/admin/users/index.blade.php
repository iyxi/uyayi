@extends('layouts.admin')

@section('title', 'Users Management - Uyayi Admin')

@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<span class="separator">&gt;</span>
<span class="current">Users</span>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Users Management</h1>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?status=all">All Users</a></li>
                <li><a class="dropdown-item" href="?status=active">Active Only</a></li>
                <li><a class="dropdown-item" href="?status=inactive">Inactive Only</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="?role=customer">Customers</a></li>
                <li><a class="dropdown-item" href="?role=admin">Admins</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Users Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Users</h6>
                        <h4 class="mb-0">{{ $users->total() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded">
                            <i class="bi bi-person-check fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Active Users</h6>
                        <h4 class="mb-0">{{ $users->where('status', 'active')->count() + $users->whereNull('status')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded">
                            <i class="bi bi-person-badge fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Admins</h6>
                        <h4 class="mb-0">{{ $users->where('role', 'admin')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 text-warning p-3 rounded">
                            <i class="bi bi-cart fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">With Orders</h6>
                        <h4 class="mb-0">{{ $users->where('orders_count', '>', 0)->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">All Users ({{ $users->total() }})</h5>
            </div>
            <div class="col-auto">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search users..." id="userSearch">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="usersTable">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Orders</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3">
                                        <img src="{{ $user->photo_url }}" 
                                             class="rounded-circle" 
                                             style="width: 40px; height: 40px; object-fit: cover;"
                                             alt="{{ $user->name }}">
                                    </div>
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-info ms-1">You</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ $user->email }}</span>
                            </td>
                            <td>
                                @if($user->phone)
                                    <span class="text-muted">{{ $user->phone }}</span>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                            <td>
                                @if($user->address)
                                    <span class="text-muted">{{ Str::limit($user->address, 30) }}</span>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                            <td>
                                <select class="form-select form-select-sm role-select" 
                                        data-user-id="{{ $user->id }}" 
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="customer" {{ ($user->role ?? 'customer') === 'customer' ? 'selected' : '' }}>Customer</option>
                                    <option value="admin" {{ ($user->role ?? 'customer') === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-select form-select-sm status-select" 
                                        data-user-id="{{ $user->id }}"
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="active" {{ ($user->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ ($user->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </td>
                            <td>
                                @if($user->orders_count > 0)
                                    <span class="badge bg-success">{{ $user->orders_count }} orders</span>
                                @else
                                    <span class="text-muted">No orders</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info" onclick="viewUser({{ $user->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button class="btn btn-outline-warning" onclick="resetPassword({{ $user->id }}, '{{ $user->name }}')">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer bg-white">
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">No Users Found</h4>
                <p class="text-muted">No users match your current filters.</p>
            </div>
        @endif
    </div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- User details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle role changes
    document.querySelectorAll('.role-select').forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const newRole = this.value;
            
            if (confirm(`Are you sure you want to change this user's role to ${newRole}?`)) {
                updateUserRole(userId, newRole);
            } else {
                // Reset to original value
                this.value = this.querySelector('option[selected]').value;
            }
        });
    });

    // Handle status changes
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const newStatus = this.value;
            
            updateUserStatus(userId, newStatus);
        });
    });

    // Search functionality
    document.getElementById('userSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#usersTable tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
});

function updateUserRole(userId, role) {
    fetch(`/admin/users/${userId}/role`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ role: role })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
        } else {
            showToast('error', 'Failed to update user role');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'An error occurred');
    });
}

function updateUserStatus(userId, status) {
    fetch(`/admin/users/${userId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
        } else {
            showToast('error', 'Failed to update user status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'An error occurred');
    });
}

function viewUser(userId) {
    // Load user details via AJAX and show in modal
    fetch(`/admin/users/${userId}`)
        .then(response => response.json())
        .then(user => {
            const content = `
                <div class="text-center mb-3">
                    <img src="${user.photo_url}" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                </div>
                <table class="table">
                    <tr><th>Name:</th><td>${user.name}</td></tr>
                    <tr><th>Email:</th><td>${user.email}</td></tr>
                    <tr><th>Phone:</th><td>${user.phone || 'Not provided'}</td></tr>
                    <tr><th>Role:</th><td><span class="badge bg-${user.role === 'admin' ? 'danger' : 'primary'}">${user.role}</span></td></tr>
                    <tr><th>Status:</th><td><span class="badge bg-${user.status === 'active' ? 'success' : 'secondary'}">${user.status}</span></td></tr>
                    <tr><th>Orders:</th><td>${user.orders_count} orders</td></tr>
                    <tr><th>Joined:</th><td>${new Date(user.created_at).toLocaleDateString()}</td></tr>
                </table>
            `;
            document.getElementById('userDetailsContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Failed to load user details');
        });
}

function resetPassword(userId, userName) {
    if (confirm(`Are you sure you want to reset the password for ${userName}? A new temporary password will be generated.`)) {
        // Implementation for password reset would go here
        showToast('info', 'Password reset functionality would be implemented here');
    }
}

function showToast(type, message) {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${message}
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endsection