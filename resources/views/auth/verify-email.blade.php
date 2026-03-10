@extends('layouts.customer')

@section('title', 'Verify Your Email - Uyayi')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0" style="border-radius: 15px;">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <div class="bg-warning bg-opacity-10 text-warning p-4 rounded-circle d-inline-flex">
                            <i class="bi bi-envelope-exclamation" style="font-size: 2rem;"></i>
                        </div>
                    </div>

                    <h2 class="fw-bold mb-3" style="color: var(--primary-blue-dark);">Verify Your Email</h2>
                    <p class="text-muted mb-4">
                        Thanks for signing up! Before getting started, please verify your email address by clicking 
                        on the link we just sent your email.
                    </p>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-info-circle"></i> A new verification link has been sent to your email address.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-4">
                        <p class="text-muted mb-3">
                            <strong>{{ auth()->user()->email }}</strong>
                        </p>
                        <small class="text-muted">
                            Check your spam folder if you don't see the email in your inbox.
                        </small>
                    </div>

                    <!-- Resend Verification Email -->
                    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-primary-custom btn-lg">
                            <i class="bi bi-arrow-clockwise"></i> Resend Verification Email
                        </button>
                    </form>

                    <!-- Logout Option -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>

                    <!-- Back to Home -->
                    <div class="mt-4">
                        <a href="{{ route('homepage') }}" class="text-muted text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Back to Store
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-question-circle text-primary"></i> Need Help?
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Check your spam/junk folder
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Make sure the email address is correct
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Wait a few minutes and try resending
                        </li>
                        <li>
                            <i class="bi bi-check text-success me-2"></i>
                            Contact support if issues persist
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endsection