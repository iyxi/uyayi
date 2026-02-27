@extends('layouts.admin')

@section('title', 'Store Settings - Uyayi Admin')

@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<span class="separator">&gt;</span>
<span class="current">Settings</span>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Store Settings</h1>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- General Settings -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>General Settings</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Store Name</label>
                        <input type="text" class="form-control" value="Uyayi Store">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Store Email</label>
                        <input type="email" class="form-control" value="contact@uyayi.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Store Phone</label>
                        <input type="text" class="form-control" placeholder="+1 234 567 8900">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Store Address</label>
                        <textarea class="form-control" rows="2" placeholder="Enter store address"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="alert('Settings saved!')">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- Currency & Tax -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Currency & Tax</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Currency</label>
                            <select class="form-select">
                                <option selected>USD - US Dollar</option>
                                <option>EUR - Euro</option>
                                <option>GBP - British Pound</option>
                                <option>CAD - Canadian Dollar</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" class="form-control" value="0" min="0" step="0.01">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="alert('Settings saved!')">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Links -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.account') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-person-gear me-3"></i>
                        Account Settings
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-box-seam me-3"></i>
                        Manage Products
                    </a>
                    <a href="{{ route('homepage') }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-shop me-3"></i>
                        Visit Store
                    </a>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>System Info</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <small class="text-muted">Laravel Version</small><br>
                        <strong>{{ app()->version() }}</strong>
                    </li>
                    <li class="mb-2">
                        <small class="text-muted">PHP Version</small><br>
                        <strong>{{ phpversion() }}</strong>
                    </li>
                    <li>
                        <small class="text-muted">Environment</small><br>
                        <strong>{{ app()->environment() }}</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
