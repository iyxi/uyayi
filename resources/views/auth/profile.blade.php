@extends('layouts.customer')

@section('title', 'My Profile - Uyayi')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Profile Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    <img src="{{ $user->photo_url }}" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    <h3 class="fw-bold mb-1">{{ $user->name }}</h3>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                    <span class="badge bg-{{ $user->isActive() ? 'success' : 'secondary' }} mt-2">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-gear"></i> Update Profile
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i>
                            <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Photo -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-camera"></i> Profile Photo
                            </label>
                            <div class="text-center mb-3">
                                <img id="photoPreview" src="{{ $user->photo_url }}" 
                                     class="rounded-circle" width="100" height="100" style="object-fit: cover;">
                            </div>
                            <input type="file" 
                                   class="form-control @error('photo') is-invalid @enderror" 
                                   name="photo" 
                                   accept="image/*"
                                   onchange="previewPhoto(this)">
                            <div class="form-text">Upload a new profile photo (JPG, PNG, GIF up to 2MB)</div>
                        </div>

                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="bi bi-person"></i> Full Name
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-semibold">
                                    <i class="bi bi-phone"></i> Phone Number
                                </label>
                                <input type="text" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="bi bi-envelope"></i> Email Address
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label fw-semibold">
                                <i class="bi bi-geo-alt"></i> Address
                            </label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="2">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Password Section -->
                        <h6 class="fw-bold mb-3">Change Password (Optional)</h6>
                        
                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">
                                <i class="bi bi-lock"></i> Current Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password">
                            <div class="form-text">Leave blank if you don't want to change your password</div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- New Password -->
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill"></i> New Password
                                </label>
                                <input type="password" 
                                       class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" 
                                       name="new_password">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm New Password -->
                            <div class="col-md-6 mb-3">
                                <label for="new_password_confirmation" class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill"></i> Confirm New Password
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation">
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('homepage') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Store
                            </a>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="bi bi-check-lg"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Photo preview function
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection